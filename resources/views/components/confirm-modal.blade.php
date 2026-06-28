<div id="confirmModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 px-4 py-6"
     aria-hidden="true">
    <div class="w-full max-w-md rounded-lg bg-white shadow-xl">
        <div class="p-6">
            <div id="confirmModalIcon"
                 class="mb-4 flex h-11 w-11 items-center justify-center rounded-full bg-red-100 text-lg font-bold text-red-700">
                !
            </div>

            <h3 id="confirmModalTitle" class="text-lg font-semibold text-gray-900">
                Konfirmasi aksi
            </h3>

            <p id="confirmModalMessage" class="mt-2 text-sm leading-6 text-gray-600">
                Apakah Anda yakin ingin melanjutkan?
            </p>
        </div>

        <div class="flex justify-end gap-3 border-t bg-gray-50 px-6 py-4">
            <button type="button"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200"
                    data-confirm-cancel>
                Batal
            </button>

            <button type="button"
                    id="confirmModalButton"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700"
                    data-confirm-submit>
                Konfirmasi
            </button>
        </div>
    </div>
</div>
