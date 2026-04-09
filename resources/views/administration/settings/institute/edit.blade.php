@extends('layouts.administration.app')

@section('page_title', __('Edit Institute'))

@section('page_name')
    <b class="text-uppercase">{{ __('Edit Institute') }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Institutions') }}</li>
    <li class="breadcrumb-item"><a href="{{ route('administration.settings.institute.show', $institute) }}">{{ $institute->name }}</a></li>
    <li class="breadcrumb-item active">{{ __('Edit') }}</li>
@endsection

@section('content')
@php
    $defaultLocations = $institute->locations->map(function ($loc) {
        return [
            'id' => $loc->id,
            'name' => $loc->name,
            'address_line_1' => $loc->address_line_1,
            'city' => $loc->city,
            'postcode' => $loc->postcode,
            'country' => $loc->country,
            'is_primary' => $loc->is_primary,
        ];
    })->values()->all();
    $oldLocations = old('locations', $defaultLocations);
    $primaryIdx = null;
    foreach ($oldLocations as $idx => $row) {
        if (! empty($row['is_primary'])) {
            $primaryIdx = (string) $idx;
            break;
        }
    }
    if ($primaryIdx === null && count($oldLocations) > 0) {
        $primaryIdx = '0';
    }
    $oldPrimary = (string) old('primary_location_index', $primaryIdx ?? '0');
@endphp
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header header-elements">
                <h5 class="mb-0">{{ __('Edit Institute') }}</h5>
                <div class="card-header-elements ms-auto">
                    <a href="{{ route('administration.settings.institute.show', $institute) }}" class="btn btn-sm btn-primary">
                        <span class="tf-icon ti ti-arrow-left ti-xs me-1"></span>
                        {{ __('Back') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('administration.settings.institute.update', $institute) }}" method="post" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">{{ __('Institute name') }} <strong class="text-danger">*</strong></label>
                            <input type="text" name="name" value="{{ old('name', $institute->name) }}" class="form-control @error('name') is-invalid @enderror" required />
                            @error('name')
                                <b class="text-danger"><i class="ti ti-info-circle me-1"></i>{{ $message }}</b>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">{{ __('Institute email code') }} <strong class="text-danger">*</strong></label>
                            <input type="text" name="email_code" value="{{ old('email_code', $institute->email_code) }}" class="form-control @error('email_code') is-invalid @enderror" required />
                            <small class="text-muted">{{ __('Institutional email suffix (e.g. @nup.ac.cy).') }}</small>
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

                    <div id="location-rows">
                        @foreach ($oldLocations as $i => $loc)
                            <div class="border rounded p-3 mb-3 location-row" data-index="{{ $i }}">
                                <div class="row">
                                    @if (! empty($loc['id']))
                                        <input type="hidden" name="locations[{{ $i }}][id]" value="{{ $loc['id'] }}" />
                                    @endif
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">{{ __('Branch name') }} <strong class="text-danger">*</strong></label>
                                        <input type="text" name="locations[{{ $i }}][name]" value="{{ $loc['name'] ?? '' }}" class="form-control" required />
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">{{ __('City') }}</label>
                                        <input type="text" name="locations[{{ $i }}][city]" value="{{ $loc['city'] ?? '' }}" class="form-control" />
                                    </div>
                                    <div class="mb-3 col-md-2">
                                        <label class="form-label">{{ __('Country') }}</label>
                                        <input type="text" name="locations[{{ $i }}][country]" value="{{ $loc['country'] ?? 'GB' }}" maxlength="2" class="form-control text-uppercase" />
                                    </div>
                                    <div class="mb-3 col-md-2 d-flex align-items-end">
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
                        <button type="submit" class="btn btn-primary">{{ __('Update institute') }}</button>
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
                <label class="form-label">{{ __('City') }}</label>
                <input type="text" name="locations[__INDEX__][city]" class="form-control" />
            </div>
            <div class="mb-3 col-md-2">
                <label class="form-label">{{ __('Country') }}</label>
                <input type="text" name="locations[__INDEX__][country]" value="GB" maxlength="2" class="form-control text-uppercase" />
            </div>
            <div class="mb-3 col-md-2 d-flex align-items-end">
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
    const container = document.getElementById('location-rows');
    const tpl = document.getElementById('location-row-template');
    let nextIndex = {{ count($oldLocations) }};

    function bindRow(row) {
        row.querySelector('.remove-location-row')?.addEventListener('click', function () {
            if (container.querySelectorAll('.location-row').length <= 1) {
                return;
            }
            const wasChecked = row.querySelector('.location-primary')?.checked;
            row.remove();
            if (wasChecked) {
                const first = container.querySelector('.location-primary');
                if (first) first.checked = true;
            }
        });
    }

    container.querySelectorAll('.location-row').forEach(bindRow);

    document.getElementById('add-location-row').addEventListener('click', function () {
        const html = tpl.innerHTML.replace(/__INDEX__/g, nextIndex++);
        const wrap = document.createElement('div');
        wrap.innerHTML = html.trim();
        const row = wrap.firstElementChild;
        container.appendChild(row);
        bindRow(row);
    });
})();
</script>
@endsection
