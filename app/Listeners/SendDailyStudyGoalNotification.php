<?php

namespace App\Listeners;

use App\Events\DailyStudyGoalReachedEvent;
use App\Notifications\DailyStudyGoalReachedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendDailyStudyGoalNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(DailyStudyGoalReachedEvent $event): void
    {
        $event->user->notify(
            new DailyStudyGoalReachedNotification($event->studyMinutes, $event->goalMinutes)
        );
    }
}
