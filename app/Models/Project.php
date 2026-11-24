<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Models\Concerns\HasLocalizedContent;
use App\Models\Concerns\HasPostedScope;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title_en', 'title_id', 'slug', 'summary_en', 'summary_id', 'content_en', 'content_id',
    'cover_image_path', 'project_url', 'repository_url', 'status', 'sort_order',
])]
class Project extends Model
{
    use HasFactory, HasLocalizedContent, HasPostedScope;

    protected function casts(): array
    {
        return ['status' => ContentStatus::class];
    }
}
