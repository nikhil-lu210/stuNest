@extends('layouts.administration.app')

@php
    $wizardStep = 1;
    if ($errors->any()) {
        if ($errors->has('institute_location_id')) {
            $wizardStep = 1;
        } elseif ($errors->hasAny(['avatar', 'userid', 'first_name', 'middle_name', 'last_name'])) {
            $wizardStep = 2;
        } else {
            $wizardStep = 3;
        }
    }
@endphp

@section('page_title', __('Add Institute Representative'))

@section('custom_css')
<style>
    .rep-wiz-track {
        position: relative;
        width: 100%;
    }
    .rep-wiz-track-inner {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.25rem;
        flex-wrap: nowrap;
    }
    @media (max-width: 575.98px) {
        .rep-wiz-track-inner {
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.75rem;
        }
    }
    .rep-wiz-node {
        flex: 1 1 0;
        min-width: 0;
        text-align: center;
        position: relative;
        z-index: 1;
    }
    @media (min-width: 576px) {
        .rep-wiz-node:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 1.125rem;
            left: calc(50% + 1.125rem);
            right: calc(-50% + 1.125rem);
            height: 2px;
            background: var(--bs-border-color);
            z-index: 0;
        }
        .rep-wiz-node.rep-wiz-done:not(:last-child)::after,
        .rep-wiz-node.rep-wiz-current:not(:last-child)::after {
            background: var(--bs-primary);
        }
    }
    .rep-wiz-circle {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.35rem;
        border: 2px solid var(--bs-border-color);
        background: var(--bs-body-bg);
        color: var(--bs-secondary-color);
        transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    }
    .rep-wiz-node.rep-wiz-current .rep-wiz-circle {
        border-color: var(--bs-primary);
        background: var(--bs-primary);
        color: #fff;
    }
    .rep-wiz-node.rep-wiz-done .rep-wiz-circle {
        border-color: var(--bs-primary);
        background: rgba(var(--bs-primary-rgb), 0.12);
        color: var(--bs-primary);
    }
    .rep-wiz-label {
        font-size: 0.75rem;
        line-height: 1.2;
        color: var(--bs-secondary-color);
        word-break: break-word;
    }
    .rep-wiz-node.rep-wiz-current .rep-wiz-label {
        color: var(--bs-heading-color);
        font-weight: 600;
    }
    #representative-wizard-form .select2-container {
        width: 100% !important;
        max-width: 100%;
    }
    .rep-wizard-panels {
        min-height: 8rem;
    }
    @media (min-width: 768px) {
        .rep-wizard-panels {
            min-height: 10rem;
        }
    }
</style>
@endsection

@section('page_name')
    <b class="text-uppercase">{{ __('Add Institute Representative') }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Institute') }}</li>
    <li class="breadcrumb-item"><a href="{{ route('administration.institute.representatives.create.entry') }}">{{ __('New representative') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('administration.institute.show', $institute) }}">{{ \Illuminate\Support\Str::limit($institute->name, 24) }}</a></li>
    <li class="breadcrumb-item active">{{ __('Add representative') }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10 col-xxl-8">
        <div class="card mb-4">
            <div class="card-header header-elements flex-column flex-md-row align-items-md-center gap-2">
                <h5 class="mb-0 text-break">{{ __('Add representative for') }} {{ $institute->name }}</h5>
                <div class="card-header-elements ms-md-auto">
                    <a href="{{ route('administration.institute.representatives.create.entry') }}" class="btn btn-sm btn-label-secondary">
                        <span class="tf-icon ti ti-arrow-left ti-xs me-1"></span>
                        {{ __('Back') }}
                    </a>
                </div>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="alert alert-primary mb-3 mb-md-4" role="alert">
                    <small class="d-block">{{ __('Email must end with') }} <code>{{ $institute->email_code }}</code>.</small>
                </div>

                <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-2 gap-sm-3 mb-3 mb-md-4">
                    <div class="rep-wiz-track flex-grow-1 order-2 order-sm-1">
                        <div class="rep-wiz-track-inner" id="rep-wiz-track" role="list">
                            <div class="rep-wiz-node" data-wiz-step="1" role="listitem">
                                <div class="rep-wiz-circle">1</div>
                                <div class="rep-wiz-label">{{ __('Branch') }}</div>
                            </div>
                            <div class="rep-wiz-node" data-wiz-step="2" role="listitem">
                                <div class="rep-wiz-circle">2</div>
                                <div class="rep-wiz-label">{{ __('Profile') }}</div>
                            </div>
                            <div class="rep-wiz-node" data-wiz-step="3" role="listitem">
                                <div class="rep-wiz-circle">3</div>
                                <div class="rep-wiz-label">{{ __('Account') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-center justify-content-sm-end flex-shrink-0 order-1 order-sm-2">
                        <span class="badge bg-label-primary px-3 py-2" id="rep-wizard-step-label">
                            {{ __('Step') }} <span id="rep-wizard-step-num">{{ $wizardStep }}</span> / 3
                        </span>
                    </div>
                </div>

                <form id="representative-wizard-form" class="d-flex flex-column flex-grow-1" action="{{ route('administration.institute.representatives.store', $institute) }}" method="post" enctype="multipart/form-data" autocomplete="off" novalidate>
                    @csrf

                    <div class="rep-wizard-panels flex-grow-1">
                        <div id="rep-step-1" class="rep-wizard-step {{ $wizardStep === 1 ? '' : 'd-none' }}" data-step="1">
                            <div class="row">
                                <div class="mb-0 col-12">
                                    <label class="form-label" for="institute_location_id">{{ __('Branch') }} <span class="text-danger">*</span></label>
                                    <select name="institute_location_id" id="institute_location_id" class="form-select @error('institute_location_id') is-invalid @enderror" data-placeholder="{{ __('Select branch') }}" data-min-search-options="0" required>
                                        <option value="" disabled {{ old('institute_location_id') ? '' : 'selected' }}>{{ __('Select branch') }}</option>
                                        @foreach ($institute->locations as $loc)
                                            <option value="{{ $loc->id }}" @selected(old('institute_location_id') == $loc->id)>{{ $loc->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('institute_location_id')
                                        <div class="invalid-feedback d-block"><i class="ti ti-info-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div id="rep-step-2" class="rep-wizard-step {{ $wizardStep === 2 ? '' : 'd-none' }}" data-step="2">
                            <div class="d-flex flex-column flex-sm-row align-items-start gap-3 gap-sm-4 mb-3">
                                <img src="{{ file_exists(public_path('assets/img/no_image.svg')) ? asset('assets/img/no_image.svg') : 'https://placehold.co/100x100/e2e8f0/64748b?text=Avatar' }}" alt="" class="d-block rounded flex-shrink-0" style="width: 100px; height: 100px; object-fit: cover;" id="uploadedAvatar" />
                                <div class="button-wrapper flex-grow-1 w-100">
                                    <label for="upload" class="btn btn-primary me-2 mb-2">
                                        <span class="d-none d-sm-inline">{{ __('Upload Avatar') }}</span>
                                        <span class="d-sm-none">{{ __('Avatar') }}</span>
                                        <input type="file" name="avatar" id="upload" class="account-file-input" hidden accept="image/png, image/jpeg, image/jpg"/>
                                    </label>
                                    <button type="button" class="btn btn-label-secondary account-image-reset mb-2">{{ __('Reset') }}</button>
                                    <div class="text-muted small">{{ __('JPG, PNG. Max 2MB') }}</div>
                                    @error('avatar')
                                        <div class="text-danger small mt-1"><i class="ti ti-info-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <hr class="my-3" />
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label" for="userid">{{ __('User ID') }} <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text">UID</span>
                                        <input type="text" id="userid" name="userid" class="form-control @error('userid') is-invalid @enderror" value="{{ old('userid', date('Ymd')) }}" required />
                                    </div>
                                    @error('userid')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row g-3 mt-0">
                                <div class="col-12 col-md-4">
                                    <label class="form-label" for="first_name">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" class="form-control @error('first_name') is-invalid @enderror" required />
                                    @error('first_name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label" for="middle_name">{{ __('Middle Name') }}</label>
                                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}" class="form-control @error('middle_name') is-invalid @enderror" />
                                    @error('middle_name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label" for="last_name">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" class="form-control @error('last_name') is-invalid @enderror" required />
                                    @error('last_name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div id="rep-step-3" class="rep-wizard-step {{ $wizardStep === 3 ? '' : 'd-none' }}" data-step="3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" for="email">{{ __('Email') }} <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="username" />
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 form-password-toggle">
                                    <label class="form-label" for="password">{{ __('Password') }} <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" minlength="8" id="password" name="password" value="{{ old('password') }}" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password" />
                                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 form-password-toggle">
                                    <label class="form-label" for="password_confirmation">{{ __('Confirm password') }} <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" minlength="8" id="password_confirmation" name="password_confirmation" value="{{ old('password_confirmation') }}" class="form-control" required autocomplete="new-password" />
                                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2 mt-3 mt-md-4 pt-3 border-top rep-wizard-footer">
                        <button type="button" class="btn btn-label-secondary order-2 order-sm-1" id="rep-wiz-back" style="{{ $wizardStep > 1 ? '' : 'visibility: hidden' }}">{{ __('Back') }}</button>
                        <div class="d-flex gap-2 ms-sm-auto order-1 order-sm-2 w-100 w-sm-auto justify-content-stretch justify-content-sm-end">
                            <button type="button" class="btn btn-primary flex-grow-1 flex-sm-grow-0" id="rep-wiz-next">{{ __('Continue') }} <i class="ti ti-arrow-right ms-1"></i></button>
                            <button type="submit" class="btn btn-success flex-grow-1 flex-sm-grow-0 d-none" id="rep-wiz-submit">{{ __('Create representative') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_script')
<script>
(function () {
    var currentStep = {{ (int) $wizardStep }};
    var maxStep = 3;

    function getBranchValue() {
        var el = document.getElementById('institute_location_id');
        if (!el) return '';
        if (typeof window.jQuery !== 'undefined') {
            var $el = window.jQuery(el);
            if ($el.hasClass('select2-hidden-accessible')) {
                var v = $el.val();
                if (v !== null && v !== undefined && v !== '') {
                    return String(v);
                }
            }
        }
        return el.value ? String(el.value) : '';
    }

    function showStep(n) {
        currentStep = n;
        document.querySelectorAll('.rep-wizard-step').forEach(function (el) {
            var step = parseInt(el.getAttribute('data-step'), 10);
            el.classList.toggle('d-none', step !== n);
        });
        var numEl = document.getElementById('rep-wizard-step-num');
        if (numEl) numEl.textContent = n;

        document.querySelectorAll('.rep-wiz-node').forEach(function (node) {
            var ps = parseInt(node.getAttribute('data-wiz-step'), 10);
            node.classList.remove('rep-wiz-current', 'rep-wiz-done');
            if (ps === n) {
                node.classList.add('rep-wiz-current');
            } else if (ps < n) {
                node.classList.add('rep-wiz-done');
            }
        });

        var back = document.getElementById('rep-wiz-back');
        var next = document.getElementById('rep-wiz-next');
        var submit = document.getElementById('rep-wiz-submit');
        if (back) {
            back.style.visibility = n > 1 ? 'visible' : 'hidden';
        }
        if (next && submit) {
            if (n < maxStep) {
                next.classList.remove('d-none');
                submit.classList.add('d-none');
            } else {
                next.classList.add('d-none');
                submit.classList.remove('d-none');
            }
        }

        if (typeof window.jQuery !== 'undefined') {
            window.jQuery(window).trigger('resize');
        }
    }

    function validateStep(step) {
        if (step === 1) {
            var v = getBranchValue();
            if (!v) {
                var loc = document.getElementById('institute_location_id');
                if (loc && typeof window.jQuery !== 'undefined') {
                    var $loc = window.jQuery(loc);
                    if ($loc.hasClass('select2-hidden-accessible')) {
                        $loc.select2('open');
                    } else {
                        loc.focus();
                    }
                }
                return false;
            }
            return true;
        }
        if (step === 2) {
            var ids = ['userid', 'first_name', 'last_name'];
            for (var i = 0; i < ids.length; i++) {
                var inp = document.getElementById(ids[i]);
                if (!inp || !String(inp.value).trim()) {
                    if (inp) {
                        inp.focus();
                        inp.reportValidity();
                    }
                    return false;
                }
            }
            return true;
        }
        if (step === 3) {
            var email = document.getElementById('email');
            var pw = document.getElementById('password');
            var pwc = document.getElementById('password_confirmation');
            if (!email || !String(email.value).trim()) {
                if (email) {
                    email.focus();
                    email.reportValidity();
                }
                return false;
            }
            if (!pw || !pw.value || pw.value.length < 8) {
                if (pw) {
                    pw.focus();
                    pw.reportValidity();
                }
                return false;
            }
            if (!pwc || pw.value !== pwc.value) {
                if (pwc) {
                    pwc.setCustomValidity({!! json_encode(__('Passwords must match.')) !!});
                    pwc.focus();
                    pwc.reportValidity();
                    pwc.setCustomValidity('');
                }
                return false;
            }
            return true;
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('representative-wizard-form');
        var nextBtn = document.getElementById('rep-wiz-next');
        var backBtn = document.getElementById('rep-wiz-back');

        showStep(currentStep);

        if (nextBtn) {
            nextBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (!validateStep(currentStep)) {
                    return;
                }
                if (currentStep < maxStep) {
                    showStep(currentStep + 1);
                }
            });
        }
        if (backBtn) {
            backBtn.addEventListener('click', function (e) {
                e.preventDefault();
                if (currentStep > 1) {
                    showStep(currentStep - 1);
                }
            });
        }
        if (form) {
            form.addEventListener('submit', function onRepWizardSubmit(e) {
                e.preventDefault();
                if (!validateStep(1)) {
                    showStep(1);
                    return;
                }
                if (!validateStep(2)) {
                    showStep(2);
                    return;
                }
                if (!validateStep(3)) {
                    showStep(3);
                    return;
                }
                form.removeEventListener('submit', onRepWizardSubmit);
                form.submit();
            });
        }

        var accountUserImage = document.getElementById('uploadedAvatar');
        var fileInput = document.querySelector('#representative-wizard-form .account-file-input');
        var resetFileInput = document.querySelector('#representative-wizard-form .account-image-reset');
        if (accountUserImage && fileInput && resetFileInput) {
            var resetImage = accountUserImage.getAttribute('src');
            fileInput.addEventListener('change', function () {
                if (this.files[0]) {
                    accountUserImage.setAttribute('src', window.URL.createObjectURL(this.files[0]));
                }
            });
            resetFileInput.addEventListener('click', function () {
                fileInput.value = '';
                accountUserImage.setAttribute('src', resetImage);
            });
        }
    });
})();
</script>
@endsection
