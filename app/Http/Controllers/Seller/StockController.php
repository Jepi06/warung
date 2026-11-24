<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get();

        return view('seller.stock.index', compact('products'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'date'              => 'required|date',
            'rows'              => 'required|array|min:1',
            'rows.*.id'         => 'required|integer|exists:products,id',
            'rows.*.old_stock'  => 'required|integer|min:0',
            'rows.*.stock'      => 'required|integer|min:0',
            'rows.*.cost_price' => 'required|integer|min:0',
            'rows.*.price'      => 'required|integer|min:0',
        ]);

        $updated = 0;
        $spent   = 0;

        DB::transaction(function () use ($data, &$updated, &$spent) {
            foreach ($data['rows'] as $row) {
                $p = Product::lockForUpdate()->find($row['id']);
                if (! $p) {
                    continue;
                }

                // Selisih dihitung dari stok saat halaman dibuka, lalu diterapkan ke stok
                // terbaru. Jadi kalau ada pesanan dikonfirmasi di sela-sela, stok tetap benar.
                $diff = $row['stock'] - $row['old_stock'];

                $p->update([
                    'stock'      => max(0, $p->stock + $diff),
                    'cost_price' => $row['cost_price'],
                    'price'      => $row['price'],
                ]);

                // Stok naik + ada harga beli => otomatis jadi pengeluaran
                if ($diff > 0 && $row['cost_price'] > 0) {
                    $amount = $diff * $row['cost_price'];

                    Transaction::create([
                        'type'        => 'out',
                        'amount'      => $amount,
                        'description' => "Belanja stok {$p->name} ({$diff} x Rp " . number_format($row['cost_price'], 0, ',', '.') . ')',
                        'product_id'  => $p->id,
                        'qty'         => $diff,
                        'date'        => $data['date'],
                    ]);

                    $spent += $amount;
                }

                $updated++;
            }
        });

        $msg = "{$updated} barang diperbarui.";
        if ($spent > 0) {
            $msg .= ' Pengeluaran Rp ' . number_format($spent, 0, ',', '.') . ' tercatat.';
        }

        return redirect()->route('seller.stock.index')->with('success', $msg);
    }
}
