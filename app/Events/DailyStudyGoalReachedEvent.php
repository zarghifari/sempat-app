<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DailyStudyGoalReachedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public int $studyMinutes;
    public int $goalMinutes;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, int $studyMinutes, int $goalMinutes)
    {
        $this->user = $user;
        $this->studyMinutes = $studyMinutes;
        $this->goalMinutes = $goalMinutes;
    }
}
