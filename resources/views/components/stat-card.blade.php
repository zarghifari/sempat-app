@props(['color' => 'blue', 'icon', 'value', 'label'])

@php
$colors = [
    'blue' => 'from-blue-500 to-blue-600',
    'green' => 'from-green-500 to-green-600',
    'purple' => 'from-purple-500 to-purple-600',
    'orange' => 'from-orange-500 to-orange-600',
];
$bgColor = $colors[$color] ?? $colors['blue'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-gradient-to-br ' . $bgColor . ' rounded-2xl p-5 text-white shadow-md']) }}>
    <div class="flex items-center gap-2 mb-3">
        @if(isset($icon))
            {{ $icon }}
        @else
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
        @endif
        <span class="text-sm font-medium">{{ $label }}</span>
    </div>
    <div class="text-4xl font-bold mb-1">{{ $value }}</div>
    @if(isset($subtitle))
        <div class="text-xs opacity-90">{{ $subtitle }}</div>
    @endif
</div>
