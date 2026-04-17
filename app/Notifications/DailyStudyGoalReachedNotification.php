<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DailyStudyGoalReachedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $studyMinutes;
    protected $goalMinutes;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $studyMinutes, int $goalMinutes)
    {
        $this->studyMinutes = $studyMinutes;
        $this->goalMinutes = $goalMinutes;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'daily_goal_reached',
            'title' => 'Target Belajar Tercapai! ✨',
            'message' => "Keren! Kamu sudah belajar {$this->studyMinutes} menit hari ini. Target {$this->goalMinutes} menit tercapai! 🎯",
            'icon' => '✨',
            'url' => route('dashboard'),
            'study_minutes' => $this->studyMinutes,
            'goal_minutes' => $this->goalMinutes,
        ];
    }

    menit tercapai! 🎯",
            'icon' => '/images/notification-icon.png',
            'click_action' => route('dashboard'),
            'data' => [
                'type' => 'daily_goal_reached',
                'study_minutes' => $this->studyMinutes,
                'url' => route('dashboard'),
            ],
        ];
    }
}

