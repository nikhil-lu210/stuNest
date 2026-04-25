@php
    $closeMobile = $onClickClose ?? 'mobileMenuOpen = false';
    $isMobileMenu = (bool) ($isMobileMenu ?? false);
    $itemClass = $isMobileMenu
        ? 'block rounded-lg px-4 py-2.5 text-sm font-medium text-gray-800 transition-colors hover:bg-gray-100'
        : 'block px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-900';
    if ($isMobileMenu) {
        $clickAttr = 'x-on:click="' . e($closeMobile) . '"';
    } else {
        $clickAttr = ! empty($closeDesktopDropdown) ? 'x-on:click="open = false"' : '';
    }
@endphp
<a href="{{ route('register.student') }}" class="{{ $itemClass }}" {!! $clickAttr !!}>{{ __('Student Registration') }}</a>
<a href="{{ route('register.landlord') }}" class="{{ $itemClass }}" {!! $clickAttr !!}>{{ __('Landlord Registration') }}</a>
<a href="{{ route('register.agent') }}" class="{{ $itemClass }}" {!! $clickAttr !!}>{{ __('Agent Registration') }}</a>
<a href="{{ route('register.institute') }}" class="{{ $itemClass }}" {!! $clickAttr !!}>{{ __('Institute Registration') }}</a>
