<?php

namespace App\Events;

use App\Models\MealPlan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MealPlanReady implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public MealPlan $mealPlan)
    {
    }

    /**
     * Public channel scoped to the meal plan so the loading page can listen
     * without needing private-channel authentication.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('meal-plans.'.$this->mealPlan->id);
    }

    public function broadcastAs(): string
    {
        return 'ready';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'meal_plan_id' => $this->mealPlan->id,
            'child_id' => $this->mealPlan->child_id,
            'status' => $this->mealPlan->status,
            'redirect_url' => route('meal-plans.show', $this->mealPlan),
        ];
    }
}
