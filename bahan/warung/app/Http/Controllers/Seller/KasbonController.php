<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\DebtEntry;
use App\Models\Debtor;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasbonController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('show') === 'all' ? 'all' : 'open';

        $all = Debtor::withBalance()->withMax('entries as last_date', 'date')->orderBy('name')->get();

        $open      = $all->filter(fn ($d) => $d->balance > 0);
        $total     = $open->sum('balance');
        $openCount = $open->count();

        $debtors = ($filter === 'open' ? $open : $all)->sortByDesc('balance')->values();

        return view('seller.kasbon.index', compact('debtors', 'total', 'openCount', 'filter'));
    }

    public function create()
    {
        $names = Debtor::orderBy('name')->pluck('name');

        return view('seller.kasbon.create', compact('names'));
    }

    /** Catat kasbon manual (tanpa barang), misalnya utang lama. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:100',
            'phone'  => 'nullable|string|max:20',
            'amount' => 'required|integer|min:1',
            'note'   => 'nullable|string|max:255',
            'date'   => 'required|date',
        ]);

        $debtor = DB::transaction(function () use ($data) {
            $debtor = Debtor::findOrCreateByName($data['name'], $data['phone'] ?? null);

            $debtor->entries()->create([
                'type'   => 'debt',
                'amount' => $data['amount'],
                'note'   => $data['note'] ?? null,
                'date'   => $data['date'],
            ]);

            return $debtor;
        });

        return redirect()->route('seller.kasbon.show', $debtor)->with('success', 'Kasbon dicatat.');
    }

    public function show(Debtor $debtor)
    {
        $entries = $debtor->entries()->orderBy('date')->orderBy('id')->get();

        $running = 0;
        foreach ($entries as $e) {
            $running += $e->type === 'debt' ? $e->amount : -$e->amount;
            $e->setAttribute('running', $running);
        }

        $balance = $running;
        $entries = $entries->reverse()->values();

        return view('seller.kasbon.show', compact('debtor', 'entries', 'balance'));
    }

    public function update(Request $request, Debtor $debtor)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $data['name']));

        $exists = Debtor::where('id', '!=', $debtor->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Sudah ada pelanggan dengan nama itu.'])->withInput();
        }

        $debtor->update(['name' => $name, 'phone' => $data['phone'] ?: null]);

        return back()->with('success', 'Data pelanggan diperbarui.');
    }

    public function destroy(Debtor $debtor)
    {
        if ($debtor->entries()->exists()) {
            return back()->withErrors(['name' => 'Pelanggan masih punya catatan kasbon, tidak bisa dihapus.']);
        }

        $debtor->delete();

        return redirect()->route('seller.kasbon.index')->with('success', 'Pelanggan dihapus.');
    }

    /** Tambah kasbon manual untuk pelanggan ini. */
    public function addDebt(Request $request, Debtor $debtor)
    {
        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'note'   => 'nullable|string|max:255',
            'date'   => 'required|date',
        ]);

        $debtor->entries()->create([
            'type'   => 'debt',
            'amount' => $data['amount'],
            'note'   => $data['note'] ?? null,
            'date'   => $data['date'],
        ]);

        return back()->with('success', 'Kasbon ditambahkan.');
    }

    /** Terima pembayaran: sisa kasbon berkurang dan uangnya masuk ke kas. */
    public function pay(Request $request, Debtor $debtor)
    {
        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'note'   => 'nullable|string|max:255',
            'date'   => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($data, $debtor) {
                $balance = $debtor->balance;

                if ($data['amount'] > $balance) {
                    throw new \RuntimeException('Pembayaran melebihi sisa kasbon (Rp ' . number_format($balance, 0, ',', '.') . ').');
                }

                $entry = $debtor->entries()->create([
                    'type'   => 'payment',
                    'amount' => $data['amount'],
                    'note'   => $data['note'] ?? null,
                    'date'   => $data['date'],
                ]);

                Transaction::create([
                    'type'          => 'in',
                    'amount'        => $data['amount'],
                    'description'   => "Pembayaran kasbon - {$debtor->name}",
                    'debt_entry_id' => $entry->id,
                    'date'          => $data['date'],
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        return back()->with('success', 'Pembayaran dicatat dan masuk ke kas.');
    }

    public function destroyEntry(Debtor $debtor, DebtEntry $entry)
    {
        abort_unless($entry->debtor_id === $debtor->id, 404);

        if ($entry->order_id) {
            return back()->withErrors(['entry' => 'Kasbon dari penjualan kasir tidak bisa dihapus di sini.']);
        }

        DB::transaction(function () use ($entry) {
            Transaction::where('debt_entry_id', $entry->id)->delete();
            $entry->delete();
        });

        return back()->with('success', 'Catatan dihapus.');
    }
}
