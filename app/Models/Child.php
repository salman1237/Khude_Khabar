<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Observers\ChildObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([ChildObserver::class])]
class Child extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'age_months',
        'gender',
        'weight_kg',
        'height_cm',
        'budget_preference',
    ];

    protected $casts = [
        'age_months' => 'integer',
        'weight_kg' => 'decimal:2',
        'height_cm' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function allergies(): HasMany
    {
        return $this->hasMany(ChildAllergy::class);
    }

    public function healthConditions(): HasMany
    {
        return $this->hasMany(ChildHealthCondition::class);
    }

    public function mealPlans(): HasMany
    {
        return $this->hasMany(MealPlan::class);
    }

    public function growthRecords(): HasMany
    {
        return $this->hasMany(GrowthRecord::class)->orderBy('recorded_date');
    }

    public function latestMealPlan(): ?MealPlan
    {
        return $this->mealPlans()->where('status', 'completed')->latest()->first();
    }

    public function getAgeDisplayAttribute(): string
    {
        $months = $this->age_months;
        if ($months < 12) {
            return $months . ' মাস';
        }
        $years = intdiv($months, 12);
        $rem = $months % 12;
        return $rem > 0 ? $years . ' বছর ' . $rem . ' মাস' : $years . ' বছর';
    }
}
