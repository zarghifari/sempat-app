<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "==========================================\n";
echo "🔔 Notification Status Check\n";
echo "==========================================\n\n";

// Total notifications
$totalNotifications = DB::table('notifications')->count();
echo "Total notifications: {$totalNotifications}\n";

// Jobs in queue
$queuedJobs = DB::table('jobs')->count();
echo "Jobs in queue: {$queuedJobs}\n";

// Failed jobs
$failedJobs = DB::table('failed_jobs')->count();
echo "Failed jobs: {$failedJobs}\n\n";

// Recent 10 notifications
echo "Recent 10 notifications:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$notifications = DB::table('notifications')
    ->latest('created_at')
    ->limit(10)
    ->get();

foreach ($notifications as $notification) {
    $data = json_decode($notification->data, true);
    $title = $data['title'] ?? 'N/A';
    $type = $data['type'] ?? 'unknown';
    $icon = $data['icon'] ?? '📬';
    $readStatus = $notification->read_at ? '[READ]' : '[UNREAD]';
    $time = \Carbon\Carbon::parse($notification->created_at)->diffForHumans();
    
    echo "{$icon} {$title} ({$type}) - {$time} {$readStatus}\n";
}

echo "\n";

// Check by type
echo "Notifications by type:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$notificationsByType = DB::table('notifications')
    ->selectRaw("JSON_EXTRACT(data, '$.type') as type, COUNT(*) as count")
    ->groupBy('type')
    ->get();

foreach ($notificationsByType as $row) {
    $type = str_replace('"', '', $row->type);
    echo "- {$type}: {$row->count}\n";
}

echo "\n";

// Recommendation
if ($queuedJobs > 0) {
    echo "⚠️  WARNING: {$queuedJobs} jobs still in queue!\n";
    echo "   Run: php artisan queue:work\n\n";
} else {
    echo "✅ Queue is empty\n\n";
}

if ($totalNotifications > 0) {
    echo "✅ Notifications exist in database\n";
    echo "   Check browser notification bell component\n\n";
} else {
    echo "❌ No notifications found\n";
    echo "   Complete a lesson to trigger notification\n\n";
}
