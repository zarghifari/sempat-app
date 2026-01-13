<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Display quiz for a lesson
     */
    public function show($lessonId, $quizId)
    {
        $quiz = Quiz::with(['questions', 'lesson.module.course'])
            ->where('id', $quizId)
            ->where('lesson_id', $lessonId)
            ->where('status', 'published')
            ->firstOrFail();

        $user = auth()->user();

        // Check if user can attempt this quiz
        $canAttempt = $quiz->userCanAttempt($user);
        $previousAttempts = $quiz->getUserAttempts($user->id);
        $bestScore = $quiz->getBestScore($user->id);

        // Check for in-progress attempt
        $inProgressAttempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        return view('quizzes.show', compact(
            'quiz',
            'canAttempt',
            'previousAttempts',
            'bestScore',
            'inProgressAttempt'
        ));
    }

    /**
     * Start a new quiz attempt
     */
    public function start(Request $request, $quizId)
    {
        $quiz = Quiz::with('questions')->findOrFail($quizId);
        $user = auth()->user();

        // Check if user can attempt
        if (!$quiz->userCanAttempt($user)) {
            return redirect()->back()->with('error', 'You have reached the maximum number of attempts for this quiz.');
        }

        // Check for existing in-progress attempt
        $existingAttempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            return redirect()->route('quizzes.take', ['attemptId' => $existingAttempt->id]);
        }

        // Get attempt number
        $attemptNumber = $quiz->attempts()
            ->where('user_id', $user->id)
            ->max('attempt_number') + 1;

        // Create new attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'attempt_number' => $attemptNumber,
            'status' => 'in_progress',
            'started_at' => now(),
            'answers' => [],
            'total_questions' => $quiz->questions->count(),
            'total_points' => $quiz->questions->sum('points'),
        ]);

        return redirect()->route('quizzes.take', ['attemptId' => $attempt->id]);
    }

    /**
     * Display quiz taking interface
     */
    public function take($attemptId)
    {
        $attempt = QuizAttempt::with(['quiz.questions', 'quiz.lesson.module.course'])
            ->where('id', $attemptId)
            ->where('user_id', auth()->id())
            ->where('status', 'in_progress')
            ->firstOrFail();

        $quiz = $attempt->quiz;

        // Shuffle questions if enabled
        $questions = $quiz->shuffle_questions
            ? $quiz->questions->shuffle()
            : $quiz->questions;

        // Shuffle options if enabled
        if ($quiz->shuffle_options) {
            $questions = $questions->map(function ($question) {
                if ($question->isMultipleChoice() && is_array($question->options)) {
                    $question->shuffled_options = collect($question->options)->shuffle();
                }
                return $question;
            });
        }

        return view('quizzes.take', compact('attempt', 'quiz', 'questions'));
    }

    /**
     * Submit quiz answers
     */
    public function submit(Request $request, $attemptId)
    {
        $attempt = QuizAttempt::with('quiz.questions')
            ->where('id', $attemptId)
            ->where('user_id', auth()->id())
            ->where('status', 'in_progress')
            ->firstOrFail();

        $quiz = $attempt->quiz;

        // Validate answers
        $validatedData = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'nullable|string',
        ]);

        $answers = $validatedData['answers'];

        // Grade the quiz
        $correctAnswers = 0;
        $pointsEarned = 0;
        $needsManualGrading = false;

        foreach ($quiz->questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;

            if ($userAnswer) {
                $isCorrect = $question->checkAnswer($userAnswer);

                if ($isCorrect === true) {
                    $correctAnswers++;
                    $pointsEarned += $question->points;
                } elseif ($isCorrect === null) {
                    // Essay question - needs manual grading
                    $needsManualGrading = true;
                }
            }
        }

        // Calculate score
        $scorePercentage = $attempt->total_questions > 0
            ? ($correctAnswers / $attempt->total_questions) * 100
            : 0;

        $passed = $scorePercentage >= $quiz->passing_score;

        // Calculate time spent
        $timeSpent = now()->diffInSeconds($attempt->started_at);

        // Update attempt
        $attempt->update([
            'status' => $needsManualGrading ? 'in_progress' : 'completed',
            'completed_at' => now(),
            'time_spent_seconds' => $timeSpent,
            'answers' => $answers,
            'correct_answers' => $correctAnswers,
            'score_percentage' => $scorePercentage,
            'points_earned' => $pointsEarned,
            'passed' => $passed,
        ]);

        // Update quiz statistics
        $quiz->increment('total_attempts');
        $quiz->update([
            'average_score' => $quiz->attempts()
                ->where('status', 'completed')
                ->avg('score_percentage')
        ]);

        // Update lesson completion
        if ($passed) {
            $this->updateLessonCompletion($quiz->lesson_id, $attempt);
        }

        return redirect()->route('quizzes.result', ['attemptId' => $attempt->id])
            ->with('success', $needsManualGrading
                ? 'Quiz submitted! Your essay answers will be graded by your teacher.'
                : 'Quiz completed!');
    }

    /**
     * Display quiz results
     */
    public function result($attemptId)
    {
        $attempt = QuizAttempt::with([
            'quiz.questions',
            'quiz.lesson.module.course',
            'user'
        ])
            ->where('id', $attemptId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $quiz = $attempt->quiz;

        // Get correct answers if quiz allows showing them
        $showAnswers = $quiz->show_correct_answers && $attempt->isCompleted();

        return view('quizzes.result', compact('attempt', 'quiz', 'showAnswers'));
    }

    /**
     * Update lesson completion based on quiz result
     */
    private function updateLessonCompletion($lessonId, $attempt)
    {
        $lessonCompletion = \App\Models\LessonCompletion::firstOrCreate(
            [
                'user_id' => $attempt->user_id,
                'lesson_id' => $lessonId,
            ],
            [
                'is_completed' => false,
                'progress_percentage' => 0,
            ]
        );

        $lessonCompletion->update([
            'is_completed' => true,
            'progress_percentage' => 100,
            'completed_at' => now(),
            'quiz_score' => $attempt->score_percentage,
            'quiz_attempts' => DB::raw('quiz_attempts + 1'),
        ]);
    }
}
