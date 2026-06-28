<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_invoice_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}