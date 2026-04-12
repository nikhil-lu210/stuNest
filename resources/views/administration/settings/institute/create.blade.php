@extends('layouts.administration.app')

@section('page_title', __('Register Institute'))

@section('page_name')
    <b class="text-uppercase">{{ __('Register Institute') }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Institutions') }}</li>
    <li class="breadcrumb-item active">{{ __('Register Institute') }}</li>
@endsection

@section('content')
@php
    $oldLocations = old('locations', [['name' => '', 'country_id' => '', 'city_id' => '', 'area_id' => '', 'address_line_1' => '', 'postcode' => '']]);
    $oldPrimary = (string) old('primary_location_index', '0');
@endphp
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header header-elements">
                <h5 class="mb-0">{{ __('Register Institute') }}</h5>
                <div class="card-header-elements ms-auto">
                    <a href="{{ route('administration.settings.institute.index') }}" class="btn btn-sm btn-primary">
                        <span class="tf-icon ti ti-list ti-xs me-1"></span>
                        {{ __('All Institutes') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('administration.settings.institute.store') }}" method="post" autocomplete="off" id="institute-form">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">{{ __('Institute name') }} <strong class="text-danger">*</strong></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required />
                            @error('name')
                                <b class="text-danger"><i class="ti ti-info-circle me-1"></i>{{ $message }}</b>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">{{ __('Institute email code') }} <strong class="text-danger">*</strong></label>
                            <input type="text" name="email_code" value="{{ old('email_code') }}" placeholder="@nup.ac.cy" class="form-control @error('email_code') is-invalid @enderror" required />
                            <small class="text-muted">{{ __('Institutional email suffix (e.g. @nup.ac.cy). Representative emails must use this domain.') }}</small>
                            @error('email_code')
                                <b class="text-danger d-block"><i class="ti ti-info-circle me-1"></i>{{ $message }}</b>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-3" />
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">{{ __('Branches / locations') }}</h6>
                        <button type="button" class="btn btn-sm btn-label-primary" id="add-location-row">
                            <i class="ti ti-plus me-1"></i>{{ __('Add branch') }}
                        </button>
                    </div>
                    <p class="text-muted small">{{ __('Select country, then city and area. Address and postcode are free text.') }}</p>

                    <div id="location-rows">
                        @foreach ($oldLocations as $i => $loc)
                            <div class="border rounded p-3 mb-3 location-row" data-index="{{ $i }}">
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">{{ __('Branch name') }} <strong class="text-danger">*</strong></label>
                                        <input type="text" name="locations[{{ $i }}][name]" value="{{ $loc['name'] ?? '' }}" class="form-control" required />
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">{{ __('Country') }} <strong class="text-danger">*</strong></label>
                                        <select name="locations[{{ $i }}][country_id]" class="form-select geo-country geo-managed" data-placeholder="{{ __('Select country') }}" required>
                                            <option value="">{{ __('Select country') }}</option>
                                            @foreach ($countries as $c)
                                                <option value="{{ $c->id }}" @selected((string)($loc['country_id'] ?? '') === (string)$c->id)>{{ $c->name }} ({{ $c->iso_code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">{{ __('City') }} <strong class="text-danger">*</strong></label>
                                        <select name="locations[{{ $i }}][city_id]" class="form-select geo-city geo-managed" data-placeholder="{{ __('Select city') }}" required></select>
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">{{ __('Area') }} <strong class="text-danger">*</strong></label>
                                        <select name="locations[{{ $i }}][area_id]" class="form-select geo-area geo-managed" data-placeholder="{{ __('Select area') }}" required></select>
                                    </div>
                                    <div class="mb-3 col-md-4 d-flex align-items-end">
                                        <div class="form-check w-100">
                                            <input class="form-check-input location-primary" type="radio" name="primary_location_index" value="{{ $i }}" {{ (string) $i === $oldPrimary ? 'checked' : '' }} />
                                            <label class="form-check-label">{{ __('Primary') }}</label>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">{{ __('Address line 1') }}</label>
                                        <input type="text" name="locations[{{ $i }}][address_line_1]" value="{{ $loc['address_line_1'] ?? '' }}" class="form-control" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">{{ __('Postcode') }}</label>
                                        <input type="text" name="locations[{{ $i }}][postcode]" value="{{ $loc['postcode'] ?? '' }}" class="form-control" />
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="button" class="btn btn-sm btn-label-danger remove-location-row" tabindex="-1">{{ __('Remove') }}</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('locations')
                        <b class="text-danger"><i class="ti ti-info-circle me-1"></i>{{ $message }}</b>
                    @enderror

                    <div class="mt-2 float-end">
                        <button type="submit" class="btn btn-primary">{{ __('Save institute') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<template id="location-row-template">
    <div class="border rounded p-3 mb-3 location-row" data-index="__INDEX__">
        <div class="row">
            <div class="mb-3 col-md-4">
                <label class="form-label">{{ __('Branch name') }} <strong class="text-danger">*</strong></label>
                <input type="text" name="locations[__INDEX__][name]" class="form-control" required />
            </div>
            <div class="mb-3 col-md-4">
                <label class="form-label">{{ __('Country') }} <strong class="text-danger">*</strong></label>
                <select name="locations[__INDEX__][country_id]" class="form-select geo-country geo-managed" required>
                    <option value="">{{ __('Select country') }}</option>
                    @foreach ($countries as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->iso_code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 col-md-4">
                <label class="form-label">{{ __('City') }} <strong class="text-danger">*</strong></label>
                <select name="locations[__INDEX__][city_id]" class="form-select geo-city geo-managed" required></select>
            </div>
            <div class="mb-3 col-md-4">
                <label class="form-label">{{ __('Area') }} <strong class="text-danger">*</strong></label>
                <select name="locations[__INDEX__][area_id]" class="form-select geo-area geo-managed" required></select>
            </div>
            <div class="mb-3 col-md-4 d-flex align-items-end">
                <div class="form-check w-100">
                    <input class="form-check-input location-primary" type="radio" name="primary_location_index" value="__INDEX__" />
                    <label class="form-check-label">{{ __('Primary') }}</label>
                </div>
            </div>
            <div class="mb-3 col-md-6">
                <label class="form-label">{{ __('Address line 1') }}</label>
                <input type="text" name="locations[__INDEX__][address_line_1]" class="form-control" />
            </div>
            <div class="mb-3 col-md-6">
                <label class="form-label">{{ __('Postcode') }}</label>
                <input type="text" name="locations[__INDEX__][postcode]" class="form-control" />
            </div>
            <div class="col-12 text-end">
                <button type="button" class="btn btn-sm btn-label-danger remove-location-row" tabindex="-1">{{ __('Remove') }}</button>
            </div>
        </div>
    </div>
</template>
@endsection

@section('custom_script')
<script>
(function () {
    const GEO = {
        cities: @json(route('administration.settings.geography.api.cities')),
        areas: @json(route('administration.settings.geography.api.areas')),
    };

    function destroySelect2($el) {
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }
    }

    function initSelect2($el) {
        destroySelect2($el);
        $el.select2({
            width: '100%',
            placeholder: $el.data('placeholder') || 'Select...',
            allowClear: true,
        });
    }

    async function loadCities(row, countryId, selectedCityId) {
        const $row = $(row);
        const $city = $row.find('.geo-city');
        const $area = $row.find('.geo-area');
        destroySelect2($city);
        destroySelect2($area);
        $city.empty().append(new Option('', '', true, false));
        $area.empty().append(new Option('', '', true, false));
        if (!countryId) {
            initSelect2($city);
            initSelect2($area);
            return;
        }
        let url = GEO.cities + '?country_id=' + encodeURIComponent(countryId);
        if (selectedCityId) {
            url += '&selected_id=' + encodeURIComponent(selectedCityId);
        }
        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }});
        const data = await res.json();
        (data.results || []).forEach(function (r) {
            const opt = new Option(r.text, r.id, false, String(r.id) === String(selectedCityId));
            $city.append(opt);
        });
        initSelect2($city);
        initSelect2($area);
    }

    async function loadAreas(row, cityId, selectedAreaId) {
        const $row = $(row);
        const $area = $row.find('.geo-area');
        destroySelect2($area);
        $area.empty().append(new Option('', '', true, false));
        if (!cityId) {
            initSelect2($area);
            return;
        }
        let url = GEO.areas + '?city_id=' + encodeURIComponent(cityId);
        if (selectedAreaId) {
            url += '&selected_id=' + encodeURIComponent(selectedAreaId);
        }
        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }});
        const data = await res.json();
        (data.results || []).forEach(function (r) {
            const opt = new Option(r.text, r.id, false, String(r.id) === String(selectedAreaId));
            $area.append(opt);
        });
        initSelect2($area);
    }

    function bindGeoRow(row) {
        const $row = $(row);
        const $c = $row.find('.geo-country');
        initSelect2($c);
        $c.off('change.geo').on('change.geo', async function () {
            const cid = $(this).val();
            await loadCities($row[0], cid, null);
            await loadAreas($row[0], null, null);
        });
        $row.find('.geo-city').off('change.geo').on('change.geo', async function () {
            const cid = $(this).val();
            await loadAreas($row[0], cid, null);
        });
    }

    async function hydrateRow(row, preCity, preArea) {
        const $row = $(row);
        const $c = $row.find('.geo-country');
        const cid = $c.val();
        initSelect2($c);
        if (cid) {
            await loadCities(row, cid, preCity || null);
            if (preCity) {
                await loadAreas(row, preCity, preArea || null);
            } else {
                initSelect2($row.find('.geo-area'));
            }
        } else {
            initSelect2($row.find('.geo-city'));
            initSelect2($row.find('.geo-area'));
        }
        bindGeoRow(row);
    }

    const container = document.getElementById('location-rows');
    const tpl = document.getElementById('location-row-template');
    let nextIndex = {{ count($oldLocations) }};

    function bindRemove(row) {
        row.querySelector('.remove-location-row')?.addEventListener('click', function () {
            if (container.querySelectorAll('.location-row').length <= 1) {
                return;
            }
            const wasChecked = row.querySelector('.location-primary')?.checked;
            $(row).find('.geo-country, .geo-city, .geo-area').each(function () {
                destroySelect2($(this));
            });
            row.remove();
            if (wasChecked) {
                const first = container.querySelector('.location-primary');
                if (first) first.checked = true;
            }
        });
    }

    container.querySelectorAll('.location-row').forEach(function (row) {
        bindRemove(row);
    });

    (async function () {
        @foreach ($oldLocations as $loc)
            await hydrateRow(
                container.querySelectorAll('.location-row')[{{ $loop->index }}],
                @json($loc['city_id'] ?? null),
                @json($loc['area_id'] ?? null)
            );
        @endforeach
    })();

    document.getElementById('add-location-row').addEventListener('click', function () {
        const html = tpl.innerHTML.replace(/__INDEX__/g, nextIndex++);
        const wrap = document.createElement('div');
        wrap.innerHTML = html.trim();
        const row = wrap.firstElementChild;
        container.appendChild(row);
        bindRemove(row);
        hydrateRow(row, null, null);
    });
})();
</script>
@endsection
