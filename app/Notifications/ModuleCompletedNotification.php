<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ModuleCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $moduleName;
    protected $courseProgress;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $moduleName, ?int $courseProgress = null)
    {
        $this->moduleName = $moduleName;
        $this->courseProgress = $courseProgress;
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
        $progressText = $this->courseProgress 
            ? " Progress course: {$this->courseProgress}%" 
            : '';
        
        return [
            'type' => 'module_completed',
            'title' => 'Module Selesai! 📖',
            'message' => "Hebat! Kamu telah menyelesaikan module \"{$this->moduleName}\".{$progressText}",
            'icon' => '📖',
            'url' => route('dashboard'),
            'module_name' => $this->moduleName,
            'course_progress' => $this->courseProgress,
        ];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm(object $notifiable): array
    {
        $progressText = $this->courseProgress 
            ? " Progress course: {$this->courseProgress}%" 
            : '';
        
        return [
            'title' => 'Module Selesai! 📖',
            'body' => "Hebat! Kamu telah menyelesaikan module \"{$this->moduleName}\".{$progressText}",
            'icon' => '/images/notification-icon.png',
            'click_action' => route('dashboard'),
            'data' => [
                'type' => 'module_completed',
                'module_name' => $this->moduleName,
                'url' => route('dashboard'),
            ],
        ];
    }
}
