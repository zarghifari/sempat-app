<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class StudyReminderNotification extends Notification implements ShouldQueue
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
            'type' => 'study_reminder',
            'title' => 'Waktunya Belajar! 📚',
            'message' => 'Belum belajar hari ini? Yuk lanjutin pembelajaran kamu! Konsistensi adalah kunci.',
            'icon' => '📚',
            'url' => route('courses.index'),
        ];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm(object $notifiable): array
    {
        return [
            'title' => 'Waktunya Belajar! 📚',
            'body' => 'Belum belajar hari ini? Yuk lanjutin pembelajaran kamu! Konsistensi adalah kunci.',
            'icon' => '/images/notification-icon.png',
            'click_action' => route('courses.index'),
            'data' => [
                'type' => 'study_reminder',
                'url' => route('courses.index'),
            ],
        ];
    }
}
