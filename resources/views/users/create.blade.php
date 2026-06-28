<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Pengguna
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-4">
                <x-alert />
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <form method="POST" action="{{ route('users.store') }}" class="space-y-5" data-loading data-loading-text="Menyimpan...">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Nama Pengguna
                        </label>
                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               class="mt-1 w-full rounded-lg @error('name') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Email
                        </label>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="mt-1 w-full rounded-lg @error('email') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Username
                        </label>
                        <input type="text"
                               name="username"
                               value="{{ old('username') }}"
                               class="mt-1 w-full rounded-lg @error('username') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror"
                               required>
                        <p class="mt-1 text-xs text-gray-500">
                            Gunakan huruf, angka, dash, atau underscore. Username bisa dipakai untuk login.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Role
                        </label>
                        <select name="role"
                                class="mt-1 w-full rounded-lg @error('role') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror"
                                required>
                            <option value="">Pilih Role</option>
                            <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                            <option value="kasir" @selected(old('role') === 'kasir')>Kasir</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Password
                            </label>
                            <input type="password"
                                   name="password"
                                   class="mt-1 w-full rounded-lg @error('password') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Konfirmasi Password
                            </label>
                            <input type="password"
                                   name="password_confirmation"
                                   class="mt-1 w-full rounded-lg @error('password_confirmation') border-red-400 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror"
                                   required>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('users.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">
                            Batal
                        </a>

                        <button type="submit"
                                class="px-4 py-2 bg-teal-700 text-white rounded-lg text-sm font-medium hover:bg-teal-800">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
