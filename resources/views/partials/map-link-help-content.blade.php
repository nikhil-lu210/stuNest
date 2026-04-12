{{-- Shared copy for map link help. Wrap in a container for colors (Tailwind vs Bootstrap). --}}
<div class="map-link-help-content text-sm">
    <p class="mb-3">{{ __('Paste any Google Maps link: a long place URL, or a short share link (for example maps.app.goo.gl). We normalize the link and fill latitude and longitude when we can.') }}</p>

    <h4 class="mb-2 font-semibold">{{ __('Computer (browser)') }}</h4>
    <ol class="mb-4 list-decimal space-y-1 ps-5">
        <li>{{ __('Open Google Maps (maps.google.com) and search for the address, or right‑click the map and choose a place or drop a pin.') }}</li>
        <li>{{ __('Click Share (or the share icon).') }}</li>
        <li>{{ __('Choose “Copy link” so the URL is on your clipboard.') }}</li>
        <li>{{ __('Paste it into the map link field here.') }}</li>
    </ol>

    <h4 class="mb-2 font-semibold">{{ __('Phone or tablet (Google Maps app)') }}</h4>
    <ol class="list-decimal space-y-1 ps-5">
        <li>{{ __('Open the Google Maps app and find the place, or touch and hold on the map to drop a pin.') }}</li>
        <li>{{ __('Tap Share, then Copy link (or share and copy the link).') }}</li>
        <li>{{ __('Paste the link into the field on this page.') }}</li>
    </ol>
</div>
