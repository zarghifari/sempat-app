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
        return ['database'];
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
            'url' => route('lessons.show', $this->lesson->id),
            'lesson_id' => $this->lesson->id,
            'lesson_title' => $this->lesson->title,
            'module_title' => $this->lesson->module->title,
            'progress' => $this->progress,
            'priority' => 0, // Lesson has lowest priority (appears first chronologically)
            'sort_timestamp' => now()->timestamp, // For proper ordering
        ];
    }


}

