<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_number',
        'supplier_name',
        'purchase_date',
        'total_amount',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}