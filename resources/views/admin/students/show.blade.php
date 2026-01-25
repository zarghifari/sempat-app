@extends('layouts.app')

@section('title', 'Student Detail')

@section('page-title', 'Student Detail')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gray-50 pb-24 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('admin.students') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-700 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Students
            </a>
        </div>

        <!-- Student Header Card -->
        <div class="bg-gradient-to-r from-slate-600 to-gray-600 rounded-xl shadow-lg p-8 mb-8 text-white">
            <div class="flex items-center gap-6">
                @if($student->avatar)
                    <img src="{{ asset('storage/' . $student->avatar) }}" 
                         alt="{{ $student->name }}" 
                         class="w-24 h-24 rounded-full border-4 border-white shadow-lg">
                @else
                    <div class="w-24 h-24 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-4xl font-bold shadow-lg border-4 border-white">
                        {{ substr($student->name, 0, 1) }}
                    </div>
                @endif
                <div class="flex-1">
                    <h1 class="text-3xl font-bold mb-2">{{ $student->name }}</h1>
                    <p class="text-slate-100 mb-4">{{ $student->email }}</p>
                    <div class="flex gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Joined {{ $student->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <button onclick="alert('Manage roles - will be implemented')" 
                            class="px-6 py-3 bg-white text-slate-700 rounded-lg hover:bg-slate-50 transition-colors font-medium">
                        Manage Roles
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Enrolled Courses</p>
                <p class="text-3xl font-bold text-blue-600">{{ $enrollments->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Avg Progress</p>
                <p class="text-3xl font-bold text-green-600">{{ number_format($enrollments->avg('progress_percentage') ?? 0, 1) }}%</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Study Time</p>
                <p class="text-3xl font-bold text-purple-600">{{ gmdate('H:i', $enrollments->sum('total_study_minutes') * 60) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Quiz Attempts</p>
                <p class="text-3xl font-bold text-orange-600">{{ $quizAttempts->count() }}</p>
            </div>
        </div>

        <!-- Content Tabs -->
        <div class="bg-white rounded-xl shadow-md mb-6">
            <div class="border-b border-gray-200 px-6">
                <nav class="flex gap-6 overflow-x-auto no-scrollbar" aria-label="Tabs">
                    <button onclick="showTab('enrollments')" id="tab-enrollments" class="py-4 px-1 border-b-2 border-slate-600 text-slate-700 font-medium whitespace-nowrap">
                        Course Enrollments
                    </button>
                    <button onclick="showTab('goals')" id="tab-goals" class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium whitespace-nowrap">
                        Learning Goals
                    </button>
                    <button onclick="showTab('journals')" id="tab-journals" class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium whitespace-nowrap">
                        Learning Journals
                    </button>
                    <button onclick="showTab('quizzes')" id="tab-quizzes" class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium whitespace-nowrap">
                        Quiz Attempts
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content: Enrollments -->
        <div id="content-enrollments" class="tab-content">
            @if($enrollments->count() > 0)
                <div class="space-y-4">
                    @foreach($enrollments as $enrollment)
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $enrollment->course->title }}</h3>
                                    <p class="text-sm text-gray-600">Enrolled {{ $enrollment->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="px-3 py-1 bg-{{ $enrollment->status === 'completed' ? 'green' : ($enrollment->status === 'in_progress' ? 'blue' : 'gray') }}-100 
                                             text-{{ $enrollment->status === 'completed' ? 'green' : ($enrollment->status === 'in_progress' ? 'blue' : 'gray') }}-800 
                                             rounded-full text-xs font-medium">
                                    {{ ucfirst(str_replace('_', ' ', $enrollment->status)) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-600">Progress</p>
                                    <p class="text-xl font-bold text-blue-600">{{ number_format($enrollment->progress_percentage, 1) }}%</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Study Time</p>
                                    <p class="text-xl font-bold text-purple-600">{{ gmdate('H:i', $enrollment->total_study_minutes * 60) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Last Activity</p>
                                    <p class="text-xl font-bold text-green-600">{{ $enrollment->last_accessed_at ? $enrollment->last_accessed_at->diffForHumans() : 'Never' }}</p>
                                </div>
                            </div>

                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-blue-600 to-purple-600 h-3 rounded-full transition-all" 
                                     style="width: {{ $enrollment->progress_percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow-md p-12 text-center">
                    <div class="text-6xl mb-4">📚</div>
                    <p class="text-gray-600">No course enrollments found</p>
                </div>
            @endif
        </div>

        <!-- Tab Content: Learning Goals -->
        <div id="content-goals" class="tab-content hidden">
            @if($learningGoals->count() > 0)
                <div class="space-y-4">
                    @foreach($learningGoals as $goal)
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $goal->title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $goal->description }}</p>
                                </div>
                                <span class="px-3 py-1 bg-{{ $goal->status === 'completed' ? 'green' : ($goal->status === 'in_progress' ? 'blue' : 'gray') }}-100 
                                             text-{{ $goal->status === 'completed' ? 'green' : ($goal->status === 'in_progress' ? 'blue' : 'gray') }}-800 
                                             rounded-full text-xs font-medium">
                                    {{ ucfirst(str_replace('_', ' ', $goal->status)) }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                                <span>Progress</span>
                                <span class="font-semibold">{{ $goal->progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                                <div class="bg-blue-600 h-2 rounded-full transition-all" 
                                     style="width: {{ $goal->progress_percentage }}%"></div>
                            </div>

                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                <span>🕐 Study Time: {{ gmdate('H:i:s', $goal->total_study_seconds ?? 0) }}</span>
                                <span>📅 Created {{ $goal->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow-md p-12 text-center">
                    <div class="text-6xl mb-4">🎯</div>
                    <p class="text-gray-600">No learning goals found</p>
                </div>
            @endif
        </div>

        <!-- Tab Content: Journals -->
        <div id="content-journals" class="tab-content hidden">
            @if($learningJournals->count() > 0)
                <div class="space-y-4">
                    @foreach($learningJournals as $journal)
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $journal->title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $journal->entry_date->format('M d, Y') }}</p>
                                </div>
                                <div class="flex items-center gap-1 text-yellow-500">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= ($journal->mood_rating ?? 0) ? 'fill-current' : '' }}" 
                                             fill="{{ $i <= ($journal->mood_rating ?? 0) ? 'currentColor' : 'none' }}" 
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>

                            <p class="text-gray-700 mb-3">{{ $journal->content }}</p>

                            @if($journal->learning_goal)
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                        🎯 {{ $journal->learning_goal->title }}
                                    </span>
                                </div>
                            @endif

                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                <span>🕐 Study: {{ gmdate('H:i', $journal->study_duration_minutes * 60) }}</span>
                                <span>📅 {{ $journal->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow-md p-12 text-center">
                    <div class="text-6xl mb-4">📓</div>
                    <p class="text-gray-600">No learning journals found</p>
                </div>
            @endif
        </div>

        <!-- Tab Content: Quiz Attempts -->
        <div id="content-quizzes" class="tab-content hidden">
            @if($quizAttempts->count() > 0)
                <div class="space-y-4">
                    @foreach($quizAttempts as $attempt)
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $attempt->quiz->title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $attempt->quiz->course->title }}</p>
                                </div>
                                @if($attempt->score !== null)
                                    <div class="text-right">
                                        <p class="text-3xl font-bold {{ $attempt->score >= 70 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($attempt->score, 1) }}%
                                        </p>
                                        <p class="text-xs text-gray-500">Score</p>
                                    </div>
                                @else
                                    <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">
                                        Pending Grading
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">Status</p>
                                    <p class="font-semibold text-gray-900">{{ ucfirst($attempt->status) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Duration</p>
                                    <p class="font-semibold text-gray-900">{{ gmdate('H:i:s', $attempt->time_spent_seconds ?? 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Submitted</p>
                                    <p class="font-semibold text-gray-900">{{ $attempt->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow-md p-12 text-center">
                    <div class="text-6xl mb-4">✍️</div>
                    <p class="text-gray-600">No quiz attempts found</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function showTab(tab) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('[id^="tab-"]').forEach(el => {
        el.classList.remove('border-slate-600', 'text-slate-700');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    document.getElementById('content-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById('tab-' + tab).classList.add('border-slate-600', 'text-slate-700');
}
</script>
@endsection
