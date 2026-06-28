<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Laporan Penjualan
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4">
                <x-alert />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Total Transaksi</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">
                        {{ $totalTransactions }}
                    </p>
                </div>

                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Total Item Terjual</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">
                        {{ $totalItemsSold }}
                    </p>
                </div>

                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Filter Laporan Penjualan
                    </h3>

                    <form method="GET" action="{{ route('reports.sales') }}" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Cari Transaksi
                            </label>
                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Kode transaksi atau nama kasir..."
                                   class="mt-1 w-full rounded-lg border-gray-300">
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

                        <div class="md:col-span-4 flex justify-end gap-3">
                            <a href="{{ route('reports.sales') }}"
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
                        Daftar Laporan Penjualan
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Laporan ini menampilkan transaksi penjualan berdasarkan filter yang dipilih.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Kode Transaksi</th>
                                <th class="px-4 py-3 text-left">Tanggal</th>
                                <th class="px-4 py-3 text-left">Kasir</th>
                                <th class="px-4 py-3 text-center">Item Terjual</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-4 py-3 text-right">Bayar</th>
                                <th class="px-4 py-3 text-right">Kembalian</th>
                                <th class="px-4 py-3 text-center">Detail</th>
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
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <x-empty-state
                                    colspan="8"
                                    title="Data laporan penjualan belum tersedia."
                                    description="Laporan akan terisi setelah ada transaksi penjualan sesuai filter." />
                            @endforelse
                        </tbody>

                        <tfoot class="bg-gray-50 font-semibold">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right">
                                    Total Pada Filter Ini
                                </td>
                                <td class="px-4 py-3 text-right">
                                    Rp {{ number_format($totalIncome, 0, ',', '.') }}
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="p-6">
                    {{ $sales->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
