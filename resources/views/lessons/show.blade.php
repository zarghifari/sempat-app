@extends('layouts.app', ['showBack' => true])

@section('title', $lesson['title'])

@section('content')
    <div class="px-4">

        <!-- Lesson Progress Bar (if enrolled) -->
        @if ($lesson['is_enrolled'])
            <div class="bg-white rounded-xl p-4 mb-4 shadow-sm">
                <div class="flex justify-between text-sm mb-2">
                    <span class="font-medium text-gray-700">Progress Anda</span>
                    <span class="font-bold text-blue-600">{{ $lesson['completion_percentage'] }}%</span>
                </div>
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-purple-600 rounded-full transition-all duration-500"
                        style="width: {{ $lesson['completion_percentage'] }}%"></div>
                </div>
            </div>
        @endif

        <!-- Lesson Content -->
        <div class="bg-white rounded-2xl p-6 mb-6 shadow-sm">
            <div class="flex items-center gap-4 text-sm text-black-100">
                <span>⏱️ {{ $lesson['duration'] }} menit</span>
                <span>•</span>
                <span>📖 {{ $lesson['module'] }}</span>
            </div>
            <div class="h-4"></div>
            <div class="prose prose-sm max-w-none lesson-content">
                {!! $lesson['content'] !!}
            </div>
        </div>

        <!-- External Links (YouTube, etc) -->
        @if (!empty($lesson['external_links']))
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">📹 Video Pembelajaran</h3>
                <div class="space-y-3">
                    @foreach ($lesson['external_links'] as $link)
                        @if ($link['type'] === 'youtube')
                            <div class="bg-white rounded-xl p-4 shadow-sm">
                                <p class="text-sm font-medium text-gray-700 mb-2">{{ $link['title'] }}</p>
                                <a href="{{ $link['url'] }}" target="_blank"
                                    class="text-blue-600 text-sm hover:underline flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd"
                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Tonton di YouTube
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- <!-- Attachments -->
    @if (!empty($lesson['attachments']))
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">📎 Lampiran</h3>
            <div class="space-y-2">
                @foreach ($lesson['attachments'] as $attachment)
                    <a href="{{ $attachment['url'] }}" 
                       target="_blank"
                       class="flex items-center gap-3 bg-white rounded-xl p-4 shadow-sm active:scale-98 transition">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $attachment['original'] }}</p>
                            <p class="text-xs text-gray-500">{{ $attachment['mime'] }} • {{ number_format($attachment['size'] / 1024, 2) }} KB</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>
    @endif --}}

        <!-- Navigation Buttons -->
        <div class="flex gap-3 mb-6">
            @if ($lesson['previous_lesson'])
                <a href="{{ route('lessons.show', $lesson['previous_lesson']['id']) }}"
                    class="flex-1 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium text-center active:bg-gray-200 transition">
                    ← Sebelumnya
                </a>
            @endif

            @if ($lesson['next_lesson'])
                <a href="{{ route('lessons.show', $lesson['next_lesson']['id']) }}"
                    class="flex-1 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-medium text-center active:scale-98 transition">
                    Selanjutnya →
                </a>
            @else
                {{-- Tombol kembali ke course jika ini lesson terakhir --}}
                <a href="{{ route('courses.show', $lesson['course_id']) }}"
                    class="flex-1 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-xl font-medium text-center active:scale-98 transition">
                    🏠 Kembali ke Course
                </a>
            @endif
        </div>

        <!-- Quiz Section -->
        @if ($lesson['quizzes'] && count($lesson['quizzes']) > 0)
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">📝 Quiz & Penilaian</h3>
                <div class="space-y-3">
                    @foreach ($lesson['quizzes'] as $quiz)
                        <div class="bg-white rounded-xl p-4 shadow-sm border-2 border-blue-100">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-900 mb-1">{{ $quiz['title'] }}</h4>
                                    <p class="text-sm text-gray-600">{{ $quiz['description'] }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mb-3">
                                <div class="bg-gray-50 rounded-lg p-2">
                                    <p class="text-xs text-gray-500">Soal</p>
                                    <p class="font-semibold text-gray-900">{{ $quiz['total_questions'] }} pertanyaan</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-2">
                                    <p class="text-xs text-gray-500">Waktu</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ $quiz['time_limit'] ? $quiz['time_limit'] . ' menit' : 'Tidak terbatas' }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-2">
                                    <p class="text-xs text-gray-500">Nilai Lulus</p>
                                    <p class="font-semibold text-gray-900">{{ $quiz['passing_score'] }}%</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-2">
                                    <p class="text-xs text-gray-500">Percobaan</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ $quiz['attempts'] }} / {{ $quiz['max_attempts'] ?: '∞' }}
                                    </p>
                                </div>
                            </div>

                            @if ($quiz['best_score'] !== null)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-2 mb-3">
                                    <p class="text-xs text-green-600 mb-1">Nilai Terbaik Anda</p>
                                    <p class="text-lg font-bold text-green-700">{{ number_format($quiz['best_score'], 1) }}%</p>
                                </div>
                            @endif

                            <div class="flex gap-2">
                                @if ($lesson['is_enrolled'])
                                    @if ($quiz['max_attempts'] > 0 && $quiz['attempts'] >= $quiz['max_attempts'])
                                        <button disabled
                                            class="flex-1 py-3 bg-gray-300 text-gray-500 rounded-xl font-medium text-center cursor-not-allowed">
                                            Batas Percobaan Tercapai
                                        </button>
                                    @else
                                        <a href="{{ route('quizzes.show', ['lessonId' => $lesson['id'], 'quizId' => $quiz['id']]) }}"
                                            class="flex-1 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-medium text-center active:scale-98 transition">
                                            {{ $quiz['attempts'] > 0 ? 'Coba Lagi' : 'Mulai Quiz' }}
                                        </a>
                                    @endif
                                @else
                                    <button disabled
                                        class="flex-1 py-3 bg-gray-300 text-gray-500 rounded-xl font-medium text-center cursor-not-allowed">
                                        Harus Enroll Dulu
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Mark as Complete Button (if enrolled) -->
        @if ($lesson['is_enrolled'] && !$lesson['is_completed'])
            <form action="{{ route('lessons.complete', $lesson['id']) }}" method="POST" class="mb-6">
                @csrf
                <button type="submit"
                    class="w-full py-4 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-semibold active:scale-98 transition shadow-lg">
                    ✓ Tandai Selesai
                </button>
            </form>
        @elseif($lesson['is_completed'])
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 text-center">
                <svg class="w-12 h-12 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="font-semibold text-green-800">Pelajaran Selesai!</p>
                <p class="text-sm text-green-600 mt-1">Diselesaikan pada {{ $lesson['completed_at'] }}</p>
            </div>
        @endif

        <!-- Spacer for bottom navigation -->
        <div class="h-24"></div>
    </div>

    <style>
        /* Lesson Content Styling */
        .lesson-content {
            color: #374151;
            line-height: 1.8;
        }

        .lesson-content h1,
        .lesson-content h2,
        .lesson-content h3 {
            font-weight: 700;
            margin-top: 1.5em;
            margin-bottom: 0.75em;
            color: #1f2937;
        }

        .lesson-content h1 {
            font-size: 1.75rem;
        }

        .lesson-content h2 {
            font-size: 1.5rem;
        }

        .lesson-content h3 {
            font-size: 1.25rem;
        }

        .lesson-content p {
            margin-bottom: 1em;
            text-align: justify;
        }

        .lesson-content ul,
        .lesson-content ol {
            margin-left: 1.5em;
            margin-bottom: 1em;
        }

        .lesson-content li {
            margin-bottom: 0.5em;
        }

        .lesson-content hr {
            margin: 2em 0;
            border: 0;
            border-top: 2px solid #e5e7eb;
        }

        .lesson-content b,
        .lesson-content strong {
            font-weight: 600;
            color: #1f2937;
        }

        .lesson-content i,
        .lesson-content em {
            font-style: italic;
        }

        .lesson-content a {
            color: #3b82f6;
            text-decoration: underline;
        }

        .lesson-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.75rem;
            margin: 1.5em 0;
        }

        .lesson-content .video-container {
            position: relative;
            padding-bottom: 56.25%;
            /* 16:9 aspect ratio */
            height: 0;
            overflow: hidden;
            margin: 1.5em 0;
            border-radius: 0.75rem;
        }

        .lesson-content .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .lesson-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5em 0;
        }

        .lesson-content table td,
        .lesson-content table th {
            border: 1px solid #e5e7eb;
            padding: 0.75em;
        }

        .lesson-content table th {
            background-color: #f3f4f6;
            font-weight: 600;
        }
    </style>
@endsection
