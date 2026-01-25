@extends('layouts.app')

@section('title', isset($article) ? 'Edit Article' : 'Create Article')

@section('page-title', isset($article) ? 'Edit Article' : 'Create New Article')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 pb-24 pt-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('teacher.articles') }}" class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-700 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Articles
            </a>
        </div>

        <!-- Form -->
        <form action="{{ isset($article) ? route('teacher.articles.update', $article->id) : route('teacher.articles.store') }}" 
              method="POST" 
              enctype="multipart/form-data"
              class="bg-white rounded-xl shadow-md p-8 space-y-6">
            @csrf
            @if(isset($article))
                @method('PUT')
            @endif

            <!-- Article Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Article Title <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title', $article->title ?? '') }}"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('title') border-red-500 @enderror"
                       placeholder="e.g., 10 Tips for Effective Learning">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Article Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Article Content <span class="text-red-500">*</span>
                </label>
                <textarea name="content" 
                          id="content" 
                          rows="12"
                          required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('content') border-red-500 @enderror"
                          placeholder="Write your article content here...">{{ old('content', $article->content ?? '') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">You can use Markdown formatting</p>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category & Tags -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category" 
                            id="category" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('category') border-red-500 @enderror">
                        <option value="">Select Category</option>
                        <option value="learning-tips" {{ old('category', $article->category ?? '') === 'learning-tips' ? 'selected' : '' }}>Learning Tips</option>
                        <option value="study-techniques" {{ old('category', $article->category ?? '') === 'study-techniques' ? 'selected' : '' }}>Study Techniques</option>
                        <option value="motivation" {{ old('category', $article->category ?? '') === 'motivation' ? 'selected' : '' }}>Motivation</option>
                        <option value="productivity" {{ old('category', $article->category ?? '') === 'productivity' ? 'selected' : '' }}>Productivity</option>
                        <option value="time-management" {{ old('category', $article->category ?? '') === 'time-management' ? 'selected' : '' }}>Time Management</option>
                        <option value="self-reflection" {{ old('category', $article->category ?? '') === 'self-reflection' ? 'selected' : '' }}>Self Reflection</option>
                        <option value="general" {{ old('category', $article->category ?? '') === 'general' ? 'selected' : '' }}>General</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                        Tags
                    </label>
                    <input type="text" 
                           name="tags" 
                           id="tags" 
                           value="{{ old('tags', $article->tags ?? '') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('tags') border-red-500 @enderror"
                           placeholder="e.g., learning, tips, study">
                    <p class="mt-1 text-sm text-gray-500">Separate with commas</p>
                    @error('tags')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Thumbnail Upload -->
            <div>
                <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-2">
                    Featured Image
                </label>
                @if(isset($article) && $article->thumbnail)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $article->thumbnail) }}" 
                             alt="Current thumbnail" 
                             class="w-48 h-32 object-cover rounded-lg border border-gray-300">
                        <p class="text-xs text-gray-500 mt-1">Current image</p>
                    </div>
                @endif
                <input type="file" 
                       name="thumbnail" 
                       id="thumbnail" 
                       accept="image/*"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('thumbnail') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">Recommended size: 1200x630px (PNG, JPG, max 2MB)</p>
                @error('thumbnail')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status" 
                        id="status" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('status') border-red-500 @enderror">
                    <option value="draft" {{ old('status', $article->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $article->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('teacher.articles') }}" 
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-8 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:from-purple-700 hover:to-blue-700 transition-colors font-medium shadow-lg">
                    {{ isset($article) ? 'Update Article' : 'Publish Article' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
