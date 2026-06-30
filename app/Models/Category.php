<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    // Satu kategori dapat dipakai oleh banyak produk.
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
