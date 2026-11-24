<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'type', 'amount', 'description', 'order_id', 'debt_entry_id', 'product_id', 'qty', 'date',
    ];

    protected $casts = ['date' => 'date'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
