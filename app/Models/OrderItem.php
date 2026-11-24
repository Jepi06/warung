<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'product_name', 'qty', 'price', 'cost'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute(): int
    {
        return $this->qty * $this->price;
    }

    /** Ringkasan penjualan selesai dalam rentang tanggal: laba (jual - beli), omzet, jumlah pesanan. */
    public static function profitBetween(CarbonInterface $from, CarbonInterface $to): array
    {
        $items = static::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.confirmed_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->get(['order_items.order_id', 'order_items.price', 'order_items.cost', 'order_items.qty']);

        return [
            'profit'  => (int) $items->sum(fn ($i) => ($i->price - $i->cost) * $i->qty),
            'revenue' => (int) $items->sum(fn ($i) => $i->price * $i->qty),
            'orders'  => $items->pluck('order_id')->unique()->count(),
            'nocost'  => $items->where('cost', 0)->count(),
        ];
    }
}
