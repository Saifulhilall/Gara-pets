<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Data Produk
            </h2>

            <a href="{{ route('products.create') }}"
               class="px-4 py-2 bg-teal-700 text-white rounded-lg text-sm font-medium hover:bg-teal-800">
                Tambah Produk
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-4">
                <x-alert />
            </div>

            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b">
                    {{-- Pencarian produk tetap mempertahankan pagination dan sorting --}}
                    <form method="GET" action="{{ route('products.index') }}" class="flex gap-3">
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Cari kode, nama, kategori, atau satuan produk..."
                               class="w-full rounded-lg border-gray-300 focus:border-gray-500 focus:ring-gray-500">

                        <button type="submit"
                                class="px-4 py-2 bg-teal-700 text-white rounded-lg text-sm font-medium hover:bg-teal-800">
                            Cari
                        </button>

                        <a href="{{ route('products.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">
                            Reset
                        </a>
                    </form>
                </div>

                {{-- Tabel master produk dengan header sortable --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <x-sortable-header field="code" label="Kode" :sort="$sort" :direction="$direction" />
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <x-sortable-header field="name" label="Nama Produk" :sort="$sort" :direction="$direction" />
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <x-sortable-header field="category" label="Kategori" :sort="$sort" :direction="$direction" />
                                </th>
                                <th class="px-4 py-3 text-right">
                                    <x-sortable-header field="purchase_price" label="Harga Beli" align="right" :sort="$sort" :direction="$direction" />
                                </th>
                                <th class="px-4 py-3 text-right">
                                    <x-sortable-header field="selling_price" label="Harga Jual" align="right" :sort="$sort" :direction="$direction" />
                                </th>
                                <th class="px-4 py-3 text-center">
                                    <x-sortable-header field="stock" label="Stok" align="center" :sort="$sort" :direction="$direction" />
                                </th>
                                <th class="px-4 py-3 text-center">
                                    <x-sortable-header field="status" label="Status" align="center" :sort="$sort" :direction="$direction" />
                                </th>
                                <th class="px-4 py-3 text-center">Aksi</th>
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

                                    <td class="px-4 py-3 text-right">
                                        Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        {{ $product->stock }} {{ $product->unit }}
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
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('products.edit', $product) }}"
                                               class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-medium">
                                                Edit
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('products.destroy', $product) }}"
                                                  data-confirm
                                                  data-confirm-title="Hapus produk?"
                                                  data-confirm-message="Produk {{ $product->name }} akan dihapus permanen jika belum digunakan dalam transaksi atau faktur."
                                                  data-confirm-button="Hapus"
                                                  data-confirm-variant="danger">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="px-3 py-1 bg-red-100 text-red-700 rounded text-xs font-medium">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <x-empty-state
                                    colspan="8"
                                    title="Data produk belum tersedia."
                                    description="Tambahkan produk pertama agar transaksi dan stok bisa mulai dicatat."
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
