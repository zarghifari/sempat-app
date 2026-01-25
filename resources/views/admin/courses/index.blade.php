@extends('layouts.app')

@section('title', 'Manage Courses')

@section('page-title', 'Manage All Courses')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gray-50 pb-24 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Header with Create Button -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manage All Courses</h1>
                <p class="text-gray-600 mt-1">View and manage all courses in the system</p>
            </div>
            <a href="{{ route('admin.courses.create') }}" 
               class="px-6 py-3 bg-gradient-to-r from-slate-600 to-gray-600 text-white rounded-lg hover:from-slate-700 hover:to-gray-700 transition-colors font-medium shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Course
            </a>
        </div>
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Total Courses</p>
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
                <p class="text-gray-600 text-sm font-medium mb-1">Total Students</p>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['total_enrollments'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <form action="{{ route('admin.courses') }}" method="GET" class="space-y-4">
                <div class="flex flex-col lg:flex-row gap-4">
                    <!-- Search Input -->
                    <div class="flex-1">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search courses by title or teacher..." 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                    </div>
                    <!-- Status Filter -->
                    <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                    <button type="submit" class="px-6 py-3 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors font-medium">
                        Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-xl shadow-md mb-6">
            <div class="border-b border-gray-200 px-6">
                <nav class="flex gap-6 overflow-x-auto no-scrollbar" aria-label="Tabs">
                    <a href="{{ route('admin.courses', ['status' => 'all'] + request()->except('status')) }}" 
                       class="py-4 px-1 border-b-2 {{ request('status', 'all') === 'all' ? 'border-slate-600 text-slate-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium whitespace-nowrap">
                        All Courses ({{ $stats['total'] ?? 0 }})
                    </a>
                    <a href="{{ route('admin.courses', ['status' => 'published'] + request()->except('status')) }}" 
                       class="py-4 px-1 border-b-2 {{ request('status') === 'published' ? 'border-green-600 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium whitespace-nowrap">
                        Published ({{ $stats['published'] ?? 0 }})
                    </a>
                    <a href="{{ route('admin.courses', ['status' => 'draft'] + request()->except('status')) }}" 
                       class="py-4 px-1 border-b-2 {{ request('status') === 'draft' ? 'border-yellow-600 text-yellow-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium whitespace-nowrap">
                        Draft ({{ $stats['draft'] ?? 0 }})
                    </a>
                    <a href="{{ route('admin.courses', ['status' => 'archived'] + request()->except('status')) }}" 
                       class="py-4 px-1 border-b-2 {{ request('status') === 'archived' ? 'border-gray-600 text-gray-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium whitespace-nowrap">
                        Archived ({{ $stats['archived'] ?? 0 }})
                    </a>
                </nav>
            </div>
        </div>

        <!-- Courses Grid -->
        @if($courses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($courses as $course)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                        <!-- Course Thumbnail -->
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                                 alt="{{ $course->title }}" 
                                 class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-slate-400 to-gray-500 flex items-center justify-center">
                                <svg class="w-20 h-20 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        @endif

                        <!-- Course Content -->
                        <div class="p-5">
                            <!-- Status Badge -->
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-3 py-1 bg-{{ $course->status === 'published' ? 'green' : ($course->status === 'draft' ? 'yellow' : 'gray') }}-100 
                                             text-{{ $course->status === 'published' ? 'green' : ($course->status === 'draft' ? 'yellow' : 'gray') }}-800 
                                             rounded-full text-xs font-medium">
                                    {{ ucfirst($course->status) }}
                                </span>
                                @if($course->is_premium)
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs font-medium">
                                        Premium
                                    </span>
                                @endif
                            </div>

                            <!-- Title -->
                            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $course->title }}</h3>
                            
                            <!-- Teacher Info -->
                            <div class="flex items-center gap-2 mb-3">
                                @if($course->teacher_avatar)
                                    <img src="{{ asset('storage/' . $course->teacher_avatar) }}" 
                                         alt="{{ $course->teacher_name }}" 
                                         class="w-6 h-6 rounded-full">
                                @else
                                    <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-xs font-semibold text-slate-600">
                                        {{ substr($course->teacher_name, 0, 1) }}
                                    </div>
                                @endif
                                <span class="text-sm text-gray-600">{{ $course->teacher_name }}</span>
                            </div>

                            <!-- Description -->
                            @if($course->description)
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $course->description }}</p>
                            @endif

                            <!-- Stats -->
                            <div class="grid grid-cols-3 gap-2 mb-4 pt-3 border-t border-gray-100">
                                <div class="text-center">
                                    <p class="text-xs text-gray-500">Students</p>
                                    <p class="text-lg font-bold text-slate-600">{{ $course->enrollments_count ?? 0 }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500">Modules</p>
                                    <p class="text-lg font-bold text-slate-600">{{ $course->modules_count ?? 0 }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500">Quizzes</p>
                                    <p class="text-lg font-bold text-slate-600">{{ $course->quizzes_count ?? 0 }}</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <a href="{{ route('courses.show', $course->id) }}"
                                   class="flex-1 px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors text-center text-sm font-medium">
                                    View Details
                                </a>
                                <a href="{{ route('admin.courses.edit', $course->id) }}"
                                   class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="mt-8">
                {{ $courses->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <div class="text-6xl mb-4">📚</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Courses Found</h3>
                <p class="text-gray-600 mb-6">
                    @if(request('search'))
                        No courses match your search criteria.
                    @else
                        There are no courses available yet.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
