<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING QUIZ DATA ===\n\n";

// Get all quizzes
$quizzes = \App\Models\Quiz::with('lesson.module.course')->get();

echo "Total Quizzes: " . $quizzes->count() . "\n\n";

foreach ($quizzes as $quiz) {
    echo "----------------------------------------\n";
    echo "Quiz ID: " . $quiz->id . "\n";
    echo "Title: " . $quiz->title . "\n";
    echo "Lesson ID: " . $quiz->lesson_id . "\n";
    echo "Lesson: " . ($quiz->lesson->title ?? 'N/A') . "\n";
    echo "Course: " . ($quiz->lesson->module->course->title ?? 'N/A') . "\n";
    echo "Questions: " . $quiz->total_questions . "\n";
    echo "Time Limit: " . ($quiz->time_limit_minutes ?? 'No limit') . " minutes\n";
    echo "Passing Score: " . $quiz->passing_score . "%\n";
    echo "Status: " . $quiz->status . "\n";
    echo "URL: /lessons/{$quiz->lesson_id}/quizzes/{$quiz->id}\n";
    echo "----------------------------------------\n\n";
}

// Get all quiz questions
$totalQuestions = \App\Models\QuizQuestion::count();
echo "\nTotal Questions in Database: " . $totalQuestions . "\n";

// Get all quiz attempts
$totalAttempts = \App\Models\QuizAttempt::count();
echo "Total Quiz Attempts: " . $totalAttempts . "\n\n";

echo "=== DONE ===\n";
