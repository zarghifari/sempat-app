<x-app-layout>
    <!-- Page Title -->
    <div class="px-4 py-3 border-b bg-white">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-900">📄 Document Imports</h1>
            <a href="{{ route('document-imports.create') }}" 
               class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 active:scale-95 transition-all">
                <span>➕</span>
                <span>Upload</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="px-4 py-4 bg-gray-50">
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <div class="text-2xl font-bold text-blue-600">{{ $statistics['total'] }}</div>
                <div class="text-sm text-gray-600">Total Imports</div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <div class="text-2xl font-bold text-green-600">{{ $statistics['completed'] }}</div>
                <div class="text-sm text-gray-600">Completed</div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <div class="text-2xl font-bold text-blue-600">{{ $statistics['processing'] }}</div>
                <div class="text-sm text-gray-600">Processing</div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <div class="text-2xl font-bold text-red-600">{{ $statistics['failed'] }}</div>
                <div class="text-sm text-gray-600">Failed</div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="sticky top-14 z-30 bg-white border-b border-gray-200">
        <div class="flex overflow-x-auto hide-scrollbar">
            <a href="{{ route('document-imports.index') }}" 
               class="px-4 py-3 text-sm font-medium {{ !request('status') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600' }}">
                All ({{ $statistics['total'] }})
            </a>
            <a href="{{ route('document-imports.index', ['status' => 'completed']) }}" 
               class="px-4 py-3 text-sm font-medium {{ request('status') === 'completed' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600' }}">
                Completed ({{ $statistics['completed'] }})
            </a>
            <a href="{{ route('document-imports.index', ['status' => 'processing']) }}" 
               class="px-4 py-3 text-sm font-medium {{ request('status') === 'processing' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600' }}">
                Processing ({{ $statistics['processing'] }})
            </a>
            <a href="{{ route('document-imports.index', ['status' => 'failed']) }}" 
               class="px-4 py-3 text-sm font-medium {{ request('status') === 'failed' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600' }}">
                Failed ({{ $statistics['failed'] }})
            </a>
        </div>
    </div>

    <!-- Imports List -->
    <div class="px-4 py-4 space-y-3">
        @forelse($imports as $import)
        <a href="{{ route('document-imports.show', $import) }}" 
           class="block bg-white rounded-xl border border-gray-200 hover:border-blue-300 active:scale-[0.98] transition-all">
            <div class="p-4">
                <!-- Header -->
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-2xl">{{ $import->status_icon }}</span>
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                {{ $import->status_color === 'green' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $import->status_color === 'blue' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $import->status_color === 'red' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $import->status_color === 'gray' ? 'bg-gray-100 text-gray-700' : '' }}">
                                {{ ucfirst($import->status) }}
                            </span>
                        </div>
                        <h3 class="font-semibold text-gray-900">{{ $import->original_filename }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $import->formatted_file_size }}</p>
                    </div>
                </div>

                <!-- Stats for completed imports -->
                @if($import->isCompleted())
                <div class="flex gap-4 mt-3 text-sm text-gray-600">
                    <div class="flex items-center gap-1">
                        <span>📝</span>
                        <span>{{ number_format($import->word_count) }} words</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span>🖼️</span>
                        <span>{{ $import->image_count }} images</span>
                    </div>
                </div>
                @endif

                <!-- Error message for failed imports -->
                @if($import->isFailed())
                <div class="mt-3 p-2 bg-red-50 rounded-lg">
                    <p class="text-sm text-red-700">{{ $import->error_message }}</p>
                </div>
                @endif

                <!-- Footer -->
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                    <span class="text-xs text-gray-500">{{ $import->created_at->diffForHumans() }}</span>
                    
                    @if($import->processing_duration)
                    <span class="text-xs text-gray-500">⏱️ {{ $import->processing_duration }}</span>
                    @endif
                </div>
            </div>
        </a>
        @empty
        <div class="text-center py-12">
            <div class="text-6xl mb-4">📄</div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Imports Yet</h3>
            <p class="text-gray-600 mb-4">Upload your first document to get started</p>
            <a href="{{ route('document-imports.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                <span>➕</span>
                <span>Upload Document</span>
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($imports->hasPages())
    <div class="px-4 py-4">
        {{ $imports->links() }}
    </div>
    @endif
</x-app-layout>
