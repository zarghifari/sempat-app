@extends('layouts.app')

@section('title', isset($lesson) ? 'Edit Lesson' : 'Import Document to Lesson')

@section('page-title', isset($lesson) ? 'Edit Lesson' : 'Import Document to Lesson')

@push('styles')
<!-- Trix Editor -->
<link rel="stylesheet" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
<style>
    trix-toolbar .trix-button-group { margin-bottom: 10px; }
    trix-editor { min-height: 200px; max-height: 400px; overflow-y: auto; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 pb-24 pt-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('teacher.modules.show', [$course->id, $module->id]) }}" class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-700 font-medium text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Module
            </a>
        </div>

        <!-- Breadcrumb -->
        <div class="flex flex-wrap items-center gap-2 text-xs sm:text-sm text-gray-600 mb-6">
            <a href="{{ route('teacher.courses') }}" class="hover:text-purple-600">My Courses</a>
            <span>/</span>
            <a href="{{ route('teacher.courses.show', $course->id) }}" class="hover:text-purple-600">{{ Str::limit($course->title, 20) }}</a>
            <span>/</span>
            <a href="{{ route('teacher.modules.show', [$course->id, $module->id]) }}" class="hover:text-purple-600">{{ Str::limit($module->title, 20) }}</a>
            <span>/</span>
            <span class="text-gray-900 font-medium">{{ isset($lesson) ? 'Edit' : 'Create' }} Lesson</span>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-600 to-orange-600 p-4 sm:p-6">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                    </svg>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-white">{{ isset($lesson) ? 'Edit Lesson' : '📄 Import Document to Lesson' }}</h1>
                        <p class="text-yellow-100 text-sm">{{ $module->title }}</p>
                    </div>
                </div>
                <p class="text-white/90 text-xs sm:text-sm mt-2">Upload DOCX/DOC for auto-conversion to HTML, or PDF for download</p>
            </div>

            <form action="{{ isset($lesson) ? route('teacher.lessons.update', [$course->id, $module->id, $lesson->id]) : route('teacher.lessons.store', [$course->id, $module->id]) }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  class="p-4 sm:p-6 md:p-8 space-y-4 sm:space-y-6">
                @csrf
                @if(isset($lesson))
                    @method('PUT')
                @endif

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Lesson Title *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $lesson->title ?? '') }}" 
                           class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm sm:text-base" 
                           placeholder="Enter lesson title"
                           required>
                    @error('title')
                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea id="description" 
                              name="description" 
                              rows="3" 
                              class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm sm:text-base" 
                              placeholder="Brief description of this lesson">{{ old('description', $lesson->description ?? '') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hidden Type Field - Always Document -->
                <input type="hidden" name="type" value="document">

                <!-- Document Import Section -->
                <div class="space-y-4">
                        <div>
                            <label for="document_file" class="block text-sm font-semibold text-gray-700 mb-2">
                                Upload Document
                            </label>
                            <input type="file" 
                                   id="document_file" 
                                   name="document_file" 
                                   accept=".pdf,.doc,.docx"
                                   class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm sm:text-base">
                            <p class="mt-1 text-xs text-gray-500">Accepted: PDF, DOC, DOCX (Max 10MB)</p>
                            @error('document_file')
                                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        @if(isset($lesson) && $lesson->attachments && isset($lesson->attachments['document']))
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-gray-900 text-sm">Current Document</p>
                                            <p class="text-xs text-gray-600">{{ basename($lesson->attachments['document']) }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $lesson->attachments['document']) }}" 
                                       target="_blank"
                                       class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs font-medium">
                                        View
                                    </a>
                                </div>
                            </div>
                        @endif
                        
                        <div class="flex items-start gap-2 p-3 bg-white rounded-lg border border-yellow-200">
                            <input type="checkbox" 
                                   id="convert_to_html" 
                                   name="convert_to_html" 
                                   value="1"
                                   checked
                                   class="mt-1 w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                            <label for="convert_to_html" class="text-sm text-gray-700">
                                <span class="font-medium">🔄 Auto-convert to editable HTML content</span>
                                <span class="block text-xs text-gray-500 mt-0.5">For DOCX/DOC only - converts document to rich text that you can edit</span>
                            </label>
                        </div>
                    </div>

                <!-- Converted Content Editor (will show after upload) -->
                @if(isset($lesson) && $lesson->content)
                <div>
                    <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">Lesson Content (Editable)</label>
                    <input id="content" type="hidden" name="content" value="{{ old('content', $lesson->content ?? '') }}">
                    <trix-editor input="content" class="w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm sm:text-base"></trix-editor>
                    <p class="mt-1 text-xs text-gray-500">Edit the converted content from your document</p>
                </div>
                @endif

                <!-- Order & Duration -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="order" class="block text-sm font-semibold text-gray-700 mb-2">Order *</label>
                        <input type="number" 
                               id="order" 
                               name="order" 
                               min="1"
                               value="{{ old('order', $lesson->order ?? ($module->lessons->max('order') + 1) ?? 1) }}" 
                               class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm sm:text-base" 
                               required>
                        <p class="mt-1 text-xs text-gray-500">Display order in module</p>
                        @error('order')
                            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 mb-2">Duration (minutes)</label>
                        <input type="number" 
                               id="duration_minutes" 
                               name="duration_minutes" 
                               min="0"
                               value="{{ old('duration_minutes', $lesson->duration_minutes ?? '') }}" 
                               class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm sm:text-base" 
                               placeholder="Estimated time">
                        <p class="mt-1 text-xs text-gray-500">Estimated completion time</p>
                        @error('duration_minutes')
                            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
                    <select id="status" 
                            name="status" 
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm sm:text-base" 
                            required>
                        <option value="draft" {{ old('status', $lesson->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $lesson->status ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Options -->
                <div class="border-t pt-4 sm:pt-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Additional Options</h3>
                    <div class="space-y-3 sm:space-y-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="is_preview" 
                                   value="1" 
                                   {{ old('is_preview', $lesson->is_preview ?? false) ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <div class="flex-1">
                                <span class="font-medium text-gray-700 text-sm sm:text-base">Free Preview</span>
                                <p class="text-xs sm:text-sm text-gray-500">Allow non-enrolled students to preview this lesson</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="is_mandatory" 
                                   value="1" 
                                   {{ old('is_mandatory', $lesson->is_mandatory ?? false) ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <div class="flex-1">
                                <span class="font-medium text-gray-700 text-sm sm:text-base">Mandatory</span>
                                <p class="text-xs sm:text-sm text-gray-500">Students must complete this lesson to proceed</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col-reverse sm:flex-row gap-3 sm:gap-4 pt-4 sm:pt-6 border-t">
                    <a href="{{ route('teacher.modules.show', [$course->id, $module->id]) }}" 
                       class="w-full sm:w-auto text-center px-4 sm:px-6 py-2.5 sm:py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm sm:text-base">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-lg hover:from-yellow-700 hover:to-orange-700 transition-colors font-medium shadow-lg text-sm sm:text-base">
                        {{ isset($lesson) ? 'Update Lesson' : 'Import & Create Lesson' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<!-- Trix Editor -->
<script src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
@endpush

<script>
// File upload preview
document.getElementById('document_file')?.addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    if (fileName) {
        console.log('File selected:', fileName);
    }
});
</script>
@endsection
