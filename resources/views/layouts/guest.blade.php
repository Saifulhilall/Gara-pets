<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-gpets.jpeg') }}">
        <link rel="shortcut icon" type="image/jpeg" href="{{ asset('images/logo-gpets.jpeg') }}">
        <title>{{ config('app.name', 'G-Pets POS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-gray-100 px-4 py-8 sm:flex sm:items-center sm:justify-center">
            <div class="w-full max-w-md rounded-xl bg-white px-6 py-8 shadow-sm sm:px-8">
                <div class="mb-7 text-center">
                    <img src="{{ asset('images/logo-gpets.jpeg') }}"
                         alt="Logo G-Pets Gara PetShop"
                         class="mx-auto h-24 w-24 rounded-xl border border-gray-100 bg-white object-contain">

                    <h1 class="mt-4 text-2xl font-bold text-teal-700">
                        G-Pets POS
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Sistem Point of Sale Gara Petshop
                    </p>
                </div>

                {{ $slot }}
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('form[data-loading]').forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        const button = event.submitter || form.querySelector('button[type="submit"]');

                        if (! button || button.dataset.loadingStarted === 'true') {
                            return;
                        }

                        button.dataset.loadingStarted = 'true';
                        button.textContent = form.dataset.loadingText || 'Memproses...';
                        button.disabled = true;
                        button.classList.add('cursor-not-allowed', 'opacity-75');
                    });
                });
            });
        </script>
    </body>
</html>
