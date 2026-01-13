@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none rounded-xl shadow-sm transition-all duration-150']) !!}>
