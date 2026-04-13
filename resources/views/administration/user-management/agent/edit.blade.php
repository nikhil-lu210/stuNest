@extends('layouts.administration.app')

@section('page_title', __('Edit agent account'))

@section('page_name')
    <b class="text-uppercase">{{ __('Edit agent account') }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('User Management') }}</li>
    <li class="breadcrumb-item">
        <a href="{{ route('administration.agents.index') }}">{{ __('Agent') }}</a>
    </li>
    <li class="breadcrumb-item active">{{ __('Edit') }}</li>
@endsection

@section('css_links')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
@endsection

@section('custom_css')
    <style>
        .agent-account-edit {
            max-width: 100%;
            overflow-x: clip;
        }
        .agent-account-edit [class*="col-"] {
            min-width: 0;
        }
        .agent-account-edit .bootstrap-select {
            width: 100% !important;
        }
        .agent-account-edit .bootstrap-select > .dropdown-toggle {
            text-align: start;
            background-color: #fff !important;
            color: #6f6b7d !important;
            border: 1px solid #dbdade !important;
            box-shadow: none !important;
        }
        .agent-account-edit .bootstrap-select > .dropdown-toggle:hover,
        .agent-account-edit .bootstrap-select > .dropdown-toggle:focus,
        .agent-account-edit .bootstrap-select.show > .dropdown-toggle {
            background-color: #fff !important;
            color: #6f6b7d !important;
            border-color: #dbdade !important;
        }
        .agent-account-edit .bootstrap-select.show > .dropdown-toggle {
            border-color: #7367f0 !important;
            box-shadow: 0 0.125rem 0.25rem rgba(165, 163, 174, 0.3) !important;
        }
        .agent-account-edit .bootstrap-select.is-invalid > .dropdown-toggle {
            border-color: #ea5455 !important;
        }
    </style>
@endsection

@section('content')
    <div class="row justify-content-center agent-account-edit">
        <div class="col-12 col-xl-10">
            <div class="card mb-4" id="agent-edit-card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="mb-0">{{ __('Edit agent account') }}</h5>
                    <span class="text-muted small">{{ __('User ID') }}: <span class="text-heading fw-medium">{{ $user->userid }}</span></span>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.agents.update', ['user' => $user]) }}" method="post" id="agent-edit-form" novalidate>
                        @csrf
                        @method('PUT')

                        <h6 class="mb-3 text-body border-bottom pb-2">{{ __('Personal Information') }}</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label" for="first_name">{{ __('First name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}"
                                    class="form-control @error('first_name') is-invalid @enderror"
                                    placeholder="{{ __('First name') }}" autocomplete="given-name" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="last_name">{{ __('Last name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}"
                                    class="form-control @error('last_name') is-invalid @enderror"
                                    placeholder="{{ __('Last name') }}" autocomplete="family-name" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="phone">{{ __('Contact number') }} <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    placeholder="{{ __('Phone') }}" autocomplete="tel" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="whatsapp">{{ __('WhatsApp number') }} <span class="text-muted">({{ __('optional') }})</span></label>
                                <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}"
                                    class="form-control @error('whatsapp') is-invalid @enderror"
                                    placeholder="{{ __('WhatsApp') }}" autocomplete="tel">
                                @error('whatsapp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="email">{{ __('Email address') }} <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="{{ __('name@example.com') }}" autocomplete="email" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 form-password-toggle">
                                <label class="form-label" for="password">{{ __('New password') }}</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" name="password" id="password" minlength="8"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="{{ __('Leave blank to keep current') }}" autocomplete="new-password">
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                                <small class="text-muted">{{ __('Leave blank to keep the current password.') }}</small>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 form-password-toggle">
                                <label class="form-label" for="password_confirmation">{{ __('Confirm new password') }}</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" name="password_confirmation" id="password_confirmation" minlength="8"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        placeholder="{{ __('Confirm new password') }}" autocomplete="new-password">
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h6 class="mb-3 text-body border-bottom pb-2">{{ __('Agency Information') }}</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="agency_name">{{ __('Agency name / Company name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="agency_name" id="agency_name" value="{{ old('agency_name', $user->agency_name) }}"
                                    class="form-control @error('agency_name') is-invalid @enderror"
                                    placeholder="{{ __('Agency name / Company name') }}" required>
                                @error('agency_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="license_number">{{ __('License / Registration number') }} <span class="text-muted">({{ __('optional') }})</span></label>
                                <input type="text" name="license_number" id="license_number" value="{{ old('license_number', $user->license_number) }}"
                                    class="form-control @error('license_number') is-invalid @enderror"
                                    placeholder="{{ __('License or registration no.') }}">
                                @error('license_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="account_status">{{ __('Account status') }} <span class="text-danger">*</span></label>
                                <select name="account_status" id="account_status"
                                    class="selectpicker form-select w-100 no-select2 @error('account_status') is-invalid @enderror"
                                    data-live-search="false"
                                    data-width="100%"
                                    required>
                                    <option value="active" @selected(old('account_status', $user->account_status) === 'active')>{{ __('Active') }}</option>
                                    <option value="pending" @selected(old('account_status', $user->account_status) === 'pending')>{{ __('Pending') }}</option>
                                    <option value="rejected" @selected(old('account_status', $user->account_status) === 'rejected')>{{ __('Rejected') }}</option>
                                </select>
                                @error('account_status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="office_address">{{ __('Office address') }}</label>
                                <textarea name="office_address" id="office_address" rows="3"
                                    class="form-control @error('office_address') is-invalid @enderror"
                                    placeholder="{{ __('Street, city, postal code') }}">{{ old('office_address', $user->office_address) }}</textarea>
                                @error('office_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex flex-column flex-sm-row gap-2 justify-content-between">
                            <a href="{{ route('administration.agents.index') }}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="ti ti-device-floppy me-1"></i>
                                {{ __('Update Agent') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script_links')
    <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.min.js') }}"></script>
@endsection

@section('custom_script')
    <script>
        (function ($) {
            'use strict';

            $(function () {
                var $status = $('#account_status');
                if ($status.length && $.fn.selectpicker) {
                    $status.selectpicker({
                        liveSearch: false,
                        width: '100%',
                        container: '#agent-edit-card',
                    });
                }
            });

            var formEl = document.getElementById('agent-edit-form');
            if (formEl) {
                formEl.addEventListener('click', function (e) {
                    var span = e.target.closest('.form-password-toggle .input-group-text');
                    if (!span || !formEl.contains(span)) {
                        return;
                    }
                    e.preventDefault();
                    e.stopPropagation();
                    var wrap = span.closest('.form-password-toggle');
                    var input = wrap ? wrap.querySelector('input') : null;
                    var icon = wrap ? wrap.querySelector('i') : null;
                    if (!input || !icon) {
                        return;
                    }
                    if (input.getAttribute('type') === 'password') {
                        input.setAttribute('type', 'text');
                        icon.classList.remove('ti-eye-off');
                        icon.classList.add('ti-eye');
                    } else {
                        input.setAttribute('type', 'password');
                        icon.classList.remove('ti-eye');
                        icon.classList.add('ti-eye-off');
                    }
                }, true);
            }
        })(window.jQuery);
    </script>
@endsection
