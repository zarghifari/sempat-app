<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudySession extends Model
{
    protected $fillable = [
        'user_id',
        'learning_goal_id',
        'session_logs',
    ];

    protected $casts = [
        'session_logs' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function learningGoal()
    {
        return $this->belongsTo(LearningGoal::class);
    }

    /**
     * Add or update study minutes for a specific date
     */
    public function addStudyMinutes($date, $minutes)
    {
        $logs = $this->session_logs ?? [];
        $goal = $this->learningGoal;
        
        // Get current minutes for this date
        $current = $logs[$date] ?? 0;
        
        // Add new minutes but cap at daily_target_minutes
        $newTotal = min(
            $current + $minutes,
            $goal->daily_target_minutes ?? PHP_INT_MAX
        );
        
        $logs[$date] = $newTotal;
        
        // Sort by date descending
        krsort($logs);
        
        $this->session_logs = $logs;
        $this->save();
        
        // Update learning goal days_completed
        $this->updateGoalProgress();
        
        return $newTotal;
    }

    /**
     * Get study minutes for today
     */
    public function getTodayMinutes()
    {
        $today = now()->format('Y-m-d');
        return $this->session_logs[$today] ?? 0;
    }

    /**
     * Check if today's target is reached
     */
    public function isTodayTargetReached()
    {
        $goal = $this->learningGoal;
        if (!$goal || !$goal->daily_target_minutes) {
            return false;
        }
        
        return $this->getTodayMinutes() >= $goal->daily_target_minutes;
    }

    /**
     * Update parent learning goal progress
     */
    private function updateGoalProgress()
    {
        $goal = $this->learningGoal;
        if (!$goal || !$goal->daily_target_minutes) {
            return;
        }
        
        // Count days where target was reached
        $daysCompleted = 0;
        foreach ($this->session_logs as $date => $minutes) {
            if ($minutes >= $goal->daily_target_minutes) {
                $daysCompleted++;
            }
        }
        
        $goal->days_completed = $daysCompleted;
        $goal->save();
    }
}
