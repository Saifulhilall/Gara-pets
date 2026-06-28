<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_code',
        'transaction_date',
        'total_amount',
        'paid_amount',
        'change_amount',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}