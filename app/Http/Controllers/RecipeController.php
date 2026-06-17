<?php

namespace App\Http\Controllers;

use App\Models\MealPlanDay;
use Illuminate\View\View;

class RecipeController extends Controller
{
    /**
     * Show the full recipe for a single meal of a plan.
     */
    public function show(MealPlanDay $meal): View
    {
        abort_unless(
            $meal->mealPlan->child->user_id === request()->user()->id,
            403
        );

        $meal->load(['ingredients', 'mealPlan.child']);

        return view('recipes.show', [
            'meal' => $meal,
            'mealPlan' => $meal->mealPlan,
            'child' => $meal->mealPlan->child,
        ]);
    }
}
