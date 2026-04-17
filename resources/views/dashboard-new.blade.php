@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Welcome Card with Streak -->
    <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-purple-700 text-white p-6 mx-4 mb-4 rounded-2xl shadow-xl">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <h2 class="text-2xl font-bold drop-shadow-md">Hello, {{ Auth::user()->first_name }}! 👋</h2>
                <p class="mt-1 text-blue-50 text-sm">
                    Ready to learn something new today?
                </p>
            </div>
            @if(Auth::user()->avatar)
                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-14 h-14 rounded-full border-2 border-white shadow-lg">
            @else
                <div class="w-14 h-14 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-xl font-bold shadow-lg">
                    {{ substr(Auth::user()->first_name, 0, 1) }}
                </div>
            @endif
        </div>

        <!-- Study Streak -->
        <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-xl p-3">
            <div class="text-3xl">🔥</div>
            <div class="flex-1">
                <p class="text-xs text-blue-100">Study Streak</p>
                <p class="text-xl font-bold">{{ $stats['current_streak'] ?? 0 }} Days</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-blue-100">Keep it up!</p>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="mx-4 mb-4">
            <x-alert variant="success">{{ session('success') }}</x-alert>
        </div>
    @endif

    <!-- Daily Learning Goal -->
    <div class="mx-4 mb-4">
        <div class="bg-gradient-to-r from-orange-50 to-amber-50 border-l-4 border-orange-500 rounded-xl p-4 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-xl">🎯</span>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-gray-900 mb-1">Today's Goal</h3>
                    <p class="text-sm text-gray-700 mb-2">Complete 3 lessons to reach your daily goal</p>
                    
                    <!-- Progress Bar -->
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-orange-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-orange-500 h-full rounded-full transition-all duration-500" style="width: 33%"></div>
                        </div>
                        <span class="text-xs font-bold text-orange-600">1/3</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="mb-4">
        <div class="grid grid-cols-4 gap-2 px-4">
            <!-- Enrolled Courses -->
            <div class="bg-white rounded-xl p-3 shadow-sm text-center">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-2 mx-auto">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <p class="text-xl font-bold text-gray-900">{{ $stats['enrolled_courses'] }}</p>
                <p class="text-xs text-gray-600 mt-1">Courses</p>
            </div>

            <!-- Completed -->
            <div class="bg-white rounded-xl p-3 shadow-sm text-center">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-2 mx-auto">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xl font-bold text-gray-900">{{ $stats['completed_courses'] }}</p>
                <p class="text-xs text-gray-600 mt-1">Done</p>
            </div>

            <!-- Study Hours -->
            <div class="bg-white rounded-xl p-3 shadow-sm text-center">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-2 mx-auto">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xl font-bold text-gray-900">{{ $stats['total_study_hours'] }}h</p>
                <p class="text-xs text-gray-600 mt-1">Studied</p>
            </div>

            <!-- Achievements -->
            <div class="bg-white rounded-xl p-3 shadow-sm text-center">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mb-2 mx-auto">
                    <span class="text-xl">🏆</span>
                </div>
                <p class="text-xl font-bold text-gray-900">{{ $stats['completed_courses'] * 3 }}</p>
                <p class="text-xs text-gray-600 mt-1">Badges</p>
            </div>
        </div>
    </div>

    <!-- Motivational Quote -->
    <div class="mx-4 mb-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-indigo-500">
            <p class="text-sm text-gray-700 italic mb-2">
                "The expert in anything was once a beginner."
            </p>
            <p class="text-xs text-gray-500">- Helen Hayes</p>
        </div>
    </div>

    <!-- Today's Focus / Recommended Lessons -->
    @if($courses->count() > 0)
    <div class="px-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <span class="text-xl">📚</span>
                Today's Focus
            </h3>
        </div>
        
        <!-- First Course Card with Continue Button -->
        @php $firstCourse = $courses->first(); @endphp
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-5 shadow-lg text-white mb-3">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <p class="text-xs text-indigo-100 mb-1">Continue Learning</p>
                    <h4 class="font-bold text-lg mb-2">{{ $firstCourse['title'] }}</h4>
                    
                    <!-- Progress -->
                    <div class="flex items-center gap-2 mb-3">
                        <div class="flex-1 bg-white/30 rounded-full h-2 overflow-hidden">
                            <div class="bg-white h-full rounded-full transition-all" style="width: {{ $firstCourse['progress'] }}%"></div>
                        </div>
                        <span class="text-sm font-bold">{{ round($firstCourse['progress']) }}%</span>
                    </div>

                    <a href="{{ route('courses.show', $firstCourse['id']) }}" 
                       class="inline-flex items-center gap-2 bg-white text-indigo-600 px-4 py-2 rounded-lg font-medium text-sm hover:bg-indigo-50 transition shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Continue
                    </a>
                </div>
                <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Continue Learning (Other Courses) -->
    @if($courses->count() > 1)
    <div class="px-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-base font-bold text-gray-900">More Courses</h3>
            <a href="{{ route('courses.index') }}" class="text-sm text-blue-600 font-medium">See All</a>
        </div>

        <div class="space-y-3">
            @foreach($courses->skip(1) as $course)
            <x-course-card 
                href="{{ route('courses.show', $course['id']) }}"
                title="{{ $course['title'] }}"
                description="Continue where you left off"
                :progress="$course['progress']"
            >
                <x-slot name="thumbnail">
                    <div class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </x-slot>
            </x-course-card>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Quick Actions for Students -->
    <div class="px-4 mb-4">
        <h3 class="text-base font-bold text-gray-900 mb-3">Quick Actions</h3>
        <div class="grid grid-cols-2 gap-3">
            <!-- Browse Courses -->
            <a href="{{ route('courses.index') }}" class="bg-white rounded-xl p-4 shadow-sm active:scale-95 transition">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm">Browse Courses</h4>
                <p class="text-xs text-gray-600 mt-1">Find new content</p>
            </a>

            <!-- Articles -->
            <a href="{{ route('articles.index') }}" class="bg-white rounded-xl p-4 shadow-sm active:scale-95 transition">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-3">
                    <span class="text-2xl">📰</span>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm">Articles</h4>
                <p class="text-xs text-gray-600 mt-1">Read & learn</p>
            </a>

            <!-- Learning Journal -->
            <a href="{{ route('learning-journals.index') }}" class="bg-white rounded-xl p-4 shadow-sm active:scale-95 transition">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm">My Journal</h4>
                <p class="text-xs text-gray-600 mt-1">Track progress</p>
            </a>

            <!-- Progress -->
            <a href="{{ route('progress.index') }}" class="bg-white rounded-xl p-4 shadow-sm active:scale-95 transition">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm">My Progress</h4>
                <p class="text-xs text-gray-600 mt-1">View stats</p>
            </a>
        </div>
    </div>

    <!-- Recent Activity Section -->
    @if(isset($recentActivity) && $recentActivity->count() > 0)
    <div class="px-4 mb-6">
        <h3 class="text-base font-bold text-gray-900 mb-3">Recent Activity</h3>
        <div class="bg-white rounded-xl shadow-sm divide-y divide-gray-100">
            @foreach($recentActivity->take(5) as $activity)
            <a href="{{ $activity['link'] }}" class="block p-3 hover:bg-gray-50 active:bg-gray-100 transition">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 bg-{{ $activity['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0 text-base">
                        {{ $activity['icon'] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm truncate">
                            {{ $activity['title'] }}
                        </p>
                        <p class="text-xs text-gray-600">
                            {{ $activity['description'] }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $activity['time'] }}
                        </p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Empty State for New Users -->
    @if($courses->count() == 0)
    <div class="px-4 mb-6">
        <div class="bg-white rounded-2xl p-8 shadow-sm text-center">
            <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Start Your Learning Journey!</h3>
            <p class="text-gray-600 mb-6">Enroll in courses and begin your path to mastery</p>
            <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-medium hover:from-blue-700 hover:to-purple-700 transition shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Browse Courses
            </a>
        </div>
    </div>
    @endif

    <!-- Teacher Tools (if teacher) -->
    @if(Auth::user()->hasRole('teacher'))
    <div class="px-4 mb-6">
        <h3 class="text-base font-bold text-gray-900 mb-3">Teacher Tools</h3>
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('teacher.dashboard') }}" class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-4 shadow-lg text-white active:scale-95 transition">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                </div>
                <h4 class="font-semibold text-sm">Teacher Dashboard</h4>
                <p class="text-xs text-white/80 mt-1">Manage content</p>
            </a>

            <a href="{{ route('document-imports.index') }}" class="bg-white rounded-xl p-4 shadow-sm active:scale-95 transition">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-3">
                    <span class="text-2xl">📄</span>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm">Import Document</h4>
                <p class="text-xs text-gray-600 mt-1">Upload & convert</p>
            </a>
        </div>
    </div>
    @endif
@endsection
