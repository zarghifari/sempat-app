@extends('layouts.app')

@section('title', $module->title)

@section('page-title', 'Module Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 pb-24 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('teacher.courses.show', $course->id) }}" class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-700 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Course
            </a>
        </div>

        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-6">
            <a href="{{ route('teacher.courses') }}" class="hover:text-purple-600">My Courses</a>
            <span>/</span>
            <a href="{{ route('teacher.courses.show', $course->id) }}" class="hover:text-purple-600">{{ $course->title }}</a>
            <span>/</span>
            <span class="text-gray-900 font-medium">{{ $module->title }}</span>
        </div>

        <!-- Module Header -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-6 text-white">
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-semibold">
                        Module {{ $module->order }}
                    </span>
                    <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium">
                        {{ ucfirst($module->status) }}
                    </span>
                    @if($module->is_locked)
                        <span class="px-3 py-1 bg-orange-500/80 rounded-full text-sm font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            Locked
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl font-bold mb-2">{{ $module->title }}</h1>
                <p class="text-white/90">{{ $module->description }}</p>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-6 text-sm text-gray-600">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            {{ $module->lessons->count() }} Lessons
                        </span>
                        @if($module->estimated_minutes)
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $module->estimated_minutes }} mins
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('teacher.modules.edit', [$course->id, $module->id]) }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                        Edit Module
                    </a>
                </div>
            </div>
        </div>

        <!-- Lessons Section -->
        <div class="bg-white rounded-xl shadow-md p-6 sm:p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Lessons</h2>
                <a href="{{ route('teacher.lessons.create', [$course->id, $module->id]) }}" 
                   class="px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-lg hover:from-yellow-700 hover:to-orange-700 transition-colors font-medium shadow-lg flex items-center gap-2 text-sm sm:text-base">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <span class="hidden sm:inline">Import Document</span>
                    <span class="sm:hidden">Import</span>
                </a>
            </div>

            @if($module->lessons->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-100 rounded-full mb-4">
                        <svg class="w-10 h-10 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Lessons Yet</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">Import documents (DOCX, DOC, PDF) to create lesson content automatically.</p>
                    <a href="{{ route('teacher.lessons.create', [$course->id, $module->id]) }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-lg hover:from-yellow-700 hover:to-orange-700 transition-colors font-medium shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Import First Document
                    </a>
                    <p class="text-xs text-gray-500 mt-4 max-w-lg mx-auto">
                        📄 <strong>Supported formats:</strong> DOCX & DOC (auto-convert to HTML), PDF (for download)
                    </p>
                </div>
            @else
                <!-- Lessons List -->
                <div class="space-y-3 sm:space-y-4">
                    @foreach($module->lessons as $lesson)
                        <div class="border border-gray-200 rounded-lg p-4 sm:p-6 hover:border-purple-300 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="text-xs sm:text-sm font-semibold text-purple-600 bg-purple-100 px-2 sm:px-3 py-1 rounded-full">
                                            Lesson {{ $lesson->order }}
                                        </span>
                                        <span class="text-xs sm:text-sm px-2 sm:px-3 py-1 rounded-full font-medium
                                            {{ $lesson->type === 'video' ? 'bg-red-100 text-red-700' : '' }}
                                            {{ $lesson->type === 'text' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $lesson->type === 'quiz' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $lesson->type === 'document' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                            {{ $lesson->type === 'audio' ? 'bg-pink-100 text-pink-700' : '' }}
                                            {{ $lesson->type === 'interactive' ? 'bg-indigo-100 text-indigo-700' : '' }}">
                                            {{ ucfirst($lesson->type) }}
                                        </span>
                                        <span class="text-xs sm:text-sm font-medium {{ $lesson->status === 'published' ? 'text-green-600 bg-green-100' : 'text-gray-600 bg-gray-100' }} px-2 sm:px-3 py-1 rounded-full">
                                            {{ ucfirst($lesson->status) }}
                                        </span>
                                        @if($lesson->is_preview)
                                            <span class="text-xs sm:text-sm font-medium text-blue-600 bg-blue-100 px-2 sm:px-3 py-1 rounded-full">
                                                Preview
                                            </span>
                                        @endif
                                        @if($lesson->is_mandatory)
                                            <span class="text-xs sm:text-sm font-medium text-orange-600 bg-orange-100 px-2 sm:px-3 py-1 rounded-full">
                                                Mandatory
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-2">{{ $lesson->title }}</h3>
                                    @if($lesson->description)
                                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($lesson->description, 150) }}</p>
                                    @endif
                                    
                                    <!-- Document Download Link -->
                                    @if($lesson->type === 'document' && $lesson->attachments && isset($lesson->attachments['document']))
                                        <div class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                </svg>
                                                <a href="{{ asset('storage/' . $lesson->attachments['document']) }}" 
                                                   target="_blank" 
                                                   class="text-sm font-medium text-yellow-700 hover:text-yellow-900 hover:underline">
                                                    📎 {{ basename($lesson->attachments['document']) }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs sm:text-sm text-gray-600">
                                        @if($lesson->duration_minutes)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $lesson->duration_minutes }} mins
                                            </span>
                                        @endif
                                        @if($lesson->views_count)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                {{ $lesson->views_count }} views
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-2 w-full sm:w-auto">
                                    <a href="{{ route('teacher.lessons.edit', [$course->id, $module->id, $lesson->id]) }}" 
                                       class="flex-1 sm:flex-none text-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                                        Edit
                                    </a>
                                    <form action="{{ route('teacher.lessons.destroy', [$course->id, $module->id, $lesson->id]) }}" method="POST" class="flex-1 sm:flex-none"
                                          onsubmit="return confirm('Are you sure you want to delete this lesson?')">
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
