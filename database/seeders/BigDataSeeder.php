<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class BigDataSeeder extends Seeder
{
    private const PRODUCT_TARGET = 800;
    private const PURCHASE_INVOICE_COUNT = 500;
    private const SALE_COUNT = 2000;
    private const STOCK_ADJUSTMENT_COUNT = 200;

    private array $petProductNames = [
        'Whiskas Adult Tuna 1kg',
        'Whiskas Junior Ocean Fish 500g',
        'Royal Canin Kitten 2kg',
        'Royal Canin Persian Adult 2kg',
        'Bolt Cat Food Salmon 1kg',
        'Me-O Cat Food Tuna 1.2kg',
        'Cat Choize Adult Chicken 800g',
        'Pedigree Adult Beef 1.5kg',
        'Pro Plan Puppy Chicken 2.5kg',
        'Pasir Kucing Wangi Lavender 10L',
        'Pasir Kucing Bentonite Lemon 10L',
        'Pasir Kucing Gumpal Apple 5L',
        'Shampoo Kucing Anti Kutu',
        'Shampoo Anjing Sensitive Skin',
        'Vitamin Kucing Imboost',
        'Vitamin Bulu Kucing',
        'Obat Tetes Telinga Hewan',
        'Mainan Bola Kucing',
        'Mainan Tali Gigitan Anjing',
        'Kalung Anjing Medium',
        'Kalung Kucing Bell',
        'Kandang Kucing Lipat',
        'Tempat Tidur Hewan Soft',
        'Tempat Makan Stainless',
        'Botol Minum Portable',
        'Tas Carrier Kucing',
        'Snack Kucing Tuna',
        'Snack Anjing Beef Stick',
        'Susu Kitten Premium',
        'Sisir Grooming Hewan',
    ];

    private array $suppliers = [
        'PT Pet Food Indonesia',
        'CV Sahabat Hewan',
        'Supplier Petcare Bandung',
        'Grosir Pakan Hewan Nusantara',
        'Distributor G-Pets',
        'Mandiri Pet Supply',
        'Global Animal Care',
        'Sentra Grooming Indonesia',
    ];

    private array $adjustmentNotes = [
        'Penyesuaian hasil stok opname.',
        'Selisih stok fisik dengan sistem.',
        'Koreksi stok barang rusak.',
        'Koreksi stok barang hilang.',
        'Koreksi data setelah pengecekan gudang.',
        'Penyesuaian retur barang dari display.',
    ];

    public function run(): void
    {
        // Seeder besar hanya untuk lokal/testing agar data produksi tidak tercampur dummy.
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('BigDataSeeder hanya boleh dijalankan pada environment local/testing.');
        }

        Model::unguarded(function (): void {
            $this->command?->info('Mulai membuat dummy big data G-Pets POS...');

            // Urutan penting: user dan kategori dibuat sebelum produk dan transaksi.
            $users = $this->seedUsers();
            $categories = $this->seedCategories();
            $products = $this->seedProducts($categories);

            // Pembelian dibuat lebih dulu agar stok cukup untuk simulasi penjualan.
            $this->seedPurchaseInvoices($users, $products);
            $this->seedSales($users, $products);
            $this->seedStockAdjustments($users, $products);

            $this->command?->info('Selesai membuat dummy big data G-Pets POS.');
            $this->command?->table(
                ['Data', 'Jumlah target'],
                [
                    ['Produk dummy PRD', self::PRODUCT_TARGET],
                    ['Faktur dummy INV-DMY', self::PURCHASE_INVOICE_COUNT],
                    ['Transaksi dummy TRX-DMY', self::SALE_COUNT],
                    ['Penyesuaian stok', self::STOCK_ADJUSTMENT_COUNT],
                ]
            );
        });
    }

    private function seedUsers()
    {
        // Akun dummy dibuat idempotent agar seeder aman dijalankan ulang.
        $users = collect();

        $users->push(User::updateOrCreate(
            ['email' => 'admin@garapetshop.test'],
            [
                'name' => 'Admin Gara Petshop',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        ));

        $users->push(User::updateOrCreate(
            ['email' => 'kasir@garapetshop.test'],
            [
                'name' => 'Kasir Gara Petshop',
                'username' => 'kasir',
                'password' => Hash::make('password'),
                'role' => 'kasir',
            ]
        ));

        for ($i = 1; $i <= 10; $i++) {
            $users->push(User::updateOrCreate(
                ['email' => "kasir{$i}@garapetshop.test"],
                [
                    'name' => "Kasir Dummy {$i}",
                    'username' => "kasir{$i}",
                    'password' => Hash::make('password'),
                    'role' => 'kasir',
                ]
            ));
        }

        return $users->values();
    }

    private function seedCategories()
    {
        $categoryNames = [
            'Makanan Kucing',
            'Makanan Anjing',
            'Pasir Kucing',
            'Vitamin dan Obat',
            'Shampoo dan Grooming',
            'Aksesoris Hewan',
            'Mainan Hewan',
            'Kandang dan Tempat Tidur',
            'Peralatan Makan dan Minum',
            'Perlengkapan Kebersihan',
            'Snack Hewan',
            'Susu dan Nutrisi',
            'Kalung dan Tali',
            'Tas dan Carrier',
            'Produk Perawatan Kulit',
        ];

        return collect($categoryNames)
            ->map(fn (string $name) => Category::firstOrCreate(
                ['name' => $name],
                ['description' => "Kategori dummy untuk {$name}."]
            ))
            ->values();
    }

    private function seedProducts($categories)
    {
        $units = ['pcs', 'pack', 'kg', 'liter', 'botol', 'karung'];
        $now = now();
        $created = 0;

        for ($i = 1; $i <= self::PRODUCT_TARGET; $i++) {
            $code = 'PRD'.str_pad((string) $i, 4, '0', STR_PAD_LEFT);

            // Produk yang sudah ada dilewati agar kode PRD tetap unik.
            if (Product::where('code', $code)->exists()) {
                continue;
            }

            $purchasePrice = mt_rand(8_000, 450_000);
            $margin = mt_rand(15, 60) / 100;
            $sellingPrice = (int) ceil($purchasePrice * (1 + $margin) / 500) * 500;
            $minimumStock = mt_rand(5, 30);
            $stock = $i % 8 === 0 ? mt_rand(0, $minimumStock) : mt_rand($minimumStock + 1, 300);
            $baseName = $this->petProductNames[array_rand($this->petProductNames)];
            $variants = ['Small', 'Medium', 'Large', 'Premium', 'Original', 'Salmon', 'Tuna', 'Chicken'];
            $variant = $variants[array_rand($variants)];

            Product::create([
                'category_id' => $categories->random()->id,
                'code' => $code,
                'name' => "{$baseName} {$variant} #{$i}",
                'purchase_price' => $purchasePrice,
                'selling_price' => $sellingPrice,
                'stock' => $stock,
                'minimum_stock' => $minimumStock,
                'unit' => $units[array_rand($units)],
                'description' => "Produk dummy petshop untuk testing performa, pagination, filter, dan laporan.",
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $created++;
        }

        $this->command?->info("Produk dummy baru dibuat: {$created}. Total produk PRD siap pakai: ".Product::where('code', 'like', 'PRD%')->count().'.');

        return Product::where('code', 'like', 'PRD%')->get();
    }

    private function seedPurchaseInvoices($users, $products): void
    {
        $existingCount = PurchaseInvoice::where('invoice_number', 'like', 'INV-DMY-%')->count();

        for ($i = 1; $i <= self::PURCHASE_INVOICE_COUNT; $i++) {
            // Tiap faktur punya item, update stok, dan histori dalam satu transaksi.
            DB::transaction(function () use ($users, $products, $existingCount, $i): void {
                $date = $this->randomDate();
                $invoiceNumber = 'INV-DMY-'.$date->format('Ymd').'-'.str_pad((string) ($existingCount + $i), 5, '0', STR_PAD_LEFT);
                $user = $users->random();
                $itemCount = mt_rand(2, 8);
                $selectedProducts = $products->random($itemCount);
                $totalAmount = 0;

                $invoice = PurchaseInvoice::create([
                    'user_id' => $user->id,
                    'invoice_number' => $invoiceNumber,
                    'supplier_name' => $this->suppliers[array_rand($this->suppliers)],
                    'purchase_date' => $date->toDateString(),
                    'total_amount' => 0,
                    'note' => "Faktur pembelian dummy {$invoiceNumber}.",
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                foreach ($selectedProducts as $product) {
                    // Lock produk agar stok masuk tetap konsisten saat seeding.
                    $product = Product::whereKey($product->id)->lockForUpdate()->first();
                    $quantity = mt_rand(5, 100);
                    $price = max(1_000, (int) round($product->purchase_price * mt_rand(92, 108) / 100));
                    $subtotal = $quantity * $price;
                    $stockBefore = $product->stock;
                    $stockAfter = $stockBefore + $quantity;

                    PurchaseItem::create([
                        'purchase_invoice_id' => $invoice->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $subtotal,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);

                    $product->update([
                        'stock' => $stockAfter,
                        'purchase_price' => $price,
                    ]);

                    StockHistory::create([
                        'product_id' => $product->id,
                        'user_id' => $user->id,
                        'type' => 'masuk',
                        'quantity' => $quantity,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'source' => 'pembelian',
                        'reference_id' => $invoice->id,
                        'note' => 'Stok bertambah dari faktur pembelian '.$invoiceNumber,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);

                    $totalAmount += $subtotal;
                }

                $invoice->update(['total_amount' => $totalAmount]);
            });

            if ($i % 100 === 0) {
                $this->command?->info("Faktur dummy dibuat: {$i}/".self::PURCHASE_INVOICE_COUNT);
            }
        }
    }

    private function seedSales($users, $products): void
    {
        $existingCount = Sale::where('transaction_code', 'like', 'TRX-DMY-%')->count();

        for ($i = 1; $i <= self::SALE_COUNT; $i++) {
            // Penjualan dummy tetap mengikuti aturan stok tidak boleh minus.
            DB::transaction(function () use ($users, $products, $existingCount, $i): void {
                $date = $this->randomDate();
                $transactionCode = 'TRX-DMY-'.$date->format('Ymd').'-'.str_pad((string) ($existingCount + $i), 6, '0', STR_PAD_LEFT);
                $user = $users->random();
                $candidateProducts = $products->shuffle();
                $selectedProducts = collect();
                $targetItemCount = mt_rand(1, 5);

                foreach ($candidateProducts as $product) {
                    $currentStock = (int) Product::whereKey($product->id)->value('stock');

                    // Hanya produk dengan stok tersedia yang masuk kandidat transaksi.
                    if ($currentStock > 0) {
                        $selectedProducts->push($product);
                    }

                    if ($selectedProducts->count() >= $targetItemCount) {
                        break;
                    }
                }

                if ($selectedProducts->isEmpty()) {
                    return;
                }

                $sale = Sale::create([
                    'user_id' => $user->id,
                    'transaction_code' => $transactionCode,
                    'transaction_date' => $date,
                    'total_amount' => 0,
                    'paid_amount' => 0,
                    'change_amount' => 0,
                    'note' => "Transaksi penjualan dummy {$transactionCode}.",
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                $totalAmount = 0;

                foreach ($selectedProducts as $product) {
                    $product = Product::whereKey($product->id)->lockForUpdate()->first();

                    // Stok dicek ulang setelah lock untuk menghindari stok negatif.
                    if ($product->stock <= 0) {
                        continue;
                    }

                    $quantity = min(mt_rand(1, 5), $product->stock);
                    $price = (int) $product->selling_price;
                    $subtotal = $quantity * $price;
                    $stockBefore = $product->stock;
                    $stockAfter = $stockBefore - $quantity;

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $subtotal,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);

                    $product->update(['stock' => $stockAfter]);

                    StockHistory::create([
                        'product_id' => $product->id,
                        'user_id' => $user->id,
                        'type' => 'keluar',
                        'quantity' => $quantity,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'source' => 'penjualan',
                        'reference_id' => $sale->id,
                        'note' => 'Stok berkurang karena transaksi penjualan '.$transactionCode,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);

                    $totalAmount += $subtotal;
                }

                if ($totalAmount <= 0) {
                    $sale->delete();
                    return;
                }

                $paidAmount = (int) ceil(($totalAmount + mt_rand(0, 100_000)) / 1_000) * 1_000;

                $sale->update([
                    'total_amount' => $totalAmount,
                    'paid_amount' => $paidAmount,
                    'change_amount' => $paidAmount - $totalAmount,
                ]);
            });

            if ($i % 250 === 0) {
                $this->command?->info("Transaksi dummy dibuat: {$i}/".self::SALE_COUNT);
            }
        }
    }

    private function seedStockAdjustments($users, $products): void
    {
        for ($i = 1; $i <= self::STOCK_ADJUSTMENT_COUNT; $i++) {
            // Penyesuaian stok dibuat realistis tanpa menghasilkan stok negatif.
            DB::transaction(function () use ($users, $products): void {
                $date = $this->randomDate();
                $product = Product::whereKey($products->random()->id)->lockForUpdate()->first();
                $user = $users->random();
                $stockBefore = $product->stock;
                $delta = mt_rand(-20, 30);
                $stockAfter = max(0, $stockBefore + $delta);

                if ($stockBefore === $stockAfter) {
                    $stockAfter = $stockBefore + mt_rand(1, 10);
                }

                $quantity = abs($stockAfter - $stockBefore);

                $product->update(['stock' => $stockAfter]);

                StockHistory::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'type' => 'penyesuaian',
                    'quantity' => $quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'source' => 'penyesuaian_manual',
                    'reference_id' => $product->id,
                    'note' => $this->adjustmentNotes[array_rand($this->adjustmentNotes)],
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            });
        }
    }

    private function randomDate(): Carbon
    {
        // Sebar data dalam 180 hari terakhir agar laporan periode bisa diuji.
        return Carbon::now()
            ->subDays(mt_rand(0, 180))
            ->setTime(mt_rand(8, 21), mt_rand(0, 59), mt_rand(0, 59));
    }
}
