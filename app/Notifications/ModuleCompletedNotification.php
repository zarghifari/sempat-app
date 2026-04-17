<?php

namespace App\Notifications;

use App\Models\Module;
use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ModuleCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $module;
    protected $course;

    /**
     * Create a new notification instance.
     */
    public function __construct(Module $module, Course $course)
    {
        $this->module = $module;
        $this->course = $course;
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
            'type' => 'module_completed',
            'title' => 'Module Selesai! 📖',
            'message' => "Hebat! Kamu telah menyelesaikan module \"{$this->module->title}\" dalam course \"{$this->course->title}\".",
            'icon' => '📖',
            'url' => route('courses.show', $this->course->id),
            'module_id' => $this->module->id,
            'module_title' => $this->module->title,
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'priority' => 100, // Module has higher priority (appears after lessons)
            'sort_timestamp' => now()->addSeconds(3)->timestamp, // Delayed sorting
        ];
    }
}

