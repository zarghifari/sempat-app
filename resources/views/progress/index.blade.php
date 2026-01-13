@extends('layouts.app', ['showBack' => true])

@section('title', 'Progress')

@section('content')
    <!-- Page Header -->
    <div class="mb-6 px-4">
        <h1 class="text-2xl font-bold text-gray-900">Learning Progress</h1>
        <p class="text-sm text-gray-600 mt-1">Track your learning journey</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 gap-4 mb-6 px-4">
        <!-- Total Courses -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 text-white shadow-md">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="text-sm font-medium">Enrolled</span>
            </div>
            <div class="text-4xl font-bold mb-1">{{ $stats['enrolled_courses'] }}</div>
            <div class="text-xs text-blue-100">of {{ $stats['total_courses'] }} courses</div>
        </div>

        <!-- Completed Courses -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-5 text-white shadow-md">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium">Completed</span>
            </div>
            <div class="text-4xl font-bold mb-1">{{ $stats['completed_courses'] }}</div>
            <div class="text-xs text-green-100">{{ round(($stats['completed_courses']/$stats['enrolled_courses'])*100) }}% completion</div>
        </div>

        <!-- Study Hours -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-5 text-white shadow-md">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium">Study Time</span>
            </div>
            <div class="text-4xl font-bold mb-1">{{ $stats['total_study_hours'] }}h</div>
            <div class="text-xs text-purple-100">Total hours</div>
        </div>

        <!-- Current Streak -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-5 text-white shadow-md">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                </svg>
                <span class="text-sm font-medium">Streak</span>
            </div>
            <div class="text-4xl font-bold mb-1">{{ $stats['current_streak'] }}</div>
            <div class="text-xs text-orange-100">days in a row 🔥</div>
        </div>
    </div>

    <!-- Overall Progress Circle -->
    <div class="bg-white rounded-xl p-6 mb-6 shadow-sm mx-4">
        <h2 class="font-bold text-gray-900 mb-6 text-center">Overall Progress</h2>
        <div class="flex items-center justify-center py-4">
            <div class="relative w-44 h-44">
                <!-- Progress Circle -->
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="88" cy="88" r="75" fill="none" stroke="#E5E7EB" stroke-width="12"></circle>
                    <circle cx="88" cy="88" r="75" fill="none" stroke="#3B82F6" stroke-width="12"
                            stroke-dasharray="{{ 2 * 3.14159 * 75 }}"
                            stroke-dashoffset="{{ 2 * 3.14159 * 75 * (1 - $stats['overall_progress']/100) }}"
                            stroke-linecap="round"></circle>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <div class="text-4xl font-bold text-gray-900">{{ $stats['overall_progress'] }}%</div>
                    <div class="text-sm text-gray-600 mt-1">Complete</div>
                </div>
            </div>
        </div>
        <div class="text-center mt-6 pt-4 border-t border-gray-100">
            <p class="text-sm text-gray-600">{{ $stats['completed_lessons'] }} of {{ $stats['total_lessons'] }} lessons completed</p>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="mb-6 px-4">
        <h2 class="font-bold text-gray-900 mb-4">Recent Activity</h2>
        <div class="space-y-3">
            @foreach($recentActivity as $activity)
                <div class="bg-white rounded-xl p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 {{ $activity['type'] === 'quiz' ? 'bg-purple-100' : 'bg-blue-100' }} rounded-lg flex items-center justify-center flex-shrink-0">
                            @if($activity['type'] === 'quiz')
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                            <p class="text-xs text-gray-600">{{ $activity['course'] }}</p>
                            @if(isset($activity['score']))
                                <div class="mt-1 inline-block px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded">
                                    Score: {{ $activity['score'] }}%
                                </div>
                            @endif
                        </div>
                        <span class="text-xs text-gray-500">{{ $activity['time'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Enrolled Courses Progress -->
    <div class="mb-20 px-4">
        <h2 class="font-bold text-gray-900 mb-4">Course Progress</h2>
        <div class="space-y-3">
            @foreach($enrolledCourses as $course)
                <a href="{{ route('courses.show', $course['id']) }}" 
                   class="block bg-white rounded-xl p-4 shadow-sm active:scale-95 transition-transform">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-900">{{ $course['title'] }}</h3>
                        @if($course['progress'] == 100)
                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Complete
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-600 mb-3">{{ $course['completed_lessons'] }}/{{ $course['total_lessons'] }} lessons • Last accessed {{ $course['last_accessed'] }}</p>
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full {{ $course['progress'] == 100 ? 'bg-green-500' : 'bg-blue-500' }} rounded-full transition-all" 
                                 style="width: {{ $course['progress'] }}%"></div>
                        </div>
                        <span class="text-sm font-medium {{ $course['progress'] == 100 ? 'text-green-600' : 'text-blue-600' }}">
                            {{ $course['progress'] }}%
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection
