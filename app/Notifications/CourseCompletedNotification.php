<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CourseCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $course;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
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
            'type' => 'course_completed',
            'title' => 'Course Selesai! 🎉🎓',
            'message' => "Luar biasa! Kamu telah menyelesaikan course \"{$this->course->title}\"",
            'icon' => '🎓',
            'url' => route('courses.show', $this->course->id),
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
        ];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm(object $notifiable): array
    {
        return [
            'title' => 'Course Selesai! 🎉🎓',
            'body' => "Luar biasa! Kamu telah menyelesaikan course \"{$this->course->title}\"",
            'icon' => '/images/notification-icon.png',
            'click_action' => route('courses.show', $this->course->id),
            'data' => [
                'type' => 'course_completed',
                'course_id' => $this->course->id,
                'url' => route('courses.show', $this->course->id),
            ],
        ];
    }
}
