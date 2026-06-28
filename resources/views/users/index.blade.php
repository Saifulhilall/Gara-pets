<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Data Pengguna
            </h2>

            <a href="{{ route('users.create') }}"
               class="px-4 py-2 bg-teal-700 text-white rounded-lg text-sm font-medium hover:bg-teal-800">
                Tambah Pengguna
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
                    <h3 class="text-lg font-semibold text-gray-800">
                        Filter Pengguna
                    </h3>

                    <form method="GET" action="{{ route('users.index') }}" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Cari Pengguna
                            </label>
                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Nama atau email pengguna..."
                                   class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Role
                            </label>
                            <select name="role" class="mt-1 w-full rounded-lg border-gray-300">
                                <option value="">Semua Role</option>
                                <option value="admin" @selected($role === 'admin')>Admin</option>
                                <option value="kasir" @selected($role === 'kasir')>Kasir</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-3">
                            <button type="submit"
                                    class="px-4 py-2 bg-teal-700 text-white rounded-lg text-sm font-medium hover:bg-teal-800">
                                Tampilkan
                            </button>

                            <a href="{{ route('users.index') }}"
                               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-left">Email</th>
                                <th class="px-4 py-3 text-center">Role</th>
                                <th class="px-4 py-3 text-left">Tanggal Dibuat</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse ($users as $user)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $user->name }}

                                        @if ($user->id === auth()->id())
                                            <span class="ml-2 text-xs text-gray-500">
                                                (Akun Anda)
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $user->email }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        @if ($user->role === 'admin')
                                            <span class="px-2 py-1 text-xs rounded-full bg-teal-100 text-teal-700">
                                                Admin
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                                Kasir
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $user->created_at->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('users.edit', $user) }}"
                                               class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-medium">
                                                Edit
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('users.destroy', $user) }}"
                                                  data-confirm
                                                  data-confirm-title="Hapus pengguna?"
                                                  data-confirm-message="Pengguna {{ $user->name }} akan dihapus permanen. Aksi ini tidak bisa dibatalkan."
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
                                    colspan="5"
                                    title="Data pengguna belum tersedia."
                                    description="Tambahkan admin atau kasir untuk mengatur akses sistem."
                                    :action-href="route('users.create')"
                                    action-label="Tambah Pengguna" />
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6">
                    {{ $users->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
