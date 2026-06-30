@props([
    'field',
    'label',
    'align' => 'left',
    'sort' => request('sort'),
    'direction' => request('direction', 'desc'),
])

@php
    $isActive = $sort === $field;
    $nextDirection = $isActive && $direction === 'asc' ? 'desc' : 'asc';
    $query = array_merge(request()->except('page'), [
        'sort' => $field,
        'direction' => $nextDirection,
    ]);
    $alignment = [
        'left' => 'justify-start text-left',
        'center' => 'justify-center text-center',
        'right' => 'justify-end text-right',
    ][$align] ?? 'justify-start text-left';
@endphp

<a href="{{ url()->current().'?'.http_build_query($query) }}"
   class="group inline-flex w-full items-center gap-1.5 {{ $alignment }} font-semibold text-gray-700 hover:text-teal-700">
    <span>{{ $label }}</span>
    <span class="flex flex-col {{ $isActive ? 'text-teal-700' : 'text-gray-300 group-hover:text-teal-500' }}" aria-hidden="true">
        <svg class="h-2.5 w-2.5 {{ $isActive && $direction === 'asc' ? 'text-teal-700' : '' }}" viewBox="0 0 10 10" fill="currentColor">
            <path d="M5 2 1.5 6.5h7L5 2Z" />
        </svg>
        <svg class="-mt-0.5 h-2.5 w-2.5 {{ $isActive && $direction === 'desc' ? 'text-teal-700' : '' }}" viewBox="0 0 10 10" fill="currentColor">
            <path d="M5 8 1.5 3.5h7L5 8Z" />
        </svg>
    </span>
</a>
