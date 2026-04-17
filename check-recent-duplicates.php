<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Checking for recent duplicate lesson notifications...\n\n";

// Get the most recent lesson notifications
$notifications = DB::table('notifications')
    ->where('type', 'App\Notifications\LessonCompletedNotification')
    ->orderBy('created_at', 'desc')
    ->limit(15)
    ->get();

echo "Recent 15 Lesson Notifications:\n";
echo str_repeat('-', 100) . "\n";

$grouped = [];

foreach ($notifications as $n) {
    $data = json_decode($n->data);
    $lessonName = $data->lesson_name ?? 'Unknown';
    $lessonId = $data->lesson_id ?? 'N/A';
    $createdAt = \Carbon\Carbon::parse($n->created_at);
    $ago = $createdAt->diffForHumans();
    
    $key = "User_{$n->notifiable_id}_Lesson_{$lessonId}";
    
    if (!isset($grouped[$key])) {
        $grouped[$key] = [];
    }
    
    $grouped[$key][] = [
        'id' => $n->id,
        'lesson_name' => $lessonName,
        'created_at' => $n->created_at,
        'ago' => $ago
    ];
    
    echo sprintf(
        "%-40s | User: %d | Lesson ID: %s | %s | %s\n",
        $lessonName,
        $n->notifiable_id,
        $lessonId,
        $createdAt->format('Y-m-d H:i:s'),
        $ago
    );
}

echo "\n" . str_repeat('-', 100) . "\n";
echo "Duplicate Analysis (same user + lesson):\n";
echo str_repeat('-', 100) . "\n";

foreach ($grouped as $key => $items) {
    if (count($items) > 1) {
        echo "⚠️  $key: " . count($items) . " notifications\n";
        foreach ($items as $item) {
            echo "   - ID: {$item['id']} | {$item['lesson_name']} | {$item['created_at']} ({$item['ago']})\n";
        }
        
        // Check time difference
        if (count($items) >= 2) {
            $first = \Carbon\Carbon::parse($items[0]['created_at']);
            $second = \Carbon\Carbon::parse($items[1]['created_at']);
            $diff = $first->diffInSeconds($second);
            echo "   ⏱️  Time difference: {$diff} seconds\n";
        }
        echo "\n";
    }
}

// Check if listener code has duplicate prevention
echo str_repeat('-', 100) . "\n";
echo "Checking listener file for duplicate prevention...\n";

$listenerFile = __DIR__ . '/app/Listeners/SendLessonCompletedNotification.php';
$content = file_get_contents($listenerFile);

if (strpos($content, 'Prevent duplicate notifications') !== false) {
    echo "✅ Duplicate prevention code EXISTS in listener\n";
} else {
    echo "❌ Duplicate prevention code MISSING in listener\n";
}

if (strpos($content, 'whereRaw') !== false) {
    echo "✅ JSON_EXTRACT check EXISTS\n";
} else {
    echo "❌ JSON_EXTRACT check MISSING\n";
}
