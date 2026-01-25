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
            <h1 class="text-xl font-bold text-gray-900">Upload Document</h1>
        </div>
    </div>

    <div class="p-4">
        <!-- Info Card -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <span class="text-2xl">ℹ️</span>
                <div class="flex-1">
                    <h3 class="font-semibold text-blue-900 mb-2">Supported Formats</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>✅ Microsoft Word (.docx, .doc)</li>
                        <li>✅ Max file size: 10MB</li>
                        <li>✅ Automatic HTML conversion</li>
                        <li>✅ Image extraction & optimization</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Upload Form -->
        <form action="{{ route('document-imports.store') }}" 
              method="POST" 
              enctype="multipart/form-data"
              class="bg-white rounded-xl border border-gray-200 p-6">
            @csrf

            <!-- File Input -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Select Document <span class="text-red-500">*</span>
                </label>
                
                <div class="relative">
                    <input type="file" 
                           name="document" 
                           id="document"
                           accept=".doc,.docx"
                           required
                           class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                    
                    @error('document')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <p class="mt-2 text-sm text-gray-500">
                    Maximum file size: 10MB. Supported formats: .doc, .docx
                </p>
            </div>

            <!-- Selected File Preview -->
            <div id="filePreview" class="hidden mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">📄</span>
                    <div class="flex-1">
                        <p id="fileName" class="font-medium text-gray-900"></p>
                        <p id="fileSize" class="text-sm text-gray-600"></p>
                    </div>
                    <button type="button" 
                            onclick="clearFile()"
                            class="text-red-600 hover:text-red-700 font-medium text-sm">
                        Remove
                    </button>
                </div>
            </div>

            <!-- Processing Info -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <span class="text-2xl">⚙️</span>
                    <div class="flex-1">
                        <h4 class="font-semibold text-yellow-900 mb-2">Processing Information</h4>
                        <p class="text-sm text-yellow-800">
                            After uploading, your document will be processed in the background. 
                            You'll be able to track the progress and view the converted content once processing is complete.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 active:scale-95 transition-all">
                    📤 Upload & Process
                </button>
                <a href="{{ route('document-imports.index') }}"
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 active:scale-95 transition-all">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- JavaScript for File Preview -->
    <script>
        document.getElementById('document').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('fileSize').textContent = formatFileSize(file.size);
                document.getElementById('filePreview').classList.remove('hidden');
            }
        });

        function clearFile() {
            document.getElementById('document').value = '';
            document.getElementById('filePreview').classList.add('hidden');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    </script>
</x-app-layout>
