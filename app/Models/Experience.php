<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'company_en', 'company_id', 'role_en', 'role_id', 'start_date', 'end_date',
    'description_en', 'description_id', 'sort_order', 'is_visible',
])]
class Experience extends Model
{
    use HasFactory, HasLocalizedContent;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_visible' => 'boolean',
        ];
    }
}
