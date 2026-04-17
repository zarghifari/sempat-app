<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Checking lesson notification details...\n\n";

// Get lesson notifications with full data
$notifications = DB::table('notifications')
    ->where('type', 'App\\Notifications\\LessonCompletedNotification')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

echo "Recent 10 Lesson Completed notifications:\n";
echo str_repeat('-', 100) . "\n";

foreach ($notifications as $n) {
    $data = json_decode($n->data);
    $lesson = DB::table('lessons')->where('id', $data->lesson_id ?? 0)->first();
    
    echo sprintf(
        "%-40s | User: %d | Lesson ID: %s | %s\n",
        $data->title ?? 'Unknown',
        $n->notifiable_id,
        $data->lesson_id ?? 'N/A',
        $lesson->title ?? 'Lesson not found',
        \Carbon\Carbon::parse($n->created_at)->format('Y-m-d H:i:s')
    );
}

echo "\n" . str_repeat('-', 100) . "\n";

// Check if same lesson_id appears multiple times
echo "\nGrouping by lesson_id:\n";
$lessonCounts = [];
$allNotifs = DB::table('notifications')
    ->where('type', 'App\\Notifications\\LessonCompletedNotification')
    ->get();

foreach ($allNotifs as $n) {
    $data = json_decode($n->data);
    $lessonId = $data->lesson_id ?? 'unknown';
    $key = "Lesson_" . $lessonId . "_User_" . $n->notifiable_id;
    
    if (!isset($lessonCounts[$key])) {
        $lessonCounts[$key] = 0;
    }
    $lessonCounts[$key]++;
}

$duplicates = array_filter($lessonCounts, fn($count) => $count > 1);

if (empty($duplicates)) {
    echo "✅ No duplicate notifications for same lesson!\n";
} else {
    echo "⚠️  Found duplicate notifications for same lessons:\n";
    foreach ($duplicates as $key => $count) {
        echo "   $key: $count times\n";
    }
}
