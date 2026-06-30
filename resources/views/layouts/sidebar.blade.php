@php
    // Daftar menu dipusatkan di sini agar role dan status aktif mudah dirawat.
    $menus = [
        [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'active' => 'dashboard',
            'roles' => ['admin', 'kasir'],
        ],
        [
            'label' => 'Data Produk',
            'route' => 'products.index',
            'active' => 'products.*',
            'roles' => ['admin'],
        ],
        [
            'label' => 'Transaksi Penjualan',
            'route' => 'sales.index',
            'active' => 'sales.*',
            'roles' => ['admin', 'kasir'],
        ],
        [
            'label' => 'Faktur Pembelian',
            'route' => 'purchase-invoices.index',
            'active' => 'purchase-invoices.*',
            'roles' => ['admin'],
        ],
        [
            'label' => 'Stok Barang',
            'route' => 'stocks.index',
            'active' => 'stocks.*',
            'roles' => ['admin'],
        ],
        [
            'label' => 'Riwayat Stok',
            'route' => 'stock-histories.index',
            'active' => 'stock-histories.*',
            'roles' => ['admin', 'kasir'],
        ],
        [
            'label' => 'Laporan Penjualan',
            'route' => 'reports.sales',
            'active' => 'reports.sales',
            'roles' => ['admin'],
        ],
        [
            'label' => 'Laporan Stok',
            'route' => 'reports.stocks',
            'active' => 'reports.stocks',
            'roles' => ['admin'],
        ],
        [
            'label' => 'Data Pengguna',
            'route' => 'users.index',
            'active' => 'users.*',
            'roles' => ['admin'],
        ],
    ];
@endphp

<aside class="w-60 min-h-screen bg-white border-r border-gray-200">
    {{-- Identitas aplikasi dan role pengguna --}}
    <div class="h-24 px-6 border-b flex items-center">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo-gpets.jpeg') }}"
                 alt="Logo G-Pets Gara PetShop"
                 class="h-12 w-12 shrink-0 object-contain rounded-lg bg-white border border-gray-200">

            <div class="min-w-0 leading-none">
                <h1 class="text-lg font-bold text-gray-800 leading-5">
                    G-Pets POS
                </h1>
                <p class="mt-1 text-sm text-gray-500 capitalize leading-4">
                    {{ Auth::user()->role }}
                </p>
            </div>
        </div>
    </div>

    {{-- Navigasi disaring berdasarkan role user login --}}
    <nav class="p-4 space-y-1">
        @foreach ($menus as $menu)
            @if (in_array(Auth::user()->role, $menu['roles']))
                <a href="{{ route($menu['route']) }}"
                   class="block px-4 py-2 rounded-lg text-sm font-medium
                   {{ request()->routeIs($menu['active'])
                    ? 'bg-teal-700 text-white'
                    : 'text-gray-700 hover:bg-teal-50 hover:text-teal-700' }}">
                    {{ $menu['label'] }}
                </a>
            @endif
        @endforeach
    </nav>

    {{-- Info akun dan aksi logout --}}
    <div class="p-4 border-t">
        <div class="mb-3">
            <p class="text-sm font-medium text-gray-800">
                {{ Auth::user()->name }}
            </p>
            <p class="text-xs text-gray-500">
                {{ Auth::user()->email }}
            </p>
        </div>

        <form method="POST"
              action="{{ route('logout') }}"
              class="w-full text-left px-4 py-2 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50"
              data-confirm
              data-confirm-title="Logout dari sistem?"
              data-confirm-message="Sesi Anda akan diakhiri dan Anda perlu login kembali untuk mengakses G-Pets POS."
              data-confirm-button="Logout"
              data-confirm-variant="danger">
            @csrf

            <button type="submit"
                    class="w-full text-left px-4 py-2 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50">
                Logout
            </button>
        </form>
    </div>
</aside>
