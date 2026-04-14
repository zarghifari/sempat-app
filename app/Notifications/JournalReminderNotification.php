<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class JournalReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->fcm_token) {
            $channels[] = 'fcm';
        }
        
        return $channels;
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'journal_reminder',
            'title' => 'Saatnya Refleksi Harian 📝',
            'message' => 'Tulis jurnal belajar hari ini. Apa yang sudah kamu pelajari? Apa yang masih perlu ditingkatkan?',
            'icon' => '📝',
            'url' => route('learning-journals.index'),
        ];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm(object $notifiable): array
    {
        return [
            'title' => 'Saatnya Refleksi Harian 📝',
            'body' => 'Tulis jurnal belajar hari ini. Apa yang sudah kamu pelajari? Apa yang masih perlu ditingkatkan?',
            'icon' => '/images/notification-icon.png',
            'click_action' => route('learning-journals.index'),
            'data' => [
                'type' => 'journal_reminder',
                'url' => route('learning-journals.index'),
            ],
        ];
    }
}
