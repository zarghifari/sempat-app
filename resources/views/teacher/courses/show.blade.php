@extends('layouts.app')

@section('title', $course->title)

@section('page-title', 'Course Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 pb-24 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('teacher.courses') }}" class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-700 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Courses
            </a>
        </div>

        <!-- Course Header -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            @if($course->thumbnail)
                <div class="h-64 w-full overflow-hidden">
                    <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                         alt="{{ $course->title }}" 
                         class="w-full h-full object-cover">
                </div>
            @endif
            <div class="p-8">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $course->title }}</h1>
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full font-medium">{{ ucfirst($course->category) }}</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-medium">{{ ucfirst($course->level) }}</span>
                            <span class="px-3 py-1 {{ $course->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }} rounded-full font-medium">
                                {{ ucfirst($course->status) }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('teacher.courses.edit', $course->id) }}" 
                       class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Edit Course
                    </a>
                </div>
                <p class="text-gray-700 leading-relaxed">{{ $course->description }}</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Total Modules</p>
                <p class="text-3xl font-bold text-purple-600">{{ $modules->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Total Lessons</p>
                <p class="text-3xl font-bold text-blue-600">{{ $modules->sum('lessons_count') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Students Enrolled</p>
                <p class="text-3xl font-bold text-green-600">{{ $enrollments }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Est. Duration</p>
                <p class="text-3xl font-bold text-yellow-600">{{ floor($modules->sum('estimated_minutes') / 60) }}h</p>
            </div>
        </div>

        <!-- Modules Section -->
        <div class="bg-white rounded-xl shadow-md p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Course Modules</h2>
                <a href="{{ route('teacher.modules.create', $course->id) }}" 
                   class="px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:from-purple-700 hover:to-blue-700 transition-colors font-medium shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add
                </a>
            </div>

            @if($modules->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-purple-100 rounded-full mb-4">
                        <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Modules Yet</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">Start building your course by adding modules. Each module can contain multiple lessons.</p>
                    <a href="{{ route('teacher.modules.create', $course->id) }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:from-purple-700 hover:to-blue-700 transition-colors font-medium shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create First Module
                    </a>
                </div>
            @else
                <!-- Modules List -->
                <div class="space-y-4">
                    @foreach($modules as $module)
                        <div class="border border-gray-200 rounded-lg p-6 hover:border-purple-300 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-2">
                                        <span class="text-xs sm:text-sm font-semibold text-purple-600 bg-purple-100 px-2 sm:px-3 py-1 rounded-full">
                                            Module {{ $module->order }}
                                        </span>
                                        <span class="text-xs sm:text-sm font-medium {{ $module->status === 'published' ? 'text-green-600 bg-green-100' : 'text-gray-600 bg-gray-100' }} px-2 sm:px-3 py-1 rounded-full">
                                            {{ ucfirst($module->status) }}
                                        </span>
                                        @if($module->is_locked)
                                            <span class="text-xs sm:text-sm font-medium text-orange-600 bg-orange-100 px-2 sm:px-3 py-1 rounded-full flex items-center gap-1">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Locked
                                            </span>
                                        @endif
                                    </div>
                                    <a href="{{ route('teacher.modules.show', [$course->id, $module->id]) }}" class="group">
                                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 group-hover:text-purple-600 transition-colors">{{ $module->title }}</h3>
                                    </a>
                                    @if($module->description)
                                        <p class="text-sm sm:text-base text-gray-600 mb-3">{{ $module->description }}</p>
                                    @endif
                                    <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs sm:text-sm text-gray-600">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            {{ $module->lessons_count ?? 0 }} Lessons
                                        </span>
                                        @if($module->estimated_minutes)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $module->estimated_minutes }} mins
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                                    <a href="{{ route('teacher.modules.show', [$course->id, $module->id]) }}" 
                                       class="flex-1 sm:flex-none text-center px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors text-sm font-medium">
                                        View Lessons
                                    </a>
                                    <a href="{{ route('teacher.modules.edit', [$course->id, $module->id]) }}" 
                                       class="flex-1 sm:flex-none text-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                                        Edit
                                    </a>
                                    <form action="{{ route('teacher.modules.destroy', [$course->id, $module->id]) }}" method="POST" class="flex-1 sm:flex-none"
                                          onsubmit="return confirm('Are you sure you want to delete this module? All lessons in this module will also be deleted.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
