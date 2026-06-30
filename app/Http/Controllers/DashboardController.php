<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\Sale;
use App\Models\StockHistory;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // Ambil ringkasan utama dan aktivitas terbaru untuk dashboard POS.
    public function index(): View
    {
        // Transaksi hari ini dipakai ulang untuk jumlah transaksi dan pendapatan harian.
        $todaySales = Sale::whereDate('transaction_date', today())->get();

        return view('dashboard', [
            'totalProducts' => Product::count(),
            'totalSales' => Sale::count(),
            'totalPurchases' => PurchaseInvoice::count(),
            'totalStockHistories' => StockHistory::count(),
            'lowStockProducts' => Product::whereColumn('stock', '<=', 'minimum_stock')->count(),

            'todayTransactions' => $todaySales->count(),
            'todayIncome' => $todaySales->sum('total_amount'),

            'recentSales' => Sale::with('user')
                ->latest('transaction_date')
                ->take(5)
                ->get(),

            'lowStockList' => Product::with('category')
                ->whereColumn('stock', '<=', 'minimum_stock')
                ->orderBy('stock')
                ->take(5)
                ->get(),

            'recentStockHistories' => StockHistory::with(['product', 'user'])
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
