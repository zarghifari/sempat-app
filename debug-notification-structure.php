<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "🔍 Debugging Notification Data Structure...\n\n";

$user = User::find(4);

// Get one notification
$notification = $user->notifications()->first();

if (!$notification) {
    echo "❌ No notifications found!\n";
    exit(1);
}

echo "Sample Notification:\n";
echo str_repeat('=', 80) . "\n";
echo "ID: {$notification->id}\n";
echo "Type: {$notification->type}\n";
echo "Notifiable ID: {$notification->notifiable_id}\n";
echo "Notifiable Type: {$notification->notifiable_type}\n";
echo "Created At: {$notification->created_at}\n";
echo "Read At: " . ($notification->read_at ?? 'NULL (unread)') . "\n";
echo "\nData Structure:\n";
echo str_repeat('-', 80) . "\n";

// Check if data is array or object
$data = $notification->data;
echo "Data Type: " . gettype($data) . "\n";

if (is_array($data)) {
    echo "Data Keys: " . implode(', ', array_keys($data)) . "\n";
    echo "\nFull Data:\n";
    print_r($data);
} else {
    echo "Data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
}

echo "\n" . str_repeat('=', 80) . "\n";

// Test accessing data like in view
echo "\nTesting View Access Patterns:\n";
echo str_repeat('-', 80) . "\n";

try {
    $title = $notification->data['title'] ?? 'MISSING TITLE';
    echo "✅ Title: $title\n";
} catch (\Exception $e) {
    echo "❌ Title Error: " . $e->getMessage() . "\n";
}

try {
    $message = $notification->data['message'] ?? 'MISSING MESSAGE';
    echo "✅ Message: $message\n";
} catch (\Exception $e) {
    echo "❌ Message Error: " . $e->getMessage() . "\n";
}

try {
    $icon = $notification->data['icon'] ?? 'MISSING ICON';
    echo "✅ Icon: $icon\n";
} catch (\Exception $e) {
    echo "❌ Icon Error: " . $e->getMessage() . "\n";
}

try {
    $url = $notification->data['url'] ?? 'NO URL';
    echo "✅ URL: $url\n";
} catch (\Exception $e) {
    echo "❌ URL Error: " . $e->getMessage() . "\n";
}

// Test diffForHumans
try {
    $ago = $notification->created_at->diffForHumans();
    echo "✅ Time Ago: $ago\n";
} catch (\Exception $e) {
    echo "❌ Time Ago Error: " . $e->getMessage() . "\n";
}

echo "\n✅ All data access tests completed!\n";
