@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gray-50 pb-20">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-800 to-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">🛡️ Admin Dashboard</h1>
                    <p class="text-gray-300">System Overview & Management</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.settings') }}" class="bg-white text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition">
                        ⚙️ Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- System Statistics -->
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Users</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('admin.users') }}" class="text-blue-600 text-sm font-medium mt-3 inline-block hover:underline">Manage Users →</a>
            </div>

            <!-- Total Courses -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Courses</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_courses'] }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('admin.courses') }}" class="text-green-600 text-sm font-medium mt-3 inline-block hover:underline">View All →</a>
            </div>

            <!-- Total Enrollments -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Enrollments</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_enrollments'] }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Students -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Active Students</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['active_students'] }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Last 30 days</p>
            </div>

            <!-- Total Articles -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Articles</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_articles'] }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('admin.articles') }}" class="text-red-600 text-sm font-medium mt-3 inline-block hover:underline">Manage Articles →</a>
            </div>

            <!-- Total Quizzes -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-indigo-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Quizzes</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_quizzes'] }}</p>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Distribution -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">👥 User Distribution</h2>
            <div class="grid grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="bg-blue-100 text-blue-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-2xl font-bold">{{ $usersByRole['students'] }}</span>
                    </div>
                    <p class="font-semibold text-gray-700">Students</p>
                </div>
                <div class="text-center">
                    <div class="bg-green-100 text-green-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-2xl font-bold">{{ $usersByRole['teachers'] }}</span>
                    </div>
                    <p class="font-semibold text-gray-700">Teachers</p>
                </div>
                <div class="text-center">
                    <div class="bg-purple-100 text-purple-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-2xl font-bold">{{ $usersByRole['admins'] }}</span>
                    </div>
                    <p class="font-semibold text-gray-700">Admins</p>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid lg:grid-cols-2 gap-8">
            <!-- System Activity -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">📊 System Activity (Last 30 Days)</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <span class="font-medium text-gray-700">New Users</span>
                        <span class="text-2xl font-bold text-blue-600">{{ $systemActivity['new_users'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <span class="font-medium text-gray-700">New Courses</span>
                        <span class="text-2xl font-bold text-green-600">{{ $systemActivity['new_courses'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <span class="font-medium text-gray-700">New Enrollments</span>
                        <span class="text-2xl font-bold text-purple-600">{{ $systemActivity['new_enrollments'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-orange-50 rounded-lg">
                        <span class="font-medium text-gray-700">Quiz Attempts</span>
                        <span class="text-2xl font-bold text-orange-600">{{ $systemActivity['quiz_attempts'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Top Performing Courses -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">🏆 Top Performing Courses</h2>
                    <a href="{{ route('admin.courses') }}" class="text-blue-600 text-sm font-medium hover:underline">View All</a>
                </div>
                @if($topCourses->count() > 0)
                    <div class="space-y-3">
                        @foreach($topCourses as $course)
                            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 text-sm">{{ Str::limit($course->title, 30) }}</h3>
                                    <p class="text-xs text-gray-600">{{ $course->enrollments_count }} enrollments</p>
                                </div>
                                <div class="text-2xl">
                                    @if($loop->index === 0) 🥇
                                    @elseif($loop->index === 1) 🥈
                                    @elseif($loop->index === 2) 🥉
                                    @else ⭐
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No courses yet</p>
                @endif
            </div>

            <!-- Top Teachers -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">👨‍🏫 Top Teachers</h2>
                @if($topTeachers->count() > 0)
                    <div class="space-y-3">
                        @foreach($topTeachers as $teacher)
                            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800">{{ $teacher->name }}</h3>
                                    <p class="text-xs text-gray-600">
                                        {{ $teacher->total_courses }} courses • {{ $teacher->student_count }} students
                                    </p>
                                </div>
                                <a href="{{ route('admin.users.show', $teacher->id) }}" class="text-blue-600 text-sm hover:underline">View</a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No teachers yet</p>
                @endif
            </div>

            <!-- Recent Users -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">🆕 Recent Registrations</h2>
                    <a href="{{ route('admin.users') }}" class="text-blue-600 text-sm font-medium hover:underline">View All</a>
                </div>
                @if($recentUsers->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentUsers->take(5) as $user)
                            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 text-sm">{{ $user->name }}</h3>
                                    <p class="text-xs text-gray-600">{{ $user->email }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                    {{ $user->roles->first()->name ?? 'No Role' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No users yet</p>
                @endif
            </div>
        </div>

        <!-- Course Statistics -->
        <div class="mt-8 bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">📈 Course Statistics</h2>
            <div class="grid grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="bg-green-100 p-4 rounded-lg mb-2">
                        <p class="text-3xl font-bold text-green-600">{{ $courseStats['published'] }}</p>
                    </div>
                    <p class="font-semibold text-gray-700">Published</p>
                </div>
                <div class="text-center">
                    <div class="bg-gray-100 p-4 rounded-lg mb-2">
                        <p class="text-3xl font-bold text-gray-600">{{ $courseStats['draft'] }}</p>
                    </div>
                    <p class="font-semibold text-gray-700">Draft</p>
                </div>
                <div class="text-center">
                    <div class="bg-red-100 p-4 rounded-lg mb-2">
                        <p class="text-3xl font-bold text-red-600">{{ $courseStats['archived'] }}</p>
                    </div>
                    <p class="font-semibold text-gray-700">Archived</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-gradient-to-r from-gray-800 to-slate-900 rounded-xl shadow-lg p-6 text-white">
            <h2 class="text-xl font-bold mb-6">⚡ Quick Actions</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('admin.users') }}" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg p-4 text-center transition">
                    <div class="text-3xl mb-2">👥</div>
                    <div class="font-semibold">Manage Users</div>
                </a>
                <a href="{{ route('admin.courses') }}" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg p-4 text-center transition">
                    <div class="text-3xl mb-2">📚</div>
                    <div class="font-semibold">Manage Courses</div>
                </a>
                <a href="{{ route('admin.analytics') }}" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg p-4 text-center transition">
                    <div class="text-3xl mb-2">📊</div>
                    <div class="font-semibold">View Analytics</div>
                </a>
                <a href="{{ route('admin.settings') }}" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg p-4 text-center transition">
                    <div class="text-3xl mb-2">⚙️</div>
                    <div class="font-semibold">System Settings</div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
