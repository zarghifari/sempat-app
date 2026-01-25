@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Welcome Card -->
    <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 text-white p-6 mx-4 mb-6 rounded-2xl shadow-xl">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-2xl font-bold drop-shadow-md">Hello, {{ Auth::user()->first_name }}! 👋</h2>
                <p class="mt-2 text-blue-50 font-medium">
                    {{ Auth::user()->roles->pluck('name')->join(', ') }}
                </p>
            </div>
            @if(Auth::user()->avatar)
                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-16 h-16 rounded-full border-2 border-white">
            @else
                <div class="w-16 h-16 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-2xl font-bold">
                    {{ substr(Auth::user()->first_name, 0, 1) }}
                </div>
            @endif
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="mx-4 mb-4">
            <x-alert variant="success">{{ session('success') }}</x-alert>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="mb-6">
        <div class="px-4 mb-3">
            <h3 class="text-base font-bold text-gray-900">Quick Stats</h3>
        </div>
        <div class="overflow-x-auto px-4">
            <div class="flex gap-3 min-w-max">
                <!-- Stat Card 1 -->
                <div class="bg-white rounded-xl p-3 shadow-sm flex-1 min-w-[120px]">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 mb-0.5">Enrolled</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['enrolled_courses'] }}</p>
                </div>

                <!-- Stat Card 2 -->
                <div class="bg-white rounded-xl p-3 shadow-sm flex-1 min-w-[120px]">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 mb-0.5">Completed</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['completed_courses'] }}</p>
                </div>

                <!-- Stat Card 3 -->
                <div class="bg-white rounded-xl p-3 shadow-sm flex-1 min-w-[120px]">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 mb-0.5">Study Hours</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['total_study_hours'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    @if(isset($recentActivity) && $recentActivity->count() > 0)
    <div class="px-4 mb-6">
        <h3 class="text-base font-bold text-gray-900 mb-3">Recent Activity</h3>
        <div class="bg-white rounded-xl shadow-sm divide-y divide-gray-100">
            @foreach($recentActivity as $activity)
            <a href="{{ $activity['link'] }}" class="block p-4 hover:bg-gray-50 active:bg-gray-100 transition">
                <div class="flex items-start gap-3">
                    <!-- Icon -->
                    <div class="w-10 h-10 bg-{{ $activity['color'] }}-100 rounded-xl flex items-center justify-center flex-shrink-0 text-lg">
                        {{ $activity['icon'] }}
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm mb-1 truncate">
                            {{ $activity['title'] }}
                        </p>
                        <p class="text-xs text-gray-600 mb-1">
                            {{ $activity['description'] }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $activity['time'] }}
                        </p>
                    </div>
                    
                    <!-- Arrow -->
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Continue Learning Section -->
    <div class="px-4 mb-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-bold text-gray-900">Continue Learning</h3>
            <a href="{{ route('courses.index') }}" class="text-sm text-blue-600 font-medium">See All</a>
        </div>

        @if($courses->count() > 0)
        <div class="space-y-3">
            @foreach($courses as $course)
            <!-- Course Card -->
            <x-course-card 
                href="{{ route('courses.show', $course['id']) }}"
                title="{{ $course['title'] }}"
                description="Continue where you left off"
                :progress="$course['progress']"
            >
                <x-slot name="thumbnail">
                    <div class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </x-slot>
            </x-course-card>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-xl p-6 shadow-sm text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p class="text-gray-600 mb-3">No courses enrolled yet</p>
            <a href="{{ route('courses.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium text-sm hover:bg-blue-700 transition">
                Browse Courses
            </a>
        </div>
        @endif
    </div>

    <!-- Quick Actions -->
    @if(Auth::user()->hasRole('teacher'))
    <div class="px-4 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-3">Teacher Tools</h3>
        <div class="grid grid-cols-2 gap-3">
            <a href="#" class="bg-white rounded-xl p-4 shadow-sm active:scale-95 transition">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm">Create Course</h4>
                <p class="text-xs text-gray-600 mt-1">Add new content</p>
            </a>

            <a href="{{ route('document-imports.index') }}" class="bg-white rounded-xl p-4 shadow-sm active:scale-95 transition">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-3">
                    <span class="text-2xl">📄</span>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm">Import Document</h4>
                <p class="text-xs text-gray-600 mt-1">Upload & convert</p>
            </a>

            <a href="#" class="bg-white rounded-xl p-4 shadow-sm active:scale-95 transition">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm">My Students</h4>
                <p class="text-xs text-gray-600 mt-1">View progress</p>
            </a>
        </div>
    </div>
    @endif

    <!-- Account Info (Collapsible) -->
    <div class="px-4 pb-6">
        <details class="bg-white rounded-xl shadow-sm overflow-hidden">
            <summary class="p-4 cursor-pointer font-semibold text-gray-900 flex items-center justify-between">
                <span>Account Information</span>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </summary>
            <div class="px-4 pb-4 space-y-3 text-sm border-t border-gray-100">
                <div class="pt-3">
                    <p class="text-gray-600">Username</p>
                    <p class="font-medium text-gray-900">{{ Auth::user()->username }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Email</p>
                    <p class="font-medium text-gray-900">{{ Auth::user()->email }}</p>
                </div>
                @if(Auth::user()->phone)
                <div>
                    <p class="text-gray-600">Phone</p>
                    <p class="font-medium text-gray-900">{{ Auth::user()->phone }}</p>
                </div>
                @endif
                <div>
                    <p class="text-gray-600">Permissions</p>
                    <div class="flex flex-wrap gap-1 mt-2">
                        @foreach(Auth::user()->roles->flatMap->permissions->unique('id')->take(6) as $permission)
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $permission->name }}
                            </span>
                        @endforeach
                        @if(Auth::user()->roles->flatMap->permissions->unique('id')->count() > 6)
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-gray-200 text-gray-700">
                                +{{ Auth::user()->roles->flatMap->permissions->unique('id')->count() - 6 }} more
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </details>
    </div>
@endsection
