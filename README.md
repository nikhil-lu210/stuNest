# Laravel 13 Starter Kit

A production-ready Laravel **13** application template with common packages for administration UIs, permissions, media, alerts, and developer tooling. It targets **PHP 8.5+** and uses the modern Laravel application structure (`bootstrap/app.php`, streamlined `config/app.php`, etc.).

**Repository:** [github.com/nikhil-lu210/laravel13starterKit](https://github.com/nikhil-lu210/laravel13starterKit)

---

## Requirements

| Requirement | Version / notes |
|-------------|-----------------|
| **PHP** | `^8.5` |
| **Composer** | 2.x |
| **Node.js** | 18+ recommended (for Vite 8) |
| **Database** | MySQL / MariaDB (default), or configure another driver in `.env` |
| **PHP extensions** | Typical Laravel set: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, **`zip`** (recommended for Composer on Windows) |

---

## Included packages

### Production (`composer.json`)

| Package | Purpose |
|---------|---------|
| **laravel/framework** | Laravel 13 |
| **laravel/sanctum** | API token authentication |
| **laravel/tinker** | REPL (`php artisan tinker`) |
| **laravel/ui** | Classic auth scaffolding (login, register, password reset) |
| **browner12/helpers** | Helper loader + optional packaged helpers (see note below) |
| **dyrynda/laravel-cascade-soft-deletes** | Cascade soft deletes for Eloquent |
| **realrashid/sweet-alert** | Flash toasts / SweetAlert in Blade |
| **spatie/laravel-permission** | Roles & permissions |
| **spatie/laravel-medialibrary** | Media attachments & conversions |
| **guzzlehttp/guzzle** | HTTP client |

### Development

| Package | Purpose |
|---------|---------|
| **barryvdh/laravel-debugbar** | Request debug bar (Fruitcake-maintained v4, Laravel 13–compatible) |
| **barryvdh/laravel-ide-helper** | IDE autocompletion for facades/models |
| **laravel/pint** | Code style |
| **laravel/sail** | Docker dev environment (optional) |
| **nunomaduro/collision** | CLI error reporting |
| **phpunit/phpunit** | Tests |
| **spatie/laravel-ignition** | Error page / debugging |

### Front-end (`package.json`)

- **Vite 8**, **laravel-vite-plugin** 3.x  
- **Bootstrap 5**, **Sass**, **Axios**, **Popper**

---

## `browner12/helpers` (path package)

The upstream Packagist release currently declares compatibility only up to **Laravel 12** (`illuminate/*` ^12). This starter ships a **local path copy** under `packages/browner12/helpers` with `composer.json` adjusted for **`illuminate/*` ^13** so it installs cleanly with Laravel 13.

- Keep the `packages/browner12/helpers` directory **in version control** when you clone the project.
- When the maintainer publishes an official **Laravel 13**-compatible release, you can remove the path repository from `composer.json` and require the package from Packagist instead.

Configuration lives in `config/helpers.php` (package helpers, custom helper filenames, `app/Helpers` directory, etc.).

---

## Installation

### 1. Clone and install PHP dependencies

```bash
git clone https://github.com/nikhil-lu210/laravel13starterKit.git
cd laravel13starterKit
composer install
```

Use **PHP 8.5** in your CLI (e.g. Laragon: menu **PHP → 8.5.x**). On Windows, ensure the **`zip`** PHP extension is enabled so Composer can extract packages.

### 2. Environment

```bash
copy .env.example .env   # Windows
# cp .env.example .env   # Linux / macOS
php artisan key:generate
```

Edit `.env`:

- **`APP_NAME`** — e.g. `Laravel 13 Starter Kit`
- **`APP_URL`** — match your virtual host, e.g. `http://laravel13starterkit.test`
- **`DB_*`** — database name, user, password

### 3. Database

```bash
php artisan migrate:fresh --seed
```

Adjust or add seeders under `database/seeders` as needed.

### 4. Storage link (uploads / public disk)

```bash
php artisan storage:link
```

### 5. Front-end assets

```bash
npm install
npm run dev
# production: npm run build
```

### 6. Optional: IDE helpers (dev)

```bash
php artisan ide-helper:generate
php artisan ide-helper:models
```

---

## Local development (Laragon example)

1. Place the project under `www` (e.g. `C:\laragon\www\laravel13starterKit`).
2. Create a virtual host pointing the **document root** to **`public`** (e.g. `laravel13starterkit.test` → `...\laravel13starterKit\public`).
3. Set **`APP_URL`** in `.env` to that URL.
4. Use **PHP 8.5** for both web and CLI.

---

## Common Artisan commands

| Command | Description |
|---------|-------------|
| `php artisan serve` | Built-in dev server |
| `php artisan route:list` | List routes |
| `php artisan optimize:clear` | Clear config, cache, views, routes |
| `./vendor/bin/pint` | Fix code style |

---

## Testing

```bash
php artisan test
# or
./vendor/bin/phpunit
```

PHPUnit is configured in `phpunit.xml` (in-memory SQLite for tests unless you change it).

---

## Project structure highlights

- **`bootstrap/app.php`** — Routing (web, API, `health`), broadcasting, middleware (CSRF, Sweet Alert, Spatie permission aliases).
- **`bootstrap/providers.php`** — Application service providers.
- **`routes/`** — Includes `web.php`, `api.php`, and modular administration routes under `routes/administration/`.
- **`app/Helpers/`** — Custom helper files loaded by `browner12/helpers` when configured in `config/helpers.php`.

---

## License

This project is open-sourced software licensed under the **MIT license** (see `LICENSE` if present).

---

## Credits

- [Laravel](https://laravel.com)
- [Laravel UI](https://github.com/laravel/ui), [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Spatie](https://spatie.be/open-source) — Permission, Media Library
- [Real Rashid — Sweet Alert](https://github.com/realrashid/sweet-alert)
- [browner12/helpers](https://github.com/browner12/helpers)
- [Michael Dyrynda — Cascade Soft Deletes](https://github.com/michaeldyrynda/laravel-cascade-soft-deletes)
