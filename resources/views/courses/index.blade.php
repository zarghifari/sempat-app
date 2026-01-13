@extends('layouts.app')

@section('title', 'Courses')

@section('content')
    <!-- Page Header -->
    <div class="mb-6 px-4">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Courses</h1>
                <p class="text-sm text-gray-600 mt-1">Explore and continue your learning journey</p>
            </div>
            <a href="{{ route('progress.index') }}" 
               class="flex flex-col items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-xl shadow-md hover:bg-blue-700 active:scale-95 transition-all">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-xs font-semibold">Progress</span>
            </a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-2 mb-6 overflow-x-auto scrollbar-hide px-4">
        <button class="px-4 py-2 bg-blue-600 text-white rounded-full text-sm font-medium whitespace-nowrap active:scale-95 transition-transform">
            All Courses
        </button>
        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium whitespace-nowrap active:scale-95 transition-transform hover:bg-gray-200">
            My Courses
        </button>
        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium whitespace-nowrap active:scale-95 transition-transform hover:bg-gray-200">
            Completed
        </button>
        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium whitespace-nowrap active:scale-95 transition-transform hover:bg-gray-200">
            Explore
        </button>
    </div>

    <!-- Course List -->
    <div class="space-y-4 mb-20 px-4">
        @foreach($courses as $course)
            <a href="{{ route('courses.show', $course['id']) }}" 
               class="block bg-white rounded-xl shadow-sm overflow-hidden active:scale-95 transition-transform">
                <div class="flex gap-4 p-4">
                    <!-- Thumbnail -->
                    <div class="w-24 h-24 bg-gradient-to-br {{ $loop->index % 4 == 0 ? 'from-blue-400 to-blue-600' : ($loop->index % 4 == 1 ? 'from-purple-400 to-pink-500' : ($loop->index % 4 == 2 ? 'from-green-400 to-teal-500' : 'from-orange-400 to-red-500')) }} rounded-lg flex-shrink-0 flex items-center justify-center text-white">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $course['title'] }}</h3>
                        <p class="text-xs text-gray-600 mb-2 line-clamp-2">{{ $course['description'] }}</p>
                        
                        <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                            <div class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>{{ $course['instructor'] }}</span>
                            </div>
                            <span>•</span>
                            <span>{{ $course['modules_count'] }} modules</span>
                            <span>•</span>
                            <span>{{ $course['duration'] }}</span>
                        </div>

                        @if($course['enrolled'])
                            <!-- Progress Bar -->
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-gray-600">Progress</span>
                                    <span class="font-medium text-blue-600">{{ $course['progress'] }}%</span>
                                </div>
                                <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-500 rounded-full transition-all" 
                                         style="width: {{ $course['progress'] }}%"></div>
                                </div>
                            </div>
                        @else
                            <!-- Enroll Button -->
                            <form action="{{ route('courses.enroll', $course['id']) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="text-xs font-medium text-blue-600 hover:text-blue-700 active:text-blue-800 transition">
                                    Enroll Now →
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Empty State (if no courses) -->
    @if(count($courses) === 0)
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Courses Available</h3>
            <p class="text-sm text-gray-600">Check back later for new courses</p>
        </div>
    @endif
@endsection
