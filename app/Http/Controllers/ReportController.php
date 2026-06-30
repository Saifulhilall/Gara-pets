<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    // Menyusun laporan penjualan berdasarkan periode dan kata kunci.
    public function sales(Request $request): View
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $search = trim((string) $request->search);

        $query = Sale::with(['user', 'items.product'])
            ->when($search, function ($query) use ($search) {
                // Filter laporan tetap sejalan dengan pencarian pada riwayat transaksi.
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('transaction_code', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('items.product', function ($productQuery) use ($search) {
                            $productQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                    });
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('transaction_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('transaction_date', '<=', $endDate);
            });

        // Clone query dipakai untuk ringkasan agar pagination tidak memotong total.
        $summarySales = (clone $query)->get();

        $totalTransactions = $summarySales->count();
        $totalIncome = $summarySales->sum('total_amount');
        $totalItemsSold = $summarySales->sum(function ($sale) {
            return $sale->items->sum('quantity');
        });

        $sales = $query
            ->latest('transaction_date')
            ->paginate(10)
            ->withQueryString();

        return view('reports.sales', compact(
            'sales',
            'search',
            'startDate',
            'endDate',
            'totalTransactions',
            'totalIncome',
            'totalItemsSold'
        ));
    }

    public function stocks(Request $request): View
    {
        $search = trim((string) $request->search);
        $status = $request->status;

        $query = Product::with('category')
            ->when($search, function ($query) use ($search) {
                // Filter laporan stok mengikuti kode, nama, satuan, dan kategori produk.
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('unit', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status === 'low', function ($query) {
                $query->whereColumn('stock', '<=', 'minimum_stock');
            })
            ->when($status === 'safe', function ($query) {
                $query->whereColumn('stock', '>', 'minimum_stock');
            });

        // Ringkasan dihitung dari semua hasil filter, bukan hanya halaman aktif.
        $summaryProducts = (clone $query)->get();

        $totalProducts = $summaryProducts->count();
        $totalStock = $summaryProducts->sum('stock');
        $lowStockCount = $summaryProducts->filter(function ($product) {
            return $product->stock <= $product->minimum_stock;
        })->count();

        $stockValue = $summaryProducts->sum(function ($product) {
            return $product->stock * $product->purchase_price;
        });

        $products = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('reports.stocks', compact(
            'products',
            'search',
            'status',
            'totalProducts',
            'totalStock',
            'lowStockCount',
            'stockValue'
        ));
    }
}
