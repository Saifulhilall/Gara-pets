<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-gpets.jpeg') }}">
        <link rel="shortcut icon" type="image/jpeg" href="{{ asset('images/logo-gpets.jpeg') }}">

        <title>{{ config('app.name', 'POS Gara Petshop') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 flex">
            @include('layouts.sidebar')

            <div class="flex-1 min-w-0">
                @isset($header)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>

        <x-confirm-modal />

        <script>
            document.addEventListener('click', function (event) {
                const closeButton = event.target.closest('[data-dismiss-alert]');

                if (closeButton) {
                    closeButton.closest('[data-dismissible-alert]')?.remove();
                }
            });

            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('confirmModal');
                const title = document.getElementById('confirmModalTitle');
                const message = document.getElementById('confirmModalMessage');
                const confirmButton = document.getElementById('confirmModalButton');
                const icon = document.getElementById('confirmModalIcon');
                let pendingForm = null;
                let pendingSubmitter = null;

                function closeModal() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    modal.setAttribute('aria-hidden', 'true');
                    pendingForm = null;
                    pendingSubmitter = null;
                }

                function openModal(form, submitter) {
                    const variant = form.dataset.confirmVariant || 'danger';
                    const danger = variant === 'danger';

                    pendingForm = form;
                    pendingSubmitter = submitter;
                    title.textContent = form.dataset.confirmTitle || 'Konfirmasi aksi';
                    message.textContent = form.dataset.confirmMessage || 'Apakah Anda yakin ingin melanjutkan?';
                    confirmButton.textContent = form.dataset.confirmButton || 'Konfirmasi';

                    confirmButton.className = danger
                        ? 'px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700'
                        : 'px-4 py-2 bg-teal-700 text-white rounded-lg text-sm font-medium hover:bg-teal-800';
                    icon.className = danger
                        ? 'mb-4 flex h-11 w-11 items-center justify-center rounded-full bg-red-100 text-lg font-bold text-red-700'
                        : 'mb-4 flex h-11 w-11 items-center justify-center rounded-full bg-teal-100 text-lg font-bold text-teal-700';

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    modal.setAttribute('aria-hidden', 'false');
                    confirmButton.focus();
                }

                document.querySelectorAll('form[data-confirm]').forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.dataset.confirmAccepted === 'true') {
                            delete form.dataset.confirmAccepted;
                            return;
                        }

                        event.preventDefault();
                        openModal(form, event.submitter);
                    });
                });

                document.querySelector('[data-confirm-cancel]')?.addEventListener('click', closeModal);

                confirmButton?.addEventListener('click', function () {
                    if (! pendingForm) {
                        return;
                    }

                    pendingForm.dataset.confirmAccepted = 'true';
                    pendingForm.requestSubmit(pendingSubmitter);
                    closeModal();
                });

                modal?.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        closeModal();
                    }
                });

                document.querySelectorAll('form[data-loading]').forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        const button = event.submitter || form.querySelector('button[type="submit"]');

                        if (! button || button.dataset.loadingStarted === 'true') {
                            return;
                        }

                        button.dataset.originalText = button.textContent.trim();
                        button.dataset.loadingStarted = 'true';
                        button.textContent = form.dataset.loadingText || button.dataset.loadingText || 'Memproses...';
                        button.disabled = true;
                        button.classList.add('cursor-not-allowed', 'opacity-75');
                    });
                });
            });
        </script>
    </body>
</html>
