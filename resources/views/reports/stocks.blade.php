<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Laporan Stok
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4">
                <x-alert />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Total Produk</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">
                        {{ $totalProducts }}
                    </p>
                </div>

                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Total Stok Barang</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">
                        {{ $totalStock }}
                    </p>
                </div>

                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Produk Stok Rendah</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">
                        {{ $lowStockCount }}
                    </p>
                </div>

                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Nilai Stok</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">
                        Rp {{ number_format($stockValue, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Filter Laporan Stok
                    </h3>

                    <form method="GET" action="{{ route('reports.stocks') }}" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Cari Produk
                            </label>
                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Kode atau nama produk..."
                                   class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Status Stok
                            </label>
                            <select name="status" class="mt-1 w-full rounded-lg border-gray-300">
                                <option value="">Semua Status</option>
                                <option value="low" @selected($status === 'low')>Stok Rendah</option>
                                <option value="safe" @selected($status === 'safe')>Aman</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-3">
                            <button type="submit"
                                    class="px-4 py-2 bg-teal-700 text-white rounded-lg text-sm font-medium hover:bg-teal-800">
                                Tampilkan
                            </button>

                            <a href="{{ route('reports.stocks') }}"
                               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Daftar Laporan Stok Barang
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Laporan ini menampilkan kondisi stok barang berdasarkan data produk yang tersedia.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Kode</th>
                                <th class="px-4 py-3 text-left">Produk</th>
                                <th class="px-4 py-3 text-left">Kategori</th>
                                <th class="px-4 py-3 text-center">Stok</th>
                                <th class="px-4 py-3 text-center">Stok Minimum</th>
                                <th class="px-4 py-3 text-right">Harga Beli</th>
                                <th class="px-4 py-3 text-right">Nilai Stok</th>
                                <th class="px-4 py-3 text-center">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse ($products as $product)
                                <tr>
                                    <td class="px-4 py-3">
                                        {{ $product->code }}
                                    </td>

                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $product->name }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $product->category->name ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        {{ $product->stock }} {{ $product->unit }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        {{ $product->minimum_stock }} {{ $product->unit }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        Rp {{ number_format($product->stock * $product->purchase_price, 0, ',', '.') }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        @if ($product->stock <= $product->minimum_stock)
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                                Stok Rendah
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                                Aman
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <x-empty-state
                                    colspan="8"
                                    title="Data laporan stok belum tersedia."
                                    description="Laporan stok akan tampil setelah data produk tersedia atau filter disesuaikan."
                                    :action-href="route('products.create')"
                                    action-label="Tambah Produk" />
                            @endforelse
                        </tbody>

                        <tfoot class="bg-gray-50 font-semibold">
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-right">
                                    Total Nilai Stok Pada Filter Ini
                                </td>
                                <td class="px-4 py-3 text-right">
                                    Rp {{ number_format($stockValue, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="p-6">
                    {{ $products->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
