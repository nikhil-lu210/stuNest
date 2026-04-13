@extends('layouts.administration.app')

@section('page_title', __('Edit student account'))

@section('page_name')
    <b class="text-uppercase">{{ __('Edit student account') }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('User Management') }}</li>
    <li class="breadcrumb-item">
        <a href="{{ route('administration.students.index') }}">{{ __('Student') }}</a>
    </li>
    <li class="breadcrumb-item active">{{ __('Edit') }}</li>
@endsection

@section('css_links')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
@endsection

@section('custom_css')
    <style>
        .student-account-edit {
            max-width: 100%;
        }
        .student-account-edit [class*="col-"] {
            min-width: 0;
        }
        .student-account-edit #student-edit-card,
        .student-account-edit #student-edit-card .card-body {
            overflow: visible;
        }
        .student-account-edit .student-select-col .select2-container {
            width: 100% !important;
            max-width: 100%;
        }
        .student-account-edit .student-select2-dropdown {
            box-sizing: border-box;
            max-width: 100% !important;
        }
        .student-account-edit .select2-container--default .select2-results > .select2-results__options {
            max-height: 16rem;
        }
        .student-account-edit .bootstrap-select {
            width: 100% !important;
        }
        .student-account-edit .bootstrap-select > .dropdown-toggle {
            text-align: start;
            background-color: #fff !important;
            color: #6f6b7d !important;
            border: 1px solid #dbdade !important;
            box-shadow: none !important;
        }
        .student-account-edit .bootstrap-select > .dropdown-toggle:hover,
        .student-account-edit .bootstrap-select > .dropdown-toggle:focus,
        .student-account-edit .bootstrap-select.show > .dropdown-toggle {
            background-color: #fff !important;
            color: #6f6b7d !important;
            border-color: #dbdade !important;
        }
        .student-account-edit .bootstrap-select.show > .dropdown-toggle {
            border-color: #7367f0 !important;
            box-shadow: 0 0.125rem 0.25rem rgba(165, 163, 174, 0.3) !important;
        }
        .student-account-edit .bootstrap-select.is-invalid > .dropdown-toggle {
            border-color: #ea5455 !important;
        }
    </style>
@endsection

@section('content')
    @php
        $defaultStudentName = trim(($user->first_name ?? '').' '.($user->last_name ?? ''));
    @endphp
    <div class="row justify-content-center student-account-edit">
        <div class="col-12 col-xl-10">
            <div class="card mb-4" id="student-edit-card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="mb-0">{{ __('Edit student account') }}</h5>
                    <span class="text-muted small">{{ __('User ID') }}: <span class="text-heading fw-medium">{{ $user->userid }}</span></span>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.students.update', ['user' => $user]) }}" method="post" id="student-edit-form" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="student_name">{{ __('Student name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="student_name" id="student_name" value="{{ old('student_name', $defaultStudentName) }}"
                                    class="form-control @error('student_name') is-invalid @enderror"
                                    placeholder="{{ __('First and last name') }}" autocomplete="name" required>
                                @error('student_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label" for="phone">{{ __('Contact no.') }} <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                    class="form-control @error('phone') is-invalid @enderror" autocomplete="tel" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label" for="whatsapp">{{ __('WhatsApp no.') }} <span class="text-muted">({{ __('optional') }})</span></label>
                                <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}"
                                    class="form-control @error('whatsapp') is-invalid @enderror" autocomplete="tel">
                                @error('whatsapp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 student-select-col">
                                <label class="form-label" for="country_code">{{ __('Student country') }} <span class="text-danger">*</span></label>
                                <select name="country_code" id="country_code" class="form-select no-select2 select2 @error('country_code') is-invalid @enderror"
                                    data-placeholder="{{ __('Select country') }}" data-min-search-options="0" required>
                                    <option value="">{{ __('Select country') }}</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country['code'] }}" @selected(strtoupper((string) old('country_code', $user->country_code)) === $country['code'])>{{ $country['name'] }}</option>
                                    @endforeach
                                </select>
                                <small class="small text-muted mb-2">{{ __('The country or region the student is from (nationality / home country).') }}</small>
                                @error('country_code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 student-select-col">
                                <label class="form-label" for="university_id">{{ __('University') }} <span class="text-danger">*</span></label>
                                <select name="university_id" id="university_id" class="form-select no-select2 select2 @error('university_id') is-invalid @enderror"
                                    data-placeholder="{{ __('Select university') }}" data-min-search-options="0" required>
                                    <option value="">{{ __('Select university') }}</option>
                                    @foreach ($universities as $uni)
                                        <option value="{{ $uni->id }}" data-domain="{{ $uni->email_code }}" @selected(old('university_id', $user->institution_id) == $uni->id)>
                                            {{ $uni->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('university_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 student-select-col">
                                <label class="form-label" for="institute_location_id">{{ __('Branch') }} <span class="text-danger">*</span></label>
                                <select name="institute_location_id" id="institute_location_id"
                                    class="form-select no-select2 select2 @error('institute_location_id') is-invalid @enderror"
                                    data-placeholder="{{ __('Select branch') }}" data-min-search-options="0"
                                    @if ($branchOptions->isEmpty()) disabled @endif required>
                                    <option value="">{{ __('Select branch') }}</option>
                                    @foreach ($branchOptions as $loc)
                                        <option value="{{ $loc->id }}" @selected(old('institute_location_id', $user->institute_location_id) == $loc->id)>{{ $loc->name }}</option>
                                    @endforeach
                                </select>
                                @error('institute_location_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="email_prefix">{{ __('Email') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="email_prefix" id="email_prefix" value="{{ old('email_prefix', $emailPrefix) }}"
                                        class="form-control @error('email_prefix') is-invalid @enderror @error('email') is-invalid @enderror"
                                        placeholder="{{ __('username') }}" autocomplete="off" inputmode="email" required>
                                    <span class="input-group-text" id="email-domain-suffix">{{ $initialEmailSuffix }}</span>
                                </div>
                                @error('email_prefix')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
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

                            <div class="col-md-2">
                                <label class="form-label" for="account_status">{{ __('Account status') }} <span class="text-danger">*</span></label>
                                <select name="account_status" id="account_status"
                                    class="selectpicker form-select w-100 no-select2 @error('account_status') is-invalid @enderror"
                                    data-live-search="false"
                                    data-width="100%"
                                    required>
                                    <option value="{{ \App\Models\User::ACCOUNT_STATUS_ACTIVE }}" @selected(old('account_status', $user->account_status) === \App\Models\User::ACCOUNT_STATUS_ACTIVE)>{{ __('Active') }}</option>
                                    <option value="{{ \App\Models\User::ACCOUNT_STATUS_PENDING }}" @selected(old('account_status', $user->account_status) === \App\Models\User::ACCOUNT_STATUS_PENDING)>{{ __('Pending') }}</option>
                                    <option value="{{ \App\Models\User::ACCOUNT_STATUS_REJECTED }}" @selected(old('account_status', $user->account_status) === \App\Models\User::ACCOUNT_STATUS_REJECTED)>{{ __('Rejected') }}</option>
                                    <option value="{{ \App\Models\User::ACCOUNT_STATUS_UNVERIFIED }}" @selected(old('account_status', $user->account_status) === \App\Models\User::ACCOUNT_STATUS_UNVERIFIED)>{{ __('Unverified') }}</option>
                                </select>
                                @error('account_status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex flex-column flex-sm-row gap-2 justify-content-between">
                            <a href="{{ route('administration.students.index') }}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i>
                                {{ __('Update student') }}
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

            var branchesUrl = @json(route('administration.students.branches'));
            var initialSuffix = @json($initialEmailSuffix);

            function select2Opts($el) {
                var $col = $el.closest('.student-select-col');
                var $parent = $col.length ? $col : $('#student-edit-card');
                if (!$parent.length) {
                    $parent = $(document.body);
                }
                return {
                    width: '100%',
                    allowClear: false,
                    placeholder: $el.attr('data-placeholder') || '',
                    dropdownParent: $parent,
                    dropdownCssClass: 'student-select2-dropdown',
                    minimumResultsForSearch: parseInt($el.attr('data-min-search-options') || '12', 10) || 0
                };
            }

            function syncSelect2DropdownWidth($el) {
                var $container = $el.next('.select2-container');
                if (!$container.length) {
                    return;
                }
                var w = $container.outerWidth();
                var $col = $el.closest('.student-select-col');
                var $dropdown = $col.length
                    ? $col.find('.select2-dropdown.student-select2-dropdown')
                    : $('#student-edit-card').find('.select2-dropdown.student-select2-dropdown');
                if ($dropdown.length && w) {
                    $dropdown.css({ width: w, maxWidth: '100%', minWidth: 0 });
                }
            }

            function destroySelect2($el) {
                if ($el.length && $el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }
            }

            function initStudentSelects() {
                $('#country_code, #university_id').each(function () {
                    var $el = $(this);
                    if (!$el.hasClass('select2-hidden-accessible')) {
                        $el.select2(select2Opts($el));
                        $el.off('select2:open.studentWidth').on('select2:open.studentWidth', function () {
                            var self = $(this);
                            window.requestAnimationFrame(function () {
                                syncSelect2DropdownWidth(self);
                            });
                        });
                    }
                });
                var $branch = $('#institute_location_id');
                destroySelect2($branch);
                $branch.select2(select2Opts($branch));
                $branch.off('select2:open.studentWidth').on('select2:open.studentWidth', function () {
                    var self = $(this);
                    window.requestAnimationFrame(function () {
                        syncSelect2DropdownWidth(self);
                    });
                });
            }

            function setEmailSuffixFromUniversity() {
                var $opt = $('#university_id').find('option:selected');
                var domain = $opt.attr('data-domain');
                if (domain && String(domain).length) {
                    $('#email-domain-suffix').text(domain);
                } else {
                    $('#email-domain-suffix').text('@—');
                }
            }

            function loadBranches(universityId) {
                var $branch = $('#institute_location_id');
                destroySelect2($branch);
                $branch.prop('disabled', true).empty().append(
                    $('<option>', { value: '', text: @json(__('Select branch')) })
                );

                if (!universityId) {
                    $branch.select2(select2Opts($branch));
                    return;
                }

                $.ajax({
                    url: branchesUrl,
                    method: 'GET',
                    data: { university_id: universityId },
                    dataType: 'json'
                }).done(function (res) {
                    var rows = (res && res.data) ? res.data : [];
                    rows.forEach(function (row) {
                        $branch.append($('<option>', { value: row.id, text: row.name }));
                    });
                    $branch.prop('disabled', rows.length === 0);
                }).fail(function () {
                    $branch.prop('disabled', true);
                }).always(function () {
                    $branch.select2(select2Opts($branch));
                });
            }

            $(document).ready(function () {
                $('#email-domain-suffix').text(initialSuffix || '@—');

                initStudentSelects();

                var $status = $('#account_status');
                if ($status.length && $.fn.selectpicker) {
                    $status.selectpicker({
                        liveSearch: false,
                        width: '100%',
                        container: '#student-edit-card',
                    });
                }

                $('#university_id').on('change', function () {
                    setEmailSuffixFromUniversity();
                    loadBranches($(this).val());
                });

                if ($('#university_id').val()) {
                    setEmailSuffixFromUniversity();
                }

                var formEl = document.getElementById('student-edit-form');
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
            });
        })(window.jQuery);
    </script>
@endsection
