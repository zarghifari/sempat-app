@extends('layouts.app')

@section('title', 'Manage Articles')

@section('page-title', 'Manage All Articles')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gray-50 pb-24 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Header with Create Button -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manage All Articles</h1>
                <p class="text-gray-600 mt-1">View and manage all articles in the system</p>
            </div>
            <a href="{{ route('admin.articles.create') }}" 
               class="px-6 py-3 bg-gradient-to-r from-slate-600 to-gray-600 text-white rounded-lg hover:from-slate-700 hover:to-gray-700 transition-colors font-medium shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Article
            </a>
        </div>
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Total Articles</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Published</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['published'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Draft</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['draft'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Total Views</p>
                <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['total_views'] ?? 0) }}</p>
            </div>
        </div>

        <!-- Content Tabs -->
        <div class="bg-white rounded-xl shadow-md mb-6">
            <div class="border-b border-gray-200 px-6">
                <nav class="flex gap-6 overflow-x-auto no-scrollbar" aria-label="Tabs">
                    <button onclick="showTab('articles')" id="tab-articles" class="py-4 px-1 border-b-2 border-slate-600 text-slate-700 font-medium whitespace-nowrap">
                        All Articles
                    </button>
                    <button onclick="showTab('goals')" id="tab-goals" class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium whitespace-nowrap">
                        Learning Goals
                    </button>
                    <button onclick="showTab('journals')" id="tab-journals" class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium whitespace-nowrap">
                        Learning Journals
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content: Articles -->
        <div id="content-articles" class="tab-content">
            <!-- Search and Filters -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <form action="{{ route('admin.articles') }}" method="GET" class="space-y-4">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <!-- Search Input -->
                        <div class="flex-1">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Search articles by title or author..." 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                        </div>
                        <!-- Status Filter -->
                        <select name="status" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                            <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                        <button type="submit" class="px-6 py-3 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors font-medium">
                            Search
                        </button>
                    </div>
                </form>
            </div>

            @if($articles->count() > 0)
                <div class="space-y-4 mb-8">
                    @foreach($articles as $article)
                        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-start gap-4">
                                <!-- Article Thumbnail -->
                                @if($article->thumbnail)
                                    <img src="{{ asset('storage/' . $article->thumbnail) }}" 
                                         alt="{{ $article->title }}" 
                                         class="w-32 h-32 object-cover rounded-lg flex-shrink-0">
                                @else
                                    <div class="w-32 h-32 bg-gradient-to-br from-slate-400 to-gray-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Article Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4 mb-2">
                                        <div class="flex-1">
                                            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $article->title }}</h3>
                                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                                <!-- Author -->
                                                <div class="flex items-center gap-1">
                                                    @if($article->author_avatar)
                                                        <img src="{{ asset('storage/' . $article->author_avatar) }}" 
                                                             alt="{{ $article->author_name }}" 
                                                             class="w-5 h-5 rounded-full">
                                                    @else
                                                        <div class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-xs font-semibold text-slate-600">
                                                            {{ substr($article->author_name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <span>{{ $article->author_name }}</span>
                                                </div>
                                                <!-- Category -->
                                                @if($article->category)
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                                        {{ $article->category }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <!-- Status Badge -->
                                        <span class="px-3 py-1 bg-{{ $article->status === 'published' ? 'green' : 'yellow' }}-100 
                                                     text-{{ $article->status === 'published' ? 'green' : 'yellow' }}-800 
                                                     rounded-full text-xs font-medium">
                                            {{ ucfirst($article->status) }}
                                        </span>
                                    </div>

                                    <!-- Description -->
                                    @if($article->content)
                                        <p class="text-gray-700 mb-3 line-clamp-2">{{ strip_tags(substr($article->content, 0, 200)) }}...</p>
                                    @endif

                                    <!-- Stats and Actions -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4 text-sm text-gray-600">
                                            <div class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span>{{ number_format($article->views ?? 0) }} views</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $article->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        <!-- Action Buttons -->
                                        <div class="flex gap-2">
                                            <a href="{{ route('articles.show', $article->id) }}"
                                               class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition-colors text-sm font-medium">
                                                View
                                            </a>
                                            <a href="{{ route('admin.articles.edit', $article->id) }}"
                                               class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this article? This action cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $articles->appends(['tab' => 'articles'])->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-md p-12 text-center">
                    <div class="text-6xl mb-4">📝</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Articles Found</h3>
                    <p class="text-gray-600">
                        @if(request('search'))
                            No articles match your search criteria.
                        @else
                            There are no articles available yet.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <!-- Tab Content: Learning Goals -->
        <div id="content-goals" class="tab-content hidden">
            @if($learningGoals->count() > 0)
                <div class="space-y-4 mb-8">
                    @foreach($learningGoals as $goal)
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $goal->title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $goal->description }}</p>
                                    <div class="flex items-center gap-3 mt-2 text-sm text-gray-600">
                                        <!-- Student -->
                                        <div class="flex items-center gap-1">
                                            @if($goal->student_avatar)
                                                <img src="{{ asset('storage/' . $goal->student_avatar) }}" 
                                                     alt="{{ $goal->student_name }}" 
                                                     class="w-5 h-5 rounded-full">
                                            @else
                                                <div class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-xs font-semibold text-slate-600">
                                                    {{ substr($goal->student_name, 0, 1) }}
                                                </div>
                                            @endif
                                            <span>{{ $goal->student_name }}</span>
                                        </div>
                                        @if($goal->course_title)
                                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">
                                                {{ $goal->course_title }}
                                            </span>
                                        @endif
                                    </div>
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

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $learningGoals->appends(['tab' => 'goals'])->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-md p-12 text-center">
                    <div class="text-6xl mb-4">🎯</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Learning Goals</h3>
                    <p class="text-gray-600">Students haven't created any learning goals yet.</p>
                </div>
            @endif
        </div>

        <!-- Tab Content: Learning Journals -->
        <div id="content-journals" class="tab-content hidden">
            @if($learningJournals->count() > 0)
                <div class="space-y-4 mb-8">
                    @foreach($learningJournals as $journal)
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $journal->title }}</h3>
                                    <div class="flex items-center gap-3 mt-1 text-sm text-gray-600">
                                        <!-- Student -->
                                        <div class="flex items-center gap-1">
                                            @if($journal->student_avatar)
                                                <img src="{{ asset('storage/' . $journal->student_avatar) }}" 
                                                     alt="{{ $journal->student_name }}" 
                                                     class="w-5 h-5 rounded-full">
                                            @else
                                                <div class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-xs font-semibold text-slate-600">
                                                    {{ substr($journal->student_name, 0, 1) }}
                                                </div>
                                            @endif
                                            <span>{{ $journal->student_name }}</span>
                                        </div>
                                        <span>{{ $journal->entry_date->format('M d, Y') }}</span>
                                    </div>
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

                            @if($journal->goal_title)
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                        🎯 {{ $journal->goal_title }}
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

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $learningJournals->appends(['tab' => 'journals'])->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-md p-12 text-center">
                    <div class="text-6xl mb-4">📓</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Learning Journals</h3>
                    <p class="text-gray-600">Students haven't written any learning journals yet.</p>
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
