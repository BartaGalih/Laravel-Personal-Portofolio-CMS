# Personal Portfolio CMS Design

**Date:** 2026-11-24
**Status:** Approved design

## Goal

Build a clean Laravel replacement for the existing CodeIgniter personal portfolio CMS. The old repository is a reference only; content starts clean and is entered manually.

The application serves a public bilingual portfolio and blog while giving one administrator an easy CMS for managing content, media, SEO, and contact messages.

## Recommended stack

- Laravel 12+
- Blade for public rendering
- Livewire 3 for interactive public and admin components
- Filament 4 for the CMS panel
- PostgreSQL in development and production
- Tailwind CSS and Vite
- Laravel filesystem with server storage on a persistent Dokploy volume
- Server-side Markdown rendering with sanitized HTML output
- Pest for tests
- Nginx and PHP-FPM on Dokploy

The application remains a Laravel monolith. A separate Vue, React, Next.js, or API frontend is intentionally excluded because it would add deployment and maintenance cost without serving the current requirements.

## Users and authentication

There is one administrator account. They can edit their name, email, avatar/profile details, and password through the Filament panel. Multi-user roles, invitations, and permissions are out of scope.

## Content and data model

Use separate tables and Filament resources for the following:

- `profile`: public identity, biography, photo, contact information, and social links
- `projects`: localized title, slug, summary, Markdown content, cover image, project URL, repository URL, ordering, and status
- `skills`: name, category, proficiency, icon, ordering, and visibility
- `experiences`: localized company, role, dates, Markdown description, ordering, and visibility
- `posts`: localized title, slug, excerpt, Markdown content, cover image, publication date, and status
- `media`: uploaded files and metadata when media organization requires a dedicated record
- `contact_messages`: sender name, email, message, read state, and timestamps
- `settings`: site title, branding, default SEO values, and social/analytics settings
- `users`: the single administrator account

Structured bilingual fields use paired columns such as `title_en`, `title_id`, `content_en`, and `content_id`. This is appropriate for a small single-owner site and avoids the extra complexity of a translation table.

## Content lifecycle

Projects and posts support three states:

- `draft`: editable and never shown publicly
- `posted`: visible on public pages
- `archived`: retained in the CMS but excluded from public pages

Public queries must filter to `posted`. Status changes happen from Filament. Scheduling is not part of the initial scope; publication uses the explicit status and publication date fields.

## Localization

English is the default language. Public routes use a locale prefix:

- `/en`
- `/id`
- `/en/projects`
- `/id/projects`

The language switcher preserves the current page where a matching localized route exists. Editors manually enter English and Indonesian values in separate Filament tabs or fields. If an Indonesian value is empty, the public site falls back to English. There is no automatic translation service in the initial release.

## Public application

Blade renders the public pages and Livewire supplies only the interaction that needs it. Initial pages are:

- Home
- About/profile
- Projects listing
- Project detail
- Blog listing
- Blog post detail
- Experience and skills
- Contact
- Custom 404 page

The public site includes responsive styling, accessible navigation and forms, language switching, SEO metadata, Open Graph metadata, sitemap generation, and robots configuration.

## Admin application

Filament provides the `/admin` panel. Resources cover profile, projects, posts, skills, experiences, media, contact messages, settings, and the administrator account.

The admin UI must support:

- CRUD forms and list views
- English/Indonesian localized fields
- Draft, posted, and archived filtering
- Markdown editing with a preview
- Image/file uploads
- Slugs and publication dates
- SEO title, description, and social image fields
- Contact-message read/unread state
- Administrator profile and password editing

## Markdown and media

Editors write Markdown manually. A Livewire editor provides a preview of the rendered result. Markdown is stored as authored text in PostgreSQL, rendered on the server, and sanitized before being inserted into HTML. Unsafe raw HTML and unsafe links must not be allowed to become executable output.

Uploads use Laravel's public filesystem disk and are exposed through the storage link. Production requires a persistent Dokploy volume mounted for application storage. Server storage is intentionally preferred over object storage for the initial release; backups are required because uploaded media is not disposable.

## Contact messages

The contact form validates name, email, and message at the public boundary, stores submissions in PostgreSQL, and exposes them in the Filament inbox. Email notification is optional and should only be added if the deployment's mail configuration is available; storing the message is the required behavior.

## Deployment

Dokploy runs the Laravel application behind Nginx with PHP-FPM and PostgreSQL. Deployment must run configuration caching, database migrations, and the storage link command. The `storage/app/public` path must use a persistent volume. Redis and queues are excluded until a concrete workload requires them.

## Testing

Use focused feature tests for:

- Public pages only exposing posted content
- Draft and archived content remaining private
- English default locale and Indonesian content
- English fallback for empty Indonesian fields
- Contact form validation and persistence
- Admin authentication and editable profile/password behavior
- Markdown rendering and unsafe HTML/link handling
- Slug and status transitions

Use PostgreSQL-compatible schema and queries in local testing to avoid production-only behavior.

## Explicitly out of scope

- Automatic translation or AI translation
- Multiple administrators, teams, roles, or permissions
- Separate frontend application
- Public API
- Redis, queues, or realtime features without a demonstrated need
- S3-compatible media storage
- Content import from the old CodeIgniter database
- Scheduled publishing in the first release
