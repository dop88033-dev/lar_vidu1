<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'category_name',
        'main_image',
        'price',
        'quantity',
        'notes',
        'is_active',
        'colors',
    ];

    protected $casts = [
        'colors' => 'array',
        'is_active' => 'boolean',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class, 'category_id');
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating') ?? 5, 1);
    }
}