<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Transaksi Penjualan
            </h2>

            <a href="{{ route('sales.create') }}"
               class="px-4 py-2 bg-teal-700 text-white rounded-lg text-sm font-medium hover:bg-teal-800">
                Tambah Transaksi
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-4">
                <x-alert />
            </div>

            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="p-6">
                    {{-- Filter transaksi berdasarkan kata kunci dan periode --}}
                    <form method="GET" action="{{ route('sales.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Cari Transaksi</label>
                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Kode transaksi, kasir, produk, atau catatan..."
                                   class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Awal</label>
                            <input type="date"
                                   name="start_date"
                                   value="{{ $startDate }}"
                                   class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                            <input type="date"
                                   name="end_date"
                                   value="{{ $endDate }}"
                                   class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div class="md:col-span-4 flex justify-end gap-3">
                            <a href="{{ route('sales.index') }}"
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

            {{-- Tabel riwayat transaksi penjualan --}}
            <div class="bg-white rounded-lg shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Kode Transaksi</th>
                                <th class="px-4 py-3 text-left">Tanggal</th>
                                <th class="px-4 py-3 text-left">Kasir</th>
                                <th class="px-4 py-3 text-center">Jumlah Item</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-4 py-3 text-right">Bayar</th>
                                <th class="px-4 py-3 text-right">Kembalian</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse ($sales as $sale)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $sale->transaction_code }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ \Carbon\Carbon::parse($sale->transaction_date)->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $sale->user->name ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        {{ $sale->items->sum('quantity') }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        Rp {{ number_format($sale->change_amount, 0, ',', '.') }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('sales.show', $sale) }}"
                                           class="px-3 py-1 bg-teal-50 text-teal-700 rounded text-xs font-medium hover:bg-teal-100">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <x-empty-state
                                    colspan="8"
                                    title="Belum ada transaksi penjualan."
                                    description="Buat transaksi pertama untuk mencatat penjualan dan mengurangi stok otomatis."
                                    :action-href="route('sales.create')"
                                    action-label="Tambah Transaksi" />
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6">
                    {{ $sales->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
