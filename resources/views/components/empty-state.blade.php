@props([
    'title',
    'description' => null,
    'actionHref' => null,
    'actionLabel' => null,
    'colspan' => 1,
])

<tr>
    <td colspan="{{ $colspan }}" class="px-4 py-10">
        <div class="mx-auto max-w-md text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-teal-50 text-xl font-semibold text-teal-700">
                +
            </div>

            <p class="mt-3 text-sm font-semibold text-gray-800">{{ $title }}</p>

            @if ($description)
                <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
            @endif

            @if ($actionHref && $actionLabel)
                <a href="{{ $actionHref }}"
                   class="mt-4 inline-block px-4 py-2 bg-teal-700 text-white rounded-lg text-sm font-medium hover:bg-teal-800">
                    {{ $actionLabel }}
                </a>
            @endif
        </div>
    </td>
</tr>
