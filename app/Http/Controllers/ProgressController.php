<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\LessonCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    /**
     * Display user's learning progress.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Fetch real data from database
        $enrollments = $user->enrollments()->with('course')->get();
        
        $totalCourses = Course::published()->count();
        $enrolledCount = $enrollments->count();
        $completedCount = $enrollments->where('status', 'completed')->count();
        $inProgressCount = $enrollments->where('status', 'active')->count();
        
        $totalStudyMinutes = $enrollments->sum('total_study_minutes');
        $totalStudyHours = round($totalStudyMinutes / 60, 1);
        
        // Calculate completed lessons
        $completedLessons = $user->lessonCompletions()
            ->count();
        
        // Calculate total lessons in enrolled courses
        $totalLessons = DB::table('lessons')
            ->join('modules', 'lessons.module_id', '=', 'modules.id')
            ->whereIn('modules.course_id', $enrollments->pluck('course_id'))
            ->count();
        
        // Calculate overall progress
        $overallProgress = $totalLessons > 0 
            ? round(($completedLessons / $totalLessons) * 100) 
            : 0;
        
        // Calculate learning streak
        $currentStreak = $this->calculateStreak($user);
        
        $stats = [
            'total_courses' => $totalCourses,
            'enrolled_courses' => $enrolledCount,
            'completed_courses' => $completedCount,
            'in_progress_courses' => $inProgressCount,
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'total_study_hours' => $totalStudyHours,
            'current_streak' => $currentStreak,
            'overall_progress' => $overallProgress,
        ];

        // Recent activity from lesson completions
        $recentCompletions = $user->lessonCompletions()
            ->with('lesson.module.course')
            ->orderBy('completed_at', 'desc')
            ->take(10)
            ->get();

        $recentActivity = $recentCompletions->map(function($completion) {
            return [
                'title' => $completion->lesson->title,
                'course' => $completion->lesson->module->course->title,
                'time' => $completion->completed_at->diffForHumans(),
                'type' => $completion->lesson->type,
                'score' => $completion->quiz_score,
            ];
        });

        // Enrolled courses with progress
        $enrolledCourses = $enrollments->map(function($enrollment) {
            $totalLessons = $enrollment->course->modules()
                ->withCount('lessons')
                ->get()
                ->sum('lessons_count');
            
            return [
                'id' => $enrollment->course->id,
                'title' => $enrollment->course->title,
                'progress' => $enrollment->progress_percentage,
                'completed_lessons' => $enrollment->lessons_completed,
                'total_lessons' => $totalLessons,
                'last_accessed' => $enrollment->last_accessed_at 
                    ? $enrollment->last_accessed_at->diffForHumans()
                    : 'Never',
            ];
        });

        return view('progress.index', compact('stats', 'recentActivity', 'enrolledCourses'));
    }
    
    /**
     * Calculate user's learning streak (consecutive days with completed lessons).
     * Made public so it can be called from routes.
     */
    public function calculateUserStreak($user)
    {
        return $this->calculateStreak($user);
    }
    
    /**
     * Calculate user's learning streak (consecutive days with completed lessons).
     */
    private function calculateStreak($user)
    {
        // Get all lesson completion dates (unique days only)
        $completionDates = $user->lessonCompletions()
            ->orderBy('completed_at', 'desc')
            ->get()
            ->pluck('completed_at')
            ->map(function($date) {
                return $date->format('Y-m-d');
            })
            ->unique()
            ->values();
        
        if ($completionDates->isEmpty()) {
            return 0;
        }
        
        $streak = 0;
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');
        
        // Check if user studied today or yesterday (to keep streak alive)
        if ($completionDates->first() !== $today && $completionDates->first() !== $yesterday) {
            return 0; // Streak broken
        }
        
        // Count consecutive days
        $expectedDate = now();
        foreach ($completionDates as $date) {
            $completionDate = \Carbon\Carbon::parse($date);
            
            // Check if this date is the expected date (today or previous consecutive day)
            if ($completionDate->format('Y-m-d') === $expectedDate->format('Y-m-d')) {
                $streak++;
                $expectedDate->subDay(); // Move to previous day
            } else if ($completionDate->format('Y-m-d') < $expectedDate->format('Y-m-d')) {
                // Gap found, stop counting
                break;
            }
        }
        
        return $streak;
    }
}
