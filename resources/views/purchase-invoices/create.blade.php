<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Faktur Pembelian
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('purchase-invoices.store') }}">
                @csrf

                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Faktur</label>
                            <input type="text"
                                   name="invoice_number"
                                   value="{{ old('invoice_number') }}"
                                   placeholder="Contoh: INV-001"
                                   class="mt-1 w-full rounded-lg border-gray-300"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Supplier</label>
                            <input type="text"
                                   name="supplier_name"
                                   value="{{ old('supplier_name') }}"
                                   placeholder="Nama pemasok"
                                   class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Pembelian</label>
                            <input type="date"
                                   name="purchase_date"
                                   value="{{ old('purchase_date', date('Y-m-d')) }}"
                                   class="mt-1 w-full rounded-lg border-gray-300"
                                   required>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Data Barang Masuk
                        </h3>
                    </div>

                    <div class="p-6 overflow-x-auto">
                        <table class="w-full text-sm" id="itemsTable">
                            <thead class="bg-gray-50 text-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left">Produk</th>
                                    <th class="px-4 py-3 text-center">Stok Saat Ini</th>
                                    <th class="px-4 py-3 text-center">Jumlah Masuk</th>
                                    <th class="px-4 py-3 text-right">Harga Beli</th>
                                    <th class="px-4 py-3 text-right">Subtotal</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td class="px-4 py-3">
                                        <select name="product_id[]"
                                                class="product-select w-full rounded-lg border-gray-300"
                                                required>
                                            <option value="">Pilih Produk</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                        data-stock="{{ $product->stock }}"
                                                        data-price="{{ $product->purchase_price }}">
                                                    {{ $product->code }} - {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td class="px-4 py-3 text-center stock-text">
                                        -
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <input type="number"
                                               name="quantity[]"
                                               value="1"
                                               min="1"
                                               class="quantity-input w-24 rounded-lg border-gray-300 text-center"
                                               required>
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <input type="number"
                                               name="price[]"
                                               value="0"
                                               min="0"
                                               class="price-input w-32 rounded-lg border-gray-300 text-right"
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
                            <label class="block text-sm font-medium text-gray-700">Total Pembelian</label>
                            <input type="text"
                                   id="totalDisplay"
                                   value="Rp 0"
                                   class="mt-1 w-full rounded-lg border-gray-300 bg-gray-100"
                                   readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catatan</label>
                            <textarea name="note"
                                      rows="3"
                                      class="mt-1 w-full rounded-lg border-gray-300">{{ old('note') }}</textarea>
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('purchase-invoices.index') }}"
                               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">
                                Batal
                            </a>

                            <button type="submit"
                                    class="px-4 py-2 bg-teal-700 text-white rounded-lg text-sm font-medium hover:bg-teal-800">
                                Simpan Faktur
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
            const priceInput = row.querySelector('.price-input');
            const selectedOption = select.options[select.selectedIndex];

            const stock = selectedOption.dataset.stock || '-';
            const quantity = Number(quantityInput.value || 0);
            const price = Number(priceInput.value || 0);
            const subtotal = quantity * price;

            row.querySelector('.stock-text').textContent = stock;
            row.querySelector('.subtotal-text').textContent = formatRupiah(subtotal);

            return subtotal;
        }

        function calculateTotal() {
            let total = 0;

            document.querySelectorAll('#itemsTable tbody tr').forEach(function(row) {
                total += calculateRow(row);
            });

            document.getElementById('totalDisplay').value = formatRupiah(total);
        }

        function bindEvents(row) {
            const select = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity-input');
            const priceInput = row.querySelector('.price-input');

            select.addEventListener('change', function() {
                const selectedOption = select.options[select.selectedIndex];
                const price = selectedOption.dataset.price || 0;

                priceInput.value = price;
                calculateTotal();
            });

            quantityInput.addEventListener('input', calculateTotal);
            priceInput.addEventListener('input', calculateTotal);

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
            newRow.querySelector('.price-input').value = 0;
            newRow.querySelector('.stock-text').textContent = '-';
            newRow.querySelector('.subtotal-text').textContent = 'Rp 0';

            tbody.appendChild(newRow);
            bindEvents(newRow);
        });

        calculateTotal();
    </script>
</x-app-layout>
