<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'week_start_date',
        'status',
    ];

    protected $casts = [
        'week_start_date' => 'date',
    ];

    const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    const DAYS_BENGALI = [
        'Monday'    => 'সোমবার',
        'Tuesday'   => 'মঙ্গলবার',
        'Wednesday' => 'বুধবার',
        'Thursday'  => 'বৃহস্পতিবার',
        'Friday'    => 'শুক্রবার',
        'Saturday'  => 'শনিবার',
        'Sunday'    => 'রবিবার',
    ];
    const MEAL_TYPES_BENGALI = [
        'breakfast' => 'সকালের নাস্তা',
        'lunch'     => 'দুপুরের খাবার',
        'dinner'    => 'রাতের খাবার',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function mealPlanDays(): HasMany
    {
        return $this->hasMany(MealPlanDay::class);
    }

    public function getTotalCostAttribute(): float
    {
        return $this->mealPlanDays->sum('cost_bdt');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
