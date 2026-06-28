<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Transaksi Penjualan
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-4">
                <x-alert />
            </div>

            <form method="POST" action="{{ route('sales.store') }}" id="saleForm" data-loading data-loading-text="Menyimpan...">
                @csrf

                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Data Produk
                        </h3>
                    </div>

                    <div class="p-6 overflow-x-auto">
                        <table class="w-full text-sm" id="itemsTable">
                            <thead class="bg-gray-50 text-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left">Produk</th>
                                    <th class="px-4 py-3 text-center">Stok</th>
                                    <th class="px-4 py-3 text-right">Harga</th>
                                    <th class="px-4 py-3 text-center">Jumlah</th>
                                    <th class="px-4 py-3 text-right">Subtotal</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td class="px-4 py-3">
                                        <select name="product_id[]"
                                                class="product-select w-full rounded-lg @error('product_id') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror"
                                                required>
                                            <option value="">Pilih Produk</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                        data-price="{{ $product->selling_price }}"
                                                        data-stock="{{ $product->stock }}">
                                                    {{ $product->code }} - {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td class="px-4 py-3 text-center stock-text">
                                        -
                                    </td>

                                    <td class="px-4 py-3 text-right price-text">
                                        Rp 0
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <input type="number"
                                               name="quantity[]"
                                               value="1"
                                               min="1"
                                               class="quantity-input w-24 rounded-lg @error('quantity') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror text-center"
                                               required>
                                    </td>

                                    <td class="px-4 py-3 text-right subtotal-text">
                                        Rp 0
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <button type="button"
                                                class="remove-row px-3 py-1 bg-red-100 text-red-700 rounded text-xs font-medium">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="mt-4">
                            <button type="button"
                                    id="addRow"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">
                                Tambah Baris Produk
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 bg-white rounded-lg shadow-sm">
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Transaksi</label>
                            <input type="text"
                                   id="totalDisplay"
                                   value="Rp 0"
                                   class="mt-1 w-full rounded-lg border-gray-300 bg-gray-100"
                                   readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah Bayar</label>
                            <input type="number"
                                   name="paid_amount"
                                   id="paidAmount"
                                   value="{{ old('paid_amount', 0) }}"
                                   min="0"
                                   class="mt-1 w-full rounded-lg @error('paid_amount') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kembalian</label>
                            <input type="text"
                                   id="changeDisplay"
                                   value="Rp 0"
                                   class="mt-1 w-full rounded-lg border-gray-300 bg-gray-100"
                                   readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catatan</label>
                            <textarea name="note"
                                      rows="3"
                                      class="mt-1 w-full rounded-lg @error('note') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror">{{ old('note') }}</textarea>
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('sales.index') }}"
                               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">
                                Batal
                            </a>

                            <button type="submit"
                                    class="px-4 py-2 bg-teal-700 text-white rounded-lg text-sm font-medium hover:bg-teal-800">
                                Simpan Transaksi
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        function formatRupiah(number) {
            return 'Rp ' + Number(number).toLocaleString('id-ID');
        }

        function calculateRow(row) {
            const select = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity-input');
            const selectedOption = select.options[select.selectedIndex];

            const price = Number(selectedOption.dataset.price || 0);
            const stock = selectedOption.dataset.stock || '-';
            const quantity = Number(quantityInput.value || 0);
            const subtotal = price * quantity;

            row.querySelector('.stock-text').textContent = stock;
            row.querySelector('.price-text').textContent = formatRupiah(price);
            row.querySelector('.subtotal-text').textContent = formatRupiah(subtotal);

            return subtotal;
        }

        function calculateTotal() {
            let total = 0;

            document.querySelectorAll('#itemsTable tbody tr').forEach(function(row) {
                total += calculateRow(row);
            });

            document.getElementById('totalDisplay').value = formatRupiah(total);

            const paidAmount = Number(document.getElementById('paidAmount').value || 0);
            const change = paidAmount - total;

            document.getElementById('changeDisplay').value = formatRupiah(change > 0 ? change : 0);
        }

        function bindEvents(row) {
            row.querySelector('.product-select').addEventListener('change', calculateTotal);
            row.querySelector('.quantity-input').addEventListener('input', calculateTotal);

            row.querySelector('.remove-row').addEventListener('click', function() {
                const totalRows = document.querySelectorAll('#itemsTable tbody tr').length;

                if (totalRows > 1) {
                    row.remove();
                    calculateTotal();
                }
            });
        }

        document.querySelectorAll('#itemsTable tbody tr').forEach(bindEvents);

        document.getElementById('addRow').addEventListener('click', function() {
            const tbody = document.querySelector('#itemsTable tbody');
            const firstRow = tbody.querySelector('tr');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelector('.product-select').value = '';
            newRow.querySelector('.quantity-input').value = 1;
            newRow.querySelector('.stock-text').textContent = '-';
            newRow.querySelector('.price-text').textContent = 'Rp 0';
            newRow.querySelector('.subtotal-text').textContent = 'Rp 0';

            tbody.appendChild(newRow);
            bindEvents(newRow);
        });

        document.getElementById('paidAmount').addEventListener('input', calculateTotal);

        calculateTotal();
    </script>
</x-app-layout>
