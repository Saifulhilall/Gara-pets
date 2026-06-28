<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Makanan Hewan',
            'Perlengkapan Hewan',
            'Obat dan Vitamin',
            'Aksesoris Hewan',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate([
                'name' => $category,
            ]);
        }
    }
}