<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Events\LessonCompletedEvent;
use App\Models\User;
use App\Models\Lesson;

echo "🧪 Final Test - Duplicate Prevention...\n\n";

// Clear log file  
file_put_contents(storage_path('logs/laravel.log'), '');

// Get a test user and different lesson
$user = User::find(4);
$lesson = Lesson::find(54); // NEW lesson ID

if (!$user || !$lesson) {
    echo "❌ User or Lesson not found!\n";
    exit(1);
}

echo "Testing with:\n";
echo "  User ID: {$user->id}\n";
echo "  Lesson ID: {$lesson->id}\n";
echo "  Lesson: {$lesson->title}\n\n";

// Clean queue
DB::table('jobs')->delete();
echo "✅ Queue cleaned\n\n";

// Get count before
$countBefore = DB::table('notifications')
    ->where('type', 'App\Notifications\LessonCompletedNotification')
    ->where('notifiable_id', $user->id)
    ->whereRaw("JSON_EXTRACT(data, '$.lesson_id') = ?", [$lesson->id])
    ->count();

echo "📊 Notifications before: $countBefore\n\n";

// Dispatch event 3 times
echo "🚀 Dispatching LessonCompletedEvent 3 times (simulating triple-click)...\n";

for ($i = 1; $i <= 3; $i++) {
    event(new LessonCompletedEvent($user, $lesson));
    echo "  ✅ Event $i dispatched\n";
}

// Check queue
$jobsInQueue = DB::table('jobs')->count();
echo "\n⏳ Jobs in queue: $jobsInQueue\n";

// Process queue
echo "\n⚙️  Processing queue...\n";
\Artisan::call('queue:work', [
    '--stop-when-empty' => true,
    '--verbose' => true
]);

// Check count after
$countAfter = DB::table('notifications')
    ->where('type', 'App\Notifications\LessonCompletedNotification')
    ->where('notifiable_id', $user->id)
    ->whereRaw("JSON_EXTRACT(data, '$.lesson_id') = ?", [$lesson->id])
    ->count();

echo "\n📊 FINAL RESULTS:\n";
echo str_repeat('=', 60) . "\n";
echo "Notifications before: $countBefore\n";
echo "Notifications after:  $countAfter\n";
echo "New notifications:    " . ($countAfter - $countBefore) . "\n";
echo str_repeat('=', 60) . "\n";

if (($countAfter - $countBefore) === 1) {
    echo "\n✅✅✅ SUCCESS! Only 1 notification created\n";
    echo "Duplicate prevention is working!\n";
} else {
    echo "\n❌ FAIL! " . ($countAfter - $countBefore) . " notifications created\n";
}

// Show logs
echo "\n📋 Log entries:\n";
echo str_repeat('-', 60) . "\n";
$log = file_get_contents(storage_path('logs/laravel.log'));
$lines = explode("\n", $log);
foreach ($lines as $line) {
    if (str_contains($line, 'Lesson notification') || str_contains($line, 'Duplicate')) {
        echo "$line\n";
    }
}
