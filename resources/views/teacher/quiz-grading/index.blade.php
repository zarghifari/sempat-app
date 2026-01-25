@extends('layouts.app')

@section('title', 'Quiz Grading')

@section('page-title', 'Quiz Grading')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 pb-24 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Quiz Grading</h1>
            <p class="text-sm sm:text-base text-gray-600">Review and grade student quiz attempts</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 mb-6">
            <form action="{{ route('teacher.quiz-grading.index') }}" method="GET" class="space-y-4 sm:space-y-0 sm:flex sm:items-end sm:gap-4">
                <div class="flex-1">
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select id="status" 
                            name="status" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Review</option>
                        <option value="graded" {{ request('status') == 'graded' ? 'selected' : '' }}>Graded</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label for="course_id" class="block text-sm font-semibold text-gray-700 mb-2">Course</label>
                    <select id="course_id" 
                            name="course_id" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" 
                            class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:from-purple-700 hover:to-blue-700 transition-colors font-medium shadow-lg text-sm">
                        Filter
                    </button>
                    @if(request()->hasAny(['status', 'course_id']))
                        <a href="{{ route('teacher.quiz-grading.index') }}" 
                           class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium text-sm">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Quiz Attempts List -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            @if($attempts->isEmpty())
                <div class="text-center py-16 px-4">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Quiz Attempts</h3>
                    <p class="text-gray-600">
                        @if(request('status') == 'pending')
                            No pending quiz attempts to grade.
                        @elseif(request('status') == 'graded')
                            No graded quizzes found.
                        @else
                            No quiz attempts found for your courses.
                        @endif
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Student
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden md:table-cell">
                                    Quiz
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden lg:table-cell">
                                    Course
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden sm:table-cell">
                                    Submitted
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($attempts as $attempt)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                                {{ strtoupper(substr($attempt->student_name, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-gray-900 text-sm">{{ $attempt->student_name }}</p>
                                                <p class="text-xs text-gray-500 truncate">{{ $attempt->student_email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 hidden md:table-cell">
                                        <div>
                                            <p class="font-medium text-gray-900 text-sm">{{ Str::limit($attempt->quiz_title, 30) }}</p>
                                            <p class="text-xs text-gray-500">{{ $attempt->lesson_title }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 hidden lg:table-cell">
                                        <p class="text-sm text-gray-600">{{ Str::limit($attempt->course_title, 25) }}</p>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4">
                                        @if($attempt->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                                Pending
                                            </span>
                                        @elseif($attempt->status === 'graded')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Graded: {{ $attempt->score }}%
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 hidden sm:table-cell">
                                        <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($attempt->submitted_at)->diffForHumans() }}</p>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 text-right">
                                        <a href="{{ route('teacher.quiz-grading.show', $attempt->id) }}" 
                                           class="inline-flex items-center gap-1 px-4 py-2 {{ $attempt->status === 'pending' ? 'bg-purple-100 text-purple-700 hover:bg-purple-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} rounded-lg transition-colors font-medium text-sm">
                                            <span class="hidden sm:inline">{{ $attempt->status === 'pending' ? 'Grade Now' : 'View Details' }}</span>
                                            <span class="sm:hidden">{{ $attempt->status === 'pending' ? 'Grade' : 'View' }}</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($attempts->hasPages())
                    <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                        {{ $attempts->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
