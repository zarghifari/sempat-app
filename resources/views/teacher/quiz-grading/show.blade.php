@extends('layouts.app')

@section('title', 'Grade Quiz Attempt')

@section('page-title', 'Grade Quiz Attempt')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 pb-24 pt-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('teacher.quiz-grading.index') }}" class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-700 font-medium text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Quiz Grading
            </a>
        </div>

        <!-- Quiz Attempt Header -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-4 sm:p-6 text-white">
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs sm:text-sm font-semibold">
                        Quiz Attempt
                    </span>
                    @if($attempt->status === 'pending')
                        <span class="px-3 py-1 bg-yellow-500/80 rounded-full text-xs sm:text-sm font-medium">
                            Pending Review
                        </span>
                    @else
                        <span class="px-3 py-1 bg-green-500/80 rounded-full text-xs sm:text-sm font-medium">
                            Graded
                        </span>
                    @endif
                </div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold mb-2">{{ $attempt->quiz_title }}</h1>
                <p class="text-white/90 text-sm sm:text-base">{{ $attempt->lesson_title }} • {{ $attempt->module_title }}</p>
            </div>
            <div class="p-4 sm:p-6">
                <!-- Student Info -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg sm:text-xl flex-shrink-0">
                            {{ strtoupper(substr($attempt->student_name, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-gray-900">{{ $attempt->student_name }}</h3>
                            <p class="text-xs sm:text-sm text-gray-600">{{ $attempt->student_email }}</p>
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-xs sm:text-sm text-gray-600">Submitted</p>
                        <p class="text-sm sm:text-base font-semibold text-gray-900">{{ \Carbon\Carbon::parse($attempt->submitted_at)->format('M d, Y h:i A') }}</p>
                    </div>
                </div>

                <!-- Quiz Details -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Course</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $attempt->course_title }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Passing Score</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $attempt->passing_score }}%</p>
                    </div>
                    @if($attempt->status === 'graded')
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Final Score</p>
                            <p class="text-sm font-semibold {{ $attempt->score >= $attempt->passing_score ? 'text-green-600' : 'text-red-600' }}">
                                {{ $attempt->score }}%
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Grading Form -->
        <form action="{{ route('teacher.quiz-grading.grade', $attempt->id) }}" method="POST">
            @csrf
            <div class="space-y-6">
                @foreach($answers as $index => $answer)
                    <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
                        <!-- Question Header -->
                        <div class="flex items-start justify-between gap-3 mb-4 pb-4 border-b">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">
                                        Question {{ $index + 1 }}
                                    </span>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                        {{ ucfirst($answer->question_type) }}
                                    </span>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">
                                        {{ $answer->points }} {{ Str::plural('point', $answer->points) }}
                                    </span>
                                </div>
                                <h3 class="text-base sm:text-lg font-bold text-gray-900">{{ $answer->question_text }}</h3>
                            </div>
                        </div>

                        <!-- Answer Display -->
                        <div class="mb-4">
                            <p class="text-xs sm:text-sm font-semibold text-gray-700 mb-2">Student Answer:</p>
                            
                            @if($answer->question_type === 'multiple_choice' || $answer->question_type === 'true_false')
                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-sm font-medium text-gray-900">{{ $answer->answer_text }}</p>
                                </div>
                                
                                @if($answer->correct_answer)
                                    <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                                        <p class="text-xs text-green-700 font-medium mb-1">Correct Answer:</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $answer->correct_answer }}</p>
                                    </div>
                                @endif
                            @elseif($answer->question_type === 'essay' || $answer->question_type === 'short_answer')
                                <div class="p-3 sm:p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $answer->answer_text ?: 'No answer provided' }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Grading Section -->
                        @if($attempt->status === 'pending')
                            <div class="space-y-4">
                                <div>
                                    <label for="score_{{ $answer->id }}" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Score (Max: {{ $answer->points }})
                                    </label>
                                    <input type="number" 
                                           id="score_{{ $answer->id }}" 
                                           name="answers[{{ $answer->id }}][score]" 
                                           min="0" 
                                           max="{{ $answer->points }}" 
                                           step="0.5"
                                           value="{{ old("answers.{$answer->id}.score", $answer->score ?? 0) }}" 
                                           class="w-full sm:w-32 px-3 sm:px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm sm:text-base" 
                                           required>
                                    @error("answers.{$answer->id}.score")
                                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="feedback_{{ $answer->id }}" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Feedback (Optional)
                                    </label>
                                    <textarea id="feedback_{{ $answer->id }}" 
                                              name="answers[{{ $answer->id }}][feedback]" 
                                              rows="2" 
                                              class="w-full px-3 sm:px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm sm:text-base" 
                                              placeholder="Provide feedback to student...">{{ old("answers.{$answer->id}.feedback", $answer->feedback ?? '') }}</textarea>
                                    @error("answers.{$answer->id}.feedback")
                                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @else
                            <!-- Already Graded -->
                            <div class="p-3 sm:p-4 bg-gray-50 rounded-lg space-y-2">
                                <div>
                                    <p class="text-xs text-gray-600">Score</p>
                                    <p class="text-sm sm:text-base font-bold text-gray-900">{{ $answer->score }} / {{ $answer->points }}</p>
                                </div>
                                @if($answer->feedback)
                                    <div>
                                        <p class="text-xs text-gray-600">Feedback</p>
                                        <p class="text-sm text-gray-900">{{ $answer->feedback }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Submit Button -->
            @if($attempt->status === 'pending')
                <div class="mt-6 flex flex-col-reverse sm:flex-row gap-3 sm:gap-4">
                    <a href="{{ route('teacher.quiz-grading.index') }}" 
                       class="w-full sm:w-auto text-center px-4 sm:px-6 py-2.5 sm:py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm sm:text-base">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:from-purple-700 hover:to-blue-700 transition-colors font-medium shadow-lg text-sm sm:text-base">
                        Submit Grades
                    </button>
                </div>
            @else
                <div class="mt-6">
                    <a href="{{ route('teacher.quiz-grading.index') }}" 
                       class="inline-block px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium text-sm sm:text-base">
                        Back to List
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
