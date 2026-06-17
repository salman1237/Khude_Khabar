<?php

namespace App\Observers;

use App\Jobs\MealPlanGenerateJob;
use App\Models\Child;
use Illuminate\Support\Carbon;

class ChildObserver
{
    /**
     * When a child is added, queue an initial weekly meal plan so the parent
     * is taken straight to the generating screen.
     */
    public function created(Child $child): void
    {
        $this->queueWeeklyPlan($child);
    }

    /**
     * Regenerate the plan when details that affect nutrition change.
     */
    public function updated(Child $child): void
    {
        if ($child->wasChanged(['age_months', 'weight_kg', 'height_cm', 'budget_preference'])) {
            $this->queueWeeklyPlan($child);
        }
    }

    protected function queueWeeklyPlan(Child $child): void
    {
        $mealPlan = $child->mealPlans()->create([
            'week_start_date' => Carbon::now()->startOfWeek()->toDateString(),
            'status' => 'pending',
        ]);

        MealPlanGenerateJob::dispatch($mealPlan);
    }
}
