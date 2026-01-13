<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'description',
        'objectives',
        'prerequisites',
        'thumbnail',
        'intro_video_url',
        'level',
        'status',
        'language',
        'estimated_hours',
        'price',
        'is_free',
        'is_featured',
        'max_students',
        'enrolled_count',
        'start_date',
        'end_date',
        'views_count',
        'rating_average',
        'rating_count',
        'completion_count',
        'created_by',
        'updated_by',
        'published_at',
    ];

    protected $casts = [
        'objectives' => 'array',
        'prerequisites' => 'array',
        'price' => 'decimal:2',
        'is_free' => 'boolean',
        'is_featured' => 'boolean',
        'rating_average' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($course) {
            if (empty($course->uuid)) {
                $course->uuid = Str::uuid();
            }
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
        });
    }

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(CourseCategory::class, 'course_category');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot('status', 'progress_percentage', 'enrolled_at', 'completed_at')
            ->withTimestamps();
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeLevel($query, $level)
    {
        return $query->where('level', $level);
    }
}
