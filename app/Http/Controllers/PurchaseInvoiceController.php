<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseItem;
use App\Models\StockHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PurchaseInvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->search;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $invoices = PurchaseInvoice::with(['user', 'items.product'])
            ->when($search, function ($query) use ($search) {
                $query->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('purchase_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('purchase_date', '<=', $endDate);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('purchase-invoices.index', compact(
            'invoices',
            'search',
            'startDate',
            'endDate'
        ));
    }

    public function create(): View
    {
        $products = Product::orderBy('name')->get();

        return view('purchase-invoices.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('purchase_invoices', 'invoice_number'),
            ],
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['required', 'date'],
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'array', 'min:1'],
            'quantity.*' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'array', 'min:1'],
            'price.*' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        if (count($validated['product_id']) !== count(array_unique($validated['product_id']))) {
            return back()
                ->withErrors(['product_id' => 'Produk yang sama tidak boleh dipilih lebih dari satu kali dalam satu faktur pembelian.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated) {
            $totalAmount = 0;
            $items = [];

            foreach ($validated['product_id'] as $index => $productId) {
                $product = Product::where('id', $productId)->lockForUpdate()->first();

                $quantity = (int) $validated['quantity'][$index];
                $price = (float) $validated['price'][$index];
                $subtotal = $quantity * $price;

                $totalAmount += $subtotal;

                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ];
            }

            $invoice = PurchaseInvoice::create([
                'user_id' => auth()->id(),
                'invoice_number' => $validated['invoice_number'],
                'supplier_name' => $validated['supplier_name'] ?? null,
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => $totalAmount,
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($items as $item) {
                $product = $item['product'];
                $stockBefore = $product->stock;
                $stockAfter = $stockBefore + $item['quantity'];

                PurchaseItem::create([
                    'purchase_invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $product->update([
                    'stock' => $stockAfter,
                    'purchase_price' => $item['price'],
                ]);

                StockHistory::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'masuk',
                    'quantity' => $item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'source' => 'pembelian',
                    'reference_id' => $invoice->id,
                    'note' => 'Stok bertambah dari faktur pembelian ' . $invoice->invoice_number,
                ]);
            }
        });

        return redirect()
            ->route('purchase-invoices.index')
            ->with('success', 'Faktur pembelian berhasil disimpan. Stok produk otomatis bertambah dan riwayat stok sudah tercatat.');
    }

    public function show(PurchaseInvoice $purchaseInvoice): View
    {
        $purchaseInvoice->load(['user', 'items.product.category']);

        return view('purchase-invoices.show', compact('purchaseInvoice'));
    }
}
