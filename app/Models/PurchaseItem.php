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

    // Baris item selalu terhubung ke satu faktur pembelian.
    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    // Produk dipakai untuk update stok dan harga beli terbaru.
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
