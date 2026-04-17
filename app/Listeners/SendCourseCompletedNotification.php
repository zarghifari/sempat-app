<?php

namespace App\Listeners;

use App\Events\CourseCompletedEvent;
use App\Notifications\CourseCompletedNotification;
use Illuminate\Support\Facades\DB;

class SendCourseCompletedNotification
{
    /**
     * Track which user+course combinations have been processed in this request
     */
    private static array $processed = [];
    
    /**
     * Handle the event.
     */
    public function handle(CourseCompletedEvent $event): void
    {
        $key = "{$event->user->id}_{$event->course->id}";
        
        // ✅ FIRST CHECK: Static in-memory check
        if (isset(self::$processed[$key])) {
            \Log::info("⚠️  [STATIC] Duplicate course notification prevented: User {$event->user->id}, Course {$event->course->id}");
            return;
        }
        
        self::$processed[$key] = true;
        
        // ✅ SECOND CHECK: Database check  
        $recentNotification = DB::table('notifications')
            ->where('type', CourseCompletedNotification::class)
            ->where('notifiable_id', $event->user->id)
            ->where('notifiable_type', get_class($event->user))
            ->where('created_at', '>', now()->subSeconds(10))
            ->whereRaw("JSON_EXTRACT(data, '$.course_id') = ?", [$event->course->id])
            ->exists();
        
        if ($recentNotification) {
            \Log::info("⚠️  [DATABASE] Duplicate course notification prevented: Course {$event->course->id}");
            return;
        }
        
        // Create notification immediately (delay is handled at job dispatch level)
        $event->user->notify(
            new CourseCompletedNotification($event->course)
        );
    }
}
