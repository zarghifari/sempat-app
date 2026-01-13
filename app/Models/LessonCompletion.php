<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonCompletion extends Model
{
    protected $fillable = [
        'user_id',
        'lesson_id',
        'enrollment_id',
        'status',
        'progress_percentage',
        'started_at',
        'completed_at',
        'time_spent_seconds',
        'last_accessed_at',
        'view_count',
        'replay_count',
        'video_progress',
        'quiz_score',
        'quiz_attempts',
        'quiz_passed_at',
        'notes',
        'bookmarks',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'quiz_passed_at' => 'datetime',
        'video_progress' => 'array',
        'bookmarks' => 'array',
        'progress_percentage' => 'decimal:2',
        'quiz_score' => 'decimal:2',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
}
