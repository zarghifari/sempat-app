<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Notifications\DailyStudyGoalReachedNotification;
use App\Notifications\StudyStreakMilestoneNotification;
use Illuminate\Support\Facades\DB;

echo "==========================================\n";
echo "🧪 Testing Notification System\n";
echo "==========================================\n\n";

// Check if notifications table exists
try {
    $tableExists = DB::table('notifications')->exists();
    echo "✅ Notifications table exists\n";
} catch (\Exception $e) {
    echo "❌ Notifications table error: " . $e->getMessage() . "\n";
    exit(1);
}

// Get or create a test user
$user = User::first();
if (!$user) {
    echo "Creating test user...\n";
    $user = User::create([
        'username' => 'testuser',
        'email' => 'test@sempat.test',
        'password' => bcrypt('password'),
        'first_name' => 'Test',
        'last_name' => 'User',
        'is_active' => true,
    ]);
    echo "✅ Test user created: {$user->email}\n";
} else {
    echo "✅ Using existing user: {$user->email}\n";
}

echo "\n--- Testing Notifications ---\n\n";

// Test 1: Daily Study Goal Notification
echo "1. Testing DailyStudyGoalReachedNotification...\n";
try {
    $user->notify(new DailyStudyGoalReachedNotification(45, 30));
    echo "   ✅ Notification queued successfully\n";
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 2: Study Streak Milestone Notification
echo "\n2. Testing StudyStreakMilestoneNotification...\n";
try {
    $user->notify(new StudyStreakMilestoneNotification(7));
    echo "   ✅ Notification queued successfully\n";
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Check notification records
echo "\n--- Database Check ---\n\n";
$notificationCount = $user->notifications()->count();
echo "Total notifications for user: {$notificationCount}\n";

if ($notificationCount > 0) {
    echo "\nRecent notifications:\n";
    $notifications = $user->notifications()->latest()->take(3)->get();
    foreach ($notifications as $notification) {
        $data = json_decode($notification->data, true);
        echo "  - {$data['title']}: {$data['message']}\n";
        echo "    Created: {$notification->created_at->diffForHumans()}\n";
        echo "    Read: " . ($notification->read_at ? 'Yes' : 'No') . "\n\n";
    }
}

// Check queue jobs
$jobCount = DB::table('jobs')->count();
echo "Pending queue jobs: {$jobCount}\n";

echo "\n==========================================\n";
echo "✅ Test completed!\n";
echo "==========================================\n\n";

echo "Next steps:\n";
echo "1. Run queue worker: php artisan queue:work\n";
echo "2. Check notifications table for records\n";
echo "3. Test with actual events\n";
