<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = [
        'quiz_id',
        'type',
        'question',
        'options',
        'correct_answer',
        'explanation',
        'points',
        'order',
        'image_url',
        'video_url',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    // Relationships
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // Helper Methods
    public function isMultipleChoice()
    {
        return $this->type === 'multiple_choice';
    }

    public function isTrueFalse()
    {
        return $this->type === 'true_false';
    }

    public function isShortAnswer()
    {
        return $this->type === 'short_answer';
    }

    public function isEssay()
    {
        return $this->type === 'essay';
    }

    public function checkAnswer($userAnswer)
    {
        if ($this->isMultipleChoice() || $this->isTrueFalse()) {
            return strtolower(trim($userAnswer)) === strtolower(trim($this->correct_answer));
        }

        if ($this->isShortAnswer()) {
            // Case-insensitive comparison, trimmed
            return strtolower(trim($userAnswer)) === strtolower(trim($this->correct_answer));
        }

        // Essay questions need manual grading
        return null;
    }
}
