<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningGoal extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'target_date',
        'completed_at',
        'progress_percentage',
        'progress_notes',
        'related_article_ids',
        'daily_target_minutes',
        'target_days',
        'days_completed',
        'final_project_title',
        'final_project_description',
        'final_project_url',
        'final_project_file',
        'final_project_submitted_at',
    ];

    protected $casts = [
        'target_date' => 'date',
        'completed_at' => 'date',
        'related_article_ids' => 'array',
        'final_project_submitted_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(LearningGoalMilestone::class)->orderBy('order');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(LearningJournal::class);
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

    // Helper Methods
    
    /**
     * Recalculate progress based on milestones
     */
    public function recalculateProgress()
    {
        $totalMilestones = $this->milestones()->count();
        
        if ($totalMilestones === 0) {
            return;
        }

        $completedMilestones = $this->milestones()->where('is_completed', true)->count();
        $this->progress_percentage = ($completedMilestones / $totalMilestones) * 100;
        $this->save();
    }

    /**
     * Update study stats from journal entries
     */
    public function updateStudyStats()
    {
        $stats = $this->journalEntries()
            ->selectRaw('
                COUNT(DISTINCT DATE(entry_date)) as unique_days,
                SUM(study_duration_minutes) as total_minutes,
                AVG(study_duration_minutes) as avg_minutes
            ')
            ->first();

        if ($this->target_days) {
            $this->days_completed = $stats->unique_days ?? 0;
            $this->progress_percentage = min(100, ($this->days_completed / $this->target_days) * 100);
        }

        $this->save();
    }

    /**
     * Check if goal has final project
     */
    public function hasFinalProject(): bool
    {
        return !empty($this->final_project_title);
    }

    /**
     * Check if final project is submitted
     */
    public function isFinalProjectSubmitted(): bool
    {
        return $this->final_project_submitted_at !== null;
    }
}

