<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Enrollment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'course_id',
        'status',
        'enrolled_at',
        'started_at',
        'completed_at',
        'expires_at',
        'progress_percentage',
        'completed_lessons',
        'total_lessons',
        'completed_modules',
        'total_modules',
        'total_study_minutes',
        'last_accessed_at',
        'access_count',
        'quiz_average_score',
        'quizzes_taken',
        'quizzes_passed',
        'certificate_number',
        'certificate_issued_at',
        'enrolled_by',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'certificate_issued_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
        'quiz_average_score' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($enrollment) {
            if (empty($enrollment->uuid)) {
                $enrollment->uuid = Str::uuid();
            }
            if (empty($enrollment->enrolled_at)) {
                $enrollment->enrolled_at = now();
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function enrolledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enrolled_by');
    }

    public function lessonCompletions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'active')
            ->where('progress_percentage', '>', 0)
            ->where('progress_percentage', '<', 100);
    }
}
