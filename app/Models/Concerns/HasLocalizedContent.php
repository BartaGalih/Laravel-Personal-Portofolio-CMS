<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\App;

trait HasLocalizedContent
{
    public function localized(string $field, ?string $locale = null): mixed
    {
        $locale ??= App::getLocale();
        $value = $this->getAttribute("{$field}_{$locale}");

        return filled($value) || $locale === 'en'
            ? $value
            : $this->getAttribute("{$field}_en");
    }
}
