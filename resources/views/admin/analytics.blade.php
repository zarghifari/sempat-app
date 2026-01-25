@extends('layouts.app')

@section('title', 'System Analytics')

@section('page-title', 'System Analytics')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gray-50 pb-24 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Overview Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Total Users</p>
                <p class="text-3xl font-bold text-blue-600">{{ $overview['total_users'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">All platform users</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Total Students</p>
                <p class="text-3xl font-bold text-green-600">{{ $overview['total_students'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">Active students</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Total Teachers</p>
                <p class="text-3xl font-bold text-purple-600">{{ $overview['total_teachers'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">Active teachers</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Total Courses</p>
                <p class="text-3xl font-bold text-orange-600">{{ $overview['total_courses'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">Published courses</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-pink-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Enrollments</p>
                <p class="text-3xl font-bold text-pink-600">{{ $overview['total_enrollments'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">Total enrollments</p>
            </div>
        </div>

        <!-- Growth Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- User Growth -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">User Growth (Last 12 Months)</h3>
                <div class="grid grid-cols-12 gap-1 h-48 items-end">
                    @php
                        $months = ['J', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'];
                        $userGrowth = $userGrowthData ?? array_fill(0, 12, 0);
                        $maxUsers = max($userGrowth) ?: 1;
                    @endphp
                    @foreach($months as $index => $month)
                        <div class="flex flex-col items-center">
                            <div class="bg-gray-200 rounded w-full flex items-end" style="height: 160px;">
                                <div class="bg-gradient-to-t from-blue-600 to-blue-400 rounded w-full transition-all" 
                                     style="height: {{ ($userGrowth[$index] / $maxUsers) * 100 }}%"
                                     title="{{ $userGrowth[$index] }} new users"></div>
                            </div>
                            <span class="text-xs text-gray-500 mt-1">{{ $month }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Enrollment Trends -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Enrollment Trends (Last 12 Months)</h3>
                <div class="grid grid-cols-12 gap-1 h-48 items-end">
                    @php
                        $enrollmentTrends = $enrollmentTrendsData ?? array_fill(0, 12, 0);
                        $maxEnrollments = max($enrollmentTrends) ?: 1;
                    @endphp
                    @foreach($months as $index => $month)
                        <div class="flex flex-col items-center">
                            <div class="bg-gray-200 rounded w-full flex items-end" style="height: 160px;">
                                <div class="bg-gradient-to-t from-green-600 to-green-400 rounded w-full transition-all" 
                                     style="height: {{ ($enrollmentTrends[$index] / $maxEnrollments) * 100 }}%"
                                     title="{{ $enrollmentTrends[$index] }} enrollments"></div>
                            </div>
                            <span class="text-xs text-gray-500 mt-1">{{ $month }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Content Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Course Categories -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Course Categories</h3>
                <div class="space-y-3">
                    @forelse($courseCategories ?? [] as $category)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $category['name'] ?? 'Uncategorized' }}</span>
                                <span class="text-gray-600 font-semibold">{{ $category['count'] }}</span>
                            </div>
                            <div class="bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full transition-all" 
                                     style="width: {{ $category['percentage'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500">
                            <p class="text-sm">No category data</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Top Teachers -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Top Teachers</h3>
                <div class="space-y-3">
                    @forelse($topTeachers ?? [] as $index => $teacher)
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs">
                                {{ $index + 1 }}
                            </div>
                            @if($teacher['avatar'])
                                <img src="{{ asset('storage/' . $teacher['avatar']) }}" 
                                     alt="{{ $teacher['name'] }}" 
                                     class="w-8 h-8 rounded-full">
                            @else
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-400 to-gray-500 flex items-center justify-center text-white text-xs font-semibold">
                                    {{ substr($teacher['name'], 0, 1) }}
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $teacher['name'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-blue-600">{{ $teacher['courses_count'] }}</p>
                                <p class="text-xs text-gray-500">courses</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500">
                            <p class="text-sm">No teacher data</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Content Stats -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Content Statistics</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-700">Modules</span>
                        </div>
                        <span class="text-xl font-bold text-blue-600">{{ $contentStats['modules'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-700">Lessons</span>
                        </div>
                        <span class="text-xl font-bold text-green-600">{{ $contentStats['lessons'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-700">Quizzes</span>
                        </div>
                        <span class="text-xl font-bold text-purple-600">{{ $contentStats['quizzes'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-700">Articles</span>
                        </div>
                        <span class="text-xl font-bold text-orange-600">{{ $contentStats['articles'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Performance -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Platform Engagement</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                    <div class="text-4xl font-bold text-blue-600 mb-2">{{ number_format($engagement['avg_completion'] ?? 0, 1) }}%</div>
                    <p class="text-sm font-medium text-gray-700">Avg Course Completion</p>
                </div>
                <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                    <div class="text-4xl font-bold text-green-600 mb-2">{{ gmdate('H:i', ($engagement['avg_study_time'] ?? 0) * 60) }}</div>
                    <p class="text-sm font-medium text-gray-700">Avg Study Time</p>
                </div>
                <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                    <div class="text-4xl font-bold text-purple-600 mb-2">{{ number_format($engagement['quiz_pass_rate'] ?? 0, 1) }}%</div>
                    <p class="text-sm font-medium text-gray-700">Quiz Pass Rate</p>
                </div>
                <div class="text-center p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg">
                    <div class="text-4xl font-bold text-orange-600 mb-2">{{ $engagement['active_today'] ?? 0 }}</div>
                    <p class="text-sm font-medium text-gray-700">Active Users Today</p>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Platform Activity</h3>
            @if(isset($recentActivity) && count($recentActivity) > 0)
                <div class="space-y-3">
                    @foreach($recentActivity as $activity)
                        <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-shrink-0">
                                @if($activity['user_avatar'])
                                    <img src="{{ asset('storage/' . $activity['user_avatar']) }}" 
                                         alt="{{ $activity['user_name'] }}" 
                                         class="w-10 h-10 rounded-full">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-400 to-gray-500 flex items-center justify-center text-white font-semibold">
                                        {{ substr($activity['user_name'], 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">
                                    <span class="font-semibold">{{ $activity['user_name'] }}</span>
                                    <span class="text-gray-600"> {{ $activity['action'] }} </span>
                                    <span class="font-medium">{{ $activity['target'] }}</span>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">{{ $activity['time'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <div class="text-4xl mb-2">📊</div>
                    <p>No recent activity</p>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
