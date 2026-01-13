<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Fetch published courses with relationships
        $allCourses = Course::with(['creator', 'categories', 'modules'])
            ->published()
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get user's enrollments if logged in
        $enrolledCourseIds = [];
        if ($user) {
            $enrolledCourseIds = $user->enrollments()
                ->pluck('course_id')
                ->toArray();
        }

        // Transform courses for view
        $courses = $allCourses->map(function($course) use ($enrolledCourseIds, $user) {
            $enrollment = null;
            if ($user && in_array($course->id, $enrolledCourseIds)) {
                $enrollment = $user->enrollments()
                    ->where('course_id', $course->id)
                    ->first();
            }

            return [
                'id' => $course->id,
                'slug' => $course->slug,
                'title' => $course->title,
                'description' => $course->description,
                'instructor' => $course->creator->name,
                'thumbnail' => $course->thumbnail,
                'enrolled' => in_array($course->id, $enrolledCourseIds),
                'progress' => $enrollment ? $enrollment->progress_percentage : 0,
                'modules_count' => $course->modules->count(),
                'duration' => $course->estimated_hours . ' Jam',
                'level' => ucfirst($course->level),
                'is_free' => $course->is_free,
                'price' => $course->is_free ? null : 'Rp ' . number_format($course->price, 0, ',', '.'),
                'rating' => $course->rating_average,
                'enrolled_count' => $course->enrolled_count,
            ];
        });

        return view('courses.index', compact('courses'));
    }

    /**
     * Display the specified course.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Fetch course with all relationships
        $courseModel = Course::with([
            'creator',
            'categories',
            'modules.lessons' => function($query) {
                $query->orderBy('order');
            }
        ])->findOrFail($id);

        // Check if user is enrolled
        $enrollment = null;
        if ($user) {
            $enrollment = $user->enrollments()
                ->where('course_id', $courseModel->id)
                ->first();
        }

        // Get completed lessons if enrolled
        $completedLessonIds = [];
        if ($enrollment) {
            $completedLessonIds = $enrollment->lessonCompletions()
                ->where('status', 'completed')
                ->pluck('lesson_id')
                ->toArray();
        }

        // Transform modules for view
        $modules = $courseModel->modules->map(function($module) use ($completedLessonIds) {
            $totalLessons = $module->lessons->count();
            $completedLessons = $module->lessons->filter(function($lesson) use ($completedLessonIds) {
                return in_array($lesson->id, $completedLessonIds);
            })->count();

            // Transform lessons
            $lessons = $module->lessons->map(function($lesson) use ($completedLessonIds) {
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'type' => $lesson->type,
                    'duration' => $lesson->duration_minutes,
                    'is_preview' => $lesson->is_preview,
                    'is_completed' => in_array($lesson->id, $completedLessonIds),
                ];
            });

            return [
                'id' => $module->id,
                'title' => $module->title,
                'description' => $module->description,
                'lessons_count' => $totalLessons,
                'lessons' => $lessons,
                'duration' => $module->estimated_minutes . ' Menit',
                'completed' => $completedLessons === $totalLessons && $totalLessons > 0,
                'progress' => $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0,
            ];
        });

        // Find next lesson to continue (for enrolled users)
        $nextLessonId = null;
        if ($enrollment) {
            // Loop through all modules and lessons to find the first incomplete lesson
            foreach ($courseModel->modules->sortBy('order') as $module) {
                foreach ($module->lessons->sortBy('order') as $lesson) {
                    if (!in_array($lesson->id, $completedLessonIds)) {
                        $nextLessonId = $lesson->id;
                        break 2; // Break both loops
                    }
                }
            }
            
            // If all lessons are completed, go to the last lesson
            if (!$nextLessonId && $courseModel->modules->isNotEmpty()) {
                $lastModule = $courseModel->modules->sortByDesc('order')->first();
                if ($lastModule && $lastModule->lessons->isNotEmpty()) {
                    $nextLessonId = $lastModule->lessons->sortByDesc('order')->first()->id;
                }
            }
        }

        // Prepare course data for view
        $course = [
            'id' => $courseModel->id,
            'slug' => $courseModel->slug,
            'title' => $courseModel->title,
            'description' => $courseModel->description,
            'objectives' => $courseModel->objectives,
            'prerequisites' => $courseModel->prerequisites,
            'instructor' => $courseModel->creator->name,
            'instructor_bio' => 'Pengajar berpengalaman di ' . config('app.name'),
            'thumbnail' => $courseModel->thumbnail,
            'intro_video_url' => $courseModel->intro_video_url,
            'enrolled' => $enrollment !== null,
            'progress' => $enrollment ? $enrollment->progress_percentage : 0,
            'modules_count' => $courseModel->modules->count(),
            'lessons_count' => $courseModel->modules->sum(function($m) { return $m->lessons->count(); }),
            'duration' => $courseModel->estimated_hours . ' Jam',
            'level' => ucfirst($courseModel->level),
            'language' => strtoupper($courseModel->language),
            'is_free' => $courseModel->is_free,
            'price' => $courseModel->is_free ? null : 'Rp ' . number_format($courseModel->price, 0, ',', '.'),
            'rating' => $courseModel->rating_average ?? 0,
            'enrolled_count' => $courseModel->enrolled_count,
            'modules' => $modules,
            'next_lesson_id' => $nextLessonId,
        ];

        return view('courses.show', compact('course'));
    }

    /**
     * Enroll user in a course.
     */
    public function enroll($id)
    {
        $user = Auth::user();
        $course = Course::findOrFail($id);

        // Check if already enrolled
        $existingEnrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('courses.show', $course->id)
                ->with('info', 'Anda sudah terdaftar di kursus ini.');
        }

        // Count total modules and lessons
        $totalModules = $course->modules()->count();
        $totalLessons = $course->modules()->withCount('lessons')->get()->sum('lessons_count');

        // Create enrollment
        try {
            $user->enrollments()->create([
                'course_id' => $course->id,
                'status' => 'active',
                'enrolled_at' => now(),
                'total_modules' => $totalModules,
                'total_lessons' => $totalLessons,
                'enrolled_by' => $user->id,
            ]);

            // Increment course enrolled count
            $course->increment('enrolled_count');

            return redirect()->route('courses.show', $course->id)
                ->with('success', 'Berhasil mendaftar di kursus ' . $course->title);
        } catch (\Exception $e) {
            return redirect()->route('courses.show', $course->id)
                ->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }
}
