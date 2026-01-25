@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header with Flat Design -->
    <div class="bg-blue-600 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Left Section: Title & Welcome -->
            <div class="flex-1">
                <div class="flex items-center gap-4 mb-4">
                <div class="bg-blue-700 p-3 rounded-lg flex-shrink-0">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white leading-tight">Teacher Dashboard</h1>
                    <p class="text-blue-100 text-sm mt-1">{{ now()->format('l, F j, Y') }}</p>
                </div>
                </div>
                <div class="bg-blue-700 bg-opacity-40 rounded-lg px-4 py-2.5 inline-block">
                <p class="text-white text-sm">Welcome back, <span class="font-semibold text-white">{{ Auth::user()->name }}</span>! 👋</p>
                </div>
            </div>
            
            <!-- Right Section: Quick Stats -->
            <div class="flex items-center gap-4">
                <div class="text-center px-5 py-4 bg-blue-700 bg-opacity-30 rounded-lg border-2 border-blue-500 border-opacity-30 min-w-[100px]">
                <p class="text-3xl font-bold text-white leading-none mb-1">{{ $stats['total_courses'] }}</p>
                <p class="text-blue-100 text-xs uppercase tracking-wide font-medium">Courses</p>
                </div>
                <div class="text-center px-5 py-4 bg-blue-700 bg-opacity-30 rounded-lg border-2 border-blue-500 border-opacity-30 min-w-[100px]">
                <p class="text-3xl font-bold text-white leading-none mb-1">{{ $stats['total_students'] }}</p>
                <p class="text-blue-100 text-xs uppercase tracking-wide font-medium">Students</p>
                </div>
                <div class="text-center px-5 py-4 bg-blue-700 bg-opacity-30 rounded-lg border-2 border-blue-500 border-opacity-30 min-w-[100px]">
                <p class="text-3xl font-bold text-white leading-none mb-1">{{ $stats['total_articles'] }}</p>
                <p class="text-blue-100 text-xs uppercase tracking-wide font-medium">Articles</p>
                </div>
            </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 mt-8">
        <!-- Action Items That Need Attention -->
        <div class="grid lg:grid-cols-2 gap-6 mb-8">
            <!-- Pending Grading Alert -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 {{ $stats['pending_grading'] > 0 ? 'border-orange-500' : 'border-green-500' }}">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        @if($stats['pending_grading'] > 0)
                            <div class="bg-orange-100 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                        @else
                            <div class="bg-green-100 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Pending Grading</h3>
                            <p class="text-sm text-gray-600">Quiz submissions awaiting review</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-4xl font-bold {{ $stats['pending_grading'] > 0 ? 'text-orange-600' : 'text-green-600' }}">
                            {{ $stats['pending_grading'] }}
                        </p>
                    </div>
                </div>
                @if($stats['pending_grading'] > 0)
                    <a href="{{ route('teacher.quiz-grading') }}" class="block w-full text-center bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2.5 rounded-lg transition">
                        Review & Grade Now →
                    </a>
                @else
                    <div class="text-center py-2.5 bg-green-50 rounded-lg text-green-700 font-medium">
                        ✅ All caught up! Great work!
                    </div>
                @endif
            </div>

            <!-- Today's Activity Summary -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500" id="todaySummary">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Today's Activity</h3>
                        <p class="text-sm text-gray-600">Live student engagement</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-blue-50 rounded-lg p-3">
                        <p class="text-xs text-gray-600 mb-1">Active Now</p>
                        <p class="text-2xl font-bold text-blue-600" id="activeToday">--</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-3">
                        <p class="text-xs text-gray-600 mb-1">Study Time</p>
                        <p class="text-2xl font-bold text-purple-600" id="totalTime">--</p>
                    </div>
                </div>
                <a href="{{ route('teacher.students') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition">
                    View Student Progress →
                </a>
            </div>
        </div>

        <!-- Mini Analytics: Learning Type Distribution (Last 7 Days) -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span>📊</span> Learning Activity (Last 7 Days)
                </h2>
                <a href="{{ route('teacher.analytics') }}" class="text-blue-600 text-sm font-medium hover:underline">
                    View Full Analytics →
                </a>
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Learning Type Breakdown -->
                <div class="lg:col-span-1">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Learning Type Distribution</h3>
                    
                    <!-- Facilitated Self-Directed Learning (Courses) -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-blue-600 rounded"></div>
                                <span class="text-sm font-medium text-gray-700">Courses</span>
                            </div>
                            <span class="text-sm font-bold text-blue-600">{{ $miniAnalytics['learning_types']['facilitated']['percentage'] }}%</span>
                        </div>
                        <div class="bg-gray-200 rounded-full h-2 mb-1">
                            <div class="bg-blue-600 h-2 rounded-full transition-all" 
                                 style="width: {{ $miniAnalytics['learning_types']['facilitated']['percentage'] }}%"></div>
                        </div>
                        <p class="text-xs text-gray-600">{{ $miniAnalytics['learning_types']['facilitated']['time_formatted'] }}</p>
                    </div>

                    <!-- Self-Paced Learning (Articles) -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-green-600 rounded"></div>
                                <span class="text-sm font-medium text-gray-700">Articles</span>
                            </div>
                            <span class="text-sm font-bold text-green-600">{{ $miniAnalytics['learning_types']['self_paced']['percentage'] }}%</span>
                        </div>
                        <div class="bg-gray-200 rounded-full h-2 mb-1">
                            <div class="bg-green-600 h-2 rounded-full transition-all" 
                                 style="width: {{ $miniAnalytics['learning_types']['self_paced']['percentage'] }}%"></div>
                        </div>
                        <p class="text-xs text-gray-600">{{ $miniAnalytics['learning_types']['self_paced']['time_formatted'] }}</p>
                    </div>

                    <!-- Total -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700">Total Learning Time</span>
                            <span class="text-lg font-bold text-purple-600">{{ $miniAnalytics['learning_types']['total']['time_formatted'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Daily Activity Trend Chart -->
                <div class="lg:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">7-Day Activity Trend (Total Study Time)</h3>
                    <div class="flex items-end gap-2 h-48 mb-3">
                        @php
                            $maxTime = max(array_column($miniAnalytics['daily_trend'], 'total_seconds')) ?: 1;
                        @endphp
                        @foreach($miniAnalytics['daily_trend'] as $day)
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <!-- Stacked bars for courses and articles -->
                                <div class="w-full flex flex-col items-center justify-end" style="height: 180px;">
                                    @if($day['total_seconds'] > 0)
                                        <!-- Articles (top) -->
                                        @if($day['articles_seconds'] > 0)
                                            <div class="w-full bg-green-500 rounded-t hover:bg-green-600 transition cursor-pointer group relative" 
                                                 style="height: {{ ($day['articles_seconds'] / $maxTime) * 180 }}px; min-height: 4px;"
                                                 title="Articles: {{ $day['articles_formatted'] }}">
                                                <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap z-10">
                                                    Articles: {{ $day['articles_formatted'] }}
                                                </div>
                                            </div>
                                        @endif
                                        <!-- Courses (bottom) -->
                                        @if($day['courses_seconds'] > 0)
                                            <div class="w-full bg-blue-500 {{ $day['articles_seconds'] > 0 ? '' : 'rounded-t' }} hover:bg-blue-600 transition cursor-pointer group relative" 
                                                 style="height: {{ ($day['courses_seconds'] / $maxTime) * 180 }}px; min-height: 4px;"
                                                 title="Courses: {{ $day['courses_formatted'] }}">
                                                <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap z-10">
                                                    Courses: {{ $day['courses_formatted'] }}
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="w-full bg-gray-200 rounded-t" style="height: 4px;"></div>
                                    @endif
                                </div>
                                <!-- Day label -->
                                <span class="text-xs text-gray-600 font-medium mt-1">{{ $day['day'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Legend -->
                    <div class="flex items-center justify-center gap-6 pt-3 border-t border-gray-200">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-blue-500 rounded"></div>
                            <span class="text-xs text-gray-600">Facilitated Learning (Courses)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-green-500 rounded"></div>
                            <span class="text-xs text-gray-600">Self-Paced Learning (Articles)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- What Needs Your Attention -->
        <div class="grid lg:grid-cols-2 gap-6 mb-8">
            <!-- Pending Quiz Grading -->
            @if($pendingQuizzes->count() > 0)
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <span>✍️</span> Needs Grading
                        </h2>
                        <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-bold">
                            {{ $pendingQuizzes->count() }}
                        </span>
                    </div>
                    
                    <div class="space-y-2 mb-4 max-h-48 overflow-y-auto">
                        @foreach($pendingQuizzes->take(3) as $attempt)
                            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $attempt->quiz->title }}</p>
                                    <p class="text-xs text-gray-600">{{ $attempt->user->name }} • {{ $attempt->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <a href="{{ route('teacher.quiz-grading') }}" class="block w-full text-center bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2.5 rounded-lg transition">
                        Review All →
                    </a>
                </div>
            @endif

            <!-- Recent Student Activity -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span>🔔</span> Recent Activity
                </h2>
                
                @if($recentActivity->count() > 0)
                    <div class="space-y-2 mb-4 max-h-48 overflow-y-auto">
                        @foreach($recentActivity->take(5) as $activity)
                            <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                <div class="bg-blue-500 text-white p-1.5 rounded-full flex-shrink-0">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $activity->student_name }}</p>
                                    <p class="text-xs text-gray-600 truncate">{{ $activity->lesson_title }}</p>
                                </div>
                                <span class="text-xs text-gray-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($activity->activity_time)->diffForHumans(null, true) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('teacher.students') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition">
                        View All Activity →
                    </a>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-4xl mb-2">📭</div>
                        <p class="text-gray-500 text-sm">No recent activity</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Content (Courses only) -->
        @if($recentCourses->count() > 0)
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span>📚</span> Recent Courses
                </h2>
                <a href="{{ route('teacher.courses') }}" class="text-blue-600 text-sm font-medium hover:underline">View All →</a>
            </div>
            
            <div class="grid lg:grid-cols-3 gap-4">
                @foreach($recentCourses->take(3) as $course)
                    <div class="border-l-4 border-blue-500 bg-blue-50 rounded-r-lg p-4 hover:bg-blue-100 transition">
                        <h3 class="font-semibold text-gray-800 text-sm mb-2 truncate">{{ $course->title }}</h3>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                {{ $course->enrollments_count }}
                            </span>
                            <span class="px-2 py-1 bg-{{ $course->status === 'published' ? 'green' : 'gray' }}-100 text-{{ $course->status === 'published' ? 'green' : 'gray' }}-700 rounded font-medium">
                                {{ ucfirst($course->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Load minimal today's stats
async function loadTodayStats() {
    try {
        const response = await fetch('/teacher/api/class/summary', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) return;
        
        const data = await response.json();
        const summary = data.summary;

        document.getElementById('activeToday').textContent = summary.active_today;
        document.getElementById('totalTime').textContent = summary.total_study_time_formatted;
    } catch (error) {
        document.getElementById('activeToday').textContent = '--';
        document.getElementById('totalTime').textContent = '--';
    }
}

document.addEventListener('DOMContentLoaded', loadTodayStats);
setInterval(loadTodayStats, 60000); // Refresh every 60s
</script>
@endpush
@endsection
