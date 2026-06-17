<?php

namespace App\Console\Commands;

use App\Models\Child;
use App\Models\User;
use App\Notifications\DailyMealPlanNotification;
use Illuminate\Console\Command;

class SendDailyMealPlanNotifications extends Command
{
    /**
     * --day lets you test any weekday (defaults to today, e.g. "Monday").
     */
    protected $signature = 'meal-plans:notify-daily {--day= : Weekday name to send for (defaults to today)}';

    protected $description = "Email each mother her children's meals for the day";

    public function handle(): int
    {
        $day = $this->option('day') ?: now()->format('l'); // Monday … Sunday

        $notified = 0;

        User::with('children')->each(function (User $user) use ($day, &$notified) {
            $entries = $user->children
                ->map(fn (Child $child) => $this->entryForChild($child, $day))
                ->filter()
                ->values()
                ->all();

            if (empty($entries)) {
                return; // nothing scheduled today for any of her children
            }

            $user->notify(new DailyMealPlanNotification($entries));
            $notified++;
        });

        $this->info("{$notified} mother(s) notified for {$day}.");

        return self::SUCCESS;
    }

    /**
     * Build the per-child digest entry, or null if the child has no completed
     * plan or no meals scheduled for the given day.
     *
     * @return array{child: Child, plan: \App\Models\MealPlan, meals: array<string, string>}|null
     */
    protected function entryForChild(Child $child, string $day): ?array
    {
        $plan = $child->latestMealPlan();

        if (! $plan) {
            return null;
        }

        $meals = $plan->mealPlanDays()
            ->where('day_name', $day)
            ->get()
            ->pluck('meal_name_bengali', 'meal_type') // [breakfast => "...", ...]
            ->filter()
            ->all();

        if (empty($meals)) {
            return null;
        }

        return ['child' => $child, 'plan' => $plan, 'meals' => $meals];
    }
}
