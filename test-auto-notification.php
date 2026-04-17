<?php

/**
 * Test Automatic Notification on Lesson Completion
 * Run: php test-auto-notification.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonCompletion;

echo "==========================================\n";
echo "🧪 Test Automatic Notification System\n";
echo "==========================================\n\n";

// Get user
$user = User::first();
if (!$user) {
    echo "❌ No users found.\n";
    exit(1);
}

echo "📊 Testing for: {$user->first_name} {$user->last_name}\n\n";

// Check existing courses
$course = Course::where('status', 'published')->first();

if (!$course) {
    echo "⚠️  No published courses found. Creating test course...\n\n";
    
    // Create test course
    $course = Course::create([
        'uuid' => \Illuminate\Support\Str::uuid(),
        'title' => 'Automatic Notification Test Course',
        'slug' => 'auto-notif-test-' . time(),
        'description' => 'Course untuk testing automatic notifications',
        'status' => 'published',
        'published_at' => now(),
        'created_by' => $user->id,
    ]);
    
    // Create test module
    $module = Module::create([
        'uuid' => \Illuminate\Support\Str::uuid(),
        'course_id' => $course->id,
        'title' => 'Test Module - Notifications',
        'slug' => 'test-module-notif',
        'description' => 'Testing module',
        'order' => 1,
        'status' => 'published',
    ]);
    
    // Create test lessons
    for ($i = 1; $i <= 3; $i++) {
        Lesson::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'module_id' => $module->id,
            'title' => "Test Lesson {$i}",
            'slug' => "test-lesson-{$i}-" . time(),
            'content' => "Content for test lesson {$i}",
            'type' => 'article',
            'order' => $i,
            'status' => 'published',
            'estimated_minutes' => 5,
        ]);
    }
    
    echo "✅ Test course created with 1 module and 3 lessons\n\n";
} else {
    echo "✅ Using existing course: {$course->title}\n\n";
}

// Check enrollment
$enrollment = Enrollment::firstOrCreate(
    [
        'user_id' => $user->id,
        'course_id' => $course->id,
    ],
    [
        'uuid' => \Illuminate\Support\Str::uuid(),
        'status' => 'active',
        'enrolled_at' => now(),
    ]
);

echo "✅ Enrollment: {$enrollment->status}\n";
echo "   Progress: {$enrollment->progress_percentage}%\n\n";

// Get first incomplete lesson
$modules = Module::where('course_id', $course->id)->with('lessons')->get();
$totalLessons = 0;
$completedLessons = 0;
$nextLesson = null;

foreach ($modules as $module) {
    foreach ($module->lessons as $lesson) {
        $totalLessons++;
        
        $completion = LessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();
        
        if ($completion && $completion->status === 'completed') {
            $completedLessons++;
        } elseif (!$nextLesson) {
            $nextLesson = $lesson;
            $nextModule = $module;
        }
    }
}

echo "📚 Course Progress:\n";
echo "   Total Lessons: {$totalLessons}\n";
echo "   Completed: {$completedLessons}\n";
echo "   Remaining: " . ($totalLessons - $completedLessons) . "\n\n";

if (!$nextLesson) {
    echo "🎉 All lessons completed! Let's reset one lesson...\n\n";
    
    $lastCompletion = LessonCompletion::where('user_id', $user->id)
        ->where('enrollment_id', $enrollment->id)
        ->latest()
        ->first();
    
    if ($lastCompletion) {
        $lastCompletion->update([
            'status' => 'in_progress',
            'completed_at' => null,
            'progress_percentage' => 50,
        ]);
        
        $nextLesson = $lastCompletion->lesson;
        $nextModule = $nextLesson->module;
        
        echo "✅ Reset lesson: {$nextLesson->title}\n\n";
    }
}

if ($nextLesson) {
    echo "🎯 Simulating lesson completion...\n";
    echo "   Lesson: {$nextLesson->title}\n";
    echo "   Module: {$nextModule->title}\n\n";
    
    // Create or update completion
    $completion = LessonCompletion::firstOrCreate(
        [
            'user_id' => $user->id,
            'lesson_id' => $nextLesson->id,
            'enrollment_id' => $enrollment->id,
        ],
        [
            'uuid' => \Illuminate\Support\Str::uuid(),
            'started_at' => now(),
        ]
    );
    
    // Check current notifications count
    $beforeCount = $user->notifications()->count();
    echo "📬 Notifications before: {$beforeCount}\n";
    
    // Mark as completed - this should trigger automatic notification!
    $completion->update([
        'status' => 'completed',
        'progress_percentage' => 100,
        'completed_at' => now(),
    ]);
    
    // Manually trigger the event (simulating what LessonController does)
    event(new \App\Events\LessonCompletedEvent($user, $nextLesson));
    
    // Check if module is completed
    $moduleLessons = $nextModule->lessons()->count();
    $moduleCompleted = LessonCompletion::where('user_id', $user->id)
        ->where('enrollment_id', $enrollment->id)
        ->where('status', 'completed')
        ->whereHas('lesson', function($query) use ($nextModule) {
            $query->where('module_id', $nextModule->id);
        })
        ->count();
    
    if ($moduleCompleted >= $moduleLessons) {
        echo "🎉 Module completed! Triggering module notification...\n";
        event(new \App\Events\ModuleCompletedEvent($user, $nextModule, $course));
    }
    
    // Update enrollment progress
    $totalCourseLessons = Lesson::whereHas('module', function($query) use ($course) {
        $query->where('course_id', $course->id);
    })->count();
    
    $completedCourseLessons = LessonCompletion::where('user_id', $user->id)
        ->where('enrollment_id', $enrollment->id)
        ->where('status', 'completed')
        ->count();
    
    $progressPercentage = $totalCourseLessons > 0 
        ? round(($completedCourseLessons / $totalCourseLessons) * 100) 
        : 0;
    
    $enrollment->update([
        'progress_percentage' => $progressPercentage,
        'completed_lessons' => $completedCourseLessons,
    ]);
    
    // Check if course completed
    if ($progressPercentage >= 100 && $enrollment->status !== 'completed') {
        echo "🏆 Course completed! Triggering course notification...\n";
        $enrollment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        event(new \App\Events\CourseCompletedEvent($user, $course));
    }
    
    echo "\n⏳ Processing queued notifications...\n\n";
    
    // Process queue
    \Artisan::call('queue:work', ['--stop-when-empty' => true]);
    
    // Check notifications after
    $afterCount = $user->notifications()->count();
    echo "\n📬 Notifications after: {$afterCount}\n";
    echo "📧 New notifications: " . ($afterCount - $beforeCount) . "\n\n";
    
    if ($afterCount > $beforeCount) {
        echo "✅ Automatic notifications working!\n\n";
        
        $newNotifications = $user->notifications()
            ->latest()
            ->limit($afterCount - $beforeCount)
            ->get();
        
        echo "Latest notifications:\n";
        foreach ($newNotifications as $notif) {
            echo "  {$notif->data['icon']} {$notif->data['title']}\n";
            echo "     {$notif->data['message']}\n\n";
        }
    } else {
        echo "⚠️  No new notifications created. Check queue processing.\n";
    }
    
    echo "\n📊 Updated Progress:\n";
    echo "   Enrollment status: {$enrollment->fresh()->status}\n";
    echo "   Progress: {$enrollment->fresh()->progress_percentage}%\n";
    echo "   Completed lessons: {$completedCourseLessons}/{$totalCourseLessons}\n";
}

echo "\n✅ Test completed!\n";
echo "\n💡 Next steps:\n";
echo "1. Complete a real lesson in the app\n";
echo "2. Check notification bell for automatic notification\n";
echo "3. View at: http://127.0.0.1:8000\n";
