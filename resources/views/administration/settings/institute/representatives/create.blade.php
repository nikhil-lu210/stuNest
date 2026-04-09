@extends('layouts.administration.app')

@section('page_title', __('Add Institute Representative'))

@section('css_links')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('page_name')
    <b class="text-uppercase">{{ __('Add Institute Representative') }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Institutions') }}</li>
    <li class="breadcrumb-item"><a href="{{ route('administration.settings.institute.show', $institute) }}">{{ $institute->name }}</a></li>
    <li class="breadcrumb-item active">{{ __('Add representative') }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header header-elements">
                <h5 class="mb-0">{{ __('Add representative for') }} {{ $institute->name }}</h5>
                <div class="card-header-elements ms-auto">
                    <a href="{{ route('administration.settings.institute.show', $institute) }}" class="btn btn-sm btn-primary">
                        <span class="tf-icon ti ti-arrow-left ti-xs me-1"></span>
                        {{ __('Back') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-primary mb-4" role="alert">
                    {{ __('Email must end with') }} <code>{{ $institute->email_code }}</code>.
                </div>
                <form action="{{ route('administration.settings.institute.representatives.store', $institute) }}" method="post" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label class="form-label">{{ __('Branch') }} <strong class="text-danger">*</strong></label>
                            <select name="institute_location_id" class="form-select @error('institute_location_id') is-invalid @enderror" required>
                                <option value="" disabled {{ old('institute_location_id') ? '' : 'selected' }}>{{ __('Select branch') }}</option>
                                @foreach ($institute->locations as $loc)
                                    <option value="{{ $loc->id }}" @selected(old('institute_location_id') == $loc->id)>{{ $loc->name }}</option>
                                @endforeach
                            </select>
                            @error('institute_location_id')
                                <b class="text-danger"><i class="ti ti-info-circle me-1"></i>{{ $message }}</b>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex align-items-start align-items-sm-center gap-4 mb-3">
                        <img src="https://fakeimg.pl/100/dddddd/?text=Upload-Image" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
                        <div class="button-wrapper">
                            <label for="upload" class="btn btn-primary me-2 mb-3" tabindex="0">
                                <span class="d-none d-sm-block">{{ __('Upload Avatar') }}</span>
                                <input type="file" name="avatar" id="upload" class="account-file-input" hidden accept="image/png, image/jpeg, image/jpg"/>
                            </label>
                            <button type="button" class="btn btn-label-secondary account-image-reset mb-3">{{ __('Reset') }}</button>
                            <div class="text-muted small">{{ __('Allowed JPG, JPEG or PNG. Max size of 2MB') }}</div>
                            @error('avatar')
                                <b class="text-danger"><i class="ti ti-info-circle me-1"></i>{{ $message }}</b>
                            @enderror
                        </div>
                    </div>
                    <hr class="my-3" />

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="userid">{{ __('User ID') }} <strong class="text-danger">*</strong></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text" style="padding-right: 2px;">UID</span>
                                <input type="text" id="userid" name="userid" class="form-control @error('userid') is-invalid @enderror" value="{{ old('userid', date('Ymd')) }}" required />
                            </div>
                            @error('userid')
                                <b class="text-danger"><i class="ti ti-info-circle me-1"></i>{{ $message }}</b>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label class="form-label" for="first_name">{{ __('First Name') }} <strong class="text-danger">*</strong></label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" class="form-control @error('first_name') is-invalid @enderror" required />
                            @error('first_name')
                                <b class="text-danger"><i class="ti ti-info-circle me-1"></i>{{ $message }}</b>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label" for="middle_name">{{ __('Middle Name') }}</label>
                            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}" class="form-control @error('middle_name') is-invalid @enderror" />
                            @error('middle_name')
                                <b class="text-danger"><i class="ti ti-info-circle me-1"></i>{{ $message }}</b>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label" for="last_name">{{ __('Last Name') }} <strong class="text-danger">*</strong></label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" class="form-control @error('last_name') is-invalid @enderror" required />
                            @error('last_name')
                                <b class="text-danger"><i class="ti ti-info-circle me-1"></i>{{ $message }}</b>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="email">{{ __('Email') }} <strong class="text-danger">*</strong></label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required />
                            @error('email')
                                <b class="text-danger"><i class="ti ti-info-circle me-1"></i>{{ $message }}</b>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-3 form-password-toggle">
                            <label class="form-label" for="password">{{ __('Password') }} <strong class="text-danger">*</strong></label>
                            <div class="input-group input-group-merge">
                                <input type="password" minlength="8" id="password" name="password" value="{{ old('password') }}" class="form-control @error('password') is-invalid @enderror" required />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                            @error('password')
                                <b class="text-danger"><i class="ti ti-info-circle me-1"></i>{{ $message }}</b>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-3 form-password-toggle">
                            <label class="form-label" for="password_confirmation">{{ __('Password Confirmation') }} <strong class="text-danger">*</strong></label>
                            <div class="input-group input-group-merge">
                                <input type="password" minlength="8" id="password_confirmation" name="password_confirmation" value="{{ old('password_confirmation') }}" class="form-control" required />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2 float-end">
                        <button type="submit" class="btn btn-primary">{{ __('Create representative') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script_links')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('custom_script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const accountUserImage = document.getElementById('uploadedAvatar');
    const fileInput = document.querySelector('.account-file-input');
    const resetFileInput = document.querySelector('.account-image-reset');
    if (accountUserImage && fileInput && resetFileInput) {
        const resetImage = accountUserImage.getAttribute('src');
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
</script>
@endsection
