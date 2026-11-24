<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

/**
 * The fixed set of site-level settings, read from the `settings` table.
 *
 * Lives here rather than in a Blade view so the header, footer, SEO tags and the
 * admin form all resolve the same way. Values are never trusted from the DB at
 * render time beyond what the upload field already validated.
 */
class SiteSettings
{
    public const KEY = 'site';

    /**
     * Defaults, used when a value is absent or an upload has been removed.
     *
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'name' => config('app.name'),
            'tagline_en' => '',
            'tagline_id' => '',
            'header_brand' => 'name',
            'logo_path' => null,
            'favicon_path' => null,
            'meta_description_en' => '',
            'meta_description_id' => '',
            'meta_image_path' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        $stored = Setting::get(self::KEY);

        return array_merge(self::defaults(), is_array($stored) ? $stored : []);
    }

    public static function get(string $field, mixed $default = null): mixed
    {
        return static::all()[$field] ?? $default;
    }

    public static function name(): string
    {
        $name = static::get('name');

        return is_string($name) && filled($name) ? $name : (string) config('app.name');
    }

    public static function tagline(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $all = static::all();
        $tagline = $all["tagline_{$locale}"] ?? null;

        if (is_string($tagline) && filled($tagline)) {
            return $tagline;
        }

        $fallback = $all['tagline_en'] ?? null;

        return is_string($fallback) && filled($fallback) ? $fallback : null;
    }

    public static function faviconUrl(): ?string
    {
        $path = static::existingPath('favicon_path');

        return $path === null ? null : Storage::disk('public')->url($path);
    }

    public static function logoUrl(): ?string
    {
        $path = static::existingPath('logo_path');

        return $path === null ? null : Storage::disk('public')->url($path);
    }

    public static function metaImageUrl(): ?string
    {
        $path = static::existingPath('meta_image_path');

        return $path === null ? null : Storage::disk('public')->url($path);
    }

    public static function usesLogo(): bool
    {
        return static::get('header_brand') === 'logo';
    }

    /**
     * True when the header should show an uploaded logo, falling back to the name
     * if the logo file has gone missing so the header never renders blank.
     */
    public static function headerLogoUrl(): ?string
    {
        return static::usesLogo() ? static::logoUrl() : null;
    }

    public static function metaDescription(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $all = static::all();
        $description = $all["meta_description_{$locale}"] ?? null;

        if (is_string($description) && filled($description)) {
            return $description;
        }

        $fallback = $all['meta_description_en'] ?? null;

        return is_string($fallback) && filled($fallback) ? $fallback : null;
    }

    /**
     * The value the FileUpload field expects: a path, or null when the file is gone.
     */
    protected static function existingPath(string $field): ?string
    {
        $path = static::get($field);

        if (! is_string($path) || blank($path)) {
            return null;
        }

        return Storage::disk('public')->exists($path) ? $path : null;
    }
}
