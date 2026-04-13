@extends('administration.user-management.landlord.show')

@section('profile_content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <small class="card-text text-uppercase">{{ __('Basic information') }}</small>
                    <dl class="row mt-3 mb-1">
                        <dt class="col-sm-4 mb-2 fw-medium text-nowrap">
                            <i class="ti ti-hash text-heading"></i>
                            <span class="fw-medium mx-2 text-heading">{{ __('User ID') }}</span>
                        </dt>
                        <dd class="col-sm-8"><span>{{ $user->userid }}</span></dd>
                    </dl>
                    <dl class="row mb-1">
                        <dt class="col-sm-4 mb-2 fw-medium text-nowrap">
                            <i class="ti ti-user text-heading"></i>
                            <span class="fw-medium mx-2 text-heading">{{ __('Full name') }}</span>
                        </dt>
                        <dd class="col-sm-8"><span>{{ $user->name }}</span></dd>
                    </dl>
                    <dl class="row mb-1">
                        <dt class="col-sm-4 fw-medium text-nowrap">
                            <i class="ti ti-flag text-heading"></i>
                            <span class="fw-medium mx-2 text-heading">{{ __('Account status') }}</span>
                        </dt>
                        <dd class="col-sm-8">
                            @switch($user->account_status ?? \App\Models\User::ACCOUNT_STATUS_ACTIVE)
                                @case(\App\Models\User::ACCOUNT_STATUS_ACTIVE)
                                    <span class="badge bg-label-success">{{ __('Active') }}</span>
                                    @break
                                @case(\App\Models\User::ACCOUNT_STATUS_PENDING)
                                    <span class="badge bg-label-warning">{{ __('Pending') }}</span>
                                    @break
                                @case(\App\Models\User::ACCOUNT_STATUS_REJECTED)
                                    <span class="badge bg-label-danger">{{ __('Rejected') }}</span>
                                    @break
                                @default
                                    <span class="badge bg-label-secondary">{{ ucfirst((string) $user->account_status) }}</span>
                            @endswitch
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <small class="card-text text-uppercase">{{ __('Contact') }}</small>
                    <dl class="row mt-3 mb-1">
                        <dt class="col-sm-4 mb-2 fw-medium text-nowrap">
                            <i class="ti ti-phone-call text-heading"></i>
                            <span class="fw-medium mx-2 text-heading">{{ __('Phone') }}</span>
                        </dt>
                        <dd class="col-sm-8">
                            @if ($user->phone)
                                <a href="tel:{{ preg_replace('/\s+/', '', $user->phone) }}" class="text-primary">{{ $user->phone }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </dd>
                    </dl>
                    <dl class="row mb-1">
                        <dt class="col-sm-4 mb-2 fw-medium text-nowrap">
                            <i class="ti ti-brand-whatsapp text-heading"></i>
                            <span class="fw-medium mx-2 text-heading">{{ __('WhatsApp') }}</span>
                        </dt>
                        <dd class="col-sm-8">
                            @if ($user->whatsapp)
                                <span>{{ $user->whatsapp }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </dd>
                    </dl>
                    <dl class="row mb-1">
                        <dt class="col-sm-4 fw-medium text-nowrap">
                            <i class="ti ti-mail text-heading"></i>
                            <span class="fw-medium mx-2 text-heading">{{ __('Email') }}</span>
                        </dt>
                        <dd class="col-sm-8">
                            <a href="mailto:{{ $user->email }}" class="text-primary">{{ $user->email }}</a>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <small class="card-text text-uppercase">{{ __('Billing / Home address') }}</small>
                    <p class="mt-3 mb-0 text-body">
                        @if ($user->billing_address)
                            {!! nl2br(e($user->billing_address)) !!}
                        @else
                            —
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
