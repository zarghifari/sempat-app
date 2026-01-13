<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Quiz extends Model
{
    protected $fillable = [
        'uuid',
        'lesson_id',
        'created_by',
        'title',
        'description',
        'instructions',
        'time_limit_minutes',
        'passing_score',
        'max_attempts',
        'show_correct_answers',
        'shuffle_questions',
        'shuffle_options',
        'status',
        'published_at',
        'total_questions',
        'total_attempts',
        'average_score',
    ];

    protected $casts = [
        'show_correct_answers' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'published_at' => 'datetime',
        'average_score' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($quiz) {
            if (empty($quiz->uuid)) {
                $quiz->uuid = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // Helper Methods
    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function hasTimeLimit()
    {
        return $this->time_limit_minutes > 0;
    }

    public function hasMaxAttempts()
    {
        return $this->max_attempts > 0;
    }

    public function userCanAttempt($user)
    {
        if (!$this->hasMaxAttempts()) {
            return true;
        }

        $userAttempts = $this->attempts()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        return $userAttempts < $this->max_attempts;
    }

    public function getUserAttempts($userId)
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getBestScore($userId)
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->max('score_percentage');
    }
}
