<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningJournal extends Model
{
    protected $table = 'learning_journal';

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'entry_date',
        'what_learned',
        'challenges_faced',
        'next_steps',
        'article_id',
        'course_id',
        'learning_goal_id',
        'mood',
        'study_duration_minutes',
        'tags',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'tags' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function learningGoal(): BelongsTo
    {
        return $this->belongsTo(LearningGoal::class);
    }
}
