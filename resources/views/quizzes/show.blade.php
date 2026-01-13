@extends('layouts.app')

@section('title', $quiz->title)

@section('content')
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Quiz Header -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white p-6 shadow-lg">
        <div class="flex items-center mb-4">
            <a href="{{ route('lessons.show', $quiz->lesson->id) }}" class="mr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1">
                <p class="text-purple-200 text-sm">{{ $quiz->lesson->module->course->title }}</p>
                <h1 class="text-xl font-bold">{{ $quiz->title }}</h1>
            </div>
        </div>
    </div>

    <div class="px-4 py-6 space-y-4">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Quiz Information Card -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">📋 Quiz Information</h2>
            
            @if($quiz->description)
                <div class="mb-4 text-gray-700">
                    <p>{{ $quiz->description }}</p>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-blue-50 rounded-lg p-3">
                    <p class="text-xs text-blue-600 font-medium">Questions</p>
                    <p class="text-xl font-bold text-blue-900">{{ $quiz->total_questions }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-3">
                    <p class="text-xs text-green-600 font-medium">Passing Score</p>
                    <p class="text-xl font-bold text-green-900">{{ $quiz->passing_score }}%</p>
                </div>
                @if($quiz->hasTimeLimit())
                <div class="bg-orange-50 rounded-lg p-3">
                    <p class="text-xs text-orange-600 font-medium">Time Limit</p>
                    <p class="text-xl font-bold text-orange-900">{{ $quiz->time_limit_minutes }} min</p>
                </div>
                @endif
                @if($quiz->hasMaxAttempts())
                <div class="bg-purple-50 rounded-lg p-3">
                    <p class="text-xs text-purple-600 font-medium">Max Attempts</p>
                    <p class="text-xl font-bold text-purple-900">{{ $quiz->max_attempts }}</p>
                </div>
                @endif
            </div>

            @if($quiz->instructions)
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-gray-900 mb-2">📝 Instructions:</h3>
                    <p class="text-sm text-gray-700">{{ $quiz->instructions }}</p>
                </div>
            @endif
        </div>

        <!-- Best Score Card (if exists) -->
        @if($bestScore !== null)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">🏆 Your Best Score</h2>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-16 h-16 rounded-full {{ $bestScore >= $quiz->passing_score ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center mr-4">
                        <span class="text-2xl font-bold {{ $bestScore >= $quiz->passing_score ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($bestScore, 0) }}%
                        </span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">
                            @if($bestScore >= $quiz->passing_score)
                                ✅ Passed
                            @else
                                ❌ Not Passed
                            @endif
                        </p>
                        <p class="text-sm text-gray-600">Out of {{ $previousAttempts->count() }} attempt(s)</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- In-Progress Attempt Alert -->
        @if($inProgressAttempt)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
            <div class="flex items-start">
                <span class="text-2xl mr-3">⚠️</span>
                <div class="flex-1">
                    <p class="font-semibold text-yellow-900">You have an in-progress attempt</p>
                    <p class="text-sm text-yellow-700 mt-1">Continue where you left off or start a new attempt.</p>
                    <a href="{{ route('quizzes.take', $inProgressAttempt->id) }}" 
                       class="inline-block mt-3 px-4 py-2 bg-yellow-600 text-white rounded-lg font-medium hover:bg-yellow-700 active:scale-95 transition">
                        Continue Quiz
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Previous Attempts -->
        @if($previousAttempts->count() > 0)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">📊 Previous Attempts</h2>
            <div class="space-y-3">
                @foreach($previousAttempts->take(5) as $attempt)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-900">Attempt #{{ $attempt->attempt_number }}</span>
                            @if($attempt->passed)
                                <span class="ml-2 text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">Passed</span>
                            @else
                                <span class="ml-2 text-xs px-2 py-1 bg-red-100 text-red-800 rounded-full">Failed</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-600 mt-1">
                            {{ $attempt->created_at->format('M d, Y') }} • {{ $attempt->getTimeTaken() }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($attempt->score_percentage, 0) }}%
                        </p>
                        <a href="{{ route('quizzes.result', $attempt->id) }}" class="text-xs text-blue-600 hover:underline">
                            View Details
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Start Quiz Button -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            @if($canAttempt)
                <form action="{{ route('quizzes.start', $quiz->id) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl font-bold text-lg hover:from-purple-700 hover:to-purple-800 active:scale-95 transition shadow-lg">
                        {{ $previousAttempts->count() > 0 ? '🔄 Start New Attempt' : '🚀 Start Quiz' }}
                    </button>
                </form>
                @if($quiz->hasMaxAttempts())
                    <p class="text-center text-sm text-gray-600 mt-3">
                        {{ $quiz->max_attempts - $previousAttempts->count() }} attempt(s) remaining
                    </p>
                @endif
            @else
                <div class="text-center py-4">
                    <p class="text-gray-600 mb-2">❌ You have reached the maximum number of attempts</p>
                    <p class="text-sm text-gray-500">Contact your instructor if you need more attempts</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
