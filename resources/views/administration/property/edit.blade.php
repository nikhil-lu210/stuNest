@extends('layouts.administration.app')

@section('page_title', __('Edit property #:id', ['id' => $property->id]))

@section('page_name')
    <b class="text-uppercase">{{ __('Edit property') }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('administration.properties.index') }}">{{ __('Property listings') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('administration.properties.show', $property) }}">#{{ $property->id }}</a></li>
    <li class="breadcrumb-item active">{{ __('Edit') }}</li>
@endsection

@section('content')
@php
    $o = fn (string $key, $default = null) => old($key, $property->{$key} ?? $default);
    $arr = fn (string $key) => old($key, $property->{$key} ?? []);
@endphp
<form action="{{ route('administration.properties.update', $property) }}" method="post">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-10 col-xl-8">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">{{ __('Location & distances') }}</h5></div>
                <div class="card-body row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="country_id">{{ __('Country') }}</label>
                        <select name="country_id" id="country_id" class="form-select geo-managed" data-placeholder="{{ __('Select…') }}" required>
                            <option value="">{{ __('Select…') }}</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" @selected((int) $o('country_id') === (int) $country->id)>{{ $country->name }}</option>
                            @endforeach
                        </select>
                        @error('country_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="city_id">{{ __('City') }}</label>
                        <select name="city_id" id="city_id" class="form-select geo-managed" data-placeholder="{{ __('Select…') }}" required
                            @disabled(! $o('country_id'))>
                            <option value="">{{ __('Select…') }}</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" @selected((int) $o('city_id') === (int) $city->id)>{{ $city->name }}</option>
                            @endforeach
                        </select>
                        @error('city_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="area_id">{{ __('Area') }}</label>
                        <select name="area_id" id="area_id" class="form-select geo-managed" data-placeholder="{{ __('Select…') }}" required
                            @disabled(! $o('city_id'))>
                            <option value="">{{ __('Select…') }}</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}" @selected((int) $o('area_id') === (int) $area->id)>{{ $area->name }}</option>
                            @endforeach
                        </select>
                        @error('area_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <label class="form-label mb-0" for="map_link">{{ __('Map link') }}</label>
                            <button type="button" class="btn btn-link btn-sm py-0" data-bs-toggle="modal" data-bs-target="#mapLinkHelpModal">
                                {{ __('How to get a map link') }}
                            </button>
                        </div>
                        <input type="url" name="map_link" id="map_link" class="form-control" required value="{{ $o('map_link') }}"
                            placeholder="https://maps.google.com/… or https://maps.app.goo.gl/…">
                        @error('map_link')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="latitude">{{ __('Latitude') }} <small class="text-muted">({{ __('optional') }})</small></label>
                        <input type="text" name="latitude" id="latitude" class="form-control" value="{{ $o('latitude') }}">
                        @error('latitude')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="longitude">{{ __('Longitude') }} <small class="text-muted">({{ __('optional') }})</small></label>
                        <input type="text" name="longitude" id="longitude" class="form-control" value="{{ $o('longitude') }}">
                        @error('longitude')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="distance_university_km">{{ __('Distance to university (km)') }}</label>
                        <input type="number" name="distance_university_km" id="distance_university_km" class="form-control" step="0.01" min="0" max="999.99" required value="{{ $o('distance_university_km') }}">
                        @error('distance_university_km')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="distance_transit_km">{{ __('Distance to nearest bus/train (km)') }}</label>
                        <input type="number" name="distance_transit_km" id="distance_transit_km" class="form-control" step="0.01" min="0" max="999.99" required value="{{ $o('distance_transit_km') }}">
                        @error('distance_transit_km')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">{{ __('Listing & core') }}</h5></div>
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="listing_category">{{ __('Listing category') }}</label>
                        <select name="listing_category" id="listing_category" class="form-select" required>
                            @if (auth()->user()->hasRole('Student'))
                                <option value="shared_room" @selected($o('listing_category') === 'shared_room')>{{ __('Shared room') }}</option>
                            @else
                                <option value="entire_place" @selected($o('listing_category') === 'entire_place')>{{ __('Entire place') }}</option>
                                <option value="shared_room" @selected($o('listing_category') === 'shared_room')>{{ __('Shared room') }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6 {{ $o('listing_category') === 'shared_room' ? '' : 'd-none' }}" id="bed-type-wrap">
                        <label class="form-label" for="bed_type">{{ __('Bed type') }}</label>
                        <select name="bed_type" id="bed_type" class="form-select">
                            <option value="single" @selected($o('bed_type') === 'single')>{{ __('Single bed') }}</option>
                            <option value="shared_double" @selected($o('bed_type') === 'shared_double')>{{ __('Shared double') }}</option>
                        </select>
                        @error('bed_type')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="property_type">{{ __('Property type') }}</label>
                        <select name="property_type" id="property_type" class="form-select" required>
                            @foreach (['studio' => __('Studio'), 'apartment' => __('Apartment'), 'house' => __('House'), 'student_seat' => __('Student hall / seat')] as $val => $label)
                                <option value="{{ $val }}" @selected($o('property_type') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="bedrooms">{{ __('Bedrooms') }}</label>
                        <select name="bedrooms" id="bedrooms" class="form-select" required>
                            @foreach ([1, 2, 3, 4, 5, 6] as $n)
                                <option value="{{ $n }}" @selected((int) $o('bedrooms') === $n)>{{ $n === 6 ? '6+' : $n }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="bathrooms">{{ __('Bathrooms') }}</label>
                        <select name="bathrooms" id="bathrooms" class="form-select" required>
                            @foreach ([1, 2, 3] as $n)
                                <option value="{{ $n }}" @selected((int) $o('bathrooms') === $n)>{{ $n === 3 ? '3+' : $n }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="bathroom_type">{{ __('Bathroom type') }}</label>
                        <select name="bathroom_type" id="bathroom_type" class="form-select" required>
                            <option value="private_ensuite" @selected($o('bathroom_type') === 'private_ensuite')>{{ __('Private / ensuite') }}</option>
                            <option value="shared" @selected($o('bathroom_type') === 'shared')>{{ __('Shared') }}</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="hidden" name="is_furnished" value="0">
                            <input class="form-check-input" type="checkbox" name="is_furnished" id="is_furnished" value="1" @checked((bool) $o('is_furnished'))>
                            <label class="form-check-label" for="is_furnished">{{ __('Furnished') }}</label>
                        </div>
                    </div>
                    <div class="col-12 {{ $o('listing_category') === 'shared_room' ? '' : 'd-none' }}" id="flatmate-wrap">
                        <label class="form-label" for="flatmate_vibe">{{ __('Flatmate vibe') }}</label>
                        <select name="flatmate_vibe" id="flatmate_vibe" class="form-select">
                            @foreach (['all_male' => __('All male'), 'all_female' => __('All female'), 'mixed' => __('Mixed')] as $val => $label)
                                <option value="{{ $val }}" @selected($o('flatmate_vibe') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">{{ __('Rent & contract') }}</h5></div>
                <div class="card-body row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="rent_duration">{{ __('Rent period') }}</label>
                        <select name="rent_duration" id="rent_duration" class="form-select" required>
                            <option value="day" @selected($o('rent_duration') === 'day')>{{ __('Per day') }}</option>
                            <option value="week" @selected($o('rent_duration') === 'week')>{{ __('Per week') }}</option>
                            <option value="month" @selected($o('rent_duration') === 'month')>{{ __('Per month') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="rent_amount">{{ __('Rent amount') }} (€)</label>
                        <input type="number" name="rent_amount" id="rent_amount" class="form-control" min="1" step="1" required value="{{ $o('rent_amount') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="bills_included">{{ __('Bills') }}</label>
                        <select name="bills_included" id="bills_included" class="form-select" required>
                            <option value="all" @selected($o('bills_included') === 'all')>{{ __('All included') }}</option>
                            <option value="some" @selected($o('bills_included') === 'some')>{{ __('Some included') }}</option>
                            <option value="none" @selected($o('bills_included') === 'none')>{{ __('Not included') }}</option>
                        </select>
                    </div>
                    <div class="col-12 {{ $o('bills_included') === 'some' ? '' : 'd-none' }}" id="included-bills-wrap">
                        <label class="form-label d-block">{{ __('Which bills are included?') }}</label>
                        @foreach (['wifi' => __('Wi‑Fi'), 'water' => __('Water'), 'electricity' => __('Electricity'), 'gas' => __('Gas')] as $val => $label)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="included_bills[]" id="bill_{{ $val }}" value="{{ $val }}"
                                    @checked(in_array($val, (array) $arr('included_bills'), true))>
                                <label class="form-check-label" for="bill_{{ $val }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="min_contract_length">{{ __('Minimum contract') }}</label>
                        <select name="min_contract_length" id="min_contract_length" class="form-select" required>
                            @foreach (['1_month' => __('1 month'), '3_months' => __('3 months'), '6_months' => __('6 months'), '1_year' => __('1 year'), 'flexible' => __('Flexible')] as $val => $label)
                                <option value="{{ $val }}" @selected($o('min_contract_length') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label d-block">{{ __('Written agreement') }}</label>
                        <input type="hidden" name="provides_agreement" value="0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="provides_agreement" id="provides_agreement" value="1" @checked((bool) $o('provides_agreement'))>
                            <label class="form-check-label" for="provides_agreement">{{ __('Provides agreement') }}</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="deposit_required">{{ __('Deposit') }}</label>
                        <select name="deposit_required" id="deposit_required" class="form-select" required>
                            <option value="none" @selected($o('deposit_required') === 'none')>{{ __('No deposit') }}</option>
                            <option value="1_month" @selected($o('deposit_required') === '1_month')>{{ __('1 month rent') }}</option>
                            <option value="5_weeks" @selected($o('deposit_required') === '5_weeks')>{{ __('5 weeks rent') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="rent_for">{{ __('Rent is for') }}</label>
                        <select name="rent_for" id="rent_for" class="form-select" required>
                            @foreach (['only_boys' => __('Only boys'), 'only_girls' => __('Only girls'), 'couples' => __('Couples'), 'anyone' => __('Anyone')] as $val => $label)
                                <option value="{{ $val }}" @selected($o('rent_for') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">{{ __('Match & rules') }}</h5></div>
                <div class="card-body row g-3">
                    <div class="col-12">
                        <label class="form-label d-block">{{ __('Suitable for') }}</label>
                        @foreach (['undergraduates' => __('Undergraduates'), 'postgraduates' => __('Postgraduates'), 'couples' => __('Couples')] as $val => $label)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="suitable_for[]" id="sf_{{ $val }}" value="{{ $val }}"
                                    @checked(in_array($val, (array) $arr('suitable_for'), true))>
                                <label class="form-check-label" for="sf_{{ $val }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-12">
                        <label class="form-label d-block">{{ __('House rules') }}</label>
                        @foreach (['pet_friendly' => __('Pet friendly'), 'smoking_allowed' => __('Smoking allowed'), 'quiet_house' => __('Quiet house')] as $val => $label)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="house_rules[]" id="hr_{{ $val }}" value="{{ $val }}"
                                    @checked(in_array($val, (array) $arr('house_rules'), true))>
                                <label class="form-check-label" for="hr_{{ $val }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">{{ __('Amenities') }}</h5></div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach ([
                            'wifi' => __('Wi‑Fi'),
                            'washing_machine' => __('Washing machine'),
                            'tumble_dryer' => __('Tumble dryer'),
                            'dishwasher' => __('Dishwasher'),
                            'balcony_garden' => __('Balcony / garden'),
                            'desk_in_room' => __('Desk in room'),
                            'building_gym' => __('Building gym'),
                            'bike_storage' => __('Bike storage'),
                        ] as $val => $label)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" id="am_{{ $val }}" value="{{ $val }}"
                                        @checked(in_array($val, (array) $arr('amenities'), true))>
                                    <label class="form-check-label" for="am_{{ $val }}">{{ $label }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if ($canManageStatus)
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">{{ __('Administration') }}</h5></div>
                    <div class="card-body">
                        <label class="form-label" for="status">{{ __('Status') }}</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="{{ \App\Models\Property\Property::STATUS_DRAFT }}" @selected($o('status') === \App\Models\Property\Property::STATUS_DRAFT)>{{ __('Draft') }}</option>
                            <option value="{{ \App\Models\Property\Property::STATUS_PENDING }}" @selected($o('status') === \App\Models\Property\Property::STATUS_PENDING)>{{ __('Pending') }}</option>
                            <option value="{{ \App\Models\Property\Property::STATUS_PUBLISHED }}" @selected($o('status') === \App\Models\Property\Property::STATUS_PUBLISHED)>{{ __('Published') }}</option>
                            <option value="{{ \App\Models\Property\Property::STATUS_REJECTED }}" @selected($o('status') === \App\Models\Property\Property::STATUS_REJECTED)>{{ __('Rejected') }}</option>
                            <option value="{{ \App\Models\Property\Property::STATUS_LET_AGREED }}" @selected($o('status') === \App\Models\Property\Property::STATUS_LET_AGREED)>{{ __('Let agreed') }}</option>
                            <option value="{{ \App\Models\Property\Property::STATUS_ARCHIVED }}" @selected($o('status') === \App\Models\Property\Property::STATUS_ARCHIVED)>{{ __('Archived') }}</option>
                        </select>
                    </div>
                </div>
            @endif

            <div class="d-flex flex-wrap gap-2 mb-4">
                <button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
                <a href="{{ route('administration.properties.show', $property) }}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="mapLinkHelpModal" tabindex="-1" aria-labelledby="mapLinkHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapLinkHelpModalLabel">{{ __('Getting a Google Maps link') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body text-muted">
                @include('partials.map-link-help-content')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_script')
<script>
(function () {
    var $ = window.jQuery;
    if (!$ || !$.fn.select2) return;

    var cat = document.getElementById('listing_category');
    var flat = document.getElementById('flatmate-wrap');
    var flatmate = document.getElementById('flatmate_vibe');
    var bedWrap = document.getElementById('bed-type-wrap');
    var bedType = document.getElementById('bed_type');
    var bills = document.getElementById('bills_included');
    var inc = document.getElementById('included-bills-wrap');
    var $country = $('#country_id');
    var $city = $('#city_id');
    var $area = $('#area_id');
    var citiesTmpl = @json(route('administration.properties.geography.cities', ['country' => 999999999]));
    var areasTmpl = @json(route('administration.properties.geography.areas', ['city' => 888888888]));
    var ph = @json(__('Select…'));

    function destroyGeo($el) {
        if ($el.length && $el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }
    }

    function initGeo($el) {
        destroyGeo($el);
        $el.select2({
            width: '100%',
            placeholder: $el.data('placeholder') || ph,
            allowClear: false,
            dropdownParent: $(document.body),
            minimumResultsForSearch: 12,
            disabled: $el.prop('disabled'),
        });
    }

    function syncFlat() {
        if (!cat) return;
        var shared = cat.value === 'shared_room';
        if (flat) flat.classList.toggle('d-none', !shared);
        if (flatmate) flatmate.disabled = !shared;
        if (bedWrap) bedWrap.classList.toggle('d-none', !shared);
        if (bedType) bedType.disabled = !shared;
    }
    function syncBills() {
        if (!bills || !inc) return;
        var some = bills.value === 'some';
        inc.classList.toggle('d-none', !some);
        inc.querySelectorAll('input[type="checkbox"]').forEach(function (cb) {
            cb.disabled = !some;
        });
    }
    function refillCities() {
        if (!$country.length || !$city.length || !$area.length) return;
        var id = $country.val();
        destroyGeo($city);
        destroyGeo($area);
        $city.empty().append(new Option(ph, '', false, false));
        $area.empty().append(new Option(ph, '', false, false));
        if (!id) {
            $city.prop('disabled', true);
            $area.prop('disabled', true);
            initGeo($city);
            initGeo($area);
            return;
        }
        $city.prop('disabled', false);
        $area.prop('disabled', true);
        fetch(citiesTmpl.replace('999999999', id))
            .then(function (r) { return r.json(); })
            .then(function (rows) {
                rows.forEach(function (row) {
                    $city.append(new Option(row.name, row.id, false, false));
                });
                initGeo($city);
                initGeo($area);
            })
            .catch(function () {
                initGeo($city);
                initGeo($area);
            });
    }
    function refillAreas() {
        if (!$city.length || !$area.length) return;
        var id = $city.val();
        destroyGeo($area);
        $area.empty().append(new Option(ph, '', false, false));
        if (!id) {
            $area.prop('disabled', true);
            initGeo($area);
            return;
        }
        $area.prop('disabled', false);
        fetch(areasTmpl.replace('888888888', id))
            .then(function (r) { return r.json(); })
            .then(function (rows) {
                rows.forEach(function (row) {
                    $area.append(new Option(row.name, row.id, false, false));
                });
                initGeo($area);
            })
            .catch(function () {
                initGeo($area);
            });
    }

    $country.on('change', refillCities);
    $city.on('change', refillAreas);
    if (cat) cat.addEventListener('change', syncFlat);
    if (bills) bills.addEventListener('change', syncBills);

    document.addEventListener('DOMContentLoaded', function () {
        syncFlat();
        syncBills();
        $city.prop('disabled', !$country.val());
        $area.prop('disabled', !$city.val());
        initGeo($country);
        initGeo($city);
        initGeo($area);
    });
})();
</script>
@endsection
