@extends('layouts.app')

@section('title', 'My Articles & Student Activity')

@section('page-title', 'My Articles')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50 pb-24 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📝 My Articles & Student Activity</h1>
                <p class="text-gray-600 mt-1">Monitor your articles and student learning progress</p>
            </div>
            <a href="{{ route('teacher.articles.create') }}" 
               class="flex items-center gap-2 bg-gradient-to-r from-green-600 to-blue-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Article
            </a>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-xl shadow-md p-2 mb-6 flex gap-2">
            <button onclick="showTab('articles')" id="tab-articles" 
                    class="flex-1 px-4 py-3 rounded-lg font-semibold transition bg-blue-600 text-white">
                My Articles
            </button>
            <button onclick="showTab('goals')" id="tab-goals" 
                    class="flex-1 px-4 py-3 rounded-lg font-semibold transition bg-gray-100 text-gray-700 hover:bg-gray-200">
                Learning Goals
            </button>
            <button onclick="showTab('journals')" id="tab-journals" 
                    class="flex-1 px-4 py-3 rounded-lg font-semibold transition bg-gray-100 text-gray-700 hover:bg-gray-200">
                Journals
            </button>
        </div>

        <!-- Tab Content: My Articles -->
        <div id="content-articles" class="tab-content">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-green-500">
                    <p class="text-gray-600 text-sm font-medium">Total Articles</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $articles->total() }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-blue-500">
                    <p class="text-gray-600 text-sm font-medium">Published</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['published'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-purple-500">
                    <p class="text-gray-600 text-sm font-medium">Total Views</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_views'] }}</p>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white rounded-xl shadow-md p-4 mb-6">
                <div class="flex gap-2 overflow-x-auto no-scrollbar">
                    <a href="{{ route('teacher.articles') }}" 
                       class="px-4 py-2 rounded-lg font-medium whitespace-nowrap {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        All Articles
                    </a>
                    <a href="{{ route('teacher.articles', ['status' => 'published']) }}" 
                       class="px-4 py-2 rounded-lg font-medium whitespace-nowrap {{ request('status') === 'published' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Published
                    </a>
                    <a href="{{ route('teacher.articles', ['status' => 'draft']) }}" 
                       class="px-4 py-2 rounded-lg font-medium whitespace-nowrap {{ request('status') === 'draft' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Draft
                    </a>
                </div>
            </div>

            <!-- Articles List -->
            @if($articles->count() > 0)
                <div class="space-y-4">
                    @foreach($articles as $article)
                        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-lg font-bold text-gray-900">{{ $article->title }}</h3>
                                        <span class="px-3 py-1 bg-{{ $article->status === 'published' ? 'green' : 'orange' }}-100 
                                                     text-{{ $article->status === 'published' ? 'green' : 'orange' }}-800 
                                                     rounded-full text-xs font-medium">
                                            {{ ucfirst($article->status) }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm">{{ Str::limit(strip_tags($article->content), 150) }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-6 text-sm text-gray-600 mb-4">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>{{ $article->views_count }} views</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $article->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('articles.show', $article->id) }}" 
                                   class="flex-1 bg-blue-600 text-white text-center px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                                    View Article
                                </a>
                                <a href="{{ route('teacher.articles.edit', $article->id) }}" 
                                   class="px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                                    Edit
                                </a>
                                <form action="{{ route('teacher.articles.destroy', $article->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this article?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6">
                    {{ $articles->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-md p-12 text-center">
                    <div class="text-6xl mb-4">📝</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No articles yet</h3>
                    <p class="text-gray-600">You haven't created any articles yet.</p>
                </div>
            @endif
        </div>

        <!-- Tab Content: Learning Goals -->
        <div id="content-goals" class="tab-content hidden">
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">🎯 Student Learning Goals</h2>
                <p class="text-gray-600 text-sm mb-4">Monitor students' learning goals and their progress</p>
                
                @if($learningGoals->count() > 0)
                    <div class="space-y-4">
                        @foreach($learningGoals as $goal)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900">{{ $goal->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">by {{ $goal->user->name }}</p>
                                    </div>
                                    <span class="px-3 py-1 bg-{{ $goal->status === 'completed' ? 'green' : ($goal->status === 'in_progress' ? 'blue' : 'gray') }}-100 
                                                 text-{{ $goal->status === 'completed' ? 'green' : ($goal->status === 'in_progress' ? 'blue' : 'gray') }}-800 
                                                 rounded-full text-xs font-medium">
                                        {{ ucfirst(str_replace('_', ' ', $goal->status)) }}
                                    </span>
                                </div>
                                
                                <div class="mt-3">
                                    <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                                        <span>Progress</span>
                                        <span class="font-semibold">{{ $goal->progress_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all" 
                                             style="width: {{ $goal->progress_percentage }}%"></div>
                                    </div>
                                </div>

                                <div class="mt-3 flex items-center gap-4 text-xs text-gray-500">
                                    <span>🕐 Study Time: {{ gmdate('H:i:s', $goal->total_study_seconds ?? 0) }}</span>
                                    <span>📅 {{ $goal->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6">
                        {{ $learningGoals->links() }}
                    </div>
                @else
                    <p class="text-center text-gray-500 py-8">No learning goals found</p>
                @endif
            </div>
        </div>

        <!-- Tab Content: Journals -->
        <div id="content-journals" class="tab-content hidden">
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">📓 Student Learning Journals</h2>
                <p class="text-gray-600 text-sm mb-4">View students' reflections and learning experiences</p>
                
                @if($learningJournals->count() > 0)
                    <div class="space-y-4">
                        @foreach($learningJournals as $journal)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-500 transition">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900">{{ $journal->title }}</h4>
                                        <p class="text-sm text-gray-600">by {{ $journal->user->name }}</p>
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
                                
                                <p class="text-sm text-gray-700 mt-2 line-clamp-3">{{ $journal->content }}</p>
                                
                                @if($journal->learning_goal)
                                    <div class="mt-3 flex items-center gap-2">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                            🎯 {{ $journal->learning_goal->title }}
                                        </span>
                                    </div>
                                @endif

                                <div class="mt-3 flex items-center gap-4 text-xs text-gray-500">
                                    <span>📅 {{ $journal->entry_date->format('M d, Y') }}</span>
                                    <span>🕐 Study Time: {{ gmdate('H:i:s', $journal->study_duration_minutes * 60) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6">
                        {{ $learningJournals->links() }}
                    </div>
                @else
                    <p class="text-center text-gray-500 py-8">No learning journals found</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tab) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('[id^="tab-"]').forEach(el => {
        el.classList.remove('bg-blue-600', 'text-white');
        el.classList.add('bg-gray-100', 'text-gray-700');
    });
    
    // Show selected tab
    document.getElementById('content-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).classList.remove('bg-gray-100', 'text-gray-700');
    document.getElementById('tab-' + tab).classList.add('bg-blue-600', 'text-white');
}
</script>
@endsection
