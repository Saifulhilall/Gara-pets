<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'source',
        'reference_id',
        'note',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}