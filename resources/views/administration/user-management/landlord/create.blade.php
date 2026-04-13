@extends('layouts.administration.app')

@section('page_title', __('Create landlord account'))

@section('page_name')
    <b class="text-uppercase">{{ __('Create landlord account') }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('User Management') }}</li>
    <li class="breadcrumb-item active">{{ __('Create landlord account') }}</li>
@endsection

@section('css_links')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
@endsection

@section('custom_css')
    <style>
        body {
            overflow-x: clip;
        }
        .landlord-account-create {
            max-width: 100%;
            overflow-x: clip;
        }
        .landlord-account-create [class*="col-"] {
            min-width: 0;
        }
        .landlord-account-create .bootstrap-select {
            width: 100% !important;
        }
        .landlord-account-create .bootstrap-select > .dropdown-toggle {
            text-align: start;
            background-color: #fff !important;
            color: #6f6b7d !important;
            border: 1px solid #dbdade !important;
            box-shadow: none !important;
        }
        .landlord-account-create .bootstrap-select > .dropdown-toggle:hover,
        .landlord-account-create .bootstrap-select > .dropdown-toggle:focus,
        .landlord-account-create .bootstrap-select.show > .dropdown-toggle {
            background-color: #fff !important;
            color: #6f6b7d !important;
            border-color: #dbdade !important;
        }
        .landlord-account-create .bootstrap-select.show > .dropdown-toggle {
            border-color: #7367f0 !important;
            box-shadow: 0 0.125rem 0.25rem rgba(165, 163, 174, 0.3) !important;
        }
        .landlord-account-create .bootstrap-select.is-invalid > .dropdown-toggle {
            border-color: #ea5455 !important;
        }
    </style>
@endsection

@section('content')
    <div class="row justify-content-center landlord-account-create">
        <div class="col-12 col-xl-10">
            <div class="card mb-4" id="landlord-create-card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Create landlord account') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.landlords.store') }}" method="post" id="landlord-create-form" novalidate>
                        @csrf

                        <h6 class="mb-3 text-body border-bottom pb-2">{{ __('Personal Information') }}</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label" for="first_name">{{ __('First name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                                    class="form-control @error('first_name') is-invalid @enderror"
                                    placeholder="{{ __('First name') }}" autocomplete="given-name" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="last_name">{{ __('Last name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                                    class="form-control @error('last_name') is-invalid @enderror"
                                    placeholder="{{ __('Last name') }}" autocomplete="family-name" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="phone">{{ __('Contact number') }} <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    placeholder="{{ __('Phone') }}" autocomplete="tel" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="whatsapp">{{ __('WhatsApp number') }} <span class="text-muted">({{ __('optional') }})</span></label>
                                <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}"
                                    class="form-control @error('whatsapp') is-invalid @enderror"
                                    placeholder="{{ __('WhatsApp') }}" autocomplete="tel">
                                @error('whatsapp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="billing_address">{{ __('Billing / Home address') }} <span class="text-muted">({{ __('optional') }})</span></label>
                                <textarea name="billing_address" id="billing_address" rows="3"
                                    class="form-control @error('billing_address') is-invalid @enderror"
                                    placeholder="{{ __('Street, city, postal code') }}">{{ old('billing_address') }}</textarea>
                                @error('billing_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h6 class="mb-3 text-body border-bottom pb-2">{{ __('Account & Security') }}</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="email">{{ __('Email address') }} <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="{{ __('name@example.com') }}" autocomplete="email" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 form-password-toggle">
                                <label class="form-label" for="password">{{ __('Password') }} <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <input type="password" name="password" id="password" minlength="8"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="{{ __('••••••••') }}" autocomplete="new-password" required>
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 form-password-toggle">
                                <label class="form-label" for="password_confirmation">{{ __('Confirm password') }} <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <input type="password" name="password_confirmation" id="password_confirmation" minlength="8"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        placeholder="{{ __('••••••••') }}" autocomplete="new-password" required>
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" for="account_status">{{ __('Account status') }} <span class="text-danger">*</span></label>
                                <select name="account_status" id="account_status"
                                    class="selectpicker form-select w-100 no-select2 @error('account_status') is-invalid @enderror"
                                    data-live-search="false"
                                    data-width="100%"
                                    required>
                                    <option value="active" @selected(old('account_status', 'active') === 'active')>{{ __('Active') }}</option>
                                    <option value="pending" @selected(old('account_status') === 'pending')>{{ __('Pending') }}</option>
                                    <option value="rejected" @selected(old('account_status') === 'rejected')>{{ __('Rejected') }}</option>
                                </select>
                                @error('account_status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex flex-column flex-sm-row gap-2 justify-content-between">
                            <a href="{{ route('administration.landlords.index') }}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="ti ti-user-plus me-1"></i>
                                {{ __('Create Landlord') }}
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
                        container: '#landlord-create-card',
                    });
                }
            });

            var formEl = document.getElementById('landlord-create-form');
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
