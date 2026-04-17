<?php

/**
 * Test Production Notification System
 * Upload this file to server and run: php test-production-notification.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Lesson;
use App\Models\Course;
use App\Events\LessonCompletedEvent;
use Illuminate\Support\Facades\DB;

echo "=== Testing Production Notification System ===\n\n";

// Get first user
$user = User::first();
if (!$user) {
    echo "❌ No users found in database\n";
    exit(1);
}

echo "✅ User: {$user->name} (ID: {$user->id})\n";

// Get first lesson
$lesson = Lesson::first();
if (!$lesson) {
    echo "❌ No lessons found in database\n";
    exit(1);
}

echo "✅ Lesson: {$lesson->title} (ID: {$lesson->id})\n\n";

// Count notifications before
$beforeCount = DB::table('notifications')
    ->where('notifiable_id', $user->id)
    ->count();
    
echo "📊 Notifications before: {$beforeCount}\n";

// Dispatch event
echo "🚀 Dispatching LessonCompletedEvent...\n";
event(new LessonCompletedEvent($user, $lesson));

echo "✅ Event dispatched!\n\n";

// Check jobs queue
$jobsCount = DB::table('jobs')->count();
echo "📋 Jobs in queue: {$jobsCount}\n";

if ($jobsCount > 0) {
    echo "✅ Notification job queued - worker will process it shortly\n";
    echo "💡 Run: php artisan queue:work --stop-when-empty\n";
} else {
    echo "⚠️  No jobs in queue - checking if notification was created directly...\n";
    
    // Wait a second and check
    sleep(2);
    
    $afterCount = DB::table('notifications')
        ->where('notifiable_id', $user->id)
        ->count();
        
    echo "📊 Notifications after: {$afterCount}\n";
    
    if ($afterCount > $beforeCount) {
        echo "✅ Notification created! (+".($afterCount - $beforeCount).")\n";
    } else {
        echo "❌ No notification created\n";
    }
}

echo "\n=== Test Complete ===\n";
