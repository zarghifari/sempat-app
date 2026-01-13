<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Module extends Model
{
    protected $fillable = [
        'uuid',
        'course_id',
        'title',
        'description',
        'order',
        'estimated_minutes',
        'lessons_count',
        'status',
        'is_locked',
        'created_by',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($module) {
            if (empty($module->uuid)) {
                $module->uuid = Str::uuid();
            }
        });
    }

    // Relationships
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }
}
