@extends('administration.user-management.student.show')

@section('profile_content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header header-elements">
                    <h5 class="mb-0">{{ __('Applications') }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0 text-muted">{{ __('Property applications for this student will appear here.') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
