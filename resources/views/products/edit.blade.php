<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Produk
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-4">
                <x-alert />
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                {{-- Form perubahan produk; perubahan stok akan tercatat ke riwayat --}}
                <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-5" data-loading data-loading-text="Memperbarui...">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori</label>
                        <select name="category_id" class="mt-1 w-full rounded-lg @error('category_id') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror">
                            <option value="">Tanpa Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kode Produk</label>
                            <input type="text" name="code" value="{{ old('code', $product->code) }}"
                                   class="mt-1 w-full rounded-lg @error('code') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}"
                                   class="mt-1 w-full rounded-lg @error('name') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Harga Beli</label>
                            <input type="number" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}"
                                   class="mt-1 w-full rounded-lg @error('purchase_price') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror" min="0" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Harga Jual</label>
                            <input type="number" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}"
                                   class="mt-1 w-full rounded-lg @error('selling_price') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror" min="0" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stok</label>
                            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"
                                   class="mt-1 w-full rounded-lg @error('stock') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror" min="0" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stok Minimum</label>
                            <input type="number" name="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock) }}"
                                   class="mt-1 w-full rounded-lg @error('minimum_stock') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror" min="0" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Satuan</label>
                            <input type="text" name="unit" value="{{ old('unit', $product->unit) }}"
                                   class="mt-1 w-full rounded-lg @error('unit') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" rows="3"
                                  class="mt-1 w-full rounded-lg @error('description') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('products.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">
                            Batal
                        </a>

                        <button type="submit"
                                class="px-4 py-2 bg-teal-700 text-white rounded-lg text-sm font-medium hover:bg-teal-800">
                            Perbarui
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
