<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $children = $request->user()
            ->children()
            ->with(['allergies', 'mealPlans' => fn ($q) => $q->latest()])
            ->withCount('growthRecords')
            ->latest()
            ->get();

        return view('dashboard', [
            'children' => $children,
        ]);
    }
}
