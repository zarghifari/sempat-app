<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🗑️  Cleaning duplicate notifications...\n\n";

// Group notifications by user, type, and related ID
$notifications = DB::table('notifications')
    ->orderBy('created_at', 'asc')
    ->get();

$seen = [];
$duplicates = [];

foreach ($notifications as $notification) {
    $data = json_decode($notification->data);
    
    // Create unique key based on type and related entity
    $key = $notification->notifiable_id . '_' . $notification->type;
    
    if ($notification->type === 'App\Notifications\LessonCompletedNotification') {
        $key .= '_lesson_' . ($data->lesson_id ?? 'unknown');
    } elseif ($notification->type === 'App\Notifications\ModuleCompletedNotification') {
        $key .= '_module_' . ($data->module_id ?? 'unknown');
    } elseif ($notification->type === 'App\Notifications\CourseCompletedNotification') {
        $key .= '_course_' . ($data->course_id ?? 'unknown');
    }
    
    // If we've seen this key, it's a duplicate (keep the first, delete rest)
    if (isset($seen[$key])) {
        $duplicates[] = $notification->id;
    } else {
        $seen[$key] = $notification->id;
    }
}

echo "Found " . count($duplicates) . " duplicate notifications\n";

if (count($duplicates) > 0) {
    echo "\n🗑️  Deleting duplicates...\n";
    
    DB::table('notifications')
        ->whereIn('id', $duplicates)
        ->delete();
    
    echo "✅ Deleted " . count($duplicates) . " duplicate notifications!\n\n";
} else {
    echo "✅ No duplicates found!\n\n";
}

// Show summary
$total = DB::table('notifications')->count();
$unread = DB::table('notifications')->whereNull('read_at')->count();

echo "📊 Current Status:\n";
echo "   Total notifications: $total\n";
echo "   Unread: $unread\n";
echo "   Read: " . ($total - $unread) . "\n";
