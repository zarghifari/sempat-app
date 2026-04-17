<?php

/**
 * Interactive Notification Tester
 * Run: php create-test-notifications.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Notifications\LessonCompletedNotification;
use App\Notifications\CourseCompletedNotification;
use App\Notifications\ModuleCompletedNotification;
use App\Notifications\DailyStudyGoalReachedNotification;
use App\Notifications\StudyStreakMilestoneNotification;
use App\Notifications\StudyReminderNotification;
use App\Notifications\JournalReminderNotification;

echo "==========================================\n";
echo "🧪 Interactive Notification Tester\n";
echo "==========================================\n\n";

// Get user
$user = User::first();
if (!$user) {
    echo "❌ No users found. Please create a user first.\n";
    exit(1);
}

echo "📊 Testing for: {$user->first_name} {$user->last_name} (ID: {$user->id})\n";
echo "Email: {$user->email}\n\n";

// Menu
echo "Pilih notifikasi yang ingin dibuat:\n\n";
echo "1. ✨ Daily Study Goal Reached (Target Belajar Tercapai)\n";
echo "2. 🔥 Study Streak Milestone (Pencapaian Streak)\n";
echo "3. 📚 Lesson Completed (Pelajaran Selesai)\n";
echo "4. 🎉 Course Completed (Kursus Selesai)\n";
echo "5. ⭐ Module Completed (Modul Selesai)\n";
echo "6. 📝 Study Reminder (Pengingat Belajar)\n";
echo "7. 📖 Journal Reminder (Pengingat Jurnal)\n";
echo "8. 🎯 Create All Test Notifications\n";
echo "0. Exit\n\n";

echo "Pilihan (0-8): ";
$choice = trim(fgets(STDIN));

echo "\n";

switch ($choice) {
    case '1':
        echo "Membuat notifikasi Daily Study Goal...\n";
        $user->notify(new DailyStudyGoalReachedNotification(
            minutesStudied: 60,
            goalMinutes: 45
        ));
        echo "✅ Notifikasi queued!\n";
        break;
        
    case '2':
        echo "Masukkan jumlah hari streak (contoh: 7, 30, 100): ";
        $days = (int) trim(fgets(STDIN));
        echo "Membuat notifikasi Study Streak...\n";
        $user->notify(new StudyStreakMilestoneNotification($days));
        echo "✅ Notifikasi queued!\n";
        break;
        
    case '3':
        echo "Membuat notifikasi Lesson Completed...\n";
        // Create dummy data for demo
        $lessonData = (object)[
            'title' => 'Introduction to Laravel Notifications',
            'id' => 1
        ];
        $courseData = (object)[
            'title' => 'Laravel Advanced Course',
            'id' => 1
        ];
        $user->notify(new LessonCompletedNotification($lessonData, $courseData));
        echo "✅ Notifikasi queued!\n";
        break;
        
    case '4':
        echo "Membuat notifikasi Course Completed...\n";
        $courseData = (object)[
            'title' => 'Full Stack Development Bootcamp',
            'id' => 1
        ];
        $user->notify(new CourseCompletedNotification($courseData));
        echo "✅ Notifikasi queued!\n";
        break;
        
    case '5':
        echo "Membuat notifikasi Module Completed...\n";
        $moduleData = (object)[
            'title' => 'Database Design Fundamentals',
            'id' => 1
        ];
        $courseData = (object)[
            'title' => 'Backend Development Course',
            'id' => 1
        ];
        $user->notify(new ModuleCompletedNotification($moduleData, $courseData));
        echo "✅ Notifikasi queued!\n";
        break;
        
    case '6':
        echo "Membuat notifikasi Study Reminder...\n";
        $user->notify(new StudyReminderNotification());
        echo "✅ Notifikasi queued!\n";
        break;
        
    case '7':
        echo "Membuat notifikasi Journal Reminder...\n";
        $user->notify(new JournalReminderNotification());
        echo "✅ Notifikasi queued!\n";
        break;
        
    case '8':
        echo "Membuat semua jenis notifikasi...\n\n";
        
        // 1. Daily Goal
        echo "1/7 Daily Study Goal...\n";
        $user->notify(new DailyStudyGoalReachedNotification(90, 60));
        
        // 2. Streak
        echo "2/7 Study Streak (14 days)...\n";
        $user->notify(new StudyStreakMilestoneNotification(14));
        
        // 3. Lesson
        echo "3/7 Lesson Completed...\n";
        $lessonData = (object)['title' => 'Building REST APIs', 'id' => 1];
        $courseData = (object)['title' => 'API Development', 'id' => 1];
        $user->notify(new LessonCompletedNotification($lessonData, $courseData));
        
        // 4. Course
        echo "4/7 Course Completed...\n";
        $courseData = (object)['title' => 'Web Development Masterclass', 'id' => 1];
        $user->notify(new CourseCompletedNotification($courseData));
        
        // 5. Module
        echo "5/7 Module Completed...\n";
        $moduleData = (object)['title' => 'Authentication & Security', 'id' => 1];
        $courseData = (object)['title' => 'Laravel Security', 'id' => 1];
        $user->notify(new ModuleCompletedNotification($moduleData, $courseData));
        
        // 6. Study Reminder
        echo "6/7 Study Reminder...\n";
        $user->notify(new StudyReminderNotification());
        
        // 7. Journal Reminder
        echo "7/7 Journal Reminder...\n";
        $user->notify(new JournalReminderNotification());
        
        echo "\n✅ Semua 7 notifikasi berhasil di-queue!\n";
        break;
        
    case '0':
        echo "Keluar dari tester.\n";
        exit(0);
        
    default:
        echo "❌ Pilihan tidak valid.\n";
        exit(1);
}

echo "\n--- Status Queue ---\n";
$pendingJobs = \DB::table('jobs')->count();
echo "Pending jobs: {$pendingJobs}\n\n";

if ($pendingJobs > 0) {
    echo "⚡ Langkah selanjutnya:\n";
    echo "1. Jalankan queue worker:\n";
    echo "   php artisan queue:work --stop-when-empty\n\n";
    echo "2. Atau gunakan command otomatis:\n";
    echo "   php artisan queue:work --stop-when-empty && php test-notification-api.php\n\n";
    echo "3. Atau cek langsung di browser:\n";
    echo "   http://127.0.0.1:8000 (login dan lihat notifikasi bell)\n\n";
    
    // Auto-process option
    echo "Proses queue sekarang? (y/n): ";
    $process = trim(fgets(STDIN));
    
    if (strtolower($process) === 'y') {
        echo "\n⚙️  Processing queue jobs...\n\n";
        \Artisan::call('queue:work', ['--stop-when-empty' => true]);
        
        echo "\n✅ Queue processed!\n";
        echo "Checking notifications...\n\n";
        
        $notifications = $user->notifications()->latest()->limit(5)->get();
        echo "Latest notifications ({$notifications->count()}):\n";
        foreach ($notifications as $notif) {
            $readStatus = $notif->read_at ? '✓' : '○';
            echo "{$readStatus} {$notif->data['icon']} {$notif->data['title']}\n";
        }
        
        $unreadCount = $user->unreadNotifications()->count();
        echo "\nTotal unread: {$unreadCount}\n";
    }
}

echo "\n✅ Test completed!\n";
