<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class StudyStreakMilestoneNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $streakDays;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $streakDays)
    {
        $this->streakDays = $streakDays;
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
        $milestoneEmoji = $this->getMilestoneEmoji($this->streakDays);
        
        return [
            'type' => 'study_streak_milestone',
            'title' => "Streak {$this->streakDays} Hari! $milestoneEmoji",
            'message' => "Luar biasa! Kamu sudah konsisten belajar {$this->streakDays} hari berturut-turut. Keep it up!",
            'icon' => '🔥',
            'url' => route('dashboard'),
            'streak_days' => $this->streakDays,
        ];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm(object $notifiable): array
    {
        $milestoneEmoji = $this->getMilestoneEmoji($this->streakDays);
        
        return [
            'title' => "Streak {$this->streakDays} Hari! $milestoneEmoji",
            'body' => "Luar biasa! Kamu sudah konsisten belajar {$this->streakDays} hari berturut-turut. Keep it up!",
            'icon' => '/images/notification-icon.png',
            'click_action' => route('dashboard'),
            'data' => [
                'type' => 'study_streak_milestone',
                'streak_days' => $this->streakDays,
                'url' => route('dashboard'),
            ],
        ];
    }

    /**
     * Get milestone emoji based on streak days.
     */
    protected function getMilestoneEmoji(int $days): string
    {
        return match(true) {
            $days >= 100 => '🏆',
            $days >= 30 => '💎',
            $days >= 14 => '⭐',
            $days >= 7 => '🔥',
            default => '✨',
        };
    }
}
