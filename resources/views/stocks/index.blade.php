<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Stok Barang
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-4">
                <x-alert />
            </div>

            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Filter Stok Barang
                    </h3>

                    {{-- Filter stok berdasarkan produk dan status aman/rendah --}}
                    <form method="GET" action="{{ route('stocks.index') }}" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Cari Produk
                            </label>
                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Kode, nama, kategori, atau satuan produk..."
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

                            <a href="{{ route('stocks.index') }}"
                               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel stok sekaligus form penyesuaian manual --}}
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Daftar Stok Barang
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Halaman ini digunakan untuk memantau stok dan mencatat penyesuaian stok manual.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Kode</th>
                                <th class="px-4 py-3 text-left">Produk</th>
                                <th class="px-4 py-3 text-left">Kategori</th>
                                <th class="px-4 py-3 text-center">Stok Saat Ini</th>
                                <th class="px-4 py-3 text-center">Stok Minimum</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-left">Penyesuaian Stok</th>
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

                                    <td class="px-4 py-3">
                                        <form method="POST"
                                              action="{{ route('stocks.adjust', $product) }}"
                                              class="space-y-2"
                                              data-loading
                                              data-loading-text="Menyimpan..."
                                              data-confirm
                                              data-confirm-title="Simpan penyesuaian stok?"
                                              data-confirm-message="Stok {{ $product->name }} akan disesuaikan dan perubahan dicatat ke riwayat stok."
                                              data-confirm-button="Simpan"
                                              data-confirm-variant="primary">
                                            @csrf
                                            @method('PATCH')

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                                <input type="number"
                                                       name="stock"
                                                       value="{{ $product->stock }}"
                                                       min="0"
                                                       class="rounded-lg @error('stock') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror text-sm"
                                                       required>

                                                <input type="text"
                                                       name="note"
                                                       placeholder="Keterangan penyesuaian"
                                                       class="md:col-span-2 rounded-lg @error('note') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror text-sm"
                                                       required>
                                            </div>

                                            <button type="submit"
                                                    class="px-3 py-1 bg-teal-700 text-white rounded text-xs font-medium hover:bg-teal-800">
                                                Simpan Penyesuaian
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <x-empty-state
                                    colspan="7"
                                    title="Data stok barang belum tersedia."
                                    description="Stok akan muncul setelah produk ditambahkan."
                                    :action-href="route('products.create')"
                                    action-label="Tambah Produk" />
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6">
                    {{ $products->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
