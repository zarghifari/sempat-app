@extends('layouts.app')

@section('title', isset($course) ? 'Edit Course' : 'Create Course')

@section('page-title', isset($course) ? 'Edit Course' : 'Create New Course')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 pb-24 pt-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('teacher.courses') }}" class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-700 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Courses
            </a>
        </div>

        <!-- Form -->
        <form action="{{ isset($course) ? route('teacher.courses.update', $course->id) : route('teacher.courses.store') }}" 
              method="POST" 
              enctype="multipart/form-data"
              class="bg-white rounded-xl shadow-md p-4 sm:p-6 md:p-8 space-y-4 sm:space-y-6">
            @csrf
            @if(isset($course))
                @method('PUT')
            @endif

            <!-- Course Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Course Title <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title', $course->title ?? '') }}"
                       required
                       class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('title') border-red-500 @enderror"
                       placeholder="e.g., Introduction to Web Development">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Course Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Course Description <span class="text-red-500">*</span>
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="4"
                          required
                          class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('description') border-red-500 @enderror"
                          placeholder="Describe what students will learn in this course...">{{ old('description', $course->description ?? '') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category & Level -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category" 
                            id="category" 
                            required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('category') border-red-500 @enderror">
                        <option value="">Select Category</option>
                        <option value="programming" {{ old('category', $course->category ?? '') === 'programming' ? 'selected' : '' }}>Programming</option>
                        <option value="design" {{ old('category', $course->category ?? '') === 'design' ? 'selected' : '' }}>Design</option>
                        <option value="business" {{ old('category', $course->category ?? '') === 'business' ? 'selected' : '' }}>Business</option>
                        <option value="marketing" {{ old('category', $course->category ?? '') === 'marketing' ? 'selected' : '' }}>Marketing</option>
                        <option value="science" {{ old('category', $course->category ?? '') === 'science' ? 'selected' : '' }}>Science</option>
                        <option value="mathematics" {{ old('category', $course->category ?? '') === 'mathematics' ? 'selected' : '' }}>Mathematics</option>
                        <option value="languages" {{ old('category', $course->category ?? '') === 'languages' ? 'selected' : '' }}>Languages</option>
                        <option value="other" {{ old('category', $course->category ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="level" class="block text-sm font-medium text-gray-700 mb-2">
                        Difficulty Level <span class="text-red-500">*</span>
                    </label>
                    <select name="level" 
                            id="level" 
                            required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('level') border-red-500 @enderror">
                        <option value="">Select Level</option>
                        <option value="beginner" {{ old('level', $course->level ?? '') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ old('level', $course->level ?? '') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ old('level', $course->level ?? '') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                    @error('level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Thumbnail Upload -->
            <div>
                <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-2">
                    Course Thumbnail
                </label>
                @if(isset($course) && $course->thumbnail)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                             alt="Current thumbnail" 
                             class="w-48 h-32 object-cover rounded-lg border border-gray-300">
                        <p class="text-xs text-gray-500 mt-1">Current thumbnail</p>
                    </div>
                @endif
                <input type="file" 
                       name="thumbnail" 
                       id="thumbnail" 
                       accept="image/*"
                       class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('thumbnail') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">Recommended size: 1200x630px (PNG, JPG, max 2MB)</p>
                @error('thumbnail')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status & Premium -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" 
                            id="status" 
                            required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="draft" {{ old('status', $course->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $course->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ old('status', $course->status ?? '') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Course Type
                    </label>
                    <div class="flex items-center gap-4 sm:gap-6 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" 
                                   name="is_premium" 
                                   value="0" 
                                   {{ old('is_premium', $course->is_premium ?? 0) == 0 ? 'checked' : '' }}
                                   class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600 border-gray-300 focus:ring-purple-500">
                            <span class="text-sm sm:text-base font-medium text-gray-700">Free</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" 
                                   name="is_premium" 
                                   value="1" 
                                   {{ old('is_premium', $course->is_premium ?? 0) == 1 ? 'checked' : '' }}
                                   class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600 border-gray-300 focus:ring-purple-500">
                            <span class="text-sm sm:text-base font-medium text-gray-700">Premium</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-4 sm:pt-6 border-t border-gray-200">
                <a href="{{ route('teacher.courses') }}" 
                   class="w-full sm:w-auto text-center px-6 py-2.5 sm:py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium text-sm sm:text-base">
                    Cancel
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-6 sm:px-8 py-2.5 sm:py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:from-purple-700 hover:to-blue-700 transition-colors font-medium shadow-lg text-sm sm:text-base">
                    {{ isset($course) ? 'Update Course' : 'Create Course' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
