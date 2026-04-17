<?php

/**
 * Manual trigger course completion notification for courses that were completed during syntax error
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Jobs\DispatchCourseCompletedEvent;
use Illuminate\Support\Facades\DB;

echo "=== Manual Course Completion Notification ===\n\n";

// Get enrollments that are completed but don't have course completion notifications
$completedEnrollments = DB::table('enrollments')
    ->where('progress_percentage', 100)
    ->where('status', 'completed')
    ->get();

echo "Found {$completedEnrollments->count()} completed enrollments\n\n";

foreach ($completedEnrollments as $enrollment) {
    $user = User::find($enrollment->user_id);
    $course = Course::find($enrollment->course_id);
    
    if (!$user || !$course) {
        echo "⚠️  Skip enrollment {$enrollment->id}: User or Course not found\n";
        continue;
    }
    
    // Check if notification already exists
    $exists = DB::table('notifications')
        ->where('type', 'App\\Notifications\\CourseCompletedNotification')
        ->where('notifiable_id', $user->id)
        ->whereRaw("JSON_EXTRACT(data, '$.course_id') = ?", [$course->id])
        ->exists();
    
    if ($exists) {
        echo "✅ User {$user->name} - Course '{$course->title}': Notification already exists\n";
        continue;
    }
    
    echo "🚀 Creating notification for User {$user->name} - Course '{$course->title}'\n";
    
    // Dispatch course completed event with delay
    DispatchCourseCompletedEvent::dispatch($user, $course)
        ->delay(now()->addSeconds(2));
    
    echo "   ✓ Job dispatched (2 second delay)\n";
}

echo "\n💡 Wait 10 seconds for queue processing...\n";
sleep(10);

echo "\n📋 Course completion notifications:\n";
$courseNotifs = DB::table('notifications')
    ->where('type', 'App\\Notifications\\CourseCompletedNotification')
    ->orderBy('created_at', 'DESC')
    ->get();

foreach ($courseNotifs as $notif) {
    $data = json_decode($notif->data);
    $time = date('H:i:s', strtotime($notif->created_at));
    echo "  [{$time}] {$data->course_title}\n";
}

echo "\n✅ Total course notifications: {$courseNotifs->count()}\n";
echo "\n=== Done ===\n";
