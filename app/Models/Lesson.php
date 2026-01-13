<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Lesson extends Model
{
    protected $fillable = [
        'uuid',
        'module_id',
        'title',
        'description',
        'order',
        'type',
        'content',
        'video_url',
        'audio_url',
        'duration_minutes',
        'attachments',
        'external_links',
        'status',
        'is_preview',
        'is_mandatory',
        'min_completion_time',
        'requires_quiz',
        'min_quiz_score',
        'views_count',
        'completion_count',
        'avg_completion_time',
        'created_by',
    ];

    protected $casts = [
        'attachments' => 'array',
        'external_links' => 'array',
        'is_preview' => 'boolean',
        'is_mandatory' => 'boolean',
        'requires_quiz' => 'boolean',
        'min_quiz_score' => 'decimal:2',
        'avg_completion_time' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($lesson) {
            if (empty($lesson->uuid)) {
                $lesson->uuid = Str::uuid();
            }
        });
    }

    // Relationships
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function completions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePreview($query)
    {
        return $query->where('is_preview', true);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }
}
