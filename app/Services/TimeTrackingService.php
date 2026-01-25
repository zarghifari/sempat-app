<?php

namespace App\Services;

use App\Models\LessonCompletion;
use App\Models\Enrollment;
use App\Models\LearningGoal;
use App\Models\ArticleReading;
use App\Models\DailyStudySession;
use App\Models\Lesson;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TimeTrackingService
{
    protected ?StudentProgressCacheService $cacheService = null;

    public function __construct(?StudentProgressCacheService $cacheService = null)
    {
        $this->cacheService = $cacheService;
    }
    /**
     * Track active study time for a lesson
     * Uses atomic increment to avoid race conditions
     */
    public function trackLessonTime(int $userId, int $lessonId, int $seconds, ?int $enrollmentId = null): array
    {
        if ($seconds < 1 || $seconds > 300) { // Max 5 minutes per sync (sanity check)
            return ['success' => false, 'message' => 'Invalid time duration'];
        }

        try {
            // Find or create lesson completion
            $completion = LessonCompletion::firstOrCreate(
                [
                    'user_id' => $userId,
                    'lesson_id' => $lessonId,
                ],
                [
                    'enrollment_id' => $enrollmentId,
                    'status' => 'in_progress',
                    'started_at' => now(),
                ]
            );

            // Atomic increment - no race condition
            $completion->increment('time_spent_seconds', $seconds);
            $completion->update([
                'last_accessed_at' => now(),
                'view_count' => DB::raw('view_count + 1'),
            ]);

            // Batch sync to enrollment (every ~5 minutes worth of study time)
            // Use probability to distribute load: 10% chance = avg every 5 syncs
            if ($this->shouldSyncEnrollment($completion)) {
                $this->syncEnrollmentTime($completion);
            }

            // Also log to daily study sessions for efficient daily tracking
            $this->trackWithDailyLog($userId, 'lesson', $lessonId, $seconds);
            // Invalidate cache untuk real-time update di teacher dashboard
            if ($this->cacheService) {
                $this->cacheService->invalidateTodayCache($userId);
            }
            return [
                'success' => true,
                'total_seconds' => $completion->time_spent_seconds,
                'formatted' => $this->formatTime($completion->time_spent_seconds),
            ];
        } catch (\Exception $e) {
            \Log::error('Time tracking failed', [
                'user_id' => $userId,
                'lesson_id' => $lessonId,
                'error' => $e->getMessage()
            ]);
            
            return ['success' => false, 'message' => 'Tracking failed'];
        }
    }

    /**
     * Track study time for a learning goal
     */
    public function trackGoalTime(int $userId, int $goalId, int $seconds): array
    {
        if ($seconds < 1 || $seconds > 300) {
            return ['success' => false, 'message' => 'Invalid time duration'];
        }

        try {
            $goal = LearningGoal::where('user_id', $userId)
                ->where('id', $goalId)
                ->firstOrFail();

            // Atomic increment
            $goal->increment('total_study_seconds', $seconds);
            $goal->update(['last_study_at' => now()]);

            // Auto-update progress based on study time (if daily target enabled)
            if ($goal->daily_target_minutes > 0) {
                $this->updateGoalProgress($goal);
            }

            // Also log to daily study sessions
            $this->trackWithDailyLog($userId, 'goal', $goalId, $seconds);

            // Invalidate cache untuk real-time update di teacher dashboard
            if ($this->cacheService) {
                $this->cacheService->invalidateTodayCache($userId);
            }

            return [
                'success' => true,
                'total_seconds' => $goal->total_study_seconds,
                'formatted' => $this->formatTime($goal->total_study_seconds),
            ];
        } catch (\Exception $e) {
            \Log::error('Goal time tracking failed', [
                'user_id' => $userId,
                'goal_id' => $goalId,
                'error' => $e->getMessage()
            ]);
            
            return ['success' => false, 'message' => 'Tracking failed'];
        }
    }

    /**
     * Sync lesson completion time to enrollment
     * Only sync if there's significant unsync'd time (5+ minutes)
     */
    protected function syncEnrollmentTime(LessonCompletion $completion): void
    {
        if (!$completion->enrollment_id) {
            return;
        }

        try {
            // Calculate time since last sync
            $lastSync = $completion->last_time_sync ?? $completion->created_at;
            $timeSinceSync = $completion->updated_at->diffInSeconds($lastSync);

            // Only sync if more than 5 minutes worth of new time
            if ($timeSinceSync < 300) {
                return;
            }

            // Get all completions for this enrollment that need syncing
            $unsynced = LessonCompletion::where('enrollment_id', $completion->enrollment_id)
                ->where(function($q) {
                    $q->whereNull('last_time_sync')
                      ->orWhereColumn('updated_at', '>', 'last_time_sync');
                })
                ->sum('time_spent_seconds');

            if ($unsynced > 0) {
                // Convert seconds to minutes (round down)
                $minutes = floor($unsynced / 60);
                
                if ($minutes > 0) {
                    // Atomic increment on enrollment
                    Enrollment::where('id', $completion->enrollment_id)
                        ->increment('total_study_minutes', $minutes);

                    // Mark completions as synced
                    LessonCompletion::where('enrollment_id', $completion->enrollment_id)
                        ->where(function($q) {
                            $q->whereNull('last_time_sync')
                              ->orWhereColumn('updated_at', '>', 'last_time_sync');
                        })
                        ->update(['last_time_sync' => now()]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Enrollment time sync failed', [
                'completion_id' => $completion->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Determine if we should sync to enrollment (probabilistic)
     */
    protected function shouldSyncEnrollment(LessonCompletion $completion): bool
    {
        // If never synced and has 5+ minutes, sync immediately
        if (is_null($completion->last_time_sync) && $completion->time_spent_seconds >= 300) {
            return true;
        }

        // Otherwise 10% probability (avg every 10 syncs = ~5 minutes of study)
        return rand(1, 10) === 1;
    }

    /**
     * Update learning goal progress based on study time
     */
    protected function updateGoalProgress(LearningGoal $goal): void
    {
        if ($goal->daily_target_minutes <= 0) {
            return;
        }

        // Calculate progress from study time
        $totalMinutes = floor($goal->total_study_seconds / 60);
        $targetTotalMinutes = $goal->daily_target_minutes * $goal->target_days;
        
        $timeProgress = $targetTotalMinutes > 0 
            ? min(100, round(($totalMinutes / $targetTotalMinutes) * 100))
            : 0;

        // Recalculate overall progress (this method should exist in LearningGoal model)
        if (method_exists($goal, 'recalculateProgress')) {
            $goal->recalculateProgress();
        } else {
            // Simple fallback
            $goal->update(['progress_percentage' => $timeProgress]);
        }
    }

    /**
     * Force sync all pending time to enrollment (called on lesson complete)
     */
    public function forceSyncEnrollment(int $enrollmentId): void
    {
        try {
            $unsynced = LessonCompletion::where('enrollment_id', $enrollmentId)
                ->where(function($q) {
                    $q->whereNull('last_time_sync')
                      ->orWhereColumn('updated_at', '>', 'last_time_sync');
                })
                ->sum('time_spent_seconds');

            if ($unsynced > 0) {
                $minutes = floor($unsynced / 60);
                
                if ($minutes > 0) {
                    Enrollment::where('id', $enrollmentId)
                        ->increment('total_study_minutes', $minutes);

                    LessonCompletion::where('enrollment_id', $enrollmentId)
                        ->where(function($q) {
                            $q->whereNull('last_time_sync')
                              ->orWhereColumn('updated_at', '>', 'last_time_sync');
                        })
                        ->update(['last_time_sync' => now()]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Force sync failed', [
                'enrollment_id' => $enrollmentId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get formatted time string
     */
    public function formatTime(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }
        
        return sprintf('%02d:%02d', $minutes, $secs);
    }

    /**
     * Get study statistics for a user
     */
    public function getUserStudyStats(int $userId): array
    {
        // Use cache for 5 minutes to reduce DB load
        return Cache::remember("user_study_stats_{$userId}", 300, function () use ($userId) {
            $enrollments = Enrollment::where('user_id', $userId)->get();
            
            $totalMinutes = $enrollments->sum('total_study_minutes');
            $activeEnrollments = $enrollments->where('status', 'active')->count();
            $completedCourses = $enrollments->where('status', 'completed')->count();
            
            return [
                'total_study_minutes' => $totalMinutes,
                'total_study_hours' => round($totalMinutes / 60, 1),
                'formatted_time' => gmdate('H:i', $totalMinutes * 60),
                'active_enrollments' => $activeEnrollments,
                'completed_courses' => $completedCourses,
            ];
        });
    }

    /**
     * Track article reading time
     */
    public function trackArticleTime(int $userId, int $articleId, int $seconds): array
    {
        if ($seconds < 1 || $seconds > 300) {
            return ['success' => false, 'message' => 'Invalid time duration'];
        }

        try {
            // Find or create article reading record
            $reading = ArticleReading::firstOrCreate(
                [
                    'user_id' => $userId,
                    'article_id' => $articleId,
                ],
                [
                    'time_spent_seconds' => 0,
                ]
            );

            // Atomic increment
            $reading->increment('time_spent_seconds', $seconds);
            $reading->update(['last_time_sync' => now()]);

            // Also log to daily study sessions
            $this->trackWithDailyLog($userId, 'article', $articleId, $seconds);

            // Invalidate cache untuk real-time update di teacher dashboard
            if ($this->cacheService) {
                $this->cacheService->invalidateTodayCache($userId);
            }

            return [
                'success' => true,
                'total_seconds' => $reading->time_spent_seconds,
                'formatted' => $this->formatTime($reading->time_spent_seconds),
            ];
        } catch (\Exception $e) {
            \Log::error('Article time tracking failed', [
                'user_id' => $userId,
                'article_id' => $articleId,
                'error' => $e->getMessage()
            ]);
            
            return ['success' => false, 'message' => 'Tracking failed'];
        }
    }

    /**
     * Track study time with daily aggregation
     * This also logs to daily_study_sessions for efficient daily tracking
     */
    public function trackWithDailyLog(int $userId, string $type, int $resourceId, int $seconds): void
    {
        try {
            $session = DailyStudySession::getTodaySession($userId);
            
            switch ($type) {
                case 'lesson':
                    // Get course ID from lesson
                    $lesson = Lesson::find($resourceId);
                    if ($lesson) {
                        $courseId = $lesson->module->course_id;
                        $session->incrementCourseTime($courseId, $seconds);
                    }
                    break;
                    
                case 'article':
                    $session->incrementArticleTime($resourceId, $seconds);
                    break;
                    
                case 'goal':
                    $session->incrementGoalTime($resourceId, $seconds);
                    break;
            }
        } catch (\Exception $e) {
            \Log::error('Daily tracking failed', [
                'user_id' => $userId,
                'type' => $type,
                'resource_id' => $resourceId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get today's total time for articles (global)
     */
    public function getTodayArticlesTime(int $userId): int
    {
        $session = DailyStudySession::getTodaySession($userId);
        return $session->total_articles_time;
    }

    /**
     * Get today's total time for a specific course
     */
    public function getTodayCourseTime(int $userId, int $courseId): int
    {
        $session = DailyStudySession::getTodaySession($userId);
        return $session->getTodayTimeFor('course', $courseId);
    }

    /**
     * Get today's total time for a specific goal
     */
    public function getTodayGoalTime(int $userId, int $goalId): int
    {
        $session = DailyStudySession::getTodaySession($userId);
        return $session->getTodayTimeFor('goal', $goalId);
    }

    /**
     * Get today's study breakdown for user
     */
    public function getTodayStudyBreakdown(int $userId): array
    {
        $session = DailyStudySession::getTodaySession($userId);
        
        return [
            'total_time' => $session->total_study_time,
            'articles_time' => $session->total_articles_time,
            'lessons_time' => $session->total_lessons_time,
            'goals_time' => $session->total_goals_time,
            'sessions_count' => $session->sessions_count,
            'first_activity' => $session->first_activity_at?->format('H:i:s'),
            'last_activity' => $session->last_activity_at?->format('H:i:s'),
            'articles_breakdown' => $session->articles_breakdown ?? [],
            'courses_breakdown' => $session->courses_breakdown ?? [],
            'goals_breakdown' => $session->goals_breakdown ?? [],
        ];
    }
}
