@props(['variant' => 'info', 'dismissible' => false])

@php
$variants = [
    'success' => 'bg-green-50 border-green-500 text-green-800',
    'error' => 'bg-red-50 border-red-500 text-red-800',
    'warning' => 'bg-yellow-50 border-yellow-500 text-yellow-800',
    'info' => 'bg-blue-50 border-blue-500 text-blue-800',
];
$classes = $variants[$variant] ?? $variants['info'];
@endphp

<div {{ $attributes->merge(['class' => 'p-4 border-l-4 rounded-r-lg ' . $classes]) }}>
    <div class="flex items-start justify-between">
        <p class="text-sm">{{ $slot }}</p>
        @if($dismissible)
            <button type="button" class="ml-4 text-current opacity-50 hover:opacity-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif
    </div>
</div>
