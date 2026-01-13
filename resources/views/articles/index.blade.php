@extends('layouts.app')

@section('title', 'Artikel Pembelajaran')

@section('content')
<div class="px-4">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">📚 Artikel Pembelajaran</h1>
        <p class="text-gray-600 mb-4">Jelajahi artikel untuk pembelajaran mandiri (SPSDL)</p>
        
        <!-- SPSDL Tools -->
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('learning-goals.index') }}" class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 shadow-sm active:scale-95 transition border border-purple-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-purple-900 text-sm">Learning Goals</h4>
                        <p class="text-xs text-purple-700">Set & track goals</p>
                    </div>
                    <svg class="w-4 h-4 text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>

            <a href="{{ route('learning-journal.index') }}" class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 shadow-sm active:scale-95 transition border border-green-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-green-900 text-sm">Learning Journal</h4>
                        <p class="text-xs text-green-700">Reflect & document</p>
                    </div>
                    <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="mb-4">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" 
                   placeholder="Cari artikel..." 
                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    <!-- Category Filter Tabs -->
    <div class="flex overflow-x-auto gap-2 pb-3 mb-6 no-scrollbar">
        <a href="{{ route('articles.index') }}" 
           class="px-4 py-2 rounded-full whitespace-nowrap transition {{ !request('category') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
            Semua
        </a>
        @foreach($categories as $category)
            <a href="{{ route('articles.index', ['category' => $category->id]) }}" 
               class="px-4 py-2 rounded-full whitespace-nowrap transition {{ request('category') == $category->id ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                {{ $category->icon }} {{ $category->name }}
            </a>
        @endforeach
    </div>

    <!-- Difficulty Filter -->
    <div class="flex gap-2 mb-6">
        <a href="{{ route('articles.index', array_merge(request()->except('difficulty'))) }}" 
           class="px-3 py-1.5 text-sm rounded-lg {{ !request('difficulty') ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
            Semua Level
        </a>
        <a href="{{ route('articles.index', array_merge(request()->all(), ['difficulty' => 'beginner'])) }}" 
           class="px-3 py-1.5 text-sm rounded-lg {{ request('difficulty') == 'beginner' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
            Pemula
        </a>
        <a href="{{ route('articles.index', array_merge(request()->all(), ['difficulty' => 'intermediate'])) }}" 
           class="px-3 py-1.5 text-sm rounded-lg {{ request('difficulty') == 'intermediate' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600' }}">
            Menengah
        </a>
        <a href="{{ route('articles.index', array_merge(request()->all(), ['difficulty' => 'advanced'])) }}" 
           class="px-3 py-1.5 text-sm rounded-lg {{ request('difficulty') == 'advanced' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
            Lanjut
        </a>
    </div>

    <!-- Featured Articles -->
    @if($articlesData->where('is_featured', true)->count() > 0 && !request('category') && !request('difficulty'))
        <div class="mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">⭐ Artikel Unggulan</h2>
            <div class="space-y-3">
                @foreach($articlesData->where('is_featured', true)->take(2) as $article)
                    <a href="{{ route('articles.show', $article['id']) }}" 
                       class="block bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-5 active:scale-98 transition">
                        <div class="flex items-start gap-3 mb-3">
                            <span class="text-2xl">{{ $article['category_icon'] }}</span>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900 mb-1">{{ $article['title'] }}</h3>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ $article['excerpt'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <span>👤 {{ $article['author'] }}</span>
                            <span>•</span>
                            <span>⏱️ {{ $article['reading_time'] }} menit</span>
                            <span>•</span>
                            <span>👁️ {{ $article['views'] }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- All Articles -->
    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-3">
            @if(request('category'))
                Artikel {{ $categories->firstWhere('id', request('category'))->name }}
            @else
                Semua Artikel
            @endif
        </h2>
        
        @if($articlesData->count() > 0)
            <div class="space-y-3">
                @foreach($articlesData as $article)
                    <a href="{{ route('articles.show', $article['id']) }}" 
                       class="block bg-white rounded-xl p-4 shadow-sm active:scale-98 transition">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">{{ $article['category_icon'] }}</span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-bold text-gray-900 line-clamp-1">{{ $article['title'] }}</h3>
                                    @if($article['is_bookmarked'])
                                        <svg class="w-4 h-4 text-yellow-500 fill-current" viewBox="0 0 20 20">
                                            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/>
                                        </svg>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 line-clamp-2 mb-2">{{ $article['excerpt'] }}</p>
                                
                                <!-- Tags -->
                                @if(count($article['tags']) > 0)
                                    <div class="flex gap-1.5 mb-2">
                                        @foreach($article['tags'] as $tag)
                                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <!-- Metadata -->
                                <div class="flex items-center gap-3 text-xs text-gray-500">
                                    <span>👤 {{ $article['author'] }}</span>
                                    <span>•</span>
                                    <span>⏱️ {{ $article['reading_time'] }} min</span>
                                    <span>•</span>
                                    <span>👁️ {{ $article['views'] }}</span>
                                    <span>•</span>
                                    <span class="px-1.5 py-0.5 rounded 
                                        {{ $article['difficulty'] === 'Beginner' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $article['difficulty'] === 'Intermediate' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                        {{ $article['difficulty'] === 'Advanced' ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ $article['difficulty'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📭</div>
                <p class="text-gray-500">Tidak ada artikel ditemukan</p>
            </div>
        @endif
    </div>

    <!-- Popular Tags -->
    @if($tags->count() > 0)
        <div class="mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">🏷️ Tag Populer</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($tags as $tag)
                    <a href="{{ route('articles.index', ['tag' => $tag->id]) }}" 
                       class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-full hover:bg-blue-100 hover:text-blue-700 transition">
                        {{ $tag->name }} ({{ $tag->articles_count }})
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
