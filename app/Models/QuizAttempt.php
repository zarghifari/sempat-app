<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'quiz_id',
        'user_id',
        'attempt_number',
        'status',
        'started_at',
        'completed_at',
        'time_spent_seconds',
        'answers',
        'correct_answers',
        'total_questions',
        'score_percentage',
        'points_earned',
        'total_points',
        'passed',
        'teacher_feedback',
        'graded_at',
        'graded_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'graded_at' => 'datetime',
        'answers' => 'array',
        'passed' => 'boolean',
        'score_percentage' => 'decimal:2',
    ];

    // Relationships
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    // Helper Methods
    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isPassed()
    {
        return $this->passed;
    }

    public function getTimeTaken()
    {
        if ($this->time_spent_seconds < 60) {
            return $this->time_spent_seconds . ' seconds';
        }

        $minutes = floor($this->time_spent_seconds / 60);
        $seconds = $this->time_spent_seconds % 60;

        return $minutes . ' min ' . $seconds . ' sec';
    }

    public function getScoreLabel()
    {
        if ($this->score_percentage >= 90) {
            return 'Excellent';
        } elseif ($this->score_percentage >= 80) {
            return 'Good';
        } elseif ($this->score_percentage >= 70) {
            return 'Passing';
        } else {
            return 'Needs Improvement';
        }
    }
}
