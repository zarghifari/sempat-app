<?php

namespace App\Events;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LessonCompletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Lesson $lesson;
    public ?int $progress;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, Lesson $lesson, ?int $progress = null)
    {
        $this->user = $user;
        $this->lesson = $lesson;
        $this->progress = $progress;
    }
}
