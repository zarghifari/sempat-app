<x-app-layout>
    <!-- Page Header -->
    <div class="px-4 py-3 border-b bg-white">
        <div class="flex items-center gap-3">
            <a href="{{ route('document-imports.index') }}" 
               class="p-2 hover:bg-gray-100 rounded-lg active:scale-95 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-lg font-bold text-gray-900">Document Import</h1>
                <p class="text-sm text-gray-600">{{ $documentImport->original_filename }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 space-y-4">
        <!-- Status Card -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center gap-4 mb-4">
                <span class="text-4xl">{{ $documentImport->status_icon }}</span>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ ucfirst($documentImport->status) }}</h3>
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full
                            {{ $documentImport->status_color === 'green' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $documentImport->status_color === 'blue' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $documentImport->status_color === 'red' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $documentImport->status_color === 'gray' ? 'bg-gray-100 text-gray-700' : '' }}">
                            {{ ucfirst($documentImport->status) }}
                        </span>
                    </div>
                    
                    @if($documentImport->isProcessing())
                    <p class="text-sm text-gray-600">Processing your document... This may take a few minutes.</p>
                    @elseif($documentImport->isCompleted())
                    <p class="text-sm text-gray-600">Document processed successfully!</p>
                    @elseif($documentImport->isFailed())
                    <p class="text-sm text-red-600">Processing failed. Please try again.</p>
                    @else
                    <p class="text-sm text-gray-600">Waiting in queue...</p>
                    @endif
                </div>
            </div>

            <!-- File Info -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-600">File Size</p>
                    <p class="font-semibold text-gray-900">{{ $documentImport->formatted_file_size }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">File Type</p>
                    <p class="font-semibold text-gray-900">.{{ $documentImport->file_type }}</p>
                </div>
                @if($documentImport->processing_duration)
                <div>
                    <p class="text-sm text-gray-600">Processing Time</p>
                    <p class="font-semibold text-gray-900">{{ $documentImport->processing_duration }}</p>
                </div>
                @endif
                @if($documentImport->completed_at)
                <div>
                    <p class="text-sm text-gray-600">Completed At</p>
                    <p class="font-semibold text-gray-900">{{ $documentImport->completed_at->format('M d, Y H:i') }}</p>
                </div>
                @endif
            </div>

            <!-- Processing Stats (for completed imports) -->
            @if($documentImport->isCompleted())
            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($documentImport->word_count) }}</p>
                    <p class="text-sm text-blue-700">Words</p>
                </div>
                <div class="text-center p-3 bg-purple-50 rounded-lg">
                    <p class="text-2xl font-bold text-purple-600">{{ $documentImport->image_count }}</p>
                    <p class="text-sm text-purple-700">Images Extracted</p>
                </div>
            </div>
            @endif

            <!-- Error Message (for failed imports) -->
            @if($documentImport->isFailed())
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <h4 class="font-semibold text-red-900 mb-2">Error Details</h4>
                <p class="text-sm text-red-700">{{ $documentImport->error_message }}</p>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h3 class="font-semibold text-gray-900 mb-3">Actions</h3>
            
            <div class="space-y-2">
                @if($documentImport->isFailed())
                <!-- Retry Button -->
                <form action="{{ route('document-imports.retry', $documentImport) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center justify-center gap-2 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 active:scale-95 transition-all">
                        <span>🔄</span>
                        <span>Retry Processing</span>
                    </button>
                </form>
                @endif

                @if($documentImport->isCompleted() && !$documentImport->lesson_id)
                <!-- Create Lesson Button -->
                <button onclick="openCreateLessonModal()" 
                        class="w-full flex items-center justify-center gap-2 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 active:scale-95 transition-all">
                    <span>📝</span>
                    <span>Create Lesson from This</span>
                </button>
                @endif

                @if($documentImport->lesson_id)
                <!-- View Lesson Button -->
                <a href="{{ route('lessons.show', $documentImport->lesson_id) }}" 
                   class="w-full flex items-center justify-center gap-2 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 active:scale-95 transition-all">
                    <span>👁️</span>
                    <span>View Lesson</span>
                </a>
                @endif

                <!-- Delete Button -->
                <button onclick="confirmDelete()" 
                        class="w-full flex items-center justify-center gap-2 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 active:scale-95 transition-all">
                    <span>🗑️</span>
                    <span>Delete Import</span>
                </button>
            </div>
        </div>

        <!-- HTML Content Preview (for completed imports) -->
        @if($documentImport->isCompleted())
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Converted Content Preview</h3>
            </div>
            <div class="p-4 prose max-w-none">
                {!! Str::limit($documentImport->html_content, 1000) !!}
                @if(strlen($documentImport->html_content) > 1000)
                <p class="text-sm text-gray-500 mt-4">... (content truncated for preview)</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Metadata (for completed imports) -->
        @if($documentImport->isCompleted() && $documentImport->metadata)
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h3 class="font-semibold text-gray-900 mb-3">Document Metadata</h3>
            <dl class="space-y-2 text-sm">
                @foreach($documentImport->metadata as $key => $value)
                @if($value)
                <div class="flex justify-between py-2 border-b border-gray-100 last:border-0">
                    <dt class="text-gray-600 capitalize">{{ str_replace('_', ' ', $key) }}</dt>
                    <dd class="text-gray-900 font-medium">{{ $value }}</dd>
                </div>
                @endif
                @endforeach
            </dl>
        </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-sm">
            <div class="p-6">
                <div class="text-center mb-4">
                    <span class="text-6xl">⚠️</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Delete Import?</h3>
                <p class="text-gray-600 text-center mb-6">
                    This will delete the import record and all associated files. This action cannot be undone.
                </p>
                <div class="flex gap-3">
                    <button onclick="closeDeleteModal()" 
                            class="flex-1 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300">
                        Cancel
                    </button>
                    <form action="{{ route('document-imports.destroy', $documentImport) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Auto-refresh for processing status
        @if($documentImport->isProcessing() || $documentImport->isPending())
        setInterval(() => {
            fetch('{{ route('document-imports.status', $documentImport) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.status !== '{{ $documentImport->status }}') {
                        location.reload();
                    }
                });
        }, 5000); // Check every 5 seconds
        @endif
    </script>
</x-app-layout>
