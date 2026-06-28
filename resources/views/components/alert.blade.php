@php
    $alerts = collect([
        'success' => session('success'),
        'error' => session('error'),
        'warning' => session('warning'),
        'info' => session('info'),
    ])->filter();

    $styles = [
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'error' => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-yellow-200 bg-yellow-50 text-yellow-800',
        'info' => 'border-teal-200 bg-teal-50 text-teal-800',
    ];

    $icons = [
        'success' => '✓',
        'error' => '!',
        'warning' => '!',
        'info' => 'i',
    ];

    $titles = [
        'success' => 'Berhasil',
        'error' => 'Terjadi kesalahan',
        'warning' => 'Perhatian',
        'info' => 'Informasi',
    ];
@endphp

<div class="space-y-3">
    @foreach ($alerts as $type => $message)
        <div class="flex items-start gap-3 rounded-lg border p-4 shadow-sm {{ $styles[$type] }}"
             data-dismissible-alert>
            <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/70 text-sm font-bold">
                {{ $icons[$type] }}
            </div>

            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold">{{ $titles[$type] }}</p>
                <p class="mt-1 text-sm leading-5">{{ $message }}</p>
            </div>

            <button type="button"
                    class="rounded-md px-2 text-lg leading-6 opacity-70 hover:bg-white/70 hover:opacity-100"
                    aria-label="Tutup alert"
                    data-dismiss-alert>
                &times;
            </button>
        </div>
    @endforeach

    @if ($errors->any())
        <div class="flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm"
             data-dismissible-alert>
            <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/70 text-sm font-bold">
                !
            </div>

            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold">Periksa kembali input</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm leading-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

            <button type="button"
                    class="rounded-md px-2 text-lg leading-6 opacity-70 hover:bg-white/70 hover:opacity-100"
                    aria-label="Tutup alert"
                    data-dismiss-alert>
                &times;
            </button>
        </div>
    @endif
</div>
