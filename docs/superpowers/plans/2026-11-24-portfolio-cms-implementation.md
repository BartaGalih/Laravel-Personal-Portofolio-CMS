# Personal Portfolio CMS Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a clean bilingual personal portfolio CMS with one editable administrator, a Blade/Livewire public site, Filament administration, Markdown content, media uploads, contact messages, education history, and Dokploy-compatible PostgreSQL deployment.

**Architecture:** Use one Laravel application. Filament owns the `/admin` CMS, Eloquent models and PostgreSQL store structured content, and Blade/Livewire render the public `/en` and `/id` pages. Localized fields use paired English/Indonesian columns with English fallback; only `posted` content is public.

**Tech Stack:** Laravel 13, PHP 8.5, Blade, Livewire 3, Filament 4, PostgreSQL, Tailwind CSS 4, Vite 8, Laravel filesystem, server-side Markdown renderer with HTML sanitization, Pest 5, Nginx/PHP-FPM on Dokploy.

## Global Constraints

- Start with a clean database; use the old CodeIgniter repository only as a feature/content reference.
- English is the default locale; Indonesian is the secondary locale.
- Editors enter translations manually; no automatic translation service in the first release.
- Projects and posts use exactly `draft`, `posted`, and `archived` states.
- Public queries expose only `posted` records.
- Empty Indonesian values fall back to English.
- There is one editable administrator; do not add multi-user roles or permissions.
- Store uploads on Laravel’s public disk backed by a persistent Dokploy volume.
- Use PostgreSQL-compatible schemas and queries in development and production.
- Do not add Redis, queues, S3 storage, a separate frontend, a public API, or scheduled publishing without a demonstrated requirement.
- Validate all public input and sanitize rendered Markdown before output.
- Use existing dependencies first; confirm package versions before relying on package APIs.

---

## File map

Create or modify only the following areas unless the framework generator requires related support files:

- `composer.json`, `composer.lock`: Filament, Livewire, and Markdown/sanitization dependencies.
- `config/filesystems.php`, `config/app.php`, `config/services.php`: public storage and locale defaults.
- `database/migrations/`: tables for profile, projects, posts, skills, experiences, education, contact messages, media/settings as needed.
- `app/Enums/ContentStatus.php`: shared draft/posted/archived enum.
- `app/Models/`: Eloquent models, casts, localized accessors, relationships, and factories.
- `app/Filament/`: admin panel provider, resources, pages, forms, tables, and Markdown preview component.
- `app/Http/Controllers/`, `app/Http/Requests/`, `app/Livewire/`: public pages and contact form behavior.
- `app/Support/`: only small shared services that are required, such as safe Markdown rendering or locale-aware content access.
- `routes/web.php`: localized public routes and admin-independent endpoints.
- `resources/views/layouts/`, `resources/views/components/`, `resources/views/pages/`, `resources/views/livewire/`: public UI.
- `resources/css/app.css`, `resources/js/app.js`: Tailwind/Vite entrypoints.
- `tests/Feature/`, `tests/Unit/`: public visibility, localization, Markdown safety, contact, authentication, and status tests.
- `database/seeders/DatabaseSeeder.php`: one local administrator and minimal development data only if useful.
- `Dockerfile`, `docker/`, or Dokploy deployment files only after inspecting the existing deployment convention; do not add a custom container if Dokploy’s Laravel service already supplies it.
- `docs/deployment.md` only if deployment steps are not already represented in project configuration and the user wants repository documentation.

## Task 1: Confirm framework baseline and install only required packages

**Files:**
- Modify: `composer.json`, `composer.lock`
- Modify: `package.json` only if an installed frontend package is actually required
- Inspect: `AGENTS.md`, `CLAUDE.md`, `.env.example`

**Interfaces:**
- Produces installed versions for Filament, Livewire, Markdown rendering, and sanitization that later tasks must use.

- [x] **Step 1: Verify current runtime and direct dependencies**

Run:

```powershell
php -v
composer -V
composer show --direct
php artisan list
```

Expected: PHP, Composer, Laravel Artisan, and the currently installed direct dependencies are available.

- [x] **Step 2: Search package documentation for version-specific installation and APIs**

Use the project’s Laravel Boost documentation search before choosing package APIs. Confirm the Laravel 13-compatible Filament and Livewire installation commands and the selected Markdown/sanitization APIs.

- [x] **Step 3: Install the smallest dependency set**

Install Filament and Livewire using their documented Laravel 13-compatible commands. Add a Markdown parser and sanitizer only if Laravel/framework does not already supply the required behavior. Do not add a media library, Redis client, queue package, or translation package.

- [x] **Step 4: Run the baseline suite and frontend build**

Run:

```powershell
php artisan test --compact
npm run build
```

Expected: the existing suite and Vite build pass before application changes begin.

- [x] **Step 5: Commit the dependency setup**

```powershell
git add composer.json composer.lock package.json package-lock.json
 git commit -m "chore: install portfolio cms dependencies"
```

If this workspace remains outside Git, record the changed files instead of attempting a commit.

## Task 2: Create shared content primitives and database schema

**Files:**
- Create: `app/Enums/ContentStatus.php`
- Create: `database/migrations/*_create_profiles_table.php`
- Create: `database/migrations/*_create_projects_table.php`
- Create: `database/migrations/*_create_posts_table.php`
- Create: `database/migrations/*_create_skills_table.php`
- Create: `database/migrations/*_create_experiences_table.php`
- Create: `database/migrations/*_create_educations_table.php`
- Create: `database/migrations/*_create_contact_messages_table.php`
- Create: `database/migrations/*_create_settings_table.php`
- Modify: `database/migrations/0001_01_01_000000_create_users_table.php` only if the existing user schema needs profile fields

**Interfaces:**
- Produces PostgreSQL tables and `ContentStatus` values `Draft`, `Posted`, and `Archived` for models, resources, and tests.

- [x] **Step 1: Write failing schema tests**

Create a feature test that migrates the test database and asserts the required tables, status values, localized columns, unique slugs, ordering columns, and contact-message read state exist.

- [x] **Step 2: Run the schema tests to verify failure**

```powershell
php artisan test --compact tests/Feature/PortfolioSchemaTest.php
```

Expected: FAIL because the portfolio tables and enum do not exist.

- [x] **Step 3: Implement migrations and enum**

Use PostgreSQL-safe Laravel migrations. Use string-backed enum values for database portability, with indexes on `status`, `slug`, `published_at`, `locale-relevant` lookup fields, and ordering fields. Use nullable Indonesian fields so English fallback can be detected. Give education records localized institution, degree, field, and description fields, plus dates, location, ordering, and visibility/status.

- [x] **Step 4: Run the schema tests**

```powershell
php artisan migrate:fresh --env=testing --force
php artisan test --compact tests/Feature/PortfolioSchemaTest.php
```

Expected: PASS against the configured PostgreSQL test database.

- [x] **Step 5: Commit the schema**

```powershell
git add app/Enums database/migrations tests/Feature/PortfolioSchemaTest.php
 git commit -m "feat: add portfolio cms schema"
```

## Task 3: Add Eloquent models, factories, and locale fallback

**Files:**
- Create: `app/Models/Profile.php`
- Create: `app/Models/Project.php`
- Create: `app/Models/Post.php`
- Create: `app/Models/Skill.php`
- Create: `app/Models/Experience.php`
- Create: `app/Models/Education.php`
- Create: `app/Models/ContactMessage.php`
- Create: `app/Models/Setting.php`
- Create: `database/factories/*Factory.php` for content models
- Create: `app/Support/LocalizedContent.php` only if shared locale access cannot remain a small model method
- Modify: `app/Models/User.php` only for the editable admin profile contract

**Interfaces:**
- Produces `Project::posted()`, `Post::posted()`, and equivalent query scopes.
- Produces locale-aware accessors/methods that return the requested language and fall back from `id` to `en`.
- Produces factories used by all later feature tests.

- [x] **Step 1: Write failing model tests**

Cover:

```php
it('returns only posted projects and posts', function (): void {
    Project::factory()->create(['status' => ContentStatus::Draft]);
    $posted = Project::factory()->create(['status' => ContentStatus::Posted]);

    expect(Project::posted()->pluck('id'))->toContain($posted->id);
});

it('falls back to english when an indonesian field is empty', function (): void {
    $project = Project::factory()->create([
        'title_en' => 'English title',
        'title_id' => null,
    ]);

    expect($project->localized('title', 'id'))->toBe('English title');
});
```

Add equivalent education and experience localized-field coverage.

- [x] **Step 2: Run the model tests to verify failure**

```powershell
php artisan test --compact tests/Feature/ModelsTest.php
```

Expected: FAIL because models, factories, scopes, and localized accessors do not exist.

- [x] **Step 3: Implement models and factories**

Use explicit casts for `ContentStatus`, dates, booleans, and JSON settings/social fields. Keep `posted()` as a shared local scope on content models. Avoid a translation package; implement one small locale-aware method or trait only if the same behavior is needed in multiple models.

- [x] **Step 4: Run the model tests and formatter**

```powershell
php artisan test --compact tests/Feature/ModelsTest.php
vendor/bin/pint --dirty --format agent
```

Expected: PASS and no unformatted PHP files.

- [x] **Step 5: Commit the model layer**

```powershell
git add app/Models app/Support database/factories tests/Feature/ModelsTest.php
 git commit -m "feat: add portfolio content models"
```

## Task 4: Configure Filament and build the single-admin CMS

**Files:**
- Create/modify: `app/Providers/Filament/AdminPanelProvider.php`
- Create: `app/Filament/Resources/ProfileResource.php`
- Create: `app/Filament/Resources/ProjectResource.php`
- Create: `app/Filament/Resources/PostResource.php`
- Create: `app/Filament/Resources/SkillResource.php`
- Create: `app/Filament/Resources/ExperienceResource.php`
- Create: `app/Filament/Resources/EducationResource.php`
- Create: `app/Filament/Resources/ContactMessageResource.php`
- Create: `app/Filament/Resources/SettingResource.php`
- Create/modify: `app/Filament/Pages/*` for administrator profile/settings where Filament’s built-in account page is insufficient
- Modify: `app/Models/User.php`
- Modify: `database/seeders/DatabaseSeeder.php`

**Interfaces:**
- Produces a working `/admin` login and CRUD resources for all content types.
- Produces localized form fields for English/Indonesian content.
- Produces status filters and safe Markdown editing/preview.

- [x] **Step 1: Write failing admin access tests**

Cover administrator login, unauthorized guest redirect, project CRUD authorization, status filtering, education CRUD, and contact-message read-state updates. Use the existing user factory and content factories.

- [x] **Step 2: Run admin tests to verify failure**

```powershell
php artisan test --compact tests/Feature/AdminAccessTest.php
```

Expected: FAIL because Filament’s panel and resources are not configured.

- [x] **Step 3: Configure the panel and resources**

Configure one admin panel. Use Filament forms with English/Indonesian field groups, status select constrained to the shared enum, slug fields, publication dates, ordering, image/file upload fields targeting the public disk, and Markdown editor fields with a preview action. Exclude draft and archived content from public visibility through model queries rather than UI-only filtering.

Expose education fields: institution, degree/program, field of study, localized description, location, start date, end date, ordering, and visible/status value.

Restrict the panel to the single administrator model without adding a role system.

- [x] **Step 4: Seed one local administrator safely**

Create a non-production local seeder path that uses environment-provided credentials or documented local defaults without committing real credentials. Do not put passwords in source control.

- [x] **Step 5: Run admin tests and inspect routes**

```powershell
php artisan test --compact tests/Feature/AdminAccessTest.php
php artisan route:list --path=admin
```

Expected: PASS and routes for the configured Filament panel are present.

- [x] **Step 6: Commit the admin panel**

```powershell
git add app/Filament app/Providers/Filament app/Models/User.php database/seeders tests/Feature/AdminAccessTest.php
 git commit -m "feat: add filament portfolio cms"
```

## Task 5: Implement safe Markdown rendering and Livewire preview

**Files:**
- Create: `app/Support/MarkdownRenderer.php`
- Create: `app/Livewire/MarkdownPreview.php`
- Create: `resources/views/livewire/markdown-preview.blade.php`
- Modify: Filament Markdown form components from Task 4 only as required
- Create: `tests/Unit/MarkdownRendererTest.php`
- Create: `tests/Feature/MarkdownPreviewTest.php`

**Interfaces:**
- `MarkdownRenderer::render(string $markdown): string` returns sanitized HTML.
- `MarkdownPreview` accepts Markdown text and renders preview HTML without persisting it.

- [x] **Step 1: Write failing renderer tests**

Test headings, emphasis, links, and removal/neutralization of unsafe raw HTML and executable URL schemes.

- [x] **Step 2: Run tests to verify failure**

```powershell
php artisan test --compact tests/Unit/MarkdownRendererTest.php tests/Feature/MarkdownPreviewTest.php
```

Expected: FAIL because the renderer and Livewire component do not exist.

- [x] **Step 3: Implement the renderer and preview**

Use the installed Markdown parser and sanitizer APIs confirmed in Task 1. Sanitize after parsing, allow only the tags and attributes needed for portfolio prose, and reject unsafe links. Keep preview state transient; do not write previews to the database.

- [x] **Step 4: Run tests and formatter**

```powershell
php artisan test --compact tests/Unit/MarkdownRendererTest.php tests/Feature/MarkdownPreviewTest.php
vendor/bin/pint --dirty --format agent
```

Expected: PASS.

- [x] **Step 5: Commit Markdown support**

```powershell
git add app/Support app/Livewire resources/views/livewire tests/Unit/MarkdownRendererTest.php tests/Feature/MarkdownPreviewTest.php
 git commit -m "feat: add safe markdown preview"
```

## Task 6: Build localized public routes and pages

**Files:**
- Modify: `routes/web.php`
- Create: `app/Http/Controllers/PortfolioController.php`
- Create: `app/Http/Middleware/SetLocale.php` if route locale middleware is needed
- Create: `resources/views/layouts/public.blade.php`
- Create: `resources/views/components/*`
- Create: `resources/views/pages/home.blade.php`
- Create: `resources/views/pages/about.blade.php`
- Create: `resources/views/pages/projects/index.blade.php`
- Create: `resources/views/pages/projects/show.blade.php`
- Create: `resources/views/pages/posts/index.blade.php`
- Create: `resources/views/pages/posts/show.blade.php`
- Create: `resources/views/pages/contact.blade.php`
- Create: `resources/views/errors/404.blade.php`
- Modify: `resources/css/app.css`, `resources/js/app.js`
- Create: `tests/Feature/PublicPortfolioTest.php`

**Interfaces:**
- Produces named localized routes for home, about, projects, posts, and contact under `/en` and `/id`.
- Produces public pages that receive only posted content and localized display values.

- [x] **Step 1: Write failing public feature tests**

Cover `/en`, `/id`, project/post listing and detail pages, absence of draft/archived records, English fallback, education display, missing-slug 404, and language-preserving links.

- [x] **Step 2: Run public tests to verify failure**

```powershell
php artisan test --compact tests/Feature/PublicPortfolioTest.php
```

Expected: FAIL because public routes, controllers, and views do not exist.

- [x] **Step 3: Implement locale middleware and routes**

Accept only `en` and `id`; default route generation to `en`. Set the application locale per request. Use named routes and route model binding constrained to posted content. Keep admin routes outside the locale prefix.

- [x] **Step 4: Implement controller queries and views**

Query through `posted()` scopes, order by explicit ordering/publication fields, and use locale-aware model methods. Render Markdown through `MarkdownRenderer`. Add accessible navigation, language switcher, responsive layout, empty states, and a custom 404 page. Include education on the About page and hide the section when no visible education records exist.

- [x] **Step 5: Add SEO essentials**

Add per-page title/description values, Open Graph tags, canonical locale URLs, sitemap generation for posted public records, and robots configuration. Do not add an SEO package unless the existing framework cannot implement these few pages directly.

- [x] **Step 6: Run feature tests and build assets**

```powershell
php artisan test --compact tests/Feature/PublicPortfolioTest.php
npm run build
vendor/bin/pint --dirty --format agent
```

Expected: PASS, successful Vite build, and formatted PHP.

- [x] **Step 7: Commit public site**

```powershell
git add routes app/Http app/Support resources tests/Feature/PublicPortfolioTest.php
 git commit -m "feat: add bilingual public portfolio"
```

## Task 7: Add contact form and inbox behavior

**Files:**
- Create: `app/Http/Requests/StoreContactMessageRequest.php`
- Create: `app/Livewire/ContactForm.php`
- Create: `resources/views/livewire/contact-form.blade.php`
- Modify: `resources/views/pages/contact.blade.php`
- Modify: `app/Filament/Resources/ContactMessageResource.php`
- Create: `tests/Feature/ContactMessageTest.php`

**Interfaces:**
- `ContactForm` validates and persists `ContactMessage` records.
- Filament marks messages read/unread and displays sender/message metadata.

- [x] **Step 1: Write failing contact tests**

Test valid persistence, required fields, invalid email rejection, rate limiting if configured, and admin read-state updates. Assert that no message is stored for invalid input.

- [x] **Step 2: Run tests to verify failure**

```powershell
php artisan test --compact tests/Feature/ContactMessageTest.php
```

Expected: FAIL because the request, Livewire form, and resource behavior are absent.

- [x] **Step 3: Implement validated persistence**

Use a form request or Livewire validation at the public boundary. Store normalized sender data and the message. Add a simple rate limit using Laravel’s built-in limiter only if the application already has a rate-limiting convention; do not add an external anti-spam service.

- [x] **Step 4: Implement inbox actions**

Add Filament table columns and an explicit read/unread action. Keep email notification optional and disabled unless deployment mail settings are configured.

- [x] **Step 5: Run tests and formatter**

```powershell
php artisan test --compact tests/Feature/ContactMessageTest.php
vendor/bin/pint --dirty --format agent
```

Expected: PASS.

- [x] **Step 6: Commit contact functionality**

```powershell
git add app/Http/Requests app/Livewire resources/views/livewire resources/views/pages/contact.blade.php app/Filament/Resources/ContactMessageResource.php tests/Feature/ContactMessageTest.php
 git commit -m "feat: add contact message inbox"
```

## Task 8: Configure storage, settings, and production-safe deployment

**Files:**
- Modify: `.env.example`
- Modify: `config/filesystems.php`, `config/app.php`, `config/services.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Create: `routes/console.php` additions only if sitemap/cache commands are required
- Create or modify: Dokploy deployment configuration files only after inspecting available project configuration
- Create: `tests/Feature/DeploymentConfigurationTest.php` only for behavior that can be tested locally

**Interfaces:**
- Produces documented environment variables and deployment commands for PostgreSQL, public storage, application URL, locale, and mail settings.

- [x] **Step 1: Write deployment configuration checks**

Assert the application defaults to English, the public disk is configured, and production can run migrations and storage linking without SQLite-only assumptions.

- [x] **Step 2: Configure environment and storage**

Add non-secret placeholders for `APP_URL`, `APP_LOCALE=en`, PostgreSQL connection variables, filesystem disk, and optional mail variables. Ensure `storage:link` exposes uploaded media and that Dokploy mounts a persistent volume at the storage path.

- [x] **Step 3: Add deployment commands**

Use the existing Dokploy Laravel service convention. The release sequence must include dependency installation, `php artisan migrate --force`, `php artisan storage:link`, config/route/view caching as appropriate, and `npm run build` during image/build time. Do not introduce a custom Dockerfile unless the service cannot run the current application.

- [x] **Step 4: Verify locally**

Run:

```powershell
php artisan config:clear
php artisan migrate:fresh --seed --force
php artisan storage:link
php artisan route:list
npm run build
php artisan test --compact
```

Expected: migrations, seeding, storage linking, routes, assets, and tests complete successfully.

- [x] **Step 5: Commit deployment configuration**

```powershell
git add .env.example config database/seeders routes
 git commit -m "chore: prepare dokploy deployment"
```

## Task 9: End-to-end verification and cleanup

**Files:**
- Modify: any files exposed by verification failures
- Test: full `tests/` suite

- [x] **Step 1: Run the full backend suite**

```powershell
php artisan test --compact
```

Expected: all tests pass against PostgreSQL.

- [x] **Step 2: Run formatting and asset build**

```powershell
vendor/bin/pint --dirty --format agent
npm run build
```

Expected: no formatting changes remain and assets compile.

- [x] **Step 3: Exercise the real application**

Start the Laravel app using the project’s run workflow. Verify in a browser:

- `/en` and `/id` render correctly.
- Language switching preserves the page.
- Posted projects/posts are visible; drafts/archived records are not.
- Empty Indonesian fields fall back to English.
- Education appears on About and disappears when empty.
- Markdown formatting renders and unsafe HTML/links are neutralized.
- Contact submission persists and appears in Filament.
- Administrator can log in, edit profile/password, and manage content.
- Uploaded media persists through the configured public storage path.

- [x] **Step 4: Inspect the final change set**

```powershell
git status --short
git diff --stat
```

Review for secrets, unnecessary dependencies, generated files, and any accidental SQLite-only behavior. Do not commit `.env` or credentials.

- [x] **Step 5: Commit final fixes**

```powershell
git add .
 git commit -m "feat: complete portfolio cms"
```

Only stage reviewed files and preserve any unrelated user changes.
