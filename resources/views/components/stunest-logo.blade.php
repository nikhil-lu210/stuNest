@props([
    'class' => 'h-8 w-auto',
])
<img
    src="{{ asset('Logo/stunest_logo.png') }}"
    alt="{{ config('app.name') }}"
    {{ $attributes->merge(['class' => $class]) }}
/>
