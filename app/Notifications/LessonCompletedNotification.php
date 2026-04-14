<?php

namespace App\Notifications;

use App\Models\Lesson;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LessonCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $lesson;
    protected $progress;

    /**
     * Create a new notification instance.
     */
    public function __construct(Lesson $lesson, ?int $progress = null)
    {
        $this->lesson = $lesson;
        $this->progress = $progress;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Add FCM channel if user has FCM token
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
            'type' => 'lesson_completed',
            'title' => 'Lesson Selesai! 🎉',
            'message' => "Selamat! Kamu telah menyelesaikan lesson \"{$this->lesson->title}\"",
            'icon' => '✅',
            'url' => route('courses.lessons.show', [
                'course' => $this->lesson->module->course_id,
                'lesson' => $this->lesson->id
            ]),
            'lesson_id' => $this->lesson->id,
            'lesson_title' => $this->lesson->title,
            'module_title' => $this->lesson->module->title,
            'progress' => $this->progress,
        ];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm(object $notifiable): array
    {
        return [
            'title' => 'Lesson Selesai! 🎉',
            'body' => "Selamat! Kamu telah menyelesaikan lesson \"{$this->lesson->title}\"",
            'icon' => '/images/notification-icon.png',
            'click_action' => route('courses.lessons.show', [
                'course' => $this->lesson->module->course_id,
                'lesson' => $this->lesson->id
            ]),
            'data' => [
                'type' => 'lesson_completed',
                'lesson_id' => $this->lesson->id,
                'url' => route('courses.lessons.show', [
                    'course' => $this->lesson->module->course_id,
                    'lesson' => $this->lesson->id
                ]),
            ],
        ];
    }
}
