<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->search;

        $products = Product::with('category')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products', 'search'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        DB::transaction(function () use ($validated) {
            $product = Product::create($validated);

            if ($product->stock > 0) {
                StockHistory::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'penyesuaian',
                    'quantity' => $product->stock,
                    'stock_before' => 0,
                    'stock_after' => $product->stock,
                    'source' => 'produk_baru',
                    'reference_id' => $product->id,
                    'note' => 'Stok awal saat produk dibuat.',
                ]);
            }
        });

        return redirect()
            ->route('products.index')
            ->with('success', 'Data produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate($this->validationRules($product));

        DB::transaction(function () use ($validated, $product) {
            $stockBefore = $product->stock;

            $product->update($validated);

            $stockAfter = $product->stock;

            if ($stockBefore !== $stockAfter) {
                StockHistory::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'penyesuaian',
                    'quantity' => abs($stockAfter - $stockBefore),
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'source' => 'penyesuaian_manual',
                    'reference_id' => $product->id,
                    'note' => 'Stok diubah melalui halaman data produk.',
                ]);
            }
        });

        return redirect()
            ->route('products.index')
            ->with('success', 'Data produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->saleItems()->exists() || $product->purchaseItems()->exists()) {
            return redirect()
                ->route('products.index')
                ->with('error', 'Produk tidak dapat dihapus karena sudah digunakan dalam transaksi atau faktur pembelian.');
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Data produk berhasil dihapus.');
    }

    private function validationRules(?Product $product = null): array
    {
        $codeRule = Rule::unique('products', 'code');

        if ($product) {
            $codeRule->ignore($product->id);
        }

        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:50', $codeRule],
            'name' => ['required', 'string', 'max:255'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:30'],
            'description' => ['nullable', 'string'],
        ];
    }
}