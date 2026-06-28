<?php

namespace App\Http\Controllers;

use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->search;
        $type = $request->type;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $histories = StockHistory::with(['product.category', 'user'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
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