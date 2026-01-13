<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== QUIZ ATTEMPTS VERIFICATION ===\n\n";

// Get all quiz attempts
$attempts = \App\Models\QuizAttempt::with(['quiz', 'user'])->get();

echo "Total Quiz Attempts: " . $attempts->count() . "\n\n";

foreach ($attempts as $attempt) {
    echo "==========================================\n";
    echo "Attempt ID: " . $attempt->id . "\n";
    echo "Quiz: " . $attempt->quiz->title . "\n";
    echo "User: " . $attempt->user->name . " (" . $attempt->user->email . ")\n";
    echo "Attempt #: " . $attempt->attempt_number . "\n";
    echo "Status: " . $attempt->status . "\n";
    echo "Score: " . $attempt->score_percentage . "%\n";
    echo "Correct Answers: " . $attempt->correct_answers . "/" . $attempt->total_questions . "\n";
    echo "Points: " . $attempt->points_earned . "/" . $attempt->total_points . "\n";
    echo "Passed: " . ($attempt->passed ? 'YES' : 'NO') . "\n";
    echo "Time Spent: " . $attempt->getTimeTaken() . "\n";
    echo "Completed At: " . $attempt->completed_at->format('Y-m-d H:i:s') . "\n";
    echo "\nAnswers:\n";
    foreach ($attempt->answers as $questionId => $answer) {
        echo "  Question $questionId: $answer\n";
    }
    echo "==========================================\n\n";
}

// Get quiz statistics
echo "\n=== QUIZ STATISTICS ===\n\n";
$quizzes = \App\Models\Quiz::all();
foreach ($quizzes as $quiz) {
    echo "Quiz: " . $quiz->title . "\n";
    echo "  Total Attempts: " . $quiz->total_attempts . "\n";
    echo "  Average Score: " . ($quiz->average_score ?? 'N/A') . "%\n\n";
}

// Get student's best scores
$student = \App\Models\User::whereHas('roles', function($q) {
    $q->where('slug', 'student');
})->first();

if ($student) {
    echo "\n=== STUDENT QUIZ PROGRESS ===\n";
    echo "Student: " . $student->name . "\n\n";
    
    foreach ($quizzes as $quiz) {
        $attempts = $quiz->attempts()->where('user_id', $student->id)->count();
        $bestScore = $quiz->getBestScore($student->id);
        
        echo "Quiz: " . $quiz->title . "\n";
        echo "  Attempts: $attempts / " . ($quiz->max_attempts ?: '∞') . "\n";
        echo "  Best Score: " . ($bestScore !== null ? number_format($bestScore, 1) . '%' : 'Not attempted') . "\n";
        echo "  Can Attempt: " . ($quiz->userCanAttempt($student) ? 'YES' : 'NO') . "\n\n";
    }
}

echo "\n=== VERIFICATION COMPLETE ===\n";
