<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\LessonCompletedEvent;
use App\Events\ModuleCompletedEvent;
use App\Events\CourseCompletedEvent;
use App\Events\DailyStudyGoalReachedEvent;
use App\Events\StudyStreakMilestoneEvent;
use App\Listeners\SendLessonCompletedNotification;
use App\Listeners\SendModuleCompletedNotification;
use App\Listeners\SendCourseCompletedNotification;
use App\Listeners\SendDailyStudyGoalNotification;
use App\Listeners\SendStudyStreakNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Lesson Completed
        LessonCompletedEvent::class => [
            SendLessonCompletedNotification::class,
        ],
        
        // Module Completed
        ModuleCompletedEvent::class => [
            SendModuleCompletedNotification::class,
        ],
        
        // Course Completed
        CourseCompletedEvent::class => [
            SendCourseCompletedNotification::class,
        ],
        
        // Daily Study Goal
        DailyStudyGoalReachedEvent::class => [
            SendDailyStudyGoalNotification::class,
        ],
        
        // Study Streak
        StudyStreakMilestoneEvent::class => [
            SendStudyStreakNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
