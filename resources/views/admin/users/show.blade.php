@extends('layouts.app')

@section('title', 'User Detail')

@section('page-title', 'User Detail')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gray-50 pb-24 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-700 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Users
            </a>
        </div>

        <!-- User Header Card -->
        <div class="bg-gradient-to-r from-slate-600 to-gray-600 rounded-xl shadow-lg p-8 mb-8 text-white">
            <div class="flex items-center gap-6">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" 
                         alt="{{ $user->name }}" 
                         class="w-24 h-24 rounded-full border-4 border-white shadow-lg">
                @else
                    <div class="w-24 h-24 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-4xl font-bold shadow-lg border-4 border-white">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
                        @php
                            $role = $user->roles->first()?->name ?? 'student';
                            $roleColors = [
                                'admin' => 'orange',
                                'teacher' => 'purple',
                                'student' => 'blue',
                            ];
                            $color = $roleColors[$role] ?? 'gray';
                        @endphp
                        <span class="px-3 py-1 bg-{{ $color }}-100 text-{{ $color }}-800 rounded-full text-sm font-medium">
                            {{ ucfirst($role) }}
                        </span>
                    </div>
                    <p class="text-slate-100 mb-4">{{ $user->email }}</p>
                    <div class="flex gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Joined {{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($user->email_verified_at)
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Verified {{ $user->email_verified_at->diffForHumans() }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="alert('Edit user - will be implemented')" 
                            class="px-6 py-3 bg-white text-slate-700 rounded-lg hover:bg-slate-50 transition-colors font-medium">
                        Edit Profile
                    </button>
                    <button onclick="alert('Change role - will be implemented')" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Change Role
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        @if($role === 'student')
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                    <p class="text-gray-600 text-sm font-medium mb-1">Enrolled Courses</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $enrollments->count() ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                    <p class="text-gray-600 text-sm font-medium mb-1">Avg Progress</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($enrollments->avg('progress_percentage') ?? 0, 1) }}%</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                    <p class="text-gray-600 text-sm font-medium mb-1">Study Time</p>
                    <p class="text-3xl font-bold text-purple-600">{{ gmdate('H:i', ($enrollments->sum('total_study_minutes') ?? 0) * 60) }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
                    <p class="text-gray-600 text-sm font-medium mb-1">Quiz Attempts</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $quizAttempts->count() ?? 0 }}</p>
                </div>
            </div>
        @elseif($role === 'teacher')
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                    <p class="text-gray-600 text-sm font-medium mb-1">Total Courses</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $courses->count() ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                    <p class="text-gray-600 text-sm font-medium mb-1">Total Students</p>
                    <p class="text-3xl font-bold text-green-600">{{ $totalStudents ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                    <p class="text-gray-600 text-sm font-medium mb-1">Published Courses</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $courses->where('status', 'published')->count() ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
                    <p class="text-gray-600 text-sm font-medium mb-1">Total Articles</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $articles->count() ?? 0 }}</p>
                </div>
            </div>
        @endif

        <!-- Account Information -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Account Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Full Name</p>
                    <p class="text-gray-900">{{ $user->name }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Email Address</p>
                    <p class="text-gray-900">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Account Status</p>
                    @if($user->email_verified_at)
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Verified</span>
                    @else
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">Unverified</span>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Member Since</p>
                    <p class="text-gray-900">{{ $user->created_at->format('F d, Y') }} ({{ $user->created_at->diffForHumans() }})</p>
                </div>
                @if($user->updated_at)
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Last Updated</p>
                        <p class="text-gray-900">{{ $user->updated_at->diffForHumans() }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Role-Specific Content -->
        @if($role === 'student' && isset($enrollments) && $enrollments->count() > 0)
            <!-- Student Course Enrollments -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Course Enrollments</h3>
                <div class="space-y-4">
                    @foreach($enrollments as $enrollment)
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="text-md font-bold text-gray-900">{{ $enrollment->course->title }}</h4>
                                    <p class="text-sm text-gray-600">Enrolled {{ $enrollment->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="px-3 py-1 bg-{{ $enrollment->status === 'completed' ? 'green' : ($enrollment->status === 'in_progress' ? 'blue' : 'gray') }}-100 
                                             text-{{ $enrollment->status === 'completed' ? 'green' : ($enrollment->status === 'in_progress' ? 'blue' : 'gray') }}-800 
                                             rounded-full text-xs font-medium">
                                    {{ ucfirst(str_replace('_', ' ', $enrollment->status)) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                                <span>Progress</span>
                                <span class="font-semibold">{{ number_format($enrollment->progress_percentage, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-blue-600 to-purple-600 h-3 rounded-full transition-all" 
                                     style="width: {{ $enrollment->progress_percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif($role === 'teacher' && isset($courses) && $courses->count() > 0)
            <!-- Teacher Courses -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Created Courses</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($courses as $course)
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-start justify-between mb-2">
                                <h4 class="text-md font-bold text-gray-900 flex-1">{{ $course->title }}</h4>
                                <span class="px-2 py-1 bg-{{ $course->status === 'published' ? 'green' : ($course->status === 'draft' ? 'yellow' : 'gray') }}-100 
                                             text-{{ $course->status === 'published' ? 'green' : ($course->status === 'draft' ? 'yellow' : 'gray') }}-800 
                                             rounded-full text-xs font-medium">
                                    {{ ucfirst($course->status) }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                                <div>
                                    <p class="text-xs text-gray-500">Students</p>
                                    <p class="font-semibold">{{ $course->enrollments_count ?? 0 }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Modules</p>
                                    <p class="font-semibold">{{ $course->modules_count ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Activity Log -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Activity</h3>
            <div class="space-y-3">
                @if(isset($recentActivity) && count($recentActivity) > 0)
                    @foreach($recentActivity as $activity)
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">{{ $activity['action'] }}</p>
                                <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-gray-500">
                        <div class="text-4xl mb-2">📋</div>
                        <p>No recent activity</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
