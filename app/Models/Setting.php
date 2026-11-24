<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['value' => 'array'];
    }

    /**
     * All settings as a key => value map, resolved once per request.
     *
     * @return array<string, mixed>
     */
    public static function values(): array
    {
        if (! app()->bound('settings.values')) {
            app()->instance(
                'settings.values',
                static::query()->get(['key', 'value'])->pluck('value', 'key')->all(),
            );
        }

        return app('settings.values');
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::values()[$key] ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);

        // The request-scoped cache is now stale.
        app()->forgetInstance('settings.values');
    }
}
