# Portfolio CMS

A bilingual (English / Indonesian) personal portfolio with a Filament admin panel. Laravel 13, PHP 8.5, PostgreSQL, Blade + Livewire on the public side, Tailwind CSS 4.

## Local setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Point the database at PostgreSQL in `.env`, then:

```bash
php artisan migrate
npm run build
composer run dev     # or: php artisan serve
```

Set `ADMIN_EMAIL` and `ADMIN_PASSWORD` before seeding to create the single administrator account:

```bash
php artisan db:seed
```

The admin panel lives at `/admin`. The public site redirects `/` to the default locale (`/en`).

## Content model

`profiles`, `projects`, `posts`, `skills`, `experiences`, `educations`, `contact_messages`, `settings`.

Translatable text is stored in paired columns (`title_en` / `title_id`). When the Indonesian column is empty, the English value is shown — `HasLocalizedContent::localized()` does the fallback. English is the default and the fallback locale.

Projects and posts carry a status of `draft`, `posted` or `archived`. Only `posted` content is reachable on the public site; drafts are excluded in the query, so a draft detail URL 404s rather than leaking.

Body copy is Markdown, rendered server-side by `MarkdownRenderer` with `html_input: strip` and `allow_unsafe_links: false`, so raw HTML and `javascript:` URLs are removed before output.

## Tests

```bash
php artisan test --compact
```

Tests run against PostgreSQL and use `RefreshDatabase`. `phpunit.xml` holds the local connection details.

## Deployment (Dokploy + Nginx + PostgreSQL)

1. Create a PostgreSQL service and point `DB_*` at it.
2. Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` to the public URL, and generate `APP_KEY`.
3. Set `FILESYSTEM_DISK=public` and mount a **persistent volume at `storage/app/public`** — uploaded media lives there, and it is not in the image.
4. Release commands:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm ci && npm run build
```

Run `npm run build` at image/build time; its output is `public/build`.

`APP_LOCALE=en` and `APP_FALLBACK_LOCALE=en` should stay as-is. Mail defaults to the `log` driver; configure `MAIL_*` if you want contact-form notifications (they are not sent by default).
