@extends('layouts.app', ['showBack' => true])

@section('title', $course['title'])

@section('content')
<div class="px-4">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-xl text-sm">
            {{ session('info') }}
        </div>
    @endif

    <!-- Course Header -->
    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-6 text-white mb-6">
        <h1 class="text-2xl font-bold mb-2">{{ $course['title'] }}</h1>
        <p class="text-blue-100 text-sm mb-4">{{ $course['description'] }}</p>
        
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium">{{ $course['instructor'] }}</p>
                <p class="text-xs text-blue-100">{{ $course['instructor_bio'] }}</p>
            </div>
        </div>

        @if($course['enrolled'])
            <div class="bg-white/10 rounded-xl p-3">
                <div class="flex justify-between text-sm mb-2">
                    <span>Your Progress</span>
                    <span class="font-bold">{{ $course['progress'] }}%</span>
                </div>
                <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full bg-white rounded-full" style="width: {{ $course['progress'] }}%"></div>
                </div>
            </div>
        @else
            <form action="{{ route('courses.enroll', $course['id']) }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="w-full bg-white text-blue-600 font-bold py-3 px-6 rounded-xl hover:bg-blue-50 transition">
                    @if($course['is_free'])
                        Daftar Gratis Sekarang
                    @else
                        Daftar - {{ $course['price'] }}
                    @endif
                </button>
            </form>
        @endif
    </div>

    <!-- Course Stats -->
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="bg-white rounded-xl p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-gray-900">{{ $course['modules_count'] }}</div>
            <div class="text-xs text-gray-600 mt-1">Modules</div>
        </div>
        <div class="bg-white rounded-xl p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-gray-900">{{ $course['lessons_count'] }}</div>
            <div class="text-xs text-gray-600 mt-1">Lessons</div>
        </div>
        <div class="bg-white rounded-xl p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-gray-900">{{ $course['duration'] }}</div>
            <div class="text-xs text-gray-600 mt-1">Duration</div>
        </div>
    </div>

    <!-- Course Modules -->
    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Course Content</h2>
        
        <div class="space-y-3">
            @foreach($course['modules'] as $index => $module)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <button class="w-full p-4 text-left flex items-center gap-3 active:bg-gray-50"
                            onclick="toggleModule({{ $index }})">
                        <!-- Module Icon -->
                        <div class="w-10 h-10 {{ $module['completed'] ? 'bg-green-100' : 'bg-blue-100' }} rounded-lg flex items-center justify-center flex-shrink-0">
                            @if($module['completed'])
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            @endif
                        </div>

                        <!-- Module Info -->
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900">{{ $module['title'] }}</h3>
                            <p class="text-xs text-gray-600 mt-1">{{ $module['lessons_count'] }} lessons • {{ $module['duration'] }}</p>
                        </div>

                        <!-- Chevron -->
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform" 
                             id="chevron-{{ $index }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Module Lessons (Hidden by default) -->
                    <div id="module-{{ $index }}" class="hidden border-t border-gray-200">
                        <div class="p-4 space-y-2">
                            @foreach($module['lessons'] as $lesson)
                                <a href="{{ route('lessons.show', $lesson['id']) }}" 
                                   class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 active:bg-gray-100 transition-colors">
                                    <div class="w-6 h-6 rounded-full border-2 {{ $lesson['is_completed'] ? 'bg-green-500 border-green-500' : 'border-gray-300' }} flex items-center justify-center flex-shrink-0">
                                        @if($lesson['is_completed'])
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium {{ $lesson['is_completed'] ? 'text-gray-900' : 'text-gray-700' }} truncate">
                                            {{ $lesson['title'] }}
                                        </p>
                                        <div class="flex items-center gap-2 text-xs text-gray-500">
                                            <span>{{ ucfirst($lesson['type']) }}</span>
                                            <span>•</span>
                                            <span>{{ $lesson['duration'] }} min</span>
                                            @if($lesson['is_preview'])
                                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">Preview</span>
                                            @endif
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Spacer untuk floating button -->
    <div class="h-24"></div>
</div>

    <!-- Action Button -->
    @if($course['enrolled'])
        <div class="fixed bottom-20 left-0 right-0 px-4 py-3 bg-gradient-to-t from-white via-white to-transparent pointer-events-none">
            @if($course['next_lesson_id'])
                <a href="{{ route('lessons.show', $course['next_lesson_id']) }}" class="block w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg active:scale-95 transition-all pointer-events-auto text-center">
                    Continue Learning
                </a>
            @else
                <a href="{{ route('courses.show', $course['id']) }}" class="block w-full bg-gradient-to-r from-gray-600 to-gray-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg active:scale-95 transition-all pointer-events-auto text-center">
                    Review Course
                </a>
            @endif
        </div>
    @else
        <div class="fixed bottom-20 left-0 right-0 px-4 py-3 bg-gradient-to-t from-white via-white to-transparent pointer-events-none">
            <form action="{{ route('courses.enroll', $course['id']) }}" method="POST" class="pointer-events-auto">
                @csrf
                <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg active:scale-95 transition-all">
                    Enroll in This Course
                </button>
            </form>
        </div>
    @endif

    <script>
        function toggleModule(index) {
            const content = document.getElementById(`module-${index}`);
            const chevron = document.getElementById(`chevron-${index}`);
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                chevron.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                chevron.style.transform = 'rotate(0deg)';
            }
        }
    </script>
@endsection
