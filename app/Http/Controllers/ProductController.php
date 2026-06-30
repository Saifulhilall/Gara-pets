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
    // Menampilkan daftar produk dengan pencarian dan pengurutan tabel.
    public function index(Request $request): View
    {
        $search = trim((string) $request->search);
        // Whitelist kolom sorting agar query tetap aman dari input bebas.
        $allowedSorts = [
            'code',
            'name',
            'category',
            'purchase_price',
            'selling_price',
            'stock',
            'status',
        ];
        $sort = in_array($request->query('sort'), $allowedSorts, true)
            ? $request->query('sort')
            : 'latest';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';

        $productsQuery = Product::with('category')
            ->when($search, function ($query) use ($search) {
                // Pencarian dikelompokkan agar tidak mengganggu filter lain.
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('unit', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'like', "%{$search}%");
                        });
                });
            });

        // Kolom relasi dan status stok butuh aturan pengurutan khusus.
        match ($sort) {
            'category' => $productsQuery->orderBy(
                Category::select('name')
                    ->whereColumn('categories.id', 'products.category_id')
                    ->limit(1),
                $direction
            ),
            'status' => $productsQuery->orderByRaw('(stock <= minimum_stock) '.$direction),
            'latest' => $productsQuery->latest(),
            default => $productsQuery->orderBy($sort, $direction),
        };

        $products = $productsQuery
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products', 'search', 'sort', 'direction'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        // Stok awal produk langsung dicatat sebagai riwayat penyesuaian.
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

        // Perubahan data produk dan riwayat stok disimpan dalam satu transaksi.
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
        // Produk yang sudah dipakai transaksi tidak boleh dihapus agar histori tetap utuh.
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
