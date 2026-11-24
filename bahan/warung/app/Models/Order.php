<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'code', 'customer_name', 'customer_phone', 'note',
        'total', 'status', 'confirmed_at',
    ];

    protected $casts = ['confirmed_at' => 'datetime'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    private static function rp(int $n): string
    {
        return 'Rp ' . number_format($n, 0, ',', '.');
    }

    /** Teks pesanan yang dikirim ke WA penjual. */
    public function waText(): string
    {
        $lines = ['Halo ' . config('shop.name') . ', saya ' . $this->customer_name . ' mau pesan:', ''];

        foreach ($this->items as $i) {
            $lines[] = "- {$i->product_name} x{$i->qty} = " . self::rp($i->subtotal);
        }

        $lines[] = '';
        $lines[] = 'Total: ' . self::rp($this->total);
        if ($this->note) {
            $lines[] = 'Catatan: ' . $this->note;
        }
        $lines[] = 'No. pesanan: #' . $this->id;

        return implode("\n", $lines);
    }

    public function waUrl(): string
    {
        $number = preg_replace('/\D/', '', config('shop.wa_number'));

        return 'https://wa.me/' . $number . '?text=' . rawurlencode($this->waText());
    }

    /** Link WA ke pembeli (untuk penjual membalas). */
    public function customerWaUrl(): ?string
    {
        if (! $this->customer_phone) {
            return null;
        }
        $n = preg_replace('/\D/', '', $this->customer_phone);
        if (str_starts_with($n, '0')) {
            $n = '62' . substr($n, 1);
        }

        return 'https://wa.me/' . $n;
    }
}
