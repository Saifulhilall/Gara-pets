<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    // Baris item selalu terhubung ke satu transaksi penjualan.
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // Produk dibutuhkan untuk harga, nama, dan pelacakan stok keluar.
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
