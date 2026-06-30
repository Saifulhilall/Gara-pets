<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'code',
        'name',
        'purchase_price',
        'selling_price',
        'stock',
        'minimum_stock',
        'unit',
        'description',
    ];

    // Produk terhubung ke kategori untuk kebutuhan filter dan laporan.
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Detail penjualan dipakai untuk mengecek apakah produk pernah terjual.
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Detail pembelian dipakai untuk melacak sumber stok masuk.
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    // Produk memiliki riwayat semua perubahan stok.
    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }
}
