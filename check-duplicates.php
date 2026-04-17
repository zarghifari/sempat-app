<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "📊 Checking for duplicate notifications...\n\n";

// Get all notifications, grouped by title and user
$notifications = DB::table('notifications')
    ->orderBy('created_at', 'desc')
    ->limit(20)
    ->get();

echo "Recent 20 notifications:\n";
echo str_repeat('-', 80) . "\n";

$counts = [];
foreach ($notifications as $n) {
    $data = json_decode($n->data);
    $title = $data->title ?? 'Unknown';
    $key = $title . '_' . $n->notifiable_id;
    
    if (!isset($counts[$key])) {
        $counts[$key] = 0;
    }
    $counts[$key]++;
    
    $time = \Carbon\Carbon::parse($n->created_at)->diffForHumans();
    $status = $n->read_at ? '✓ READ' : '○ UNREAD';
    
    echo sprintf(
        "[%d] %s | %s | %s | User: %s\n",
        $counts[$key],
        $title,
        $status,
        $time,
        $n->notifiable_id
    );
}

echo "\n📈 Duplicate Analysis:\n";
echo str_repeat('-', 80) . "\n";

$duplicates = array_filter($counts, fn($count) => $count > 1);

if (empty($duplicates)) {
    echo "✅ No duplicates found!\n";
} else {
    foreach ($duplicates as $key => $count) {
        echo "⚠️  '$key' appears $count times\n";
    }
}

echo "\n✅ Analysis complete!\n";
