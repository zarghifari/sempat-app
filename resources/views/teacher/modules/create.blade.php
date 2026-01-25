@extends('layouts.app')

@section('title', isset($module) ? 'Edit Module' : 'Create Module')

@section('page-title', isset($module) ? 'Edit Module' : 'Create Module')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 pb-24 pt-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('teacher.courses.show', $course->id) }}" class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-700 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Course
            </a>
        </div>

        <!-- Course Info -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6 border-l-4 border-purple-500">
            <p class="text-sm text-gray-600 mb-1">Course:</p>
            <h2 class="text-xl font-bold text-gray-900">{{ $course->title }}</h2>
        </div>

        <!-- Form -->
        <form action="{{ isset($module) ? route('teacher.modules.update', [$course->id, $module->id]) : route('teacher.modules.store', $course->id) }}" 
              method="POST" 
              class="bg-white rounded-xl shadow-md p-4 sm:p-6 md:p-8 space-y-4 sm:space-y-6">
            @csrf
            @if(isset($module))
                @method('PUT')
            @endif

            <!-- Module Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Module Title <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title', $module->title ?? '') }}"
                       required
                       placeholder="e.g., Introduction to Web Development"
                       class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Module Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Module Description
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="4"
                          placeholder="Brief description of what students will learn in this module..."
                          class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $module->description ?? '') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Order & Duration -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                        Module Order <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="order" 
                           id="order" 
                           value="{{ old('order', $module->order ?? 1) }}"
                           min="1"
                           required
                           class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('order') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Position in course (1 = first)</p>
                    @error('order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estimated_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                        Estimated Duration (minutes)
                    </label>
                    <input type="number" 
                           name="estimated_minutes" 
                           id="estimated_minutes" 
                           value="{{ old('estimated_minutes', $module->estimated_minutes ?? '') }}"
                           min="0"
                           placeholder="e.g., 120"
                           class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('estimated_minutes') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Estimated time to complete</p>
                    @error('estimated_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status & Lock -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="draft" {{ old('status', $module->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $module->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Access Control
                    </label>
                    <div class="flex items-center gap-2 sm:gap-3 pt-2">
                        <input type="checkbox" 
                               name="is_locked" 
                               id="is_locked" 
                               value="1"
                               {{ old('is_locked', $module->is_locked ?? false) ? 'checked' : '' }}
                               class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <label for="is_locked" class="text-xs sm:text-sm text-gray-700">
                            Lock this module (students must complete previous modules first)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-4 sm:pt-6 border-t border-gray-200">
                <a href="{{ route('teacher.courses.show', $course->id) }}" 
                   class="w-full sm:w-auto text-center px-6 py-2.5 sm:py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium text-sm sm:text-base">
                    Cancel
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-6 sm:px-8 py-2.5 sm:py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:from-purple-700 hover:to-blue-700 transition-colors font-medium shadow-lg text-sm sm:text-base">
                    {{ isset($module) ? 'Update Module' : 'Create Module' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
