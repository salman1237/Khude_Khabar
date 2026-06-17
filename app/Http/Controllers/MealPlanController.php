<?php

namespace App\Http\Controllers;

use App\Jobs\MealPlanGenerateJob;
use App\Models\Child;
use App\Models\MealPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MealPlanController extends Controller
{
    /**
     * The "generating…" screen shown while the queued job runs.
     */
    public function loading(MealPlan $mealPlan): View|RedirectResponse
    {
        $this->authorizeMealPlan($mealPlan);

        if ($mealPlan->isCompleted()) {
            return redirect()->route('meal-plans.show', $mealPlan);
        }

        return view('meal-plans.loading', [
            'mealPlan' => $mealPlan,
            'child' => $mealPlan->child,
        ]);
    }

    /**
     * Lightweight JSON endpoint the loading page polls as a fallback to Echo.
     */
    public function status(MealPlan $mealPlan): JsonResponse
    {
        $this->authorizeMealPlan($mealPlan);

        return response()->json([
            'status' => $mealPlan->status,
            'redirect_url' => $mealPlan->isCompleted()
                ? route('meal-plans.show', $mealPlan)
                : null,
        ]);
    }

    public function show(MealPlan $mealPlan): View|RedirectResponse
    {
        $this->authorizeMealPlan($mealPlan);

        if (! $mealPlan->isCompleted()) {
            return redirect()->route('meal-plans.loading', $mealPlan);
        }

        $mealPlan->load(['child', 'mealPlanDays.ingredients']);

        // Group meals by day in the canonical week order for the view.
        $mealsByDay = $mealPlan->mealPlanDays
            ->sortBy(fn ($d) => array_search($d->day_name, MealPlan::DAYS, true))
            ->groupBy('day_name');

        return view('meal-plans.show', [
            'mealPlan' => $mealPlan,
            'child' => $mealPlan->child,
            'mealsByDay' => $mealsByDay,
        ]);
    }

    /**
     * Queue a fresh weekly plan for a child.
     */
    public function regenerate(Child $child): RedirectResponse
    {
        abort_unless($child->user_id === request()->user()->id, 403);

        $mealPlan = $child->mealPlans()->create([
            'week_start_date' => now()->startOfWeek()->toDateString(),
            'status' => 'pending',
        ]);

        MealPlanGenerateJob::dispatch($mealPlan);

        return redirect()->route('meal-plans.loading', $mealPlan);
    }

    protected function authorizeMealPlan(MealPlan $mealPlan): void
    {
        abort_unless($mealPlan->child->user_id === request()->user()->id, 403);
    }
}
