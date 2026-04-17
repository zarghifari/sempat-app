<?php

namespace App\Jobs;

use App\Events\ModuleCompletedEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchModuleCompletedEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $module;
    public $course;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $module, $course)
    {
        $this->user = $user;
        $this->module = $module;
        $this->course = $course;
    }

    /**
     * Execute the job - dispatch the event after delay
     */
    public function handle(): void
    {
        event(new ModuleCompletedEvent($this->user, $this->module, $this->course));
    }
}
