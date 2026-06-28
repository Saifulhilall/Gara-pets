<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $title }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $title }}
                    </h3>

                    <p class="mt-2 text-gray-600">
                        {{ $description }}
                    </p>

                    <div class="mt-6 p-4 bg-gray-50 border rounded-lg text-sm text-gray-600">
                        Halaman ini masih berupa kerangka awal dan akan dikembangkan pada milestone berikutnya.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>