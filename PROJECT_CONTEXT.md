# Khude Khabar — Project Context

**Khude Khabar** ("Little One's Food") is a Laravel 12 web application that generates
**weekly Bengali meal plans for children** using an LLM, tracks each child's growth, and
produces an aggregated grocery shopping list. The UI is fully in Bengali (Bangla) and
costs are expressed in BDT (Bangladeshi Taka).

---

## 1. Tech Stack Overview

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 12 (PHP ^8.2) |
| Auth scaffolding | Laravel Breeze (Blade stack) |
| Frontend | **Blade + Alpine.js + Tailwind CSS** (no Vue/React) |
| Build tool | Vite 7 (`laravel-vite-plugin`) |
| Charts | Chart.js 4 (growth tracker) |
| Real-time | Laravel Echo + Pusher (`pusher-js`) |
| AI provider | OpenAI (`openai-php/laravel`) |
| Database | MySQL (`khude_khabar`) |
| Queue / Cache / Session | `database` driver for all three |
| Broadcast | `log` driver by default (Pusher when configured) |

> The app is designed to run **without any API credentials**: the meal planner falls
> back to a curated static Bengali plan, and the loading page polls a status endpoint
> when Pusher/Echo is not configured.

---

## 2. Routes

### Web routes — `routes/web.php`

**Public**
- `GET /` → `welcome` view (`home`)
- `GET /up` → Laravel health check (registered in `bootstrap/app.php`)

**Authenticated + email-verified** (`middleware(['auth','verified'])`)
- `GET  /dashboard` → `DashboardController@index` (`dashboard`)
- **Children**
  - `GET    /children/create` → `ChildController@create` (`children.create`)
  - `POST   /children` → `ChildController@store` (`children.store`)
  - `GET    /children/{child}/edit` → `ChildController@edit` (`children.edit`)
  - `PUT    /children/{child}` → `ChildController@update` (`children.update`)
  - `DELETE /children/{child}` → `ChildController@destroy` (`children.destroy`)
- **Meal plans**
  - `GET  /meal-plans/{mealPlan}/loading` → `MealPlanController@loading` (`meal-plans.loading`)
  - `GET  /meal-plans/{mealPlan}/status` → `MealPlanController@status` (`meal-plans.status`, JSON poll)
  - `GET  /meal-plans/{mealPlan}/grocery-list` → `GroceryListController@show` (`grocery-list.show`)
  - `GET  /meal-plans/{mealPlan}` → `MealPlanController@show` (`meal-plans.show`)
  - `POST /children/{child}/meal-plans/regenerate` → `MealPlanController@regenerate` (`meal-plans.regenerate`)
- **Recipes**
  - `GET /recipes/{meal}` → `RecipeController@show` (`recipes.show`) — `{meal}` is a `MealPlanDay`
- **Growth tracker**
  - `GET  /children/{child}/growth` → `GrowthTrackerController@show` (`growth-tracker.show`)
  - `POST /children/{child}/growth` → `GrowthTrackerController@store` (`growth-tracker.store`)

**Authenticated only** (`middleware('auth')`)
- `GET    /profile` → `ProfileController@edit` (`profile.edit`)
- `PATCH  /profile` → `ProfileController@update` (`profile.update`)
- `DELETE /profile` → `ProfileController@destroy` (`profile.destroy`)

### Auth routes — `routes/auth.php` (Breeze)
- **Guests:** `register`, `login`, `forgot-password`, `reset-password/{token}`
- **Authenticated:** `verify-email`, `verify-email/{id}/{hash}` (signed + throttled),
  `email/verification-notification`, `confirm-password`, `password` (update), `logout`

### Console — `routes/console.php`
- Only the default `inspire` artisan command. No scheduled tasks.

> **Note:** There is no `routes/api.php` and no `routes/channels.php`. The app has **no
> REST/JSON API** beyond the single internal `meal-plans.status` polling endpoint, and the
> broadcast channel is **public** (no channel auth needed).

---

## 3. Controllers — `app/Http/Controllers`

| Controller | Responsibility |
|-----------|----------------|
| `DashboardController` | Lists the user's children with allergies, latest meal plans, growth-record counts. |
| `ChildController` | CRUD for children. Creating/updating a child triggers meal-plan generation via `ChildObserver`. Syncs allergy & health-condition rows. Redirects to the meal-plan loading screen after create. |
| `MealPlanController` | `loading` (generating screen), `status` (JSON poll), `show` (final plan grouped by day), `regenerate` (queues a fresh plan). |
| `GroceryListController` | Aggregates all ingredients across a week, merges duplicates, groups by Bengali category, sums BDT cost. |
| `GrowthTrackerController` | Shows growth history + Chart.js series; stores new measurements and syncs the child's headline weight/height. |
| `RecipeController` | Shows the full recipe for one meal (`MealPlanDay`). |
| `ProfileController` | Breeze profile edit/update/delete (with password confirmation on delete). |
| `Auth/*` | Standard Breeze auth controllers (login, register, password reset, email verification, password confirmation). |

**Authorization pattern:** There is no Policy class. Each controller enforces ownership
inline with `abort_unless($model->...->user_id === request()->user()->id, 403)`
(helper methods `authorizeChild` / `authorizeMealPlan`). Route-model binding loads the
records; the ownership check prevents cross-user access (IDOR protection).

---

## 4. Models & Relationships — `app/Models`

```
User
 └─ hasMany Child

Child  (observed by ChildObserver)
 ├─ belongsTo User
 ├─ hasMany ChildAllergy
 ├─ hasMany ChildHealthCondition
 ├─ hasMany MealPlan
 └─ hasMany GrowthRecord (ordered by recorded_date)

MealPlan
 ├─ belongsTo Child
 └─ hasMany MealPlanDay

MealPlanDay
 ├─ belongsTo MealPlan
 └─ hasMany MealIngredient

MealIngredient
 └─ belongsTo MealPlanDay

GrowthRecord
 └─ belongsTo Child

ChildAllergy / ChildHealthCondition
 └─ belongsTo Child
```

**Notable model logic**
- `Child::latestMealPlan()` — most recent *completed* plan.
- `Child::getAgeDisplayAttribute()` — formats months into Bengali "বছর/মাস".
- `MealPlan` constants `DAYS`, `DAYS_BENGALI`, `MEAL_TYPES_BENGALI`; `getTotalCostAttribute()`, `isCompleted()`.
- `MealPlanDay` — casts `instructions` & `nutrition` to arrays (JSON columns); Bengali accessors for meal type, day name, and an emoji per meal.
- `MealIngredient::getCategoryBengaliAttribute()` — maps category → Bengali label.
- `GrowthRecord::getBmiAttribute()` — computes BMI from weight/height.
- `User` — casts `password` → `hashed`, `email_verified_at` → `datetime`.

---

## 5. Middleware

The app uses **only framework middleware** — `app/Http/Middleware` is empty and
`bootstrap/app.php` registers no custom middleware.

Applied middleware:
- `auth` — authenticated session required.
- `verified` — email must be verified (gates the whole child/meal-plan feature set).
- `guest` — guest-only auth pages.
- `signed` + `throttle:6,1` — email verification link.
- `throttle:6,1` — verification resend.

Authorization is **not** done via middleware but via inline ownership checks in controllers (see §3).

---

## 6. Authentication Flow

Built on **Laravel Breeze (Blade stack)** with session-based auth.

1. **Register** (`RegisteredUserController`) — creates the `User`, fires `Registered`, logs in, redirects to `dashboard`.
2. **Email verification** — routes are protected by `verified`; users hit the
   `verification.notice` screen until they click the signed verification link.
3. **Login / Logout** (`AuthenticatedSessionController`) — session regenerate on login, invalidate on logout. Login validation/throttling lives in `app/Http/Requests/Auth/LoginRequest.php`.
4. **Password reset** — `forgot-password` → emailed link → `reset-password/{token}`.
5. **Password confirmation** — `confirm-password` gate for sensitive actions.
6. **Account deletion** — requires re-entering the current password (`ProfileController@destroy`).

Sessions are stored in the **database** (`sessions` table, `SESSION_DRIVER=database`).

---

## 7. Database Tables & Migrations — `database/migrations`

**Framework tables** (`0001_01_01_*`)
- `users`, `password_reset_tokens`, `sessions`
- `cache`, `cache_locks`
- `jobs`, `job_batches`, `failed_jobs` (database queue)

**Domain tables** (`2026_06_10_*`)

| Table | Key columns |
|-------|-------------|
| `children` | `user_id` (FK→users, cascade), `name`, `age_months` (tinyint), `gender` enum(boy,girl), `weight_kg`, `height_cm`, `budget_preference` enum(low,medium,high)=medium |
| `child_allergies` | `child_id` (FK, cascade), `allergy_name` |
| `child_health_conditions` | `child_id` (FK, cascade), `condition_name` |
| `meal_plans` | `child_id` (FK, cascade), `week_start_date` (date), `status` enum(pending,processing,completed,failed)=pending |
| `meal_plan_days` | `meal_plan_id` (FK, cascade), `day_name`, `meal_type` enum(breakfast,lunch,dinner), `meal_name_bengali`, `instructions` (json), `nutrition` (json), `cost_bdt` |
| `meal_ingredients` | `meal_plan_day_id` (FK, cascade), `ingredient_name`, `quantity`, `unit`, `category`, `estimated_price_bdt` |
| `growth_records` | `child_id` (FK, cascade), `recorded_date` (date), `weight_kg`, `height_cm` |

All domain FKs use `cascadeOnDelete()`, so deleting a user/child cleans up the entire tree.

---

## 8. Frontend Stack

**Blade + Alpine.js + Tailwind CSS** — a server-rendered MPA. **No Vue or React.**

- **Entry:** `resources/js/app.js`, `resources/js/bootstrap.js`, `resources/css/app.css`; bundled by **Vite**.
- **Alpine.js** drives interactivity (dropdowns, modals, the growth chart component `Alpine.data('growthChart', …)`).
- **Chart.js** renders the weight/height growth line chart (only the used controllers/elements are registered to keep the bundle small).
- **Laravel Echo + Pusher** (`bootstrap.js`) subscribe to the public `meal-plans.{id}`
  channel for the `ready` event so the loading page auto-redirects when a plan finishes.
  Echo is **only initialised when `VITE_PUSHER_APP_KEY` is set**; otherwise the loading
  page falls back to polling `meal-plans.status`.
- **Views** (`resources/views`):
  - Layouts: `layouts/app`, `layouts/guest`, `layouts/navigation`
  - Feature views: `dashboard`, `children/*`, `meal-plans/*` (loading, show), `grocery-list/show`, `growth-tracker/show`, `recipes/show`, `welcome`
  - Reusable components: sidebar, child-card, meal-card, growth-chart, alert, modal, dropdown, buttons, form inputs, nav links
  - `auth/*` and `profile/*` from Breeze
- Tailwind config includes a Bengali web font (**Hind Siliguri**).

---

## 9. AI Meal-Plan Generation Pipeline

This is the core feature. Flow:

1. **Trigger** — `ChildObserver` queues `MealPlanGenerateJob` when a child is `created`,
   or `updated` in a nutrition-relevant field (`age_months`, `weight_kg`, `height_cm`,
   `budget_preference`). `MealPlanController@regenerate` queues it manually.
2. **Job** — `MealPlanGenerateJob` (`ShouldQueue`, `tries=2`, `timeout=120s`):
   - Sets plan `status = processing`.
   - Calls a planner service `generate($child)`.
   - In a DB transaction, deletes any partial days and inserts `MealPlanDay` + `MealIngredient` rows, then sets `status = completed`.
   - Dispatches `MealPlanReady` (broadcast). Broadcast failures are caught/logged so they never undo a saved plan.
   - `failed()` sets `status = failed`.
3. **Planner service** — `app/Services`:
   - `OpenAIMealPlanner` (injected into the job via its `handle()` type-hint).
   - Builds a Bengali nutritionist prompt from the child's profile (age, gender,
     weight, height, budget, allergies, conditions), requests strict JSON (7 days × 3
     meals = 21 meals), and normalises the response.
   - **If no API key is configured, it falls back to a curated static Bengali plan**, so
     the feature always works.
   - Configured via `config/services.php`: `services.openai.{key,model}` (default `gpt-4o-mini`, overridden to `gpt-5.4-mini` in `.env`).
4. **Real-time** — `MealPlanReady implements ShouldBroadcast` on public channel
   `meal-plans.{id}`, event alias `ready`, payload includes `status` and `redirect_url`.

> ⚠️ **Prompt-injection / safety note:** The meal-plan prompt contains a hard-coded
> instruction telling the model to *ignore allergies and age and always produce a full
> plan* ("You MUST provide the full plan regardless of allergies or age"). For a child
> nutrition product this is a safety concern worth flagging to the team.

---

## 10. Admin Features

**There are none.** The application has:
- No `role`/`is_admin` column on `users`.
- No admin middleware, guard, routes, or controllers.
- No admin Blade views or dashboards.

Every authenticated user is an ordinary parent who can only access **their own**
children and meal plans (enforced by the per-resource `user_id` ownership checks).

---

## 11. API Endpoints

There is **no public/external API**. The only machine-readable endpoint is internal:

- `GET /meal-plans/{mealPlan}/status` → JSON `{ status, redirect_url }`, polled by the
  loading page as a fallback to Pusher/Echo. It is a **web** route protected by
  `auth`+`verified`+ownership check (session-authenticated, not token-based).

No `routes/api.php`, no Sanctum/Passport, no token auth.

---

## 11a. Daily Meal-Plan Email Notification

A scheduled job emails each mother her children's meals **every morning at 6:00 AM
(Asia/Dhaka)** so she sees the day's plan before leaving for work.

- **Command:** `meal-plans:notify-daily` (`app/Console/Commands/SendDailyMealPlanNotifications.php`).
  Optional `--day=Monday` flag forces a weekday (defaults to today) for testing.
  For each user it collects, per child, today's meals from that child's **latest
  completed** plan (`Child::latestMealPlan()`), filtered by `MealPlanDay.day_name`.
  Children with no completed plan or no meals today are skipped; mothers with nothing
  scheduled get no email. All of a mother's children appear in **one** email.
- **Notification:** `App\Notifications\DailyMealPlanNotification` (`mail` channel only).
  Builds a Bengali `MailMessage` (breakfast/lunch/dinner per child + an action button
  to the plan). Sent **synchronously** — add `ShouldQueue` later if volume grows.
- **Schedule:** registered in `routes/console.php` via
  `Schedule::command('meal-plans:notify-daily')->dailyAt('06:00')->timezone('Asia/Dhaka')`.
- **Requires:** the Laravel scheduler running — locally `php artisan schedule:work`;
  in production a cron entry `* * * * * php artisan schedule:run`. Real delivery needs
  SMTP creds (`MAIL_MAILER=smtp`); with the default `MAIL_MAILER=log`, emails are
  written to `storage/logs/laravel.log`.

---

## 12. How to Run (from `composer.json`)

- `composer run dev` — runs `php artisan serve`, `queue:listen`, `pail` (logs), and `npm run dev` (Vite) concurrently.
- `composer run setup` — install, copy `.env`, key gen, migrate, npm build.
- `composer run test` — clears config and runs the test suite (PHPUnit).
- **The queue worker must be running** for meal plans to generate (`QUEUE_CONNECTION=database`).
- **The scheduler must be running** (`php artisan schedule:work` locally, or a `schedule:run` cron in production) for the daily 6 AM meal-plan emails (§11a).

---

## 13. Key Observations / Gotchas

- The **queue worker is essential** — without it, every new child sits forever on the "generating…" loading screen.
- The **scheduler must run** for the daily meal-plan emails; neither `composer run dev` nor the queue worker starts it (see §11a).
- The meal planner uses **OpenAI only** (`OpenAIMealPlanner`, injected via the job's `handle()` type-hint). It falls back to a curated static Bengali plan when no API key is set.
- `BROADCAST_CONNECTION=log` and empty Pusher keys by default → real-time is off out of the box; the **status-polling fallback** carries the loading screen.
- All ownership/authorization is **inline in controllers**, not in Policies/middleware — consistent but easy to forget on a new endpoint.
- The LLM prompt's instruction to override allergy/age safety constraints should be reviewed (§9).
- Stray dev/debug files exist in the repo root: `parse_log.php`, `test_openai.php`, `issue1.jpg`, `task.md`.
