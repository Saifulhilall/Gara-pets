<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Faktur Pembelian
            </h2>

            <a href="{{ route('purchase-invoices.create') }}"
               class="px-4 py-2 bg-teal-700 text-white rounded-lg text-sm font-medium hover:bg-teal-800">
                Tambah Faktur
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
                    <form method="GET" action="{{ route('purchase-invoices.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Cari Faktur</label>
                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Nomor faktur, supplier, atau admin..."
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
                            <a href="{{ route('purchase-invoices.index') }}"
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
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Nomor Faktur</th>
                                <th class="px-4 py-3 text-left">Tanggal</th>
                                <th class="px-4 py-3 text-left">Supplier</th>
                                <th class="px-4 py-3 text-left">Admin</th>
                                <th class="px-4 py-3 text-center">Jumlah Item</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse ($invoices as $invoice)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $invoice->invoice_number }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ \Carbon\Carbon::parse($invoice->purchase_date)->format('d/m/Y') }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $invoice->supplier_name ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $invoice->user->name ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        {{ $invoice->items->sum('quantity') }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('purchase-invoices.show', $invoice) }}"
                                           class="px-3 py-1 bg-teal-50 text-teal-700 rounded text-xs font-medium hover:bg-teal-100">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <x-empty-state
                                    colspan="7"
                                    title="Belum ada faktur pembelian."
                                    description="Buat faktur pembelian untuk mencatat barang masuk dan menambah stok otomatis."
                                    :action-href="route('purchase-invoices.create')"
                                    action-label="Tambah Faktur" />
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6">
                    {{ $invoices->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
