<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register notification events
        Event::listen(
            \App\Events\LessonCompletedEvent::class,
            \App\Listeners\SendLessonCompletedNotification::class,
        );

        Event::listen(
            \App\Events\CourseCompletedEvent::class,
            \App\Listeners\SendCourseCompletedNotification::class,
        );

        Event::listen(
            \App\Events\DailyStudyGoalReachedEvent::class,
            \App\Listeners\SendDailyStudyGoalNotification::class,
        );

        Event::listen(
            \App\Events\StudyStreakMilestoneEvent::class,
            \App\Listeners\SendStudyStreakNotification::class,
        );
    }
}
