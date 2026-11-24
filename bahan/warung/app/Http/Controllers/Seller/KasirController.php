<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\DebtEntry;
use App\Models\Debtor;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KasirController extends Controller
{
    public function index()
    {
        $products    = Product::orderBy('name')->get();
        $debtorNames = Debtor::orderBy('name')->pluck('name');

        return view('seller.kasir.index', compact('products', 'debtorNames'));
    }

    /**
     * Penjualan langsung. Stok langsung berkurang.
     * Tunai   : pemasukan kas langsung tercatat.
     * Kasbon  : dicatat sebagai utang pelanggan, kas masuk saat dibayar.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'payment'       => 'required|in:cash,kasbon',
            'customer_name' => 'nullable|string|max:100',
            'items'         => 'required|array',
            'items.*'       => 'nullable|integer|min:0|max:9999',
        ]);

        $kasbon = $data['payment'] === 'kasbon';
        $name   = trim($data['customer_name'] ?? '');

        if ($kasbon && $name === '') {
            return back()->withErrors(['customer_name' => 'Nama pelanggan wajib diisi untuk kasbon.']);
        }

        $items = collect($data['items'])->map(fn ($q) => (int) $q)->filter(fn ($q) => $q > 0);

        if ($items->isEmpty()) {
            return back()->withErrors(['items' => 'Pilih minimal satu barang.']);
        }

        try {
            $order = DB::transaction(function () use ($items, $kasbon, $name) {
                $products = Product::whereIn('id', $items->keys())->lockForUpdate()->get()->keyBy('id');

                foreach ($items as $id => $qty) {
                    $p = $products->get($id);
                    if (! $p) {
                        throw new \RuntimeException('Ada barang yang sudah tidak tersedia.');
                    }
                    if ($p->stock < $qty) {
                        throw new \RuntimeException("Stok {$p->name} tidak cukup (sisa {$p->stock}).");
                    }
                }

                $debtor = $kasbon ? Debtor::findOrCreateByName($name) : null;
                $buyer  = $debtor ? $debtor->name : ($name !== '' ? $name : 'Pembeli langsung');

                $order = Order::create([
                    'code'          => Str::lower(Str::random(10)),
                    'customer_name' => $buyer,
                    'status'        => 'completed',
                    'confirmed_at'  => now(),
                    'total'         => 0,
                ]);

                $total = 0;
                foreach ($items as $id => $qty) {
                    $p = $products[$id];

                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $p->id,
                        'product_name' => $p->name,
                        'qty'          => $qty,
                        'price'        => $p->price,
                        'cost'         => $p->cost_price,
                    ]);

                    $p->decrement('stock', $qty);
                    $total += $p->price * $qty;
                }

                $order->update(['total' => $total]);

                if ($debtor) {
                    DebtEntry::create([
                        'debtor_id' => $debtor->id,
                        'type'      => 'debt',
                        'amount'    => $total,
                        'note'      => "Belanja #{$order->id}",
                        'date'      => now()->toDateString(),
                        'order_id'  => $order->id,
                    ]);
                } else {
                    Transaction::create([
                        'type'        => 'in',
                        'amount'      => $total,
                        'description' => "Penjualan #{$order->id} - {$buyer}",
                        'order_id'    => $order->id,
                        'date'        => now()->toDateString(),
                    ]);
                }

                return $order;
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['items' => $e->getMessage()]);
        }

        $rp  = 'Rp ' . number_format($order->total, 0, ',', '.');
        $msg = $kasbon
            ? "Penjualan #{$order->id} ({$rp}) dicatat sebagai kasbon atas nama {$order->customer_name}. Stok sudah dikurangi."
            : "Penjualan #{$order->id} tercatat: {$rp}. Stok sudah dikurangi.";

        return redirect()->route('seller.kasir.index')->with('success', $msg);
    }
}
