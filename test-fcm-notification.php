<?php

/**
 * Test FCM (Firebase Cloud Messaging) Push Notifications
 * Run: php test-fcm-notification.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Http;

echo "==========================================\n";
echo "🔥 Test Firebase Cloud Messaging (FCM)\n";
echo "==========================================\n\n";

// Check FCM Server Key
$serverKey = config('services.fcm.server_key');

if (empty($serverKey) || $serverKey === 'YOUR_FCM_SERVER_KEY_HERE') {
    echo "❌ FCM Server Key not configured!\n\n";
    echo "Please add to .env:\n";
    echo "FCM_SERVER_KEY=AAAA...\n\n";
    echo "Get it from:\n";
    echo "Firebase Console → Project Settings → Cloud Messaging → Server key\n\n";
    exit(1);
}

echo "✅ FCM Server Key: " . substr($serverKey, 0, 15) . "...\n\n";

// Get users with FCM tokens
$users = User::whereNotNull('fcm_token')->get();

if ($users->isEmpty()) {
    echo "⚠️  No users with FCM tokens found!\n\n";
    echo "Steps to get FCM token:\n";
    echo "1. Login to app at http://127.0.0.1:8000\n";
    echo "2. Allow notification permission when prompted\n";
    echo "3. FCM token will be saved automatically\n\n";
    
    // Show all users
    $allUsers = User::all();
    echo "Available users:\n";
    foreach ($allUsers as $user) {
        $hasToken = $user->fcm_token ? '✅' : '❌';
        echo "  {$hasToken} {$user->first_name} {$user->last_name} (ID: {$user->id})\n";
    }
    echo "\n";
    exit(1);
}

echo "📊 Found {$users->count()} user(s) with FCM token:\n";
foreach ($users as $user) {
    echo "  ✅ {$user->first_name} {$user->last_name} (ID: {$user->id})\n";
    echo "     Token: " . substr($user->fcm_token, 0, 30) . "...\n";
}
echo "\n";

// Pilih user untuk test
echo "Pilih user untuk test notification (1-{$users->count()}): ";
$choice = (int) trim(fgets(STDIN));

if ($choice < 1 || $choice > $users->count()) {
    echo "❌ Invalid choice\n";
    exit(1);
}

$targetUser = $users[$choice - 1];
echo "\n✅ Sending test notification to: {$targetUser->first_name} {$targetUser->last_name}\n\n";

// Jenis notifikasi
echo "Pilih jenis test notification:\n";
echo "1. 📚 Lesson Completed\n";
echo "2. ✨ Daily Goal Reached\n";
echo "3. 🔥 Study Streak\n";
echo "4. 🎉 Course Completed\n";
echo "5. 📝 Custom message\n";
echo "\nPilihan (1-5): ";

$notifChoice = (int) trim(fgets(STDIN));

$title = 'Test Notification';
$body = 'This is a test push notification from SEMPAT LMS';
$icon = '🔔';
$url = '/';

switch ($notifChoice) {
    case 1:
        $title = 'Lesson Selesai! 🎉';
        $body = 'Selamat! Kamu telah menyelesaikan lesson "Test Lesson"';
        $icon = '✅';
        $url = '/lessons/1';
        break;
    
    case 2:
        $title = 'Target Belajar Tercapai! ✨';
        $body = 'Keren! Kamu sudah belajar 60 menit hari ini. Target tercapai! 🎯';
        $icon = '✨';
        $url = '/progress';
        break;
    
    case 3:
        $title = 'Streak 7 Hari! 🔥';
        $body = 'Luar biasa! Kamu sudah konsisten belajar 7 hari berturut-turut!';
        $icon = '🔥';
        $url = '/progress';
        break;
    
    case 4:
        $title = 'Kursus Selesai! 🎉';
        $body = 'Wow! Kamu telah menyelesaikan kursus "Test Course"';
        $icon = '🎉';
        $url = '/courses/1';
        break;
    
    case 5:
        echo "\nMasukkan judul: ";
        $title = trim(fgets(STDIN));
        echo "Masukkan pesan: ";
        $body = trim(fgets(STDIN));
        break;
}

echo "\n📤 Sending FCM notification...\n";
echo "   Title: {$title}\n";
echo "   Body: {$body}\n";
echo "   URL: {$url}\n\n";

// Send FCM notification
try {
    $response = Http::withHeaders([
        'Authorization' => 'key=' . $serverKey,
        'Content-Type' => 'application/json',
    ])->post('https://fcm.googleapis.com/fcm/send', [
        'to' => $targetUser->fcm_token,
        'notification' => [
            'title' => $title,
            'body' => $body,
            'icon' => '/images/logo-192.png',
            'click_action' => url($url),
        ],
        'data' => [
            'type' => 'test_notification',
            'title' => $title,
            'message' => $body,
            'icon' => $icon,
            'url' => $url,
            'user_id' => $targetUser->id,
            'timestamp' => now()->toIso8601String(),
        ],
        'priority' => 'high',
        'time_to_live' => 86400, // 24 hours
    ]);

    $result = $response->json();

    if ($response->successful() && isset($result['success']) && $result['success'] === 1) {
        echo "✅ Notification sent successfully!\n\n";
        echo "Response:\n";
        echo "  Message ID: {$result['results'][0]['message_id']}\n";
        echo "\n";
        echo "📱 Check notification on:\n";
        echo "  - Desktop: System notification tray\n";
        echo "  - Mobile: Notification center\n";
        echo "  - Browser: Top-right corner (if tab is open)\n";
        echo "\n";
        echo "✅ Test completed!\n";
    } else {
        echo "❌ Failed to send notification\n\n";
        echo "Response:\n";
        print_r($result);
        
        if (isset($result['results'][0]['error'])) {
            echo "\nError: {$result['results'][0]['error']}\n";
            
            if ($result['results'][0]['error'] === 'InvalidRegistration') {
                echo "\n💡 Solution: User needs to login again and allow notifications\n";
            }
        }
    }

} catch (\Exception $e) {
    echo "❌ Error sending notification: {$e->getMessage()}\n";
    echo "\nFull error:\n";
    echo $e->getTraceAsString();
}

echo "\n==========================================\n";
echo "💡 Tips:\n";
echo "1. Make sure browser has notification permission\n";
echo "2. FCM token should be fresh (login recently)\n";
echo "3. Check browser console for errors\n";
echo "4. Test on actual device for best results\n";
echo "==========================================\n";
