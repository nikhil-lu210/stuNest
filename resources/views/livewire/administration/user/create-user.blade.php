<div>
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card mb-4">
                <div class="card-header header-elements">
                    <h5 class="mb-0">{{ __('Create administration user') }}</h5>
                    <div class="card-header-elements ms-auto">
                        <a href="{{ route('administration.users.index') }}" class="btn btn-sm btn-label-secondary">
                            <span class="tf-icon ti ti-arrow-left ti-xs me-1"></span>
                            {{ __('All Users') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <p class="text-muted mb-4">
                        {{ __('Create staff accounts for this Vuexy dashboard. Student, landlord, agent, and institute accounts are created from their own workflows.') }}
                    </p>

                    <form wire:submit.prevent="save">
                        <h6 class="mb-3 text-muted text-uppercase small">{{ __('Access') }}</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="selected_admin_role">{{ __('Administration role') }} <span class="text-danger">*</span></label>
                                <select
                                    id="selected_admin_role"
                                    wire:model="selected_admin_role"
                                    class="form-select @error('selected_admin_role') is-invalid @enderror"
                                >
                                    <option value="">{{ __('Select role') }}</option>
                                    @foreach ($available_roles as $r)
                                        <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('selected_admin_role')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h6 class="mb-3 text-muted text-uppercase small">{{ __('Personal details') }}</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="first_name">{{ __('First name') }} <span class="text-danger">*</span></label>
                                <input type="text" id="first_name" wire:model.blur="first_name" class="form-control @error('first_name') is-invalid @enderror" autocomplete="given-name">
                                @error('first_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="last_name">{{ __('Last name') }} <span class="text-danger">*</span></label>
                                <input type="text" id="last_name" wire:model.blur="last_name" class="form-control @error('last_name') is-invalid @enderror" autocomplete="family-name">
                                @error('last_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="email">{{ __('Email') }} <span class="text-danger">*</span></label>
                                <input type="email" id="email" wire:model.blur="email" class="form-control @error('email') is-invalid @enderror" autocomplete="email">
                                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="phone">{{ __('Phone') }}</label>
                                <input type="text" id="phone" wire:model.blur="phone" class="form-control @error('phone') is-invalid @enderror" autocomplete="tel">
                                @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-2">
                            <a href="{{ route('administration.users.index') }}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading.remove wire:target="save">
                                    <i class="ti ti-device-floppy ti-xs me-1"></i>{{ __('Create user') }}
                                </span>
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                <span wire:loading wire:target="save">{{ __('Saving…') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
