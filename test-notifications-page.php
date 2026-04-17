<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "🧪 Testing Notifications Index Page...\n\n";

// Get user
$user = User::find(4);

if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "Testing with User ID: {$user->id}\n";
echo "User Name: {$user->name}\n\n";

// Check notifications
$allNotifications = $user->notifications()->count();
$unreadNotifications = $user->unreadNotifications()->count();

echo "📊 Notification Counts:\n";
echo "   Total: $allNotifications\n";
echo "   Unread: $unreadNotifications\n";
echo "   Read: " . ($allNotifications - $unreadNotifications) . "\n\n";

// Get paginated notifications (simulate controller)
$notifications = $user->notifications()
    ->orderBy('created_at', 'desc')
    ->paginate(20);

echo "📋 Paginated Results:\n";
echo "   Items on page: " . $notifications->count() . "\n";
echo "   Total pages: " . $notifications->lastPage() . "\n";
echo "   Current page: " . $notifications->currentPage() . "\n\n";

// Display first 5
echo "Recent 5 notifications:\n";
echo str_repeat('-', 80) . "\n";

foreach ($notifications->take(5) as $notification) {
    $data = $notification->data;
    $title = $data['title'] ?? 'No title';
    $message = $data['message'] ?? 'No message';
    $icon = $data['icon'] ?? '📬';
    $status = $notification->read_at ? 'READ' : 'UNREAD';
    
    echo "$icon $title\n";
    echo "   $message\n";
    echo "   Status: $status | " . $notification->created_at->diffForHumans() . "\n\n";
}

echo str_repeat('-', 80) . "\n";

// Test if view file exists
$viewPath = resource_path('views/notifications/index.blade.php');
if (file_exists($viewPath)) {
    echo "✅ View file exists: notifications/index.blade.php\n";
} else {
    echo "❌ View file NOT found: notifications/index.blade.php\n";
}

// Test if route exists
try {
    $route = \Route::getRoutes()->getByName('notifications.index');
    if ($route) {
        echo "✅ Route exists: notifications.index\n";
        echo "   URI: " . $route->uri() . "\n";
        echo "   Controller: " . $route->getActionName() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Route error: " . $e->getMessage() . "\n";
}

echo "\n✅ Test complete!\n";
echo "\nTry accessing: http://127.0.0.1:8000/notifications\n";
