<?php

namespace App\Listeners;

use App\Events\LessonCompletedEvent;
use App\Notifications\LessonCompletedNotification;
use Illuminate\Support\Facades\DB;

class SendLessonCompletedNotification
{
    /**
     * Track which user+lesson combinations have been processed in this request
     */
    private static array $processed = [];
    
    /**
     * Handle the event.
     */
    public function handle(LessonCompletedEvent $event): void
    {
        $key = "{$event->user->id}_{$event->lesson->id}";
        
        // ✅ FIRST CHECK: Static in-memory check for this request cycle
        if (isset(self::$processed[$key])) {
            \Log::info("⚠️  [STATIC] Duplicate prevented - already processed in this request cycle: User {$event->user->id}, Lesson {$event->lesson->id}");
            return;
        }
        
        // Mark as processed for this request
        self::$processed[$key] = true;
        
        \Log::info("🔔 Lesson notification triggered for User: {$event->user->id}, Lesson: {$event->lesson->id}");
        
        // ✅ SECOND CHECK: Database check for recent notifications
        $recentNotification = DB::table('notifications')
            ->where('type', LessonCompletedNotification::class)
            ->where('notifiable_id', $event->user->id)
            ->where('notifiable_type', get_class($event->user))
            ->where('created_at', '>', now()->subSeconds(5))
            ->whereRaw("JSON_EXTRACT(data, '$.lesson_id') = ?", [$event->lesson->id])
            ->exists();
        
        if ($recentNotification) {
            \Log::info("⚠️  [DATABASE] Duplicate prevented - notification already exists: User {$event->user->id}, Lesson {$event->lesson->id}");
            return;
        }
        
        \Log::info("✅ Sending lesson notification for User: {$event->user->id}, Lesson: {$event->lesson->id}");
        
        // Send notification (notification itself will be queued)
        $event->user->notify(
            new LessonCompletedNotification($event->lesson, $event->progress)
        );
    }
}
