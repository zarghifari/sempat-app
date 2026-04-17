<?php

/**
 * Quick test to create one lesson + one module notification
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Lesson;
use App\Models\Module;
use App\Events\LessonCompletedEvent;
use App\Jobs\DispatchModuleCompletedEvent;

$user = User::first();
$lesson = Lesson::first();
$module = Module::first();

echo "Creating notifications...\n";

// Lesson notification (immediate)
event(new LessonCompletedEvent($user, $lesson));
echo "✅ Lesson event dispatched\n";

// Module notification (3 second delay)
DispatchModuleCompletedEvent::dispatch($user, $module, $module->course)
    ->delay(now()->addSeconds(3));
echo "✅ Module job dispatched (3s delay)\n";

echo "\nWait 10 seconds for processing...\n";
sleep(10);

echo "\nFetching from API...\n";

// Simulate API call
$notifications = $user->notifications()
    ->latest()
    ->limit(10)
    ->get()
    ->sortByDesc(function($notification) {
        $data = $notification->data;
        $sortTimestamp = $data['sort_timestamp'] ?? $notification->created_at->timestamp;
        $priority = $data['priority'] ?? 0;
        return $sortTimestamp * 1000 - $priority;
    })
    ->take(5)
    ->values();

echo "\n📋 Notifications (sorted by API logic):\n";
foreach ($notifications as $index => $notif) {
    $data = $notif->data;
    $type = $data['type'] ?? 'unknown';
    $priority = $data['priority'] ?? 'none';
    $sortTs = $data['sort_timestamp'] ?? 'none';
    $time = $notif->created_at->format('H:i:s');
    
    $emoji = $type === 'module_completed' ? '📦' : '📄';
    echo ($index + 1) . ". [$time] $emoji $type (priority: $priority, sort_ts: $sortTs)\n";
}

echo "\n✅ Done!\n";
