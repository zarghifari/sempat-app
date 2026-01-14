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
        'completion_type',
        'assessment_what_learned',
        'assessment_how_applied',
        'assessment_challenges',
        'assessment_next_steps',
        'assessment_submitted_at',
    ];

    protected $casts = [
        'target_date' => 'date',
        'completed_at' => 'date',
        'related_article_ids' => 'array',
        'final_project_submitted_at' => 'datetime',
        'assessment_submitted_at' => 'datetime',
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
     * Recalculate progress based on all components
     * Progress = weighted average of:
     * - Daily target (if enabled): 40%
     * - Milestones: 40%
     * - Final completion (project/assessment): 20%
     */
    public function recalculateProgress()
    {
        $components = [];
        $weights = [];
        
        // Component 1: Daily Target Progress (if enabled)
        if ($this->daily_target_minutes && $this->target_days) {
            $dailyProgress = min(100, ($this->days_completed / $this->target_days) * 100);
            $components[] = $dailyProgress;
            $weights[] = 40;
        }
        
        // Component 2: Milestones Progress
        $totalMilestones = $this->milestones()->count();
        if ($totalMilestones > 0) {
            $completedMilestones = $this->milestones()->where('is_completed', true)->count();
            $milestoneProgress = ($completedMilestones / $totalMilestones) * 100;
            $components[] = $milestoneProgress;
            $weights[] = 40;
        }
        
        // Component 3: Final Completion Progress (project or assessment)
        if ($this->completion_type) {
            $finalProgress = 0;
            if ($this->completion_type === 'final_project' && $this->isFinalProjectSubmitted()) {
                $finalProgress = 100;
            } elseif ($this->completion_type === 'final_assessment' && $this->isAssessmentSubmitted()) {
                $finalProgress = 100;
            }
            $components[] = $finalProgress;
            $weights[] = 20;
        }
        
        // Calculate weighted average
        if (count($components) > 0) {
            $totalWeight = array_sum($weights);
            $weightedSum = 0;
            for ($i = 0; $i < count($components); $i++) {
                $weightedSum += $components[$i] * $weights[$i];
            }
            $this->progress_percentage = round($weightedSum / $totalWeight);
        } else {
            $this->progress_percentage = 0;
        }
        
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
    
    /**
     * Check if assessment is submitted
     */
    public function isAssessmentSubmitted(): bool
    {
        return $this->assessment_submitted_at !== null;
    }
    
    /**
     * Check if prerequisites are met to submit final completion
     * (milestones and daily target must be completed first)
     */
    public function canSubmitFinalCompletion(): bool
    {
        // Check daily target (if enabled)
        if ($this->daily_target_minutes && $this->target_days) {
            if ($this->days_completed < $this->target_days) {
                return false;
            }
        }
        
        // Check milestones
        $totalMilestones = $this->milestones()->count();
        if ($totalMilestones > 0) {
            $completedMilestones = $this->milestones()->where('is_completed', true)->count();
            if ($completedMilestones < $totalMilestones) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check if goal is fully completed
     */
    public function isFullyCompleted(): bool
    {
        // Check daily target (if enabled)
        if ($this->daily_target_minutes && $this->target_days) {
            if ($this->days_completed < $this->target_days) {
                return false;
            }
        }
        
        // Check milestones
        $totalMilestones = $this->milestones()->count();
        if ($totalMilestones > 0) {
            $completedMilestones = $this->milestones()->where('is_completed', true)->count();
            if ($completedMilestones < $totalMilestones) {
                return false;
            }
        }
        
        // Check final completion
        if ($this->completion_type) {
            if ($this->completion_type === 'final_project' && !$this->isFinalProjectSubmitted()) {
                return false;
            }
            if ($this->completion_type === 'final_assessment' && !$this->isAssessmentSubmitted()) {
                return false;
            }
        }
        
        return true;
    }
}

