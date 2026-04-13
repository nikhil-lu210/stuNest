<x-mail::message>
# {{ __('Welcome') }}

{{ __('Hello :name,', ['name' => $user->first_name]) }}

{{ __('An administrator has created an account for you on :app.', ['app' => config('app.name')]) }}

{{ __('You can sign in with your email address and the password below:') }}

<x-mail::panel>
{{ $plainPassword }}
</x-mail::panel>

{{ __('Please change your password after your first login.') }}

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
</x-mail::message>
