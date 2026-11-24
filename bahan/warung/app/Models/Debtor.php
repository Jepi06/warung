<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debtor extends Model
{
    protected $fillable = ['name', 'phone'];

    public function entries()
    {
        return $this->hasMany(DebtEntry::class);
    }

    /** Tambahkan kolom debt_total & paid_total supaya sisa kasbon bisa dihitung tanpa query ulang. */
    public function scopeWithBalance($query)
    {
        return $query
            ->withSum(['entries as debt_total' => fn ($e) => $e->where('type', 'debt')], 'amount')
            ->withSum(['entries as paid_total' => fn ($e) => $e->where('type', 'payment')], 'amount');
    }

    /** Sisa kasbon (kasbon - pembayaran). */
    public function getBalanceAttribute(): int
    {
        if (array_key_exists('debt_total', $this->attributes)) {
            return (int) $this->attributes['debt_total'] - (int) ($this->attributes['paid_total'] ?? 0);
        }

        return (int) $this->entries()->where('type', 'debt')->sum('amount')
             - (int) $this->entries()->where('type', 'payment')->sum('amount');
    }

    public static function outstanding()
    {
        return static::withBalance()->get()->filter(fn ($d) => $d->balance > 0);
    }

    public static function findOrCreateByName(string $name, ?string $phone = null): self
    {
        $name = trim(preg_replace('/\s+/', ' ', $name));

        $debtor = static::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();

        if (! $debtor) {
            return static::create(['name' => $name, 'phone' => $phone ?: null]);
        }

        if ($phone && ! $debtor->phone) {
            $debtor->update(['phone' => $phone]);
        }

        return $debtor;
    }

    public function waReminderUrl(): ?string
    {
        if (! $this->phone) {
            return null;
        }

        $n = preg_replace('/\D/', '', $this->phone);
        if (str_starts_with($n, '0')) {
            $n = '62' . substr($n, 1);
        }

        $text = "Halo {$this->name}, ini pengingat kasbon di " . config('shop.name')
            . ' sebesar Rp ' . number_format($this->balance, 0, ',', '.') . '. Terima kasih.';

        return 'https://wa.me/' . $n . '?text=' . rawurlencode($text);
    }
}
