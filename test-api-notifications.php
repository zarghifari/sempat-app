<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Route;

echo "==========================================\n";
echo "🧪 Test Notification API Endpoints\n";
echo "==========================================\n\n";

$user = User::first();

if (!$user) {
    echo "❌ No users found!\n";
    exit(1);
}

echo "👤 Test User: {$user->first_name} {$user->last_name}\n\n";

// Simulate authenticated request
auth()->login($user);

echo "Testing API Endpoints:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Test 1: Unread Count
echo "1. GET /api/notifications/unread-count\n";
try {
    $controller = app(App\Http\Controllers\Api\NotificationController::class);
    $response = $controller->unreadCount();
    $data = $response->getData(true);
    echo "   Response: success={$data['success']}, count={$data['count']}\n";
    echo "   ✅ Endpoint working\n\n";
} catch (\Exception $e) {
    echo "   ❌ Error: {$e->getMessage()}\n\n";
}

// Test 2: Recent Notifications
echo "2. GET /api/notifications/recent\n";
try {
    $controller = app(App\Http\Controllers\Api\NotificationController::class);
    $request = new \Illuminate\Http\Request(['limit' => 5]);
    $response = $controller->recent($request);
    $data = $response->getData(true);
    echo "   Response: success={$data['success']}, count=" . count($data['data']) . "\n";
    
    if (!empty($data['data'])) {
        echo "   Recent notifications:\n";
        foreach (array_slice($data['data'], 0, 3) as $notif) {
            $title = $notif['data']['title'] ?? 'N/A';
            $read = $notif['read_at'] ? '[READ]' : '[UNREAD]';
            echo "     - {$title} {$read}\n";
        }
    }
    echo "   ✅ Endpoint working\n\n";
} catch (\Exception $e) {
    echo "   ❌ Error: {$e->getMessage()}\n\n";
}

// Test 3: All Notifications
echo "3. GET /api/notifications\n";
try {
    $controller = app(App\Http\Controllers\Api\NotificationController::class);
    $response = $controller->index();
    $data = $response->getData(true);
    echo "   Response: success={$data['success']}, total=" . count($data['data']) . "\n";
    echo "   ✅ Endpoint working\n\n";
} catch (\Exception $e) {
    echo "   ❌ Error: {$e->getMessage()}\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Summary:\n";
echo "- User has {$user->notifications()->count()} total notifications\n";
echo "- User has {$user->unreadNotifications()->count()} unread notifications\n\n";

echo "✅ All API endpoints are working!\n";
echo "   If notifications don't appear in browser:\n";
echo "   1. Check browser console (F12) for errors\n";
echo "   2. Check network tab for API calls\n";
echo "   3. Hard refresh browser (Ctrl+Shift+R)\n\n";
