<?php

/**
 * Test Module & Course Completion Notifications
 * Run: php test-module-course-notification.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Events\ModuleCompletedEvent;
use App\Events\CourseCompletedEvent;

echo "==========================================\n";
echo "🧪 Test Module & Course Notifications\n";
echo "==========================================\n\n";

// Get first user
$user = User::first();

if (!$user) {
    echo "❌ No users found! Please create a user first.\n";
    exit(1);
}

echo "👤 Test User: {$user->first_name} {$user->last_name} (ID: {$user->id})\n\n";

// Get first course
$course = Course::first();

if (!$course) {
    echo "❌ No courses found! Please create a course first.\n";
    exit(1);
}

echo "📚 Test Course: {$course->title}\n\n";

// Get first module
$module = Module::where('course_id', $course->id)->first();

if (!$module) {
    echo "❌ No modules found in this course!\n";
    exit(1);
}

echo "📖 Test Module: {$module->title}\n\n";

// Test Module Completed Event
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🧪 Testing Module Completed Event...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

try {
    event(new ModuleCompletedEvent($user, $module, $course));
    echo "✅ ModuleCompletedEvent dispatched!\n";
    echo "   Listener: SendModuleCompletedNotification\n";
    echo "   Status: Queued (ShouldQueue)\n\n";
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n\n";
}

// Test Course Completed Event
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🧪 Testing Course Completed Event...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

try {
    event(new CourseCompletedEvent($user, $course));
    echo "✅ CourseCompletedEvent dispatched!\n";
    echo "   Listener: SendCourseCompletedNotification\n";
    echo "   Status: Queued (ShouldQueue)\n\n";
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n\n";
}

// Check queue
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 Queue Status\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$queuedJobs = DB::table('jobs')->count();
echo "Jobs in queue: {$queuedJobs}\n\n";

if ($queuedJobs > 0) {
    echo "⚠️  Notifications are queued!\n";
    echo "   Run queue worker to process:\n";
    echo "   php artisan queue:work\n\n";
} else {
    echo "✅ Queue is empty (jobs might be processed already)\n\n";
}

// Check notifications
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔔 Notifications Check\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "⏳ Waiting 3 seconds for queue to process...\n";
sleep(3);

$notifications = $user->notifications()->latest()->take(5)->get();

echo "\nRecent notifications for {$user->first_name}:\n";
echo "Total: {$notifications->count()}\n\n";

foreach ($notifications as $notification) {
    $icon = $notification->data['icon'] ?? '📬';
    $title = $notification->data['title'] ?? 'Notification';
    $time = $notification->created_at->diffForHumans();
    $read = $notification->read_at ? '[READ]' : '[UNREAD]';
    
    echo "{$icon} {$title} - {$time} {$read}\n";
}

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Test Complete!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "Next steps:\n";
echo "1. Run queue worker: php artisan queue:work\n";
echo "2. Complete a full course in the app\n";
echo "3. Check notification bell for updates\n\n";
