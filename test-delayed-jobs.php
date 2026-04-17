<?php

/**
 * Test Delayed Job Dispatch for Notification Order
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Lesson;
use App\Models\Module;
use App\Events\LessonCompletedEvent;
use App\Jobs\DispatchModuleCompletedEvent;
use Illuminate\Support\Facades\DB;

echo "=== Testing Delayed Job Dispatch ===\n\n";

$user = User::first();
$lesson = Lesson::first();
$module = Module::first();

if (!$user || !$lesson || !$module) {
    echo "❌ Missing test data\n";
    exit(1);
}

echo "✅ User: {$user->name}\n";
echo "✅ Lesson: {$lesson->title}\n";
echo "✅ Module: {$module->title}\n\n";

$beforeCount = DB::table('notifications')
    ->where('notifiable_id', $user->id)
    ->count();

echo "📊 Notifications before: {$beforeCount}\n\n";

// Dispatch lesson event immediately
echo "🚀 Dispatching LessonCompletedEvent (immediate)...\n";
event(new LessonCompletedEvent($user, $lesson));

// Dispatch module event with 3 second delay via Job
echo "⏰ Dispatching ModuleCompletedEvent via Job (3 second delay)...\n";
DispatchModuleCompletedEvent::dispatch($user, $module, $module->course)
    ->delay(now()->addSeconds(3));

echo "\n✅ Dispatched!\n";
echo "📋 Lesson notification job: queued (immediate)\n";
echo "📋 Module notification job: queued (delayed 3 seconds)\n";

// Check jobs
$pendingJobs = DB::table('jobs')->count();
echo "\n💼 Pending jobs in queue: {$pendingJobs}\n";

echo "\n💡 Jobs will be processed in order:\n";
echo "   1. LessonCompletedEvent → LessonCompletedNotification (immediate)\n";
echo "   2. (wait 3 seconds)\n";
echo "   3. ModuleCompletedEvent → ModuleCompletedNotification (after 3s)\n";

echo "\n⏳ Wait 10 seconds and check results...\n";

// Wait for processing
sleep(10);

// Check results
$afterCount = DB::table('notifications')
    ->where('notifiable_id', $user->id)
    ->count();

$notifications = DB::table('notifications')
    ->where('notifiable_id', $user->id)
    ->orderBy('created_at', 'DESC')
    ->limit(5)
    ->get(['type', 'created_at']);

echo "\n📋 Latest notifications (newest first):\n";
foreach ($notifications as $index => $notif) {
    $type = str_replace('App\\Notifications\\', '', $notif->type);
    $time = \Carbon\Carbon::parse($notif->created_at)->format('H:i:s');
    $emoji = strpos($type, 'Module') !== false ? '📦' : '📄';
    echo ($index + 1) . ". [{$time}] $emoji {$type}\n";
}

echo "\n📊 Notifications after: {$afterCount}\n";
echo "📈 New notifications: " . ($afterCount - $beforeCount) . "\n";

// Verify order
if ($notifications->count() >= 2) {
    $latest = $notifications->first();
    $secondLatest = $notifications->skip(1)->first();
    
    if (strpos($latest->type, 'ModuleCompleted') !== false &&
        strpos($secondLatest->type, 'LessonCompleted') !== false) {
        
        $timeDiff = \Carbon\Carbon::parse($latest->created_at)
            ->diffInSeconds(\Carbon\Carbon::parse($secondLatest->created_at));
        
        echo "\n✅ SUCCESS! Correct order:\n";
        echo "   - ModuleCompleted is newest\n";
        echo "   - LessonCompleted is older\n";
        echo "   - Time difference: ~{$timeDiff} seconds\n";
    } else {
        echo "\n⚠️  Order may not be correct. Review timestamps above.\n";
    }
}

echo "\n=== Test Complete ===\n";
