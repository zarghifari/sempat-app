<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    /**
     * Display the specified lesson
     */
    public function show($id)
    {
        $lesson = Lesson::with(['module.course', 'creator', 'quizzes' => function($query) {
            $query->where('status', 'published');
        }])->findOrFail($id);
        $user = Auth::user();
        
        // Check if user is enrolled in the course
        $enrollment = null;
        $isEnrolled = false;
        if ($user) {
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $lesson->module->course_id)
                ->first();
            $isEnrolled = $enrollment !== null;
        }
        
        // Check if lesson is completed
        $completion = null;
        $isCompleted = false;
        $completionPercentage = 0;
        if ($isEnrolled) {
            $completion = LessonCompletion::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->first();
            $isCompleted = $completion && $completion->status === 'completed';
            $completionPercentage = $completion ? $completion->completion_percentage : 0;
        }
        
        // Get previous and next lessons
        $previousLesson = Lesson::where('module_id', $lesson->module_id)
            ->where('order', '<', $lesson->order)
            ->orderBy('order', 'desc')
            ->first();
            
        $nextLesson = Lesson::where('module_id', $lesson->module_id)
            ->where('order', '>', $lesson->order)
            ->orderBy('order', 'asc')
            ->first();
        
        // Increment views count
        $lesson->increment('views_count');
        
        // Track lesson access
        if ($isEnrolled && $completion) {
            $completion->update([
                'last_accessed_at' => now(),
            ]);
        }
        
        // Format lesson data
        $lessonData = [
            'id' => $lesson->id,
            'title' => $lesson->title,
            'type' => $lesson->type,
            'content' => $lesson->content,
            'duration' => $lesson->duration_minutes,
            'module' => $lesson->module->title,
            'course' => $lesson->module->course->title,
            'is_preview' => $lesson->is_preview,
            'is_enrolled' => $isEnrolled,
            'is_completed' => $isCompleted,
            'completion_percentage' => $completionPercentage,
            'completed_at' => $completion && $completion->completed_at 
                ? $completion->completed_at->format('d M Y H:i') 
                : null,
            'external_links' => json_decode($lesson->external_links, true) ?? [],
            'attachments' => json_decode($lesson->attachments, true) ?? [],
            'previous_lesson' => $previousLesson ? [
                'id' => $previousLesson->id,
                'title' => $previousLesson->title,
            ] : null,
            'next_lesson' => $nextLesson ? [
                'id' => $nextLesson->id,
                'title' => $nextLesson->title,
            ] : null,
            'quizzes' => $lesson->quizzes->map(function($quiz) use ($user) {
                $attempts = $user ? $quiz->attempts()->where('user_id', $user->id)->count() : 0;
                $bestScore = $user ? $quiz->getBestScore($user->id) : null;
                return [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'description' => $quiz->description,
                    'total_questions' => $quiz->total_questions,
                    'time_limit' => $quiz->time_limit_minutes,
                    'passing_score' => $quiz->passing_score,
                    'max_attempts' => $quiz->max_attempts,
                    'attempts' => $attempts,
                    'best_score' => $bestScore,
                ];
            }),
        ];
        
        return view('lessons.show', ['lesson' => $lessonData]);
    }
    
    /**
     * Mark lesson as complete
     */
    public function complete(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);
        $user = Auth::user();
        
        // Check enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->module->course_id)
            ->firstOrFail();
        
        // Find or create completion
        $completion = LessonCompletion::firstOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'enrollment_id' => $enrollment->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
            ]
        );
        
        // Mark as complete
        $completion->update([
            'status' => 'completed',
            'completion_percentage' => 100,
            'completed_at' => now(),
            'time_spent_minutes' => $completion->time_spent_minutes + $lesson->duration_minutes,
        ]);
        
        // Update enrollment progress
        $this->updateEnrollmentProgress($enrollment);
        
        return redirect()->back()->with('success', 'Pelajaran berhasil diselesaikan!');
    }
    
    /**
     * Update enrollment progress
     */
    protected function updateEnrollmentProgress(Enrollment $enrollment)
    {
        $totalLessons = Lesson::whereHas('module', function($query) use ($enrollment) {
            $query->where('course_id', $enrollment->course_id);
        })->count();
        
        $completedLessons = LessonCompletion::where('user_id', $enrollment->user_id)
            ->where('enrollment_id', $enrollment->id)
            ->where('status', 'completed')
            ->count();
        
        $progressPercentage = $totalLessons > 0 
            ? round(($completedLessons / $totalLessons) * 100) 
            : 0;
        
        $enrollment->update([
            'progress_percentage' => $progressPercentage,
            'lessons_completed' => $completedLessons,
            'last_accessed_at' => now(),
        ]);
        
        // Check if course is completed
        if ($progressPercentage >= 100) {
            $enrollment->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }
}
