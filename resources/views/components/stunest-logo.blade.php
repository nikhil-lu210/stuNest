@props([
    'class' => 'h-10 w-auto max-w-[min(100%,280px)] object-left object-contain',
])
<img
    src="{{ asset('Logo/stunest_logo.png') }}"
    alt="{{ config('app.name') }}"
    {{ $attributes->merge(['class' => $class]) }}
/>
