# Khude Khabar — খুদে খাবার

**Khude Khabar** ("Little One's Food") is a Laravel 12 web application that generates
**weekly Bengali meal plans for children** using an LLM, tracks each child's growth, and
produces an aggregated grocery shopping list. The UI is fully in Bengali (Bangla) and all
costs are expressed in BDT (Bangladeshi Taka).

The app is designed to run **without any API credentials**: the meal planner falls back to
a curated static Bengali plan, and the loading screen polls a status endpoint when
Pusher/Echo is not configured.

---

## Features

- 👶 **Child profiles** — manage multiple children with age, gender, weight, height, budget
  preference, allergies, and health conditions.
- 🍱 **AI-generated meal plans** — a weekly plan (7 days × 3 meals = 21 meals) tailored to
  each child's profile, generated via OpenAI with a curated static Bengali fallback.
- 🛒 **Grocery list** — ingredients aggregated across the week, de-duplicated, grouped by
  Bengali category, with total BDT cost.
- 📈 **Growth tracker** — record weight/height over time and visualize trends with Chart.js
  (BMI computed automatically).
- 📧 **Daily email** — every morning at 6:00 AM (Asia/Dhaka) each parent gets that day's
  meals for all of their children.
- 🔒 **Per-user isolation** — every parent only sees their own children and plans, enforced
  by inline ownership checks (IDOR protection).

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 12 (PHP ^8.2) |
| Auth | Laravel Breeze (Blade stack), session-based |
| Frontend | Blade + Alpine.js + Tailwind CSS (server-rendered MPA) |
| Build tool | Vite 7 (`laravel-vite-plugin`) |
| Charts | Chart.js 4 |
| Real-time | Laravel Echo + Pusher (optional) |
| AI provider | OpenAI (`openai-php/laravel`) |
| Database | MySQL (`khude_khabar`) |
| Queue / Cache / Session | `database` driver |
| Broadcast | `log` by default (`pusher` when configured) |

Tailwind ships with the **Hind Siliguri** Bengali web font.

---

## Requirements

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL

---

## Getting Started

```bash
# 1. Install dependencies and scaffold the environment
composer run setup       # install + copy .env + key:gen + migrate + npm build

# 2. Run the full dev stack (server, queue worker, logs, Vite)
composer run dev
```

`composer run dev` runs `php artisan serve`, `queue:listen`, `pail` (logs), and
`npm run dev` concurrently.

> ⚠️ **The queue worker must be running** (`QUEUE_CONNECTION=database`) for meal plans to
> generate — otherwise a new child sits forever on the "generating…" loading screen.

### Daily email scheduler

The 6:00 AM meal-plan email requires the Laravel scheduler:

```bash
php artisan schedule:work        # local
# or in production, a cron entry:
# * * * * * php artisan schedule:run
```

Real delivery needs SMTP creds (`MAIL_MAILER=smtp`); with the default `MAIL_MAILER=log`,
emails are written to `storage/logs/laravel.log`. You can trigger it manually:

```bash
php artisan meal-plans:notify-daily          # today
php artisan meal-plans:notify-daily --day=Monday
```

---

## Configuration

Copy `.env.example` to `.env` and configure as needed. Key settings:

| Variable | Purpose |
|----------|---------|
| `OPENAI_API_KEY` | OpenAI key. **Optional** — without it the planner uses a static Bengali plan. |
| `OPENAI_MODEL` | Model for meal-plan generation (default `gpt-4o-mini`). |
| `QUEUE_CONNECTION=database` | Required for meal-plan generation jobs. |
| `BROADCAST_CONNECTION` | `log` (default) or `pusher` for real-time loading screen. |
| `VITE_PUSHER_APP_KEY` | When set, the loading screen uses Echo/Pusher; otherwise it polls. |
| `MAIL_MAILER` | `smtp` for real delivery, `log` to write emails to the log. |

---

## Architecture Overview

### Routes

- **Public:** `/` (welcome), `/up` (health check).
- **Auth + verified:** dashboard, children CRUD, meal plans, grocery list, recipes, growth
  tracker.
- **Auth only:** profile edit/update/delete.
- **Auth (Breeze):** register, login, password reset, email verification.

There is **no public REST/JSON API** — the only machine-readable endpoint is the internal
`meal-plans.status` polling route.

### Data model

```
User
 └─ Child (observed by ChildObserver)
     ├─ ChildAllergy
     ├─ ChildHealthCondition
     ├─ MealPlan
     │   └─ MealPlanDay
     │       └─ MealIngredient
     └─ GrowthRecord
```

All domain foreign keys use `cascadeOnDelete()`, so deleting a user or child cleans up the
entire tree.

### AI meal-plan pipeline

1. **Trigger** — `ChildObserver` queues `MealPlanGenerateJob` when a child is created or
   updated in a nutrition-relevant field; `MealPlanController@regenerate` queues it
   manually.
2. **Job** — `MealPlanGenerateJob` (`ShouldQueue`, `tries=2`, `timeout=120s`) sets the
   plan to `processing`, calls the planner, writes `MealPlanDay` + `MealIngredient` rows in
   a transaction, marks it `completed`, and broadcasts `MealPlanReady`.
3. **Planner** — `OpenAIMealPlanner` builds a Bengali nutritionist prompt from the child's
   profile, requests strict JSON, and normalizes the response — falling back to a curated
   static plan when no API key is set.
4. **Real-time** — `MealPlanReady` broadcasts on the public `meal-plans.{id}` channel so
   the loading screen auto-redirects when the plan finishes (with status polling as a
   fallback).

---

## Useful Commands

```bash
composer run dev      # full dev stack (server + queue + logs + Vite)
composer run setup    # first-time install & scaffold
composer run test     # clear config + run PHPUnit
php artisan schedule:work   # run the scheduler (daily emails)
```

---

## License

Built on the Laravel framework, which is open-sourced under the
[MIT license](https://opensource.org/licenses/MIT).
