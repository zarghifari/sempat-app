<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TimeTrackingService;
use App\Models\Lesson;
use App\Models\LearningGoal;
use App\Models\Enrollment;
use App\Models\Article;
use App\Models\ArticleReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeTrackingController extends Controller
{
    protected TimeTrackingService $trackingService;

    public function __construct(TimeTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
        // Middleware 'auth' now handled at route level (in web.php)
    }

    /**
     * Track lesson study time
     * POST /api/lessons/{lesson}/track-time
     */
    public function trackLessonTime(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'seconds' => 'required|integer|min:1|max:300', // Max 5 minutes per request
            'is_active' => 'boolean',
        ]);

        $user = Auth::user();

        // Verify user has access to this lesson
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->module->course_id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this course'
            ], 403);
        }

        $result = $this->trackingService->trackLessonTime(
            $user->id,
            $lesson->id,
            $validated['seconds'],
            $enrollment->id
        );

        // Add today's course time to response
        $courseId = $lesson->module->course_id;
        $result['today_course_time'] = $this->trackingService->getTodayCourseTime($user->id, $courseId);
        $result['today_formatted'] = $this->trackingService->formatTime($result['today_course_time']);

        return response()->json($result);
    }

    /**
     * Track learning goal study time
     * POST /api/learning-goals/{goal}/track-time
     * 
     * @deprecated Learning goals no longer track time directly.
     * Time is tracked from lessons and articles only.
     */
    public function trackGoalTime(Request $request, LearningGoal $goal)
    {
        return response()->json([
            'success' => false,
            'message' => 'Learning goals no longer track time directly. Time is automatically tracked from your lessons and articles.',
            'today_minutes' => $goal->getTodayProgress()['today_minutes'],
        ], 400);
    }

    /**
     * Get current lesson time
     * GET /api/lessons/{lesson}/time
     * 
     * Returns TODAY's COURSE time (shared across all lessons in same course)
     */
    public function getLessonTime(Lesson $lesson)
    {
        $user = Auth::user();
        
        // Debug logging
        \Log::info('getLessonTime called', [
            'lesson_id' => $lesson->id,
            'user_authenticated' => $user !== null,
            'user_id' => $user?->id,
            'session_id' => request()->session()->getId(),
        ]);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        // Get today's COURSE time (shared timer for all lessons in this course)
        $courseId = $lesson->module->course_id;
        $todayCourseTime = $this->trackingService->getTodayCourseTime($user->id, $courseId);
        
        // Get individual lesson time (for breakdown/history)
        $completion = \App\Models\LessonCompletion::where([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ])->first();

        return response()->json([
            'total_seconds' => $todayCourseTime, // Course timer for today
            'today_course_time' => $todayCourseTime, // Explicit field
            'this_lesson_time' => $completion ? $completion->time_spent_seconds : 0, // Individual time
            'formatted' => $this->trackingService->formatTime($todayCourseTime),
            'last_accessed' => $completion?->last_accessed_at?->toIso8601String(),
        ]);
    }

    /**
     * Get current learning goal time (from lessons + articles)
     * GET /api/learning-goals/{goal}/time
     */
    public function getGoalTime(LearningGoal $goal)
    {
        $user = Auth::user();

        if ($goal->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Get today's progress from lessons + articles
        $progress = $goal->getTodayProgress();

        return response()->json($progress);
    }

    /**
     * Get user study statistics
     * GET /api/user/study-stats
     */
    public function getUserStats()
    {
        $stats = $this->trackingService->getUserStudyStats(Auth::id());
        return response()->json($stats);
    }

    /**
     * Get today's study breakdown
     * GET /api/user/today-stats
     */
    public function getTodayStats()
    {
        $breakdown = $this->trackingService->getTodayStudyBreakdown(Auth::id());
        return response()->json($breakdown);
    }

    /**
     * Track article reading time
     * POST /api/articles/{article}/track-time
     */
    public function trackArticleTime(Request $request, Article $article)
    {
        $validated = $request->validate([
            'seconds' => 'required|integer|min:1|max:300',
            'is_active' => 'boolean',
        ]);

        $user = Auth::user();

        $result = $this->trackingService->trackArticleTime(
            $user->id,
            $article->id,
            $validated['seconds']
        );

        // Add today's global time to response
        $result['today_global_time'] = $this->trackingService->getTodayArticlesTime($user->id);
        $result['today_formatted'] = $this->trackingService->formatTime($result['today_global_time']);

        return response()->json($result);
    }

    /**
     * Get current article reading time
     * GET /api/articles/{article}/time
     * 
     * Returns GLOBAL articles time for today (shared across all articles)
     */
    public function getArticleTime(Article $article)
    {
        $user = Auth::user();
        
        // Get today's GLOBAL articles time (shared timer)
        $todayGlobalTime = $this->trackingService->getTodayArticlesTime($user->id);
        
        // Get individual article time (for breakdown/history)
        $reading = ArticleReading::where([
            'user_id' => $user->id,
            'article_id' => $article->id,
        ])->first();

        return response()->json([
            'total_seconds' => $todayGlobalTime, // Global timer for today
            'today_global_time' => $todayGlobalTime, // Explicit field
            'this_article_time' => $reading ? $reading->time_spent_seconds : 0, // Individual time
            'formatted' => $this->trackingService->formatTime($todayGlobalTime),
            'last_read' => $reading?->updated_at?->toIso8601String(),
        ]);
    }

    /**
     * Get today's global study time (for learning goals daily progress)
     * GET /api/daily-study-time
     * 
     * Returns total study time across all articles today
     */
    public function getDailyStudyTime()
    {
        $user = Auth::user();
        
        // Get today's GLOBAL articles time (same as articles tracker)
        $todayGlobalTime = $this->trackingService->getTodayArticlesTime($user->id);
        
        return response()->json([
            'today_total_seconds' => $todayGlobalTime,
            'today_total_minutes' => floor($todayGlobalTime / 60),
            'formatted' => $this->trackingService->formatTime($todayGlobalTime),
            'date' => now()->toDateString(),
        ]);
    }
}
