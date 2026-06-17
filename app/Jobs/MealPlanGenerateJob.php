<?php

namespace App\Jobs;

use App\Events\MealPlanReady;
use App\Models\MealPlan;
use App\Services\OpenAIMealPlanner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MealPlanGenerateJob implements ShouldQueue
{
    use Queueable;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 2;

    /**
     * Seconds before the job times out (OpenAI calls can be slow).
     */
    public int $timeout = 120;

    public function __construct(public MealPlan $mealPlan)
    {
    }

    public function handle(OpenAIMealPlanner $planner): void
    {
        $plan = $this->mealPlan->fresh();

        if (! $plan || $plan->status === 'completed') {
            return;
        }

        $plan->update(['status' => 'processing']);

        $child = $plan->child()->with(['allergies', 'healthConditions'])->first();

        $days = $planner->generate($child);

        DB::transaction(function () use ($plan, $days) {
            // Clear any partial data from a previous attempt.
            $plan->mealPlanDays()->delete();

            foreach ($days as $day) {
                $mealDay = $plan->mealPlanDays()->create([
                    'day_name' => $day['day_name'],
                    'meal_type' => $day['meal_type'],
                    'meal_name_bengali' => $day['meal_name_bengali'],
                    'instructions' => $day['instructions'],
                    'nutrition' => $day['nutrition'],
                    'cost_bdt' => $day['cost_bdt'],
                ]);

                foreach ($day['ingredients'] as $ingredient) {
                    $mealDay->ingredients()->create($ingredient);
                }
            }

            $plan->update(['status' => 'completed']);
        });

        // Notify listeners (real-time). A broadcast failure must never undo a
        // successfully generated plan, so swallow any transport error here.
        try {
            MealPlanReady::dispatch($plan->fresh());
        } catch (\Throwable $e) {
            Log::warning('MealPlanReady broadcast failed (plan already saved).', [
                'meal_plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mark the plan failed so the UI can react.
     */
    public function failed(\Throwable $e): void
    {
        Log::error('MealPlanGenerateJob failed.', [
            'meal_plan_id' => $this->mealPlan->id,
            'error' => $e->getMessage(),
        ]);

        $this->mealPlan->fresh()?->update(['status' => 'failed']);
    }
}
