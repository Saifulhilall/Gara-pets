<?php

use App\Http\Controllers\ProductController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockHistoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

// Halaman utama diarahkan ke login karena POS hanya untuk pengguna terdaftar.
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard menampilkan ringkasan utama POS.
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

    // Fitur transaksi penjualan dapat diakses oleh user yang sudah login.
    Route::middleware(['auth', 'verified'])->group(function () {
     Route::resource('/transaksi', SaleController::class)
    ->only(['index', 'create', 'store', 'show'])
    ->names('sales')
    ->parameters(['transaksi' => 'sale']);
    });

require __DIR__.'/auth.php';


    // Riwayat stok dipisahkan agar admin dan kasir bisa menelusuri pergerakan barang.
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/riwayat-stok', [StockHistoryController::class, 'index'])
        ->name('stock-histories.index');
    });

    // Fitur master data dan laporan dibatasi untuk admin.
    Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
        Route::resource('/produk', ProductController::class)
            ->names('products')
            ->parameters(['produk' => 'product']);

            // Faktur pembelian menambah stok dan hanya dikelola admin.
            Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
                Route::resource('/faktur-pembelian', PurchaseInvoiceController::class)
                ->only(['index', 'create', 'store', 'show'])
                ->names('purchase-invoices')
                ->parameters(['faktur-pembelian' => 'purchaseInvoice']);
            });

        // Penyesuaian stok manual hanya tersedia untuk admin.
        Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
        Route::get('/stok', [StockController::class, 'index'])
        ->name('stocks.index');
        
        Route::patch('/stok/{product}/penyesuaian', [StockController::class, 'adjust'])
            ->name('stocks.adjust');
        });

        // Laporan penjualan dan stok digunakan untuk monitoring toko.
        Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
        Route::get('/laporan-penjualan', [ReportController::class, 'sales'])
            ->name('reports.sales');
        
        Route::get('/laporan-stok', [ReportController::class, 'stocks'])
            ->name('reports.stocks');  
        });

        // Pengelolaan akun internal admin dan kasir.
        Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
            Route::resource('/pengguna', UserController::class)
            ->names('users')
            ->parameters(['pengguna' => 'user']);
        });
    });
