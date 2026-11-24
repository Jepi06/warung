<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $to   = $request->date('to') ?? now();
        $type = in_array($request->type, ['in', 'out']) ? $request->type : null;

        $range = Transaction::whereBetween('date', [$from->toDateString(), $to->toDateString()]);

        $income  = (clone $range)->where('type', 'in')->sum('amount');
        $expense = (clone $range)->where('type', 'out')->sum('amount');

        $transactions = (clone $range)
            ->with('product')
            ->when($type, fn ($q, $t) => $q->where('type', $t))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('seller.transactions.index', compact('transactions', 'from', 'to', 'type', 'income', 'expense'));
    }

    public function create(Request $request)
    {
        $type     = $request->query('type') === 'in' ? 'in' : 'out';
        $products = Product::orderBy('name')->get(['id', 'name', 'stock']);

        return view('seller.transactions.create', compact('type', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'        => 'required|in:in,out',
            'amount'      => 'required|integer|min:1',
            'description' => 'required|string|max:255',
            'date'        => 'required|date',
            'product_id'  => 'nullable|exists:products,id',
            'qty'         => 'nullable|integer|min:1|required_with:product_id',
        ]);

        // Tambah stok hanya berlaku untuk pengeluaran (belanja barang)
        if ($data['type'] !== 'out' || empty($data['product_id'])) {
            $data['product_id'] = null;
            $data['qty']        = null;
        }

        DB::transaction(function () use ($data) {
            Transaction::create($data);

            if ($data['product_id']) {
                Product::whereKey($data['product_id'])->increment('stock', $data['qty']);
            }
        });

        $msg = 'Transaksi dicatat.';
        if ($data['product_id']) {
            $msg .= ' Stok bertambah ' . $data['qty'] . '.';
        }

        return redirect()->route('seller.transactions.index')->with('success', $msg);
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->order_id) {
            return back()->withErrors(['transaction' => 'Pemasukan dari pesanan tidak bisa dihapus manual.']);
        }

        if ($transaction->debt_entry_id) {
            return back()->withErrors(['transaction' => 'Pembayaran kasbon dihapus dari halaman Kasbon pelanggan.']);
        }

        DB::transaction(function () use ($transaction) {
            // Batalkan tambahan stok dari belanja ini
            if ($transaction->product_id && $transaction->qty) {
                $p = Product::lockForUpdate()->find($transaction->product_id);
                if ($p) {
                    $p->update(['stock' => max(0, $p->stock - $transaction->qty)]);
                }
            }
            $transaction->delete();
        });

        return back()->with('success', 'Transaksi dihapus.');
    }
}
