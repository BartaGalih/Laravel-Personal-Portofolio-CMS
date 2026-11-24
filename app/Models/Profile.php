<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name_en', 'name_id', 'bio_en', 'bio_id', 'photo_path', 'email', 'phone', 'social_links'])]
class Profile extends Model
{
    use Concerns\HasLocalizedContent, HasFactory;

    protected function casts(): array
    {
        return ['social_links' => 'array'];
    }
}
