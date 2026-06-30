<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Faktur Pembelian
            </h2>

            <a href="{{ route('purchase-invoices.index') }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Informasi utama faktur pembelian --}}
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Nomor Faktur</p>
                        <p class="font-semibold text-gray-800">{{ $purchaseInvoice->invoice_number }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Tanggal Pembelian</p>
                        <p class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($purchaseInvoice->purchase_date)->format('d/m/Y') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Supplier</p>
                        <p class="font-semibold text-gray-800">{{ $purchaseInvoice->supplier_name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Dicatat Oleh</p>
                        <p class="font-semibold text-gray-800">{{ $purchaseInvoice->user->name ?? '-' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-gray-500">Catatan</p>
                        <p class="font-semibold text-gray-800">{{ $purchaseInvoice->note ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Detail barang masuk pada faktur --}}
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Barang Masuk
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Kode</th>
                                <th class="px-4 py-3 text-left">Produk</th>
                                <th class="px-4 py-3 text-center">Jumlah</th>
                                <th class="px-4 py-3 text-right">Harga Beli</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @foreach ($purchaseInvoice->items as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        {{ $item->product->code ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $item->product->name ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        {{ $item->quantity }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot class="bg-gray-50 font-semibold">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right">Total</td>
                                <td class="px-4 py-3 text-right">
                                    Rp {{ number_format($purchaseInvoice->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
