@extends('layouts.administration.app')

@section('page_title', __('Create New Representative'))

@section('page_name')
    <b class="text-uppercase">{{ __('Create New Representative') }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Institute') }}</li>
    <li class="breadcrumb-item"><a href="{{ route('administration.institute.representatives.index') }}">{{ __('Representative') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Create New Representative') }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12 col-lg-8">
        <div class="card mb-4">
            <div class="card-header header-elements">
                <h5 class="mb-0">{{ __('Step 1: Choose an institute') }}</h5>
                <div class="card-header-elements ms-auto">
                    <a href="{{ route('administration.institute.representatives.index') }}" class="btn btn-sm btn-label-secondary">
                        <span class="tf-icon ti ti-arrow-left ti-xs me-1"></span>
                        {{ __('Back') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if ($institutes->isEmpty())
                    <p class="text-muted mb-0">{{ __('No institutes yet.') }}</p>
                    @can('Institute Create')
                        <a href="{{ route('administration.institute.create') }}" class="btn btn-primary mt-3">{{ __('Create Institute') }}</a>
                    @endcan
                @else
                    <p class="text-muted mb-3">{{ __('Search and select an institute, then click Continue to go to the registration steps.') }}</p>
                    <div class="mb-4">
                        <label class="form-label" for="institute_id">{{ __('Institute') }} <strong class="text-danger">*</strong></label>
                        <select id="institute_id" name="institute_id" class="form-select" data-placeholder="{{ __('Select institute') }}" data-min-search-options="8" data-allow-clear="true">
                            <option value="">{{ __('Select institute') }}</option>
                            @foreach ($institutes as $inst)
                                <option value="{{ route('administration.institute.representatives.create', $inst) }}">{{ $inst->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span class="text-muted small">{{ __('Next: branch, profile, and account details (multi-step).') }}</span>
                        <button type="button" class="btn btn-primary" id="btn-institute-continue" disabled>
                            {{ __('Continue') }}
                            <i class="ti ti-arrow-right ms-1"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_script')
<script>
(function () {
    function bind() {
        if (typeof window.jQuery === 'undefined') {
            return false;
        }
        var $ = window.jQuery;
        var $sel = $('#institute_id');
        var $btn = $('#btn-institute-continue');
        if (!$sel.length || !$btn.length) {
            return true;
        }
        function sync() {
            var v = $sel.val();
            $btn.prop('disabled', !v);
        }
        $sel.off('.instituteEntry').on('change.instituteEntry', sync);
        $(document).off('.instituteEntry').on('select2:select.instituteEntry select2:clear.instituteEntry', '#institute_id', sync);
        sync();
        $btn.off('click').on('click', function () {
            var v = $sel.val();
            if (v) {
                window.location.href = v;
            }
        });
        return true;
    }

    function boot() {
        if (!bind()) {
            setTimeout(boot, 30);
            return;
        }
        setTimeout(function () {
            var $ = window.jQuery;
            var $sel = $('#institute_id');
            if ($sel.length) {
                $sel.trigger('change');
            }
        }, 150);
        setTimeout(function () {
            var $ = window.jQuery;
            var $sel = $('#institute_id');
            if ($sel.length) {
                $('#btn-institute-continue').prop('disabled', !$sel.val());
            }
        }, 500);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
</script>
@endsection
