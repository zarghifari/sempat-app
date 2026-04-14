<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudyStreakMilestoneEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public int $streakDays;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, int $streakDays)
    {
        $this->user = $user;
        $this->streakDays = $streakDays;
    }
}
