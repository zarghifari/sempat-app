@extends('layouts.app', ['showBack' => true])

@section('title', $lesson['title'])

@push('styles')
<style>
    .tracker-status {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 4px;
    }
    .tracker-active { background-color: #22c55e; animation: pulse 2s infinite; }
    .tracker-idle { background-color: #eab308; }
    .tracker-paused { background-color: #94a3b8; }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
</style>
@endpush

@section('content')
    <div class="px-4">

        <!-- Lesson Progress Bar & Study Timer (if enrolled) -->
        @if ($lesson['is_enrolled'])
            <div class="bg-white rounded-xl p-4 mb-4 shadow-sm">
                <div class="flex justify-between items-center text-sm mb-2">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-700">Progress Anda</span>
                        <span class="font-bold text-blue-600">{{ $lesson['completion_percentage'] }}%</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <span id="tracker-status" class="tracker-status tracker-active" title="Tracking active"></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="study-timer" class="font-mono font-semibold">00:00</span>
                    </div>
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

    <!-- Floating Video Info Button -->
    <div id="floating-video-btn" class="fixed bottom-20 right-4 z-40 hidden animate-slide-up">
        <button onclick="scrollToActiveVideo()" 
                class="bg-gradient-to-r from-red-600 to-red-700 text-white rounded-full shadow-2xl px-4 py-3 flex items-center gap-3 hover:shadow-xl transition-all active:scale-95">
            <div class="w-10 h-10 bg-red-800/50 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/>
                </svg>
            </div>
            <div class="text-left pr-2">
                <div id="floating-video-title" class="text-xs font-semibold line-clamp-1 max-w-[150px]">Video</div>
                <div id="floating-video-time" class="text-xs opacity-90 font-mono">0:00 / 0:00</div>
            </div>
        </button>
    </div>

    <style>
        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-slide-up {
            animation: slide-up 0.3s ease-out;
        }
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection

@if ($lesson['is_enrolled'])
@push('scripts')
    <script>
        // ============================================================
        // VIDEO CONTROLS - Auto-pause other videos & Floating button
        // ============================================================
        let activeVideo = null;
        let activeVideoTitle = '';
        let videoUpdateInterval = null;
        let youtubePlayers = {};
        
        // YouTube Player States (fallback if YT not loaded)
        const YTPlayerState = {
            UNSTARTED: -1,
            ENDED: 0,
            PLAYING: 1,
            PAUSED: 2,
            BUFFERING: 3,
            CUED: 5
        };
        
        // Load YouTube iframe API
        function loadYouTubeAPI() {
            if (window.YT) {
                console.log('[Video Controls] YouTube API already loaded');
                return;
            }
            
            console.log('[Video Controls] Loading YouTube API...');
            const tag = document.createElement('script');
            tag.src = 'https://www.youtube.com/iframe_api';
            const firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        }
        
        // YouTube API ready callback
        window.onYouTubeIframeAPIReady = function() {
            console.log('[Video Controls] ✅ YouTube API ready');
            initYouTubePlayers();
        };
        
        function initYouTubePlayers() {
            const youtubeIframes = document.querySelectorAll('.lesson-content iframe[src*="youtube.com/embed"]');
            console.log('[Video Controls] Initializing', youtubeIframes.length, 'YouTube players');
            
            youtubeIframes.forEach((iframe, index) => {
                // Ensure iframe has ID
                if (!iframe.id) {
                    iframe.id = 'youtube-player-' + index;
                }
                
                // Get title
                let title = 'YouTube Video ' + (index + 1);
                const container = iframe.closest('figure, div, .video-container');
                if (container) {
                    const caption = container.querySelector('figcaption, .caption, p');
                    if (caption && caption.textContent.trim()) {
                        title = caption.textContent.trim();
                    }
                }
                iframe.dataset.videoTitle = title;
                iframe.dataset.videoIndex = index;
                
                // Enable YouTube API
                const src = iframe.src;
                if (!src.includes('enablejsapi=1')) {
                    iframe.src = src + (src.includes('?') ? '&' : '?') + 'enablejsapi=1';
                }
                
                // Create player
                try {
                    const player = new YT.Player(iframe.id, {
                        events: {
                            'onStateChange': function(event) {
                                onYouTubePlayerStateChange(event, iframe, index, title);
                            }
                        }
                    });
                    youtubePlayers[iframe.id] = player;
                    console.log('[Video Controls] ✅ YouTube player created:', iframe.id, '-', title);
                } catch(e) {
                    console.error('[Video Controls] ❌ Failed to init YouTube player:', e);
                }
            });
        }
        
        function onYouTubePlayerStateChange(event, iframe, index, title) {
            const playerState = window.YT ? YT.PlayerState : YTPlayerState;
            
            console.log('[Video Controls] YouTube state change:', event.data, 'for', title);
            
            if (event.data === playerState.PLAYING) {
                console.log('[Video Controls] 🎬 YouTube playing:', title);
                pauseOtherVideos(iframe);
                setActiveYouTubeVideo(iframe, title, youtubePlayers[iframe.id]);
            } else if (event.data === playerState.PAUSED || event.data === playerState.ENDED) {
                console.log('[Video Controls] ⏸️ YouTube paused/ended:', title);
                if (activeVideo === iframe) {
                    hideFloatingButton();
                    activeVideo = null;
                }
            }
        }
        
        function setActiveYouTubeVideo(iframe, title, player) {
            activeVideo = iframe;
            activeVideoTitle = title;
            
            console.log('[Video Controls] 🎯 Active YouTube video:', title);
            document.getElementById('floating-video-title').textContent = title;
            
            // Start updating time
            if (videoUpdateInterval) {
                clearInterval(videoUpdateInterval);
            }
            
            videoUpdateInterval = setInterval(() => {
                if (activeVideo && player) {
                    try {
                        // Check if player methods are available
                        if (typeof player.getCurrentTime === 'function' && typeof player.getDuration === 'function') {
                            const current = player.getCurrentTime();
                            const duration = player.getDuration();
                            
                            console.log('[Video Controls] ⏱️ YouTube time:', current, '/', duration);
                            
                            if (!isNaN(current) && !isNaN(duration) && current >= 0 && duration > 0) {
                                const currentFormatted = formatVideoTime(current);
                                const durationFormatted = formatVideoTime(duration);
                                const timeText = currentFormatted + ' / ' + durationFormatted;
                                document.getElementById('floating-video-time').textContent = timeText;
                                console.log('[Video Controls] ⏱️ Time updated:', timeText);
                            } else {
                                console.log('[Video Controls] ⚠️ Invalid time values:', current, duration);
                            }
                        } else {
                            console.log('[Video Controls] ⚠️ Player methods not available');
                        }
                    } catch(e) {
                        console.error('[Video Controls] ❌ Error updating time:', e);
                    }
                }
            }, 1000);
            
            // Check visibility immediately and start checking
            setTimeout(() => {
                checkVideoVisibility();
                // Force first time update
                if (player && typeof player.getCurrentTime === 'function') {
                    try {
                        const current = player.getCurrentTime();
                        const duration = player.getDuration();
                        if (!isNaN(current) && !isNaN(duration)) {
                            const timeText = formatVideoTime(current) + ' / ' + formatVideoTime(duration);
                            document.getElementById('floating-video-time').textContent = timeText;
                        }
                    } catch(e) {
                        console.error('[Video Controls] Error in initial time update:', e);
                    }
                }
            }, 500);
        }
        
        function initVideoControls() {
            const videos = document.querySelectorAll('.lesson-content video, .lesson-content iframe[src*="youtube"], .lesson-content iframe[src*="vimeo"]');
            console.log('[Video Controls] Found', videos.length, 'videos');
            
            // Check if YouTube videos exist
            const hasYouTube = document.querySelector('.lesson-content iframe[src*="youtube.com/embed"]');
            if (hasYouTube) {
                loadYouTubeAPI();
            }
            
            videos.forEach((video, index) => {
                // Add data attribute for tracking
                video.dataset.videoIndex = index;
                
                // Get video title from nearby elements
                let title = 'Video ' + (index + 1);
                const parent = video.closest('figure, div, p');
                if (parent) {
                    const caption = parent.querySelector('figcaption, .caption, p');
                    if (caption && caption.textContent.trim()) {
                        title = caption.textContent.trim();
                    }
                }
                video.dataset.videoTitle = title;
                
                if (video.tagName === 'VIDEO') {
                    // Native HTML5 video
                    video.addEventListener('play', function() {
                        console.log('[Video Controls] 🎬 HTML5 video playing:', title);
                        pauseOtherVideos(this);
                        setActiveVideo(this, title);
                    });
                    
                    video.addEventListener('pause', function() {
                        console.log('[Video Controls] ⏸️ HTML5 video paused:', title);
                        if (activeVideo === this) {
                            hideFloatingButton();
                            activeVideo = null;
                        }
                    });
                    
                    video.addEventListener('ended', function() {
                        console.log('[Video Controls] ⏹️ HTML5 video ended:', title);
                        if (activeVideo === this) {
                            hideFloatingButton();
                            activeVideo = null;
                        }
                    });
                }
            });
            
            console.log('[Video Controls] ✅ Video controls initialized');
        }
        
        function setActiveVideo(video, title) {
            activeVideo = video;
            activeVideoTitle = title;
            
            console.log('[Video Controls] 🎯 Active HTML5 video:', title);
            document.getElementById('floating-video-title').textContent = title;
            
            // Start updating time
            if (videoUpdateInterval) {
                clearInterval(videoUpdateInterval);
            }
            
            videoUpdateInterval = setInterval(() => {
                if (activeVideo && activeVideo.tagName === 'VIDEO' && !activeVideo.paused) {
                    const current = formatVideoTime(activeVideo.currentTime);
                    const duration = formatVideoTime(activeVideo.duration);
                    document.getElementById('floating-video-time').textContent = current + ' / ' + duration;
                }
            }, 1000);
            
            // Check visibility immediately
            setTimeout(() => checkVideoVisibility(), 100);
        }
        
        function pauseOtherVideos(currentVideo) {
            console.log('[Video Controls] 🔇 Pausing other videos, current:', currentVideo.id || currentVideo.dataset?.videoIndex);
            
            // Pause HTML5 videos
            const videos = document.querySelectorAll('.lesson-content video');
            videos.forEach(video => {
                if (video !== currentVideo && !video.paused) {
                    video.pause();
                    console.log('[Video Controls] ⏸️ Paused HTML5 video', video.dataset.videoIndex);
                }
            });
            
            // Pause YouTube videos
            const playerState = window.YT ? YT.PlayerState : YTPlayerState;
            Object.keys(youtubePlayers).forEach(playerId => {
                const player = youtubePlayers[playerId];
                const iframe = document.getElementById(playerId);
                if (iframe !== currentVideo) {
                    try {
                        if (player.getPlayerState && player.getPlayerState() === playerState.PLAYING) {
                            player.pauseVideo();
                            console.log('[Video Controls] ⏸️ Paused YouTube video', playerId);
                        }
                    } catch(e) {
                        console.error('[Video Controls] Error pausing YouTube:', e);
                    }
                }
            });
        }
        
        function setActiveVideo(video, title) {
            activeVideo = video;
            activeVideoTitle = title;
            
            console.log('[Video Controls] Active video:', title);
            
            // Update floating button info
            document.getElementById('floating-video-title').textContent = title;
            
            // Start updating time
            if (videoUpdateInterval) {
                clearInterval(videoUpdateInterval);
            }
            
            videoUpdateInterval = setInterval(() => {
                if (activeVideo && !activeVideo.paused) {
                    const current = formatVideoTime(activeVideo.currentTime);
                    const duration = formatVideoTime(activeVideo.duration);
                    document.getElementById('floating-video-time').textContent = current + ' / ' + duration;
                }
            }, 1000);
            
            // Check visibility immediately
            checkVideoVisibility();
        }
        
        function formatVideoTime(seconds) {
            if (isNaN(seconds)) return '0:00';
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return mins + ':' + (secs < 10 ? '0' : '') + secs;
        }
        
        function checkVideoVisibility() {
            if (!activeVideo) {
                hideFloatingButton();
                return;
            }
            
            // Check if video is in viewport
            const rect = activeVideo.getBoundingClientRect();
            const windowHeight = window.innerHeight || document.documentElement.clientHeight;
            const isVisible = (
                rect.top >= 0 &&
                rect.top < windowHeight &&
                rect.bottom > 0 &&
                rect.bottom <= windowHeight
            );
            
            console.log('[Video Controls] 👁️ Video visible:', isVisible, 'top:', rect.top, 'bottom:', rect.bottom, 'windowHeight:', windowHeight);
            
            // Check if video is playing
            let isPlaying = false;
            const playerState = window.YT ? YT.PlayerState : YTPlayerState;
            
            if (activeVideo.tagName === 'VIDEO') {
                isPlaying = !activeVideo.paused;
            } else if (activeVideo.tagName === 'IFRAME' && youtubePlayers[activeVideo.id]) {
                try {
                    const player = youtubePlayers[activeVideo.id];
                    if (player.getPlayerState) {
                        const state = player.getPlayerState();
                        isPlaying = state === playerState.PLAYING;
                    }
                } catch(e) {
                    console.error('[Video Controls] Error checking player state:', e);
                    isPlaying = false;
                }
            }
            
            console.log('[Video Controls] 🎬 Is playing:', isPlaying);
            
            // Show floating button if video is playing but not fully visible
            if (isPlaying && !isVisible) {
                console.log('[Video Controls] 📺 Showing floating button');
                showFloatingButton();
            } else {
                console.log('[Video Controls] 🚫 Hiding floating button');
                hideFloatingButton();
            }
        }
        
        function showFloatingButton() {
            const btn = document.getElementById('floating-video-btn');
            if (btn && btn.classList.contains('hidden')) {
                btn.classList.remove('hidden');
                console.log('[Video Controls] ✅ Floating button shown');
            }
        }
        
        function hideFloatingButton() {
            const btn = document.getElementById('floating-video-btn');
            if (btn && !btn.classList.contains('hidden')) {
                btn.classList.add('hidden');
                console.log('[Video Controls] ❌ Floating button hidden');
            }
            
            if (videoUpdateInterval) {
                clearInterval(videoUpdateInterval);
                videoUpdateInterval = null;
            }
        }
        
        function scrollToActiveVideo() {
            if (!activeVideo) {
                console.log('[Video Controls] ⚠️ No active video to scroll to');
                return;
            }
            
            console.log('[Video Controls] 📍 Scrolling to active video');
            activeVideo.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            
            // Hide button after scrolling
            setTimeout(() => {
                checkVideoVisibility();
            }, 800);
        }
        
        // ============================================================
        // STUDY TIME TRACKING
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            console.log('[Video Controls] 🚀 Initializing video controls...');
            
            // Initialize video controls
            initVideoControls();
            
            // Add scroll listener with throttle
            let scrollTimeout;
            window.addEventListener('scroll', function() {
                if (scrollTimeout) {
                    clearTimeout(scrollTimeout);
                }
                scrollTimeout = setTimeout(() => {
                    checkVideoVisibility();
                }, 100);
            }, { passive: true });
            
            // Check visibility periodically for playing videos
            setInterval(() => {
                if (activeVideo) {
                    checkVideoVisibility();
                }
            }, 2000);
            // Initialize tracker for this COURSE (shared across all lessons)
            // Using course_id as resourceId so all lessons share same localStorage key
            const courseId = {{ $lesson['course_id'] }};
            const lessonId = {{ $lesson['id'] }};
            
            const tracker = new StudyTimeTracker({
                resourceType: 'course', // CHANGED: course-level timer
                resourceId: courseId, // CHANGED: Use course_id for shared timer
                actualLessonId: lessonId, // Track actual lesson for API
                apiEndpoint: '/api/lessons/' + lessonId + '/track-time',
                displayElement: document.getElementById('study-timer'),
                idleThreshold: 3 * 60 * 1000, // 3 minutes
                syncInterval: 30 * 1000, // 30 seconds
                minSyncSeconds: 5, // Minimum 5 seconds before syncing
            });

            // Load initial time from server
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            console.log('[Lesson Tracker] CSRF Token:', csrfToken ? 'Found' : 'NOT FOUND');
            console.log('[Lesson Tracker] Loading COURSE time for today...');
            
            fetch('/api/lessons/' + lessonId + '/time', {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                console.log('[Lesson Tracker] Response status:', res.status);
                if (!res.ok) {
                    throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                }
                return res.json();
            })
            .then(data => {
                console.log('[Lesson Tracker] ✓ Course time loaded:', data);
                console.log('[Lesson Tracker] Today course total:', data.today_course_time || data.total_seconds);
                console.log('[Lesson Tracker] This lesson:', data.this_lesson_time);
                
                // Use course time (all lessons in course combined today)
                const courseTime = data.today_course_time || data.total_seconds || 0;
                tracker.totalSeconds = courseTime;
                
                // Mark as initialized - now safe to display and count time
                tracker.isInitialized = true;
                
                // CRITICAL: Reset all timing variables to prevent spike from initialization delay
                tracker.lastSync = Date.now();
                tracker.startTime = Date.now();
                tracker.accumulatedTime = 0; // Reset any accumulated time during init
                tracker.totalActiveTime = 0;
                
                console.log(`[Lesson Tracker] 🎯 INITIALIZED! totalSeconds=${courseTime}, isInitialized=${tracker.isInitialized}`);
                
                // Force immediate display update
                tracker.updateDisplay();
            })
            .catch(err => console.error('Failed to load initial time:', err));

            // Cleanup on page unload
            window.addEventListener('beforeunload', () => {
                tracker.destroy();
            });

            // Listen for time updates
            window.addEventListener('study-time-updated', (event) => {
                console.log('Study time updated:', event.detail.formatted);
            });
        });
    </script>
@endpush
@endif
