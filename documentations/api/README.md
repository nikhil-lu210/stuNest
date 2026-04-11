# API notes

## Laravel `routes/api.php`

- Includes files under `routes/api/` (e.g. `auth.php` for Sanctum `/api/user`).
- These routes use the `api` middleware group (no session / CSRF by default).

## Session-authenticated JSON (admin UI)

Geography lookup endpoints for Select2 are **not** on `/api/*`. They are registered under **web** middleware with `auth`:

- `GET /administration/settings/geography/api/countries`
- `GET /administration/settings/geography/api/cities?country_id=`
- `GET /administration/settings/geography/api/areas?city_id=`

Controller: `App\Http\Controllers\Api\Geography\GeographyLookupController`

Route names: `administration.settings.geography.api.countries`, `.cities`, `.areas`
