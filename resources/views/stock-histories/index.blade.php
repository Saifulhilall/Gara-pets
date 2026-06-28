<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Riwayat Perubahan Stok
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Filter Riwayat Stok
                    </h3>

                    <form method="GET" action="{{ route('stock-histories.index') }}" class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-4">
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
                                Tipe
                            </label>
                            <select name="type" class="mt-1 w-full rounded-lg border-gray-300">
                                <option value="">Semua Tipe</option>
                                <option value="masuk" @selected($type === 'masuk')>Masuk</option>
                                <option value="keluar" @selected($type === 'keluar')>Keluar</option>
                                <option value="penyesuaian" @selected($type === 'penyesuaian')>Penyesuaian</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Tanggal Awal
                            </label>
                            <input type="date"
                                   name="start_date"
                                   value="{{ $startDate }}"
                                   class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Tanggal Akhir
                            </label>
                            <input type="date"
                                   name="end_date"
                                   value="{{ $endDate }}"
                                   class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div class="md:col-span-5 flex justify-end gap-3">
                            <a href="{{ route('stock-histories.index') }}"
                               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">
                                Reset
                            </a>

                            <button type="submit"
                                    class="px-4 py-2 bg-teal-700 text-white rounded-lg text-sm font-medium hover:bg-teal-800">
                                Tampilkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Daftar Riwayat Stok
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Riwayat ini digunakan untuk menelusuri perubahan stok setiap produk.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Tanggal</th>
                                <th class="px-4 py-3 text-left">Produk</th>
                                <th class="px-4 py-3 text-center">Tipe</th>
                                <th class="px-4 py-3 text-center">Jumlah</th>
                                <th class="px-4 py-3 text-center">Stok Sebelum</th>
                                <th class="px-4 py-3 text-center">Stok Sesudah</th>
                                <th class="px-4 py-3 text-left">Sumber</th>
                                <th class="px-4 py-3 text-left">Pengguna</th>
                                <th class="px-4 py-3 text-left">Keterangan</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse ($histories as $history)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        {{ $history->created_at->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-800">
                                            {{ $history->product->name ?? '-' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $history->product->code ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        @if ($history->type === 'masuk')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                                Masuk
                                            </span>
                                        @elseif ($history->type === 'keluar')
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                                Keluar
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                                Penyesuaian
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        {{ $history->quantity }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        {{ $history->stock_before }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        {{ $history->stock_after }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ str_replace('_', ' ', $history->source ?? '-') }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $history->user->name ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $history->note ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                        Riwayat perubahan stok belum tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6">
                    {{ $histories->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
