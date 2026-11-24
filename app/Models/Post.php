<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Models\Concerns\HasLocalizedContent;
use App\Models\Concerns\HasPostedScope;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title_en', 'title_id', 'slug', 'excerpt_en', 'excerpt_id', 'content_en', 'content_id',
    'cover_image_path', 'published_at', 'status',
])]
class Post extends Model
{
    use HasFactory, HasLocalizedContent, HasPostedScope;

    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'published_at' => 'datetime',
        ];
    }
}
