<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Verification code') }}</title>
</head>
<body style="font-family: system-ui, sans-serif; line-height: 1.5; color: #111827; margin: 0; padding: 24px; background: #f9fafb;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 32rem; margin: 0 auto; background: #fff; border-radius: 8px; border: 1px solid #e5e7eb; padding: 32px;">
        <tr>
            <td>
                <p style="margin: 0 0 16px; font-size: 16px;">{{ __('Your verification code is:') }}</p>
                <p style="margin: 0; font-size: 28px; font-weight: 700; letter-spacing: 0.25em; font-family: ui-monospace, monospace;">{{ $code }}</p>
                <p style="margin: 24px 0 0; font-size: 14px; color: #6b7280;">{{ __('If you did not try to register, you can ignore this email.') }}</p>
            </td>
        </tr>
    </table>
</body>
</html>
