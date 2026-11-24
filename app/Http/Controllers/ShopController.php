<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get();

        return view('shop.index', compact('products'));
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'customer_name'  => 'required|string|max:100',
            'items'          => 'required|array',
            'items.*'        => 'nullable|integer|min:0|max:9999',
        ]);

        $items = collect($data['items'])->map(fn ($q) => (int) $q)->filter(fn ($q) => $q > 0);

        if ($items->isEmpty()) {
            return back()->withErrors(['items' => 'Pilih minimal satu barang.'])->withInput();
        }

        $products = Product::whereIn('id', $items->keys())->get()->keyBy('id');

        // Cek stok (stok BELUM dikurangi di sini, baru saat penjual konfirmasi)
        foreach ($items as $id => $qty) {
            $p = $products->get($id);
            if (! $p) {
                return back()->withErrors(['items' => 'Ada barang yang sudah tidak tersedia.'])->withInput();
            }
            if ($p->stock < $qty) {
                return back()->withErrors([
                    'items' => "Stok {$p->name} tidak cukup (sisa {$p->stock}).",
                ])->withInput();
            }
        }

        $order = DB::transaction(function () use ($data, $items, $products) {
            $order = Order::create([
                'code'           => Str::lower(Str::random(10)),
                'customer_name'  => $data['customer_name'],
                'status'         => 'pending',
                'total'          => 0,
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
                ]);
                $total += $p->price * $qty;
            }

            $order->update(['total' => $total]);

            return $order;
        });

        return redirect()->route('thanks', $order->code);
    }

    public function thanks(string $code)
    {
        $order = Order::with('items')->where('code', $code)->firstOrFail();

        return view('shop.thanks', compact('order'));
    }
}
