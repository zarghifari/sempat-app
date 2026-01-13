@extends('layouts.app')

@section('title', 'Quiz Result')

@section('content')
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Result Header -->
    <div class="bg-gradient-to-r {{ $attempt->passed ? 'from-green-600 to-green-700' : 'from-red-600 to-red-700' }} text-white p-6 shadow-lg">
        <div class="text-center">
            <div class="text-6xl mb-2">{{ $attempt->passed ? '🎉' : '😔' }}</div>
            <h1 class="text-2xl font-bold mb-2">
                {{ $attempt->passed ? 'Congratulations!' : 'Keep Trying!' }}
            </h1>
            <p class="text-lg opacity-90">{{ $quiz->title }}</p>
        </div>
    </div>

    <div class="px-4 py-6 space-y-4">
        <!-- Score Card -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-32 h-32 rounded-full {{ $attempt->passed ? 'bg-green-100' : 'bg-red-100' }} mb-4">
                    <span class="text-5xl font-bold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($attempt->score_percentage, 0) }}%
                    </span>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $attempt->getScoreLabel() }}</h2>
                <p class="text-gray-600">
                    @if($attempt->passed)
                        ✓ You passed the quiz!
                    @else
                        ✗ Passing score: {{ $quiz->passing_score }}%
                    @endif
                </p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $attempt->correct_answers }}</p>
                    <p class="text-xs text-blue-700 font-medium">Correct Answers</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $attempt->total_questions }}</p>
                    <p class="text-xs text-purple-700 font-medium">Total Questions</p>
                </div>
                <div class="bg-orange-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-orange-600">{{ $attempt->getTimeTaken() }}</p>
                    <p class="text-xs text-orange-700 font-medium">Time Taken</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $attempt->points_earned }}/{{ $attempt->total_points }}</p>
                    <p class="text-xs text-green-700 font-medium">Points Earned</p>
                </div>
            </div>

            <!-- Attempt Info -->
            <div class="border-t border-gray-200 pt-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Attempt Number:</span>
                    <span class="font-semibold text-gray-900">#{{ $attempt->attempt_number }}</span>
                </div>
                <div class="flex items-center justify-between text-sm mt-2">
                    <span class="text-gray-600">Completed At:</span>
                    <span class="font-semibold text-gray-900">{{ $attempt->completed_at->format('M d, Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Teacher Feedback (if exists) -->
        @if($attempt->teacher_feedback)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-3 flex items-center">
                <span class="text-xl mr-2">💬</span>
                Teacher's Feedback
            </h3>
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-gray-700">{{ $attempt->teacher_feedback }}</p>
                @if($attempt->graded_at)
                    <p class="text-xs text-gray-500 mt-2">
                        Graded by {{ $attempt->grader->first_name }} on {{ $attempt->graded_at->format('M d, Y') }}
                    </p>
                @endif
            </div>
        </div>
        @endif

        <!-- Review Answers -->
        @if($showAnswers)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                <span class="text-xl mr-2">📋</span>
                Review Answers
            </h3>

            <div class="space-y-4">
                @foreach($quiz->questions as $index => $question)
                @php
                    $userAnswer = $attempt->answers[$question->id] ?? null;
                    $isCorrect = $question->checkAnswer($userAnswer);
                @endphp

                <div class="border-2 rounded-lg p-4 {{ $isCorrect === true ? 'border-green-200 bg-green-50' : ($isCorrect === false ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-gray-50') }}">
                    <!-- Question Header -->
                    <div class="flex items-start justify-between mb-2">
                        <span class="text-sm font-bold text-gray-900">Question {{ $index + 1 }}</span>
                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $isCorrect === true ? 'bg-green-200 text-green-800' : '' }}
                            {{ $isCorrect === false ? 'bg-red-200 text-red-800' : '' }}
                            {{ $isCorrect === null ? 'bg-gray-200 text-gray-800' : '' }}">
                            {{ $isCorrect === true ? '✓ Correct' : ($isCorrect === false ? '✗ Incorrect' : 'Pending Review') }}
                        </span>
                    </div>

                    <!-- Question Text -->
                    <p class="text-gray-900 mb-3">{{ $question->question }}</p>

                    <!-- Your Answer -->
                    <div class="mb-2">
                        <p class="text-xs font-semibold text-gray-700 mb-1">Your Answer:</p>
                        <p class="text-sm text-gray-900 bg-white/70 rounded px-3 py-2">
                            {{ $userAnswer ?? 'No answer provided' }}
                        </p>
                    </div>

                    <!-- Correct Answer -->
                    @if($isCorrect !== null)
                    <div class="mb-2">
                        <p class="text-xs font-semibold text-gray-700 mb-1">Correct Answer:</p>
                        <p class="text-sm font-medium text-green-700 bg-green-100 rounded px-3 py-2">
                            {{ $question->correct_answer }}
                        </p>
                    </div>
                    @endif

                    <!-- Explanation -->
                    @if($question->explanation)
                    <div class="mt-3 pt-3 border-t border-gray-300">
                        <p class="text-xs font-semibold text-gray-700 mb-1">💡 Explanation:</p>
                        <p class="text-sm text-gray-700">{{ $question->explanation }}</p>
                    </div>
                    @endif

                    <!-- Points -->
                    <div class="mt-2">
                        <p class="text-xs text-gray-600">
                            Points: {{ $isCorrect === true ? $question->points : 0 }} / {{ $question->points }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="space-y-3">
            @if(!$attempt->passed && $quiz->userCanAttempt(auth()->user()))
            <form action="{{ route('quizzes.start', $quiz->id) }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl font-bold hover:from-purple-700 hover:to-purple-800 active:scale-95 transition shadow-lg">
                    🔄 Try Again
                </button>
            </form>
            @endif

            <a href="{{ route('lessons.show', $quiz->lesson->id) }}" 
               class="block w-full py-4 bg-white border-2 border-gray-300 text-gray-700 text-center rounded-xl font-bold hover:bg-gray-50 active:scale-95 transition">
                ← Back to Lesson
            </a>

            <a href="{{ route('courses.show', $quiz->lesson->module->course->id) }}" 
               class="block w-full py-4 bg-white border-2 border-gray-300 text-gray-700 text-center rounded-xl font-bold hover:bg-gray-50 active:scale-95 transition">
                📚 Back to Course
            </a>
        </div>

        <!-- Motivation Message -->
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 text-center">
            @if($attempt->passed)
                <p class="text-lg font-semibold text-gray-900 mb-2">🎓 Well Done!</p>
                <p class="text-sm text-gray-700">
                    You've successfully completed this quiz. Keep up the great work and continue learning!
                </p>
            @else
                <p class="text-lg font-semibold text-gray-900 mb-2">💪 Don't Give Up!</p>
                <p class="text-sm text-gray-700">
                    Review the material and try again. Every attempt brings you closer to success!
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
