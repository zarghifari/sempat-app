<?php

namespace App\Listeners;

use App\Events\LessonCompletedEvent;
use App\Notifications\LessonCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendLessonCompletedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(LessonCompletedEvent $event): void
    {
        $event->user->notify(
            new LessonCompletedNotification($event->lesson, $event->progress)
        );
    }
}
