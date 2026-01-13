<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $student = User::where('email', 'student@sempat.test')->first();
        
        // Get first 3 courses for enrollment
        $courses = Course::with(['modules.lessons'])->take(3)->get();

        foreach ($courses as $index => $course) {
            // Calculate totals
            $totalModules = $course->modules->count();
            $totalLessons = $course->modules->sum(function($module) {
                return $module->lessons->count();
            });

            // Different progress for each enrollment
            $progressPercentages = [45, 20, 80]; // Different progress for variety
            $progress = $progressPercentages[$index] ?? 0;
            
            $completedLessonsCount = (int)($totalLessons * ($progress / 100));
            $completedModulesCount = (int)($totalModules * ($progress / 100));

            // Create enrollment
            $enrollment = Enrollment::create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'status' => $progress == 100 ? 'completed' : 'active',
                'enrolled_at' => now()->subDays(30 - ($index * 5)),
                'started_at' => now()->subDays(29 - ($index * 5)),
                'completed_at' => $progress == 100 ? now()->subDays(1) : null,
                'progress_percentage' => $progress,
                'completed_lessons' => $completedLessonsCount,
                'total_lessons' => $totalLessons,
                'completed_modules' => $completedModulesCount,
                'total_modules' => $totalModules,
                'total_study_minutes' => rand(200, 800),
                'last_accessed_at' => now()->subHours(rand(2, 48)),
                'access_count' => rand(15, 50),
                'quiz_average_score' => rand(70, 95),
                'quizzes_taken' => rand(3, 10),
                'quizzes_passed' => rand(2, 8),
                'enrolled_by' => $student->id,
            ]);

            // Create lesson completions for completed lessons
            $lessonsCompleted = 0;
            foreach ($course->modules as $module) {
                foreach ($module->lessons as $lesson) {
                    if ($lessonsCompleted >= $completedLessonsCount) {
                        break 2;
                    }

                    LessonCompletion::create([
                        'user_id' => $student->id,
                        'lesson_id' => $lesson->id,
                        'enrollment_id' => $enrollment->id,
                        'status' => 'completed',
                        'progress_percentage' => 100,
                        'started_at' => now()->subDays(rand(1, 25)),
                        'completed_at' => now()->subDays(rand(1, 20)),
                        'time_spent_seconds' => rand(600, 2400),
                        'last_accessed_at' => now()->subDays(rand(1, 20)),
                        'view_count' => rand(1, 3),
                        'replay_count' => rand(0, 2),
                        'quiz_score' => $lesson->requires_quiz ? rand(70, 100) : null,
                        'quiz_attempts' => $lesson->requires_quiz ? rand(1, 3) : 0,
                        'quiz_passed_at' => $lesson->requires_quiz ? now()->subDays(rand(1, 20)) : null,
                    ]);

                    $lessonsCompleted++;
                }
            }
        }
    }
}
