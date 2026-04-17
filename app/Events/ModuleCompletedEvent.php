<?php

namespace App\Events;

use App\Models\Module;
use App\Models\Course;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModuleCompletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Module $module;
    public Course $course;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, Module $module, Course $course)
    {
        $this->user = $user;
        $this->module = $module;
        $this->course = $course;
    }
}
