<?php

namespace App\Notifications;

use App\Models\MealPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * The "today's meals" digest emailed to a mother every morning.
 *
 * Sent synchronously — a once-a-day batch is small. If the user base grows,
 * implement `ShouldQueue` (a queue worker already runs for meal-plan generation)
 * to push delivery onto the database queue.
 */
class DailyMealPlanNotification extends Notification
{
    use Queueable;

    /**
     * @param  array<int, array{child: \App\Models\Child, plan: MealPlan, meals: array<string, string>}>  $entries
     *   One entry per child that has at least one meal scheduled for today.
     */
    public function __construct(public array $entries)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $today = MealPlan::DAYS_BENGALI[now()->format('l')] ?? now()->format('l');

        $mail = (new MailMessage)
            ->subject("আজকের ({$today}) খাবারের তালিকা — খুদে খাবার")
            ->greeting("সুপ্রভাত, {$notifiable->name}!")
            ->line("আজ {$today}। আপনার সন্তানের জন্য আজকের খাবারের তালিকা নিচে দেওয়া হলো।");

        foreach ($this->entries as $entry) {
            $child = $entry['child'];

            $mail->line("**👶 {$child->name} — আজকের খাবার:**");

            foreach (MealPlan::MEAL_TYPES_BENGALI as $type => $label) {
                if (! empty($entry['meals'][$type])) {
                    $emoji = $this->mealEmoji($type);
                    $mail->line("{$emoji} {$label}: {$entry['meals'][$type]}");
                }
            }
        }

        // Link to the first child's current plan so she can open the full menu.
        $firstPlan = $this->entries[0]['plan'] ?? null;
        if ($firstPlan) {
            $mail->action('আজকের মেন্যু দেখুন', route('meal-plans.show', $firstPlan));
        }

        return $mail->salutation('শুভকামনা,
খুদে খাবার টিম');
    }

    protected function mealEmoji(string $type): string
    {
        return match ($type) {
            'breakfast' => '🌅',
            'lunch'     => '🍽️',
            'dinner'    => '🌙',
            default     => '🍴',
        };
    }
}
