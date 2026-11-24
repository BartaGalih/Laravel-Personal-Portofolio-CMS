<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'category', 'proficiency', 'icon', 'sort_order', 'is_visible'])]
class Skill extends Model
{
    use HasFactory;
}
