<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChildRequest;
use App\Models\Child;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChildController extends Controller
{
    public function create(): View
    {
        return view('children.create');
    }

    public function store(StoreChildRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $child = $request->user()->children()->create([
            'name' => $data['name'],
            'age_months' => $data['age_months'],
            'gender' => $data['gender'],
            'weight_kg' => $data['weight_kg'] ?? null,
            'height_cm' => $data['height_cm'] ?? null,
            'budget_preference' => $data['budget_preference'],
        ]);

        $this->syncRelations($child, $data);

        // The ChildObserver has queued a meal plan; send the parent to it.
        $mealPlan = $child->mealPlans()->latest()->first();

        if ($mealPlan) {
            return redirect()->route('meal-plans.loading', $mealPlan);
        }

        return redirect()->route('dashboard')
            ->with('success', "{$child->name} যোগ করা হয়েছে।");
    }

    public function edit(Child $child): View
    {
        $this->authorizeChild($child);

        $child->load(['allergies', 'healthConditions']);

        return view('children.edit', compact('child'));
    }

    public function update(StoreChildRequest $request, Child $child): RedirectResponse
    {
        $this->authorizeChild($child);

        $data = $request->validated();

        $child->update([
            'name' => $data['name'],
            'age_months' => $data['age_months'],
            'gender' => $data['gender'],
            'weight_kg' => $data['weight_kg'] ?? null,
            'height_cm' => $data['height_cm'] ?? null,
            'budget_preference' => $data['budget_preference'],
        ]);

        $this->syncRelations($child, $data);

        return redirect()->route('dashboard')
            ->with('success', "{$child->name}-এর তথ্য আপডেট হয়েছে।");
    }

    public function destroy(Child $child): RedirectResponse
    {
        $this->authorizeChild($child);

        $name = $child->name;
        $child->delete();

        return redirect()->route('dashboard')
            ->with('success', "{$name} মুছে ফেলা হয়েছে।");
    }

    /**
     * Replace the child's allergy and health-condition rows from the request.
     *
     * @param  array<string, mixed>  $data
     */
    protected function syncRelations(Child $child, array $data): void
    {
        $child->allergies()->delete();
        $child->healthConditions()->delete();

        collect($data['allergies'] ?? [])
            ->map(fn ($a) => trim((string) $a))
            ->filter()
            ->each(fn ($a) => $child->allergies()->create(['allergy_name' => $a]));

        collect($data['health_conditions'] ?? [])
            ->map(fn ($c) => trim((string) $c))
            ->filter()
            ->each(fn ($c) => $child->healthConditions()->create(['condition_name' => $c]));
    }

    protected function authorizeChild(Child $child): void
    {
        abort_unless($child->user_id === request()->user()->id, 403);
    }
}
