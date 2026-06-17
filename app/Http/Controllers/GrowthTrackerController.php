<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGrowthRecordRequest;
use App\Models\Child;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GrowthTrackerController extends Controller
{
    public function show(Child $child): View
    {
        $this->authorizeChild($child);

        $records = $child->growthRecords()->get();

        return view('growth-tracker.show', [
            'child' => $child,
            'records' => $records,
            // Pre-shaped series for Chart.js.
            'chartLabels' => $records->map(fn ($r) => $r->recorded_date->format('d M y'))->values(),
            'weightData' => $records->map(fn ($r) => (float) $r->weight_kg)->values(),
            'heightData' => $records->map(fn ($r) => (float) $r->height_cm)->values(),
        ]);
    }

    public function store(StoreGrowthRecordRequest $request, Child $child): RedirectResponse
    {
        $this->authorizeChild($child);

        $child->growthRecords()->create($request->validated());

        // Keep the child's headline weight/height in sync with the latest reading.
        $child->update([
            'weight_kg' => $request->validated()['weight_kg'],
            'height_cm' => $request->validated()['height_cm'],
        ]);

        return redirect()->route('growth-tracker.show', $child)
            ->with('success', 'নতুন পরিমাপ যোগ করা হয়েছে।');
    }

    protected function authorizeChild(Child $child): void
    {
        abort_unless($child->user_id === request()->user()->id, 403);
    }
}
