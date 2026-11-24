<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebtEntry extends Model
{
    protected $fillable = ['debtor_id', 'type', 'amount', 'note', 'date', 'order_id'];

    protected $casts = ['date' => 'date'];

    public function debtor()
    {
        return $this->belongsTo(Debtor::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
