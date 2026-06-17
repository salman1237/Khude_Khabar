<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use Illuminate\View\View;

class GroceryListController extends Controller
{
    /**
     * Aggregated shopping list for a whole week's meal plan, grouped by category.
     */
    public function show(MealPlan $mealPlan): View
    {
        abort_unless(
            $mealPlan->child->user_id === request()->user()->id,
            403
        );

        $mealPlan->load('mealPlanDays.ingredients');

        $ingredients = $mealPlan->mealPlanDays->flatMap->ingredients;

        // Merge identical ingredients, summing quantities and prices.
        $grouped = $ingredients
            ->groupBy(fn ($i) => mb_strtolower($i->ingredient_name).'|'.$i->unit)
            ->map(function ($items) {
                $first = $items->first();

                return [
                    'ingredient_name' => $first->ingredient_name,
                    'unit' => $first->unit,
                    'category' => $first->category,
                    'category_bengali' => $first->category_bengali,
                    'quantities' => $items->pluck('quantity')->filter()->implode(' + '),
                    'count' => $items->count(),
                    'total_price' => $items->sum('estimated_price_bdt'),
                ];
            })
            ->groupBy('category_bengali')
            ->sortKeys();

        $totalCost = $ingredients->sum('estimated_price_bdt');

        return view('grocery-list.show', [
            'mealPlan' => $mealPlan,
            'child' => $mealPlan->child,
            'groceriesByCategory' => $grouped,
            'totalCost' => $totalCost,
        ]);
    }
}
