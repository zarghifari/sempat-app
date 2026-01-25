@extends('layouts.app')

@section('title', 'My Courses')

@section('page-title', 'My Courses')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 pb-24 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Header with Create Button -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📚 My Courses</h1>
                <p class="text-gray-600 mt-1">Manage and create your courses</p>
            </div>
            <a href="{{ route('teacher.courses.create') }}" 
               class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Course
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-blue-500">
                <p class="text-gray-600 text-sm font-medium">Total Courses</p>
                <p class="text-2xl font-bold text-gray-800">{{ $courses->total() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-green-500">
                <p class="text-gray-600 text-sm font-medium">Published</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['published'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-orange-500">
                <p class="text-gray-600 text-sm font-medium">Draft</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['draft'] }}</p>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-xl shadow-md p-4 mb-6">
            <div class="flex gap-2 overflow-x-auto no-scrollbar">
                <a href="{{ route('teacher.courses') }}" 
                   class="px-4 py-2 rounded-lg font-medium whitespace-nowrap {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    All Courses
                </a>
                <a href="{{ route('teacher.courses', ['status' => 'published']) }}" 
                   class="px-4 py-2 rounded-lg font-medium whitespace-nowrap {{ request('status') === 'published' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Published
                </a>
                <a href="{{ route('teacher.courses', ['status' => 'draft']) }}" 
                   class="px-4 py-2 rounded-lg font-medium whitespace-nowrap {{ request('status') === 'draft' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Draft
                </a>
                <a href="{{ route('teacher.courses', ['status' => 'archived']) }}" 
                   class="px-4 py-2 rounded-lg font-medium whitespace-nowrap {{ request('status') === 'archived' ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Archived
                </a>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="bg-white rounded-xl shadow-md p-4 mb-6">
            <form action="{{ route('teacher.courses') }}" method="GET" class="flex gap-2">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search courses..." 
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Search
                </button>
            </form>
        </div>

        <!-- Courses List -->
        @if($courses->count() > 0)
            <div class="space-y-4">
                @foreach($courses as $course)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-lg font-bold text-gray-900">{{ $course->title }}</h3>
                                        <span class="px-3 py-1 bg-{{ $course->status === 'published' ? 'green' : ($course->status === 'draft' ? 'orange' : 'gray') }}-100 
                                                     text-{{ $course->status === 'published' ? 'green' : ($course->status === 'draft' ? 'orange' : 'gray') }}-800 
                                                     rounded-full text-xs font-medium">
                                            {{ ucfirst($course->status) }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm line-clamp-2">{{ $course->description }}</p>
                                </div>
                                @if($course->thumbnail)
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                                         alt="{{ $course->title }}" 
                                         class="w-20 h-20 rounded-lg object-cover ml-4">
                                @endif
                            </div>

                            <!-- Course Stats -->
                            <div class="flex items-center gap-6 text-sm text-gray-600 mb-4">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span>{{ $course->enrollments_count }} students</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>{{ $course->modules_count }} modules</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $course->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <a href="{{ route('teacher.courses.show', $course->id) }}" 
                                   class="flex-1 bg-purple-600 text-white text-center px-4 py-2 rounded-lg font-semibold hover:bg-purple-700 transition">
                                    Manage
                                </a>
                                <a href="{{ route('teacher.courses.edit', $course->id) }}" 
                                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('teacher.courses.destroy', $course->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 rounded-lg font-semibold hover:bg-red-200 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $courses->links() }}
            </div>
        @else
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <div class="text-6xl mb-4">📚</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No courses yet</h3>
                <p class="text-gray-600 mb-6">Start creating your first course to share knowledge with students!</p>
                <a href="{{ route('teacher.courses.create') }}" 
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Your First Course
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
