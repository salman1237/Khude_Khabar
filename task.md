# Khude Khabar — Task Tracker

## Phase 1 — Package Installation & .env Setup
- `[x]` Install Laravel Breeze (Blade stack)
- `[x]` Install pusher-php-server via composer
- `[x]` Install laravel-echo + pusher-js via npm
- `[x]` Update .env with app name, Pusher placeholders, OpenAI key placeholder
- `[x]` Run `php artisan breeze:install blade`
- `[x]` Run `npm install && npm run build`

## Phase 2 — Database Migrations
- `[x]` create_children_table
- `[x]` create_child_allergies_table
- `[x]` create_child_health_conditions_table
- `[x]` create_meal_plans_table
- `[x]` create_meal_plan_days_table
- `[x]` create_meal_ingredients_table
- `[x]` create_growth_records_table
- `[x]` Run `php artisan migrate`

## Phase 3 — Models & Relationships
- `[x]` Child model (with Observer boot)
- `[x]` ChildAllergy model
- `[x]` ChildHealthCondition model
- `[x]` MealPlan model
- `[x]` MealPlanDay model (JSON casts)
- `[x]` MealIngredient model
- `[x]` GrowthRecord model

## Phase 4 — Auth Views (Breeze Customization)
- `[x]` welcome.blade.php (custom landing page)
- `[x]` login.blade.php (branded)
- `[x]` register.blade.php (branded)

## Phase 5 — Blade Layouts & Components
- `[x]` layouts/app.blade.php
- `[x]` layouts/guest.blade.php
- `[x]` components/sidebar.blade.php
- `[x]` components/child-card.blade.php
- `[x]` components/meal-card.blade.php
- `[x]` components/growth-chart.blade.php
- `[x]` components/alert.blade.php
- `[x]` components/modal.blade.php

## Phase 6 — Controllers & Form Requests
- `[x]` DashboardController
- `[x]` ChildController
- `[x]` MealPlanController
- `[x]` RecipeController
- `[x]` GroceryListController
- `[x]` GrowthTrackerController
- `[x]` StoreChildRequest (Bengali validation)
- `[x]` StoreGrowthRecordRequest

## Phase 7 — Queue Jobs & Events
- `[x]` MealPlanGenerateJob
- `[x]` MealPlanReady event
- `[x]` ChildObserver

## Phase 8 — Blade Views (7 pages)
- `[x]` dashboard.blade.php
- `[x]` children/create.blade.php
- `[x]` children/edit.blade.php
- `[x]` meal-plans/loading.blade.php
- `[x]` meal-plans/show.blade.php
- `[x]` recipes/show.blade.php
- `[x]` grocery-list/show.blade.php
- `[x]` growth-tracker/show.blade.php
- `[x]` partials/empty-children.blade.php

## Phase 9 — Routes
- `[x]` routes/web.php (all routes)

## Phase 10 — Frontend Assets
- `[x]` resources/css/app.css (fonts + custom properties)
- `[x]` resources/js/app.js (Alpine + Echo + Chart.js)
- `[x]` vite.config.js updates
- `[x]` Final build

---

## Notes
- **OpenAI integration**: `App\Services\OpenAIMealPlanner` calls the OpenAI API when `OPENAI_API_KEY` is set, and otherwise falls back to a curated Bengali meal library (allergy-aware), so the app is fully functional without credentials.
- **Real-time**: `MealPlanReady` broadcasts on a public `meal-plans.{id}` channel (Echo/Pusher). The loading screen also polls `meal-plans.{id}/status` as a fallback, so it works without Pusher keys.
- **Migration order**: `meal_plan_days`/`meal_ingredients` migrations were renamed (175616/175617) so foreign keys resolve after `meal_plans`.
- Added `config/broadcasting.php` (not shipped in the Laravel 12 skeleton) and a `children()` relation on the `User` model.
- Run the dev stack with `composer dev` (server + queue worker + logs + vite). The queue worker is required for meal-plan generation.
