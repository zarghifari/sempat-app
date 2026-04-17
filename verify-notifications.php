<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "==========================================\n";
echo "📊 Notification Database Verification\n";
echo "==========================================\n\n";

$user = User::where('email', 'test@sempat.test')->first();

if (!$user) {
    echo "❌ Test user not found\n";
    exit(1);
}

echo "User: {$user->email}\n";
echo "Total notifications: " . $user->notifications()->count() . "\n\n";

// Get all notifications
$notifications = $user->notifications()->latest()->get();

if ($notifications->isEmpty()) {
    echo "⚠️  No notifications found. Queue may not be processed yet.\n";
    echo "Run: php artisan queue:work\n";
} else {
    echo "✅ Notifications successfully created!\n\n";
    
    foreach ($notifications as $index => $notification) {
        $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
        
        echo "Notification #" . ($index + 1) . ":\n";
        echo "  ID: {$notification->id}\n";
        echo "  Type: {$notification->type}\n";
        echo "  Title: {$data['title']}\n";
        echo "  Message: {$data['message']}\n";
        echo "  Icon: {$data['icon']}\n";
        echo "  URL: {$data['url']}\n";
        echo "  Created: {$notification->created_at->format('Y-m-d H:i:s')} ({$notification->created_at->diffForHumans()})\n";
        echo "  Read: " . ($notification->read_at ? 'Yes - ' . $notification->read_at->format('Y-m-d H:i:s') : 'No (Unread)') . "\n";
        echo "  ---\n\n";
    }
}

// Check queue status
$pendingJobs = DB::table('jobs')->count();
$failedJobs = DB::table('failed_jobs')->count();

echo "Queue Status:\n";
echo "  Pending jobs: {$pendingJobs}\n";
echo "  Failed jobs: {$failedJobs}\n\n";

// Test marking as read
if ($notifications->isNotEmpty()) {
    echo "Testing mark as read functionality...\n";
    $firstNotification = $notifications->first();
    
    if (!$firstNotification->read_at) {
        $firstNotification->markAsRead();
        echo "✅ Marked notification as read\n";
        echo "  Read at: {$firstNotification->read_at->format('Y-m-d H:i:s')}\n";
    } else {
        echo "  Already read\n";
    }
}

echo "\n==========================================\n";
echo "✅ Verification Complete\n";
echo "==========================================\n";
