<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuizAttemptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = User::whereHas('roles', function ($query) {
            $query->where('slug', 'student');
        })->first();

        if (!$student) {
            $this->command->warn('No student found. Skipping QuizAttemptSeeder.');
            return;
        }

        $quizzes = Quiz::where('status', 'published')->get();

        if ($quizzes->isEmpty()) {
            $this->command->warn('No quizzes found. Please run QuizSeeder first.');
            return;
        }

        $this->command->info('Creating quiz attempts...');

        // Create some completed attempts for the first quiz (HTML Basics)
        $htmlQuiz = $quizzes->first();
        
        // First attempt - Failed (60%)
        QuizAttempt::create([
            'quiz_id' => $htmlQuiz->id,
            'user_id' => $student->id,
            'attempt_number' => 1,
            'status' => 'completed',
            'started_at' => now()->subDays(2),
            'completed_at' => now()->subDays(2)->addMinutes(12),
            'time_spent_seconds' => 720, // 12 minutes
            'answers' => [
                '1' => 'A',  // Correct
                '2' => 'B',  // Correct
                '3' => 'true',  // Wrong (should be false)
                '4' => 'B',  // Correct
                '5' => 'h2',  // Wrong (should be h1)
            ],
            'total_questions' => 5,
            'correct_answers' => 3,
            'score_percentage' => 60.00,
            'points_earned' => 4,
            'total_points' => 6,
            'passed' => false,
        ]);

        // Second attempt - Passed (80%)
        QuizAttempt::create([
            'quiz_id' => $htmlQuiz->id,
            'user_id' => $student->id,
            'attempt_number' => 2,
            'status' => 'completed',
            'started_at' => now()->subDay(),
            'completed_at' => now()->subDay()->addMinutes(10),
            'time_spent_seconds' => 600, // 10 minutes
            'answers' => [
                '1' => 'A',  // Correct
                '2' => 'B',  // Correct
                '3' => 'false',  // Correct
                '4' => 'B',  // Correct
                '5' => 'h2',  // Wrong (should be h1)
            ],
            'total_questions' => 5,
            'correct_answers' => 4,
            'score_percentage' => 80.00,
            'points_earned' => 5,
            'total_points' => 6,
            'passed' => true,
        ]);

        // Update quiz statistics
        $htmlQuiz->update([
            'total_attempts' => 2,
            'average_score' => 70.00,
        ]);

        $this->command->info('✓ Created 2 quiz attempts for student');
    }
}
