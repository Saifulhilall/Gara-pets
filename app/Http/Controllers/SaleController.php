<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    // Menampilkan riwayat penjualan dengan filter tanggal dan kata kunci.
    public function index(Request $request): View
    {
        $search = trim((string) $request->search);
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $sales = Sale::with(['user', 'items.product'])
            ->when($search, function ($query) use ($search) {
                // Pencarian mencakup kode transaksi, kasir, catatan, dan produk.
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
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales', 'search', 'startDate', 'endDate'));
    }

    public function create(): View
    {
        // Produk dipakai sebagai pilihan item pada form transaksi.
        $products = Product::orderBy('name')->get();

        return view('sales.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Validasi memastikan baris produk, jumlah, dan pembayaran siap diproses.
        $validated = $request->validate([
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'array', 'min:1'],
            'quantity.*' => ['required', 'integer', 'min:1'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        // Semua perubahan stok dan detail transaksi dibungkus agar tetap sinkron.
        DB::transaction(function () use ($validated, $request) {
            $totalAmount = 0;
            $items = [];

            foreach ($validated['product_id'] as $index => $productId) {
                // Lock produk saat transaksi untuk mencegah stok berubah bersamaan.
                $product = Product::where('id', $productId)->lockForUpdate()->first();
                $quantity = (int) $validated['quantity'][$index];

                // Cegah transaksi jika jumlah jual melebihi stok tersedia.
                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => "Stok produk {$product->name} tidak mencukupi. Stok tersedia: {$product->stock}.",
                    ]);
                }

                $subtotal = $product->selling_price * $quantity;
                $totalAmount += $subtotal;

                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $product->selling_price,
                    'subtotal' => $subtotal,
                ];
            }

            if ($validated['paid_amount'] < $totalAmount) {
                throw ValidationException::withMessages([
                    'paid_amount' => 'Jumlah pembayaran tidak boleh kurang dari total transaksi.',
                ]);
            }

            $sale = Sale::create([
                'user_id' => auth()->id(),
                'transaction_code' => $this->generateTransactionCode(),
                'transaction_date' => now(),
                'total_amount' => $totalAmount,
                'paid_amount' => $validated['paid_amount'],
                'change_amount' => $validated['paid_amount'] - $totalAmount,
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($items as $item) {
                $product = $item['product'];
                $stockBefore = $product->stock;
                $stockAfter = $stockBefore - $item['quantity'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $product->update([
                    'stock' => $stockAfter,
                ]);

                // Setiap stok keluar disimpan ke riwayat untuk audit barang.
                StockHistory::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'keluar',
                    'quantity' => $item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'source' => 'penjualan',
                    'reference_id' => $sale->id,
                    'note' => 'Stok berkurang karena transaksi penjualan ' . $sale->transaction_code,
                ]);
            }
        });

        return redirect()
            ->route('sales.index')
            ->with('success', 'Transaksi penjualan berhasil disimpan. Stok produk otomatis berkurang dan riwayat stok sudah tercatat.');
    }

    public function show(Sale $sale): View
    {
        // Detail transaksi memuat kasir, item, produk, dan kategori.
        $sale->load(['user', 'items.product.category']);

        return view('sales.show', compact('sale'));
    }

    private function generateTransactionCode(): string
    {
        // Kode transaksi menggabungkan waktu dan user agar mudah ditelusuri.
        return 'TRX-' . now()->format('YmdHis') . '-' . auth()->id();
    }
}
