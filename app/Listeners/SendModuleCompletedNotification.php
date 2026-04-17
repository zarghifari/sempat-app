<?php

namespace App\Listeners;

use App\Events\ModuleCompletedEvent;
use App\Notifications\ModuleCompletedNotification;
use Illuminate\Support\Facades\DB;

class SendModuleCompletedNotification
{
    /**
     * Track which user+module combinations have been processed in this request
     */
    private static array $processed = [];
    
    /**
     * Handle the event.
     */
    public function handle(ModuleCompletedEvent $event): void
    {
        $key = "{$event->user->id}_{$event->module->id}";
        
        // ✅ FIRST CHECK: Static in-memory check
        if (isset(self::$processed[$key])) {
            \Log::info("⚠️  [STATIC] Duplicate module notification prevented: User {$event->user->id}, Module {$event->module->id}");
            return;
        }
        
        self::$processed[$key] = true;
        
        // ✅ SECOND CHECK: Database check
        $recentNotification = DB::table('notifications')
            ->where('type', ModuleCompletedNotification::class)
            ->where('notifiable_id', $event->user->id)
            ->where('notifiable_type', get_class($event->user))
            ->where('created_at', '>', now()->subSeconds(10))
            ->whereRaw("JSON_EXTRACT(data, '$.module_id') = ?", [$event->module->id])
            ->exists();
        
        if ($recentNotification) {
            \Log::info("⚠️  [DATABASE] Duplicate module notification prevented: Module {$event->module->id}");
            return;
        }
        
        // Create notification immediately (delay is handled at job dispatch level)
        $event->user->notify(new ModuleCompletedNotification(
            $event->module,
            $event->course
        ));
    }
}
