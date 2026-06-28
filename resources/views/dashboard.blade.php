<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4">
                <x-alert />
            </div>

            <div class="mb-6 bg-white rounded-lg shadow-sm p-6 border-l-4 border-teal-700">
                <h3 class="text-lg font-semibold text-gray-800">
                    Selamat datang, {{ Auth::user()->name }}
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Anda masuk sebagai {{ Auth::user()->role }} pada Sistem Point of Sale G-Pets Gara PetShop.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Total Produk</p>
                    <p class="text-2xl font-bold text-teal-700 mt-2">{{ $totalProducts }}</p>
                </div>

                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Total Transaksi</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">{{ $totalSales }}</p>
                </div>

                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Faktur Pembelian</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">{{ $totalPurchases }}</p>
                </div>

                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Produk Stok Rendah</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">{{ $lowStockProducts }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Transaksi Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">{{ $todayTransactions }}</p>
                </div>

                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Pendapatan Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">
                        Rp {{ number_format($todayIncome, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Transaksi Terbaru
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[520px] text-sm">
                            <thead class="bg-gray-50 text-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left">Kode</th>
                                    <th class="px-4 py-3 text-left">Kasir</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">
                                @forelse ($recentSales as $sale)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-800 whitespace-nowrap">
                                            {{ $sale->transaction_code }}
                                        </td>

                                        <td class="px-4 py-3">
                                            {{ $sale->user->name ?? '-' }}
                                        </td>

                                        <td class="px-4 py-3 text-right">
                                            Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <x-empty-state
                                        colspan="3"
                                        title="Belum ada transaksi."
                                        description="Transaksi terbaru akan muncul setelah penjualan dibuat." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Produk Stok Rendah
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[520px] text-sm">
                            <thead class="bg-gray-50 text-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left">Produk</th>
                                    <th class="px-4 py-3 text-center">Stok</th>
                                    <th class="px-4 py-3 text-center">Minimum</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">
                                @forelse ($lowStockList as $product)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-800">{{ $product->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $product->code }}</div>
                                        </td>

                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            {{ $product->stock }} {{ $product->unit }}
                                        </td>

                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            {{ $product->minimum_stock }} {{ $product->unit }}
                                        </td>
                                    </tr>
                                @empty
                                    <x-empty-state
                                        colspan="3"
                                        title="Tidak ada produk stok rendah."
                                        description="Semua stok produk masih berada di atas batas minimum." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Riwayat Stok Terbaru
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Tanggal</th>
                                <th class="px-4 py-3 text-left">Produk</th>
                                <th class="px-4 py-3 text-center">Tipe</th>
                                <th class="px-4 py-3 text-center">Jumlah</th>
                                <th class="px-4 py-3 text-left">Pengguna</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse ($recentStockHistories as $history)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        {{ $history->created_at->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $history->product->name ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        {{ ucfirst($history->type) }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        {{ $history->quantity }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $history->user->name ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <x-empty-state
                                    colspan="5"
                                    title="Belum ada riwayat stok."
                                    description="Riwayat akan muncul setelah stok berubah." />
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
