<?php

namespace App\Http\Controllers;

use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockHistoryController extends Controller
{
    // Menampilkan audit perubahan stok dari transaksi, pembelian, dan penyesuaian.
    public function index(Request $request): View
    {
        $search = trim((string) $request->search);
        $type = $request->type;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $histories = StockHistory::with(['product.category', 'user'])
            ->when($search, function ($query) use ($search) {
                // Pencarian riwayat mencakup sumber, catatan, produk, kategori, dan user.
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('source', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('product', function ($productQuery) use ($search) {
                            $productQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%")
                                ->orWhereHas('category', function ($categoryQuery) use ($search) {
                                    $categoryQuery->where('name', 'like', "%{$search}%");
                                });
                        })
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('stock-histories.index', compact(
            'histories',
            'search',
            'type',
            'startDate',
            'endDate'
        ));
    }
}
