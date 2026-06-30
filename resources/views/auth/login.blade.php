<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Form login internal admin dan kasir --}}
    <form method="POST" action="{{ route('login') }}" data-loading data-loading-text="Memproses...">
        @csrf

        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-800">
                Masuk ke Sistem
            </h2>
            <p class="mt-1 text-sm leading-5 text-gray-500">
                Sistem ini hanya digunakan oleh admin dan kasir terdaftar.
            </p>
        </div>

        {{-- Identitas login bisa memakai email atau username --}}
        <div class="space-y-5">
            <div>
            <x-input-label for="login" value="Email atau Username" />
            <x-text-input id="login" class="block mt-1 w-full" type="text" name="login" :value="old('login')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
        </div>

        <div class="mt-5 flex items-center justify-between gap-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-teal-700 shadow-sm focus:ring-teal-600" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-teal-700 hover:text-teal-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                Masuk
            </x-primary-button>
        </div>

        <p class="mt-5 text-center text-xs leading-5 text-gray-500">
            Akun pengguna dibuat melalui menu Data Pengguna oleh admin.
        </p>
    </form>
</x-guest-layout>
