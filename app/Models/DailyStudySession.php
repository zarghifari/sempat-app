<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyStudySession extends Model
{
    protected $fillable = [
        'user_id',
        'study_date',
        'total_articles_time',
        'total_lessons_time',
        'total_goals_time',
        'total_study_time',
        'articles_breakdown',
        'courses_breakdown',
        'goals_breakdown',
        'sessions_count',
        'first_activity_at',
        'last_activity_at',
    ];

    protected $casts = [
        'study_date' => 'date',
        'articles_breakdown' => 'array',
        'courses_breakdown' => 'array',
        'goals_breakdown' => 'array',
        'first_activity_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Get the user that owns the study session
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get today's session for a user (or create if not exists)
     */
    public static function getTodaySession(int $userId): self
    {
        return self::firstOrCreate(
            [
                'user_id' => $userId,
                'study_date' => now()->toDateString(),
            ],
            [
                'total_articles_time' => 0,
                'total_lessons_time' => 0,
                'total_goals_time' => 0,
                'total_study_time' => 0,
                'sessions_count' => 0,
            ]
        );
    }

    /**
     * Increment article time
     */
    public function incrementArticleTime(int $articleId, int $seconds): void
    {
        $breakdown = $this->articles_breakdown ?? [];
        $breakdown["article_$articleId"] = ($breakdown["article_$articleId"] ?? 0) + $seconds;
        
        $this->update([
            'articles_breakdown' => $breakdown,
            'total_articles_time' => $this->total_articles_time + $seconds,
            'total_study_time' => $this->total_study_time + $seconds,
            'last_activity_at' => now(),
            'sessions_count' => $this->sessions_count + 1,
        ]);
        
        if (!$this->first_activity_at) {
            $this->update(['first_activity_at' => now()]);
        }
    }

    /**
     * Increment course/lessons time
     */
    public function incrementCourseTime(int $courseId, int $seconds): void
    {
        $breakdown = $this->courses_breakdown ?? [];
        $breakdown["course_$courseId"] = ($breakdown["course_$courseId"] ?? 0) + $seconds;
        
        $this->update([
            'courses_breakdown' => $breakdown,
            'total_lessons_time' => $this->total_lessons_time + $seconds,
            'total_study_time' => $this->total_study_time + $seconds,
            'last_activity_at' => now(),
            'sessions_count' => $this->sessions_count + 1,
        ]);
        
        if (!$this->first_activity_at) {
            $this->update(['first_activity_at' => now()]);
        }
    }

    /**
     * Increment goal time
     */
    public function incrementGoalTime(int $goalId, int $seconds): void
    {
        $breakdown = $this->goals_breakdown ?? [];
        $breakdown["goal_$goalId"] = ($breakdown["goal_$goalId"] ?? 0) + $seconds;
        
        $this->update([
            'goals_breakdown' => $breakdown,
            'total_goals_time' => $this->total_goals_time + $seconds,
            'total_study_time' => $this->total_study_time + $seconds,
            'last_activity_at' => now(),
            'sessions_count' => $this->sessions_count + 1,
        ]);
        
        if (!$this->first_activity_at) {
            $this->update(['first_activity_at' => now()]);
        }
    }

    /**
     * Get total time for today for a specific type
     */
    public function getTodayTimeFor(string $type, int $id): int
    {
        $key = "{$type}_{$id}";
        $breakdown = match($type) {
            'article' => $this->articles_breakdown ?? [],
            'course' => $this->courses_breakdown ?? [],
            'goal' => $this->goals_breakdown ?? [],
            default => [],
        };
        
        return $breakdown[$key] ?? 0;
    }
    
    /**
     * Get goal study time history for a user (all dates)
     * Returns format: ["2026-01-24" => 45, "2026-01-23" => 60, ...]
     */
    public static function getGoalHistory(int $userId, int $goalId): array
    {
        $sessions = self::where('user_id', $userId)
            ->whereNotNull('goals_breakdown')
            ->orderBy('study_date', 'desc')
            ->get();
        
        $history = [];
        $goalKey = "goal_$goalId";
        
        foreach ($sessions as $session) {
            $breakdown = $session->goals_breakdown ?? [];
            if (isset($breakdown[$goalKey]) && $breakdown[$goalKey] > 0) {
                $date = $session->study_date->format('Y-m-d');
                $minutes = floor($breakdown[$goalKey] / 60);
                $history[$date] = $minutes;
            }
        }
        
        return $history;
    }
    
    /**
     * Get total minutes studied for a goal (all time)
     */
    public static function getTotalGoalMinutes(int $userId, int $goalId): int
    {
        $sessions = self::where('user_id', $userId)
            ->whereNotNull('goals_breakdown')
            ->get();
        
        $totalSeconds = 0;
        $goalKey = "goal_$goalId";
        
        foreach ($sessions as $session) {
            $breakdown = $session->goals_breakdown ?? [];
            $totalSeconds += $breakdown[$goalKey] ?? 0;
        }
        
        return floor($totalSeconds / 60);
    }
    
    /**
     * Count days where goal target was reached
     */
    public static function countGoalTargetDays(int $userId, int $goalId, int $targetMinutes): int
    {
        $sessions = self::where('user_id', $userId)
            ->whereNotNull('goals_breakdown')
            ->get();
        
        $daysReached = 0;
        $goalKey = "goal_$goalId";
        
        foreach ($sessions as $session) {
            $breakdown = $session->goals_breakdown ?? [];
            $minutes = floor(($breakdown[$goalKey] ?? 0) / 60);
            
            if ($minutes >= $targetMinutes) {
                $daysReached++;
            }
        }
        
        return $daysReached;
    }
}
