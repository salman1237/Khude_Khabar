<?php

use App\Http\Controllers\ChildController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroceryListController;
use App\Http\Controllers\GrowthTrackerController;
use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Children
    Route::get('/children/create', [ChildController::class, 'create'])->name('children.create');
    Route::post('/children', [ChildController::class, 'store'])->name('children.store');
    Route::get('/children/{child}/edit', [ChildController::class, 'edit'])->name('children.edit');
    Route::put('/children/{child}', [ChildController::class, 'update'])->name('children.update');
    Route::delete('/children/{child}', [ChildController::class, 'destroy'])->name('children.destroy');

    // Meal plans
    Route::get('/meal-plans/{mealPlan}/loading', [MealPlanController::class, 'loading'])->name('meal-plans.loading');
    Route::get('/meal-plans/{mealPlan}/status', [MealPlanController::class, 'status'])->name('meal-plans.status');
    Route::get('/meal-plans/{mealPlan}/grocery-list', [GroceryListController::class, 'show'])->name('grocery-list.show');
    Route::get('/meal-plans/{mealPlan}', [MealPlanController::class, 'show'])->name('meal-plans.show');
    Route::post('/children/{child}/meal-plans/regenerate', [MealPlanController::class, 'regenerate'])->name('meal-plans.regenerate');

    // Recipes (a single meal of a plan)
    Route::get('/recipes/{meal}', [RecipeController::class, 'show'])->name('recipes.show');

    // Growth tracker
    Route::get('/children/{child}/growth', [GrowthTrackerController::class, 'show'])->name('growth-tracker.show');
    Route::post('/children/{child}/growth', [GrowthTrackerController::class, 'store'])->name('growth-tracker.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
