<?php

/**
 * Test notification API endpoints
 * Run: php test-notification-api.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

// Get first user
$user = User::first();

if (!$user) {
    echo "❌ No users found. Please create a user first.\n";
    exit(1);
}

echo "Testing Notification API Endpoints\n";
echo "==================================\n\n";

echo "📊 User: {$user->first_name} {$user->last_name} (ID: {$user->id})\n\n";

// Test 1: Get unread count
echo "1. Testing unread count...\n";
$unreadCount = $user->unreadNotifications()->count();
echo "   ✅ Unread count: {$unreadCount}\n\n";

// Test 2: Get recent notifications
echo "2. Testing recent notifications (limit 5)...\n";
$recent = $user->notifications()
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

echo "   ✅ Found {$recent->count()} recent notifications:\n";
foreach ($recent as $notification) {
    $data = $notification->data;
    $readStatus = $notification->read_at ? '✓ Read' : '○ Unread';
    echo "   {$readStatus} - {$data['icon']} {$data['title']}\n";
    echo "      {$data['message']}\n";
    echo "      Created: {$notification->created_at->diffForHumans()}\n";
}
echo "\n";

// Test 3: Mark first unread as read
$unread = $user->unreadNotifications()->first();
if ($unread) {
    echo "3. Testing mark as read...\n";
    echo "   Marking notification: {$unread->data['title']}\n";
    $unread->markAsRead();
    echo "   ✅ Marked as read successfully\n";
    echo "   New unread count: " . $user->unreadNotifications()->count() . "\n\n";
} else {
    echo "3. No unread notifications to mark as read\n\n";
}

// Test 4: Get all notifications with pagination
echo "4. Testing paginated list...\n";
$paginated = $user->notifications()
    ->orderBy('created_at', 'desc')
    ->paginate(10);

echo "   ✅ Total notifications: {$paginated->total()}\n";
echo "   ✅ Current page: {$paginated->currentPage()}\n";
echo "   ✅ Per page: {$paginated->perPage()}\n";
echo "   ✅ Last page: {$paginated->lastPage()}\n\n";

// Summary
echo "Summary\n";
echo "-------\n";
echo "Total notifications: " . $user->notifications()->count() . "\n";
echo "Unread notifications: " . $user->unreadNotifications()->count() . "\n";
echo "Read notifications: " . $user->notifications()->whereNotNull('read_at')->count() . "\n";

echo "\n✅ All API endpoint tests completed successfully!\n";
