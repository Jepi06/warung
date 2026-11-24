<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $orders = Order::withCount('items')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('seller.orders.index', compact('orders', 'status'));
    }

    public function show(Order $order)
    {
        $order->load('items.product');

        return view('seller.orders.show', compact('order'));
    }

    /** Barang sudah diserahkan: kurangi stok + catat pemasukan. */
    public function confirm(Order $order)
    {
        try {
            DB::transaction(function () use ($order) {
                $order = Order::lockForUpdate()->findOrFail($order->id);

                if ($order->status !== 'pending') {
                    throw new \RuntimeException('Pesanan ini sudah diproses.');
                }

                foreach ($order->items as $item) {
                    if (! $item->product_id) {
                        throw new \RuntimeException("Barang {$item->product_name} sudah dihapus dari daftar.");
                    }

                    $product = Product::lockForUpdate()->find($item->product_id);

                    if (! $product || $product->stock < $item->qty) {
                        $sisa = $product->stock ?? 0;
                        throw new \RuntimeException("Stok {$item->product_name} tidak cukup (sisa {$sisa}, diminta {$item->qty}).");
                    }

                    $product->decrement('stock', $item->qty);
                    $item->update(['cost' => $product->cost_price]);
                }

                $order->update(['status' => 'completed', 'confirmed_at' => now()]);

                Transaction::create([
                    'type'        => 'in',
                    'amount'      => $order->total,
                    'description' => "Penjualan #{$order->id} - {$order->customer_name}",
                    'order_id'    => $order->id,
                    'date'        => now()->toDateString(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['order' => $e->getMessage()]);
        }

        return redirect()->route('seller.orders.show', $order)->with('success', 'Pesanan selesai. Stok dikurangi dan pemasukan dicatat.');
    }

    public function cancel(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->withErrors(['order' => 'Hanya pesanan pending yang bisa dibatalkan.']);
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('seller.orders.index')->with('success', 'Pesanan dibatalkan.');
    }
}
