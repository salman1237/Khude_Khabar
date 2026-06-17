<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealPlanDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'meal_plan_id',
        'day_name',
        'meal_type',
        'meal_name_bengali',
        'instructions',
        'nutrition',
        'cost_bdt',
    ];

    protected $casts = [
        'instructions' => 'array',
        'nutrition'    => 'array',
        'cost_bdt'     => 'decimal:2',
    ];

    public function mealPlan(): BelongsTo
    {
        return $this->belongsTo(MealPlan::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(MealIngredient::class);
    }

    public function getMealTypebengaliAttribute(): string
    {
        return MealPlan::MEAL_TYPES_BENGALI[$this->meal_type] ?? $this->meal_type;
    }

    public function getDayNameBengaliAttribute(): string
    {
        return MealPlan::DAYS_BENGALI[$this->day_name] ?? $this->day_name;
    }

    public function getMealEmojiAttribute(): string
    {
        return match ($this->meal_type) {
            'breakfast' => '🌅',
            'lunch'     => '🍽️',
            'dinner'    => '🌙',
            default     => '🍴',
        };
    }
}
