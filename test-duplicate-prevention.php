<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Events\LessonCompletedEvent;
use App\Models\User;
use App\Models\Lesson;

echo "🧪 Testing Duplicate Prevention with Cache Lock...\n\n";

// Get a test user and lesson
$user = User::find(4);
$lesson = Lesson::find(53); // New lesson ID

if (!$user || !$lesson) {
    echo "❌ User or Lesson not found!\n";
    exit(1);
}

echo "Testing with:\n";
echo "  User ID: {$user->id}\n";
echo "  Lesson ID: {$lesson->id}\n";
echo "  Lesson: {$lesson->title}\n\n";

// Get count before
$countBefore = DB::table('notifications')
    ->where('type', 'App\Notifications\LessonCompletedNotification')
    ->where('notifiable_id', $user->id)
    ->whereRaw("JSON_EXTRACT(data, '$.lesson_id') = ?", [$lesson->id])
    ->count();

echo "📊 Notifications before: $countBefore\n\n";

// Dispatch event 3 times simultaneously (simulating duplicate clicks/race condition)
echo "🚀 Dispatching LessonCompletedEvent 3 times...\n";

for ($i = 1; $i <= 3; $i++) {
    event(new LessonCompletedEvent($user, $lesson));
    echo "  ✅ Event $i dispatched\n";
}

// Check queue
$jobsInQueue = DB::table('jobs')->count();
echo "\n⏳ Jobs in queue: $jobsInQueue\n";

// Wait for queue to process
echo "\n⏱️  Waiting 5 seconds for queue worker to process...\n";
sleep(5);

// Check count after
$countAfter = DB::table('notifications')
    ->where('type', 'App\Notifications\LessonCompletedNotification')
    ->where('notifiable_id', $user->id)
    ->whereRaw("JSON_EXTRACT(data, '$.lesson_id') = ?", [$lesson->id])
    ->count();

$jobsAfter = DB::table('jobs')->count();

echo "\n📊 RESULTS:\n";
echo str_repeat('-', 60) . "\n";
echo "Notifications before: $countBefore\n";
echo "Notifications after:  $countAfter\n";
echo "New notifications:    " . ($countAfter - $countBefore) . "\n";
echo "Jobs remaining:       $jobsAfter\n";
echo str_repeat('-', 60) . "\n";

if (($countAfter - $countBefore) === 1) {
    echo "\n✅ SUCCESS! Only 1 notification created (duplicates prevented)\n";
} elseif (($countAfter - $countBefore) > 1) {
    echo "\n❌ FAIL! " . ($countAfter - $countBefore) . " notifications created (duplicates NOT prevented)\n";
} else {
    echo "\n⚠️  WARNING! No new notifications created\n";
}

// Show recent notifications for this lesson
echo "\nRecent notifications for lesson {$lesson->id}:\n";
$recent = DB::table('notifications')
    ->where('type', 'App\Notifications\LessonCompletedNotification')
    ->where('notifiable_id', $user->id)
    ->whereRaw("JSON_EXTRACT(data, '$.lesson_id') = ?", [$lesson->id])
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get(['id', 'created_at']);

foreach ($recent as $n) {
    echo "  - {$n->created_at} | ID: " . substr($n->id, 0, 8) . "...\n";
}
