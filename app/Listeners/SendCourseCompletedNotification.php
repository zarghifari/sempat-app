<?php

namespace App\Listeners;

use App\Events\CourseCompletedEvent;
use App\Notifications\CourseCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCourseCompletedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(CourseCompletedEvent $event): void
    {
        $event->user->notify(
            new CourseCompletedNotification($event->course)
        );
    }
}
