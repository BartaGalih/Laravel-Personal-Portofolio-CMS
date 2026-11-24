<?php

namespace App\Models\Concerns;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Builder;

trait HasPostedScope
{
    public function scopePosted(Builder $query): void
    {
        $query->where('status', ContentStatus::Posted);
    }
}
