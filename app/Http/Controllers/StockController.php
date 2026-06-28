<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->search;
        $status = $request->status;

        $products = Product::with('category')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->when($status === 'low', function ($query) {
                $query->whereColumn('stock', '<=', 'minimum_stock');
            })
            ->when($status === 'safe', function ($query) {
                $query->whereColumn('stock', '>', 'minimum_stock');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('stocks.index', compact('products', 'search', 'status'));
    }

    public function adjust(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
            'note' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated, $product) {
            $product = Product::where('id', $product->id)->lockForUpdate()->first();

            $stockBefore = $product->stock;
            $stockAfter = (int) $validated['stock'];

            if ($stockBefore === $stockAfter) {
                return;
            }

            $product->update([
                'stock' => $stockAfter,
            ]);

            StockHistory::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => 'penyesuaian',
                'quantity' => abs($stockAfter - $stockBefore),
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'source' => 'penyesuaian_manual',
                'reference_id' => $product->id,
                'note' => $validated['note'],
            ]);
        });

        return redirect()
            ->route('stocks.index')
            ->with('success', 'Penyesuaian stok berhasil disimpan. Perubahan stok sudah masuk ke riwayat stok.');
    }
}
