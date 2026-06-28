<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-sm max-w-md w-full text-center">
        <h1 class="text-4xl font-bold text-gray-900">
            403
        </h1>

        <h2 class="mt-4 text-xl font-semibold text-gray-800">
            Akses Ditolak
        </h2>

        <p class="mt-2 text-gray-600">
            Anda tidak memiliki hak akses untuk membuka halaman ini.
        </p>

        <a href="{{ route('dashboard') }}"
           class="inline-block mt-6 px-4 py-2 bg-teal-700 text-white rounded-lg text-sm font-medium hover:bg-teal-800">
            Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
