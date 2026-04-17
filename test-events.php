<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Events\DailyStudyGoalReachedEvent;
use App\Events\StudyStreakMilestoneEvent;
use Illuminate\Support\Facades\DB;

echo "==========================================\n";
echo "🎯 Testing Event-Driven Notifications\n";
echo "==========================================\n\n";

$user = User::where('email', 'test@sempat.test')->first();

if (!$user) {
    echo "❌ Test user not found\n";
    exit(1);
}

echo "User: {$user->email}\n";
$beforeCount = $user->notifications()->count();
echo "Notifications before: {$beforeCount}\n\n";

echo "--- Dispatching Events ---\n\n";

// Test Event 1: Daily Study Goal Reached
echo "1. Dispatching DailyStudyGoalReachedEvent...\n";
try {
    event(new DailyStudyGoalReachedEvent($user, 60, 45));
    echo "   ✅ Event dispatched\n";
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test Event 2: Study Streak Milestone
echo "\n2. Dispatching StudyStreakMilestoneEvent (14 days)...\n";
try {
    event(new StudyStreakMilestoneEvent($user, 14));
    echo "   ✅ Event dispatched\n";
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n--- Checking Queue ---\n\n";
$jobCount = DB::table('jobs')->count();
echo "Pending queue jobs: {$jobCount}\n";

if ($jobCount > 0) {
    echo "\n✅ Events successfully queued notifications!\n";
    echo "Processing queue jobs...\n\n";
    
    // Process all jobs
    $processed = 0;
    while (DB::table('jobs')->exists() && $processed < 10) {
        echo "Processing job " . ($processed + 1) . "...\n";
        exec('php artisan queue:work --once 2>&1', $output, $returnVar);
        if ($returnVar === 0) {
            echo "  ✅ Done\n";
        }
        $processed++;
    }
    
    echo "\n";
}

// Check final notification count
$afterCount = $user->notifications()->count();
echo "Notifications after: {$afterCount}\n";
echo "New notifications: " . ($afterCount - $beforeCount) . "\n\n";

if ($afterCount > $beforeCount) {
    echo "✅ Event-driven notification flow working!\n\n";
    
    echo "Latest notifications:\n";
    $latest = $user->notifications()->latest()->take(2)->get();
    foreach ($latest as $notification) {
        $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
        echo "  - {$data['title']}\n";
        echo "    {$data['message']}\n\n";
    }
} else {
    echo "⚠️  No new notifications created. Check event listeners.\n";
}

echo "==========================================\n";
echo "✅ Event Test Complete\n";
echo "==========================================\n";
