<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-teal-700 text-white rounded-lg text-sm font-medium hover:bg-teal-800']) }}>
    {{ $slot }}
</button>
