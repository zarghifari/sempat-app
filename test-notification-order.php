<?php

/**
 * Test Notification Order - Module notification should appear AFTER lesson notifications
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Lesson;
use App\Models\Module;
use App\Events\LessonCompletedEvent;
use App\Events\ModuleCompletedEvent;
use Illuminate\Support\Facades\DB;

echo "=== Testing Notification Order ===\n\n";

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

// Dispatch lesson event (no delay)
echo "🚀 Dispatching LessonCompletedEvent (immediate)...\n";
event(new LessonCompletedEvent($user, $lesson));

// Dispatch module event (3 second delay)
echo "🚀 Dispatching ModuleCompletedEvent (3 second delay)...\n";
event(new ModuleCompletedEvent($user, $module, $module->course));

echo "\n✅ Events dispatched!\n";
echo "⏰ Lesson notification will be created immediately\n";
echo "⏰ Module notification will be created after 3 seconds\n";
echo "\n💡 Wait 10 seconds, then check notifications order...\n";

// Wait 10 seconds
echo "⏳ Waiting 10 seconds for queue processing...\n";
sleep(10);

// Check results
$notifications = DB::table('notifications')
    ->where('notifiable_id', $user->id)
    ->orderBy('created_at', 'DESC')
    ->limit(5)
    ->get(['id', 'type', 'created_at']);

echo "\n📋 Latest notifications (newest first):\n";
foreach ($notifications as $index => $notif) {
    $type = class_basename($notif->type);
    $time = \Carbon\Carbon::parse($notif->created_at)->format('H:i:s');
    echo ($index + 1) . ". [{$time}] {$type}\n";
}

$afterCount = DB::table('notifications')
    ->where('notifiable_id', $user->id)
    ->count();

echo "\n📊 Notifications after: {$afterCount}\n";
echo "📈 New notifications: " . ($afterCount - $beforeCount) . "\n";

// Check order
$latest = $notifications->first();
if ($latest && strpos($latest->type, 'ModuleCompletedNotification') !== false) {
    echo "\n✅ SUCCESS! Module notification is at the top (newest)\n";
} else {
    echo "\n⚠️  Module notification order might not be correct\n";
}

echo "\n=== Test Complete ===\n";
