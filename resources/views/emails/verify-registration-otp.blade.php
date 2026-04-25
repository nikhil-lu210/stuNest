<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ __('Verification') }}</title>
</head>
<body style="font-family: system-ui, sans-serif; line-height: 1.5; color: #111827; padding: 24px;">
    <h1 style="font-size: 1.25rem; margin: 0 0 0.5rem;">{{ __('Verify your e-mail') }}</h1>
    <p style="margin: 0 0 1rem;">{{ __('Use this 6-digit code to continue registration:') }}</p>
    <p style="font-size: 1.75rem; font-weight: 700; letter-spacing: 0.35em; margin: 0;">{{ $code }}</p>
    <p style="margin: 1.5rem 0 0; font-size: 0.875rem; color: #6B7280;">{{ __('If you did not start registration, you can ignore this e-mail.') }}</p>
</body>
</html>
