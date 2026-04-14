<?php

namespace App\Listeners;

use App\Events\StudyStreakMilestoneEvent;
use App\Notifications\StudyStreakMilestoneNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendStudyStreakNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(StudyStreakMilestoneEvent $event): void
    {
        $event->user->notify(
            new StudyStreakMilestoneNotification($event->streakDays)
        );
    }
}
