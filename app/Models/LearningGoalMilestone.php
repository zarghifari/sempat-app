<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningGoalMilestone extends Model
{
    protected $fillable = [
        'learning_goal_id',
        'title',
        'description',
        'order',
        'requires_evidence',
        'is_completed',
        'completed_at',
        'completed_by_journal_id',
        'evidence_text',
        'evidence_file',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'requires_evidence' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the learning goal that owns the milestone.
     */
    public function learningGoal(): BelongsTo
    {
        return $this->belongsTo(LearningGoal::class);
    }

    /**
     * Get the journal entry that completed this milestone.
     */
    public function completedByJournal(): BelongsTo
    {
        return $this->belongsTo(LearningJournal::class, 'completed_by_journal_id');
    }

    /**
     * Mark this milestone as completed.
     */
    public function markCompleted($journalId = null)
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'completed_by_journal_id' => $journalId,
        ]);

        // Update parent goal progress
        $this->learningGoal->recalculateProgress();
    }

    /**
     * Mark this milestone as incomplete.
     */
    public function markIncomplete()
    {
        $this->update([
            'is_completed' => false,
            'completed_at' => null,
            'completed_by_journal_id' => null,
        ]);

        // Update parent goal progress
        $this->learningGoal->recalculateProgress();
    }
}
