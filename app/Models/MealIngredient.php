<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealIngredient extends Model
{
    protected $fillable = [
        'meal_plan_day_id',
        'ingredient_name',
        'quantity',
        'unit',
        'category',
        'estimated_price_bdt',
    ];

    protected $casts = [
        'estimated_price_bdt' => 'decimal:2',
    ];

    public function mealPlanDay(): BelongsTo
    {
        return $this->belongsTo(MealPlanDay::class);
    }

    public function getCategoryBengaliAttribute(): string
    {
        return match ($this->category) {
            'vegetables' => 'সবজি',
            'protein'    => 'আমিষ',
            'dairy'      => 'দুগ্ধজাত',
            'spices'     => 'মশলা',
            'grains'     => 'শস্য',
            'fruits'     => 'ফলমূল',
            default      => 'অন্যান্য',
        };
    }
}
