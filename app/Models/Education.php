<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'institution_en', 'institution_id', 'degree_en', 'degree_id', 'field_en', 'field_id',
    'description_en', 'description_id', 'location', 'start_date', 'end_date', 'sort_order', 'is_visible',
])]
class Education extends Model
{
    use HasFactory, HasLocalizedContent;

    protected $table = 'educations';

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_visible' => 'boolean',
        ];
    }
}
