<?php

namespace App\Jobs;

use App\Events\CourseCompletedEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchCourseCompletedEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $course;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $course)
    {
        $this->user = $user;
        $this->course = $course;
    }

    /**
     * Execute the job - dispatch the event after delay
     */
    public function handle(): void
    {
        event(new CourseCompletedEvent($this->user, $this->course));
    }
}
