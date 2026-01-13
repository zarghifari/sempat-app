@extends('layouts.app', ['showBack' => true])

@section('title', $article['title'])

@section('content')
<div class="px-4">
    <!-- Article Header -->
    <div class="bg-gradient-to-br from-{{ $article['category_color'] }}-50 to-white rounded-2xl p-6 mb-6">
        <div class="flex items-center gap-2 mb-3">
            <span class="px-3 py-1 bg-white rounded-full text-sm">
                {{ $article['category_icon'] }} {{ $article['category'] }}
            </span>
            <span class="px-3 py-1 bg-white rounded-full text-sm 
                {{ $article['difficulty'] === 'Beginner' ? 'text-green-700' : '' }}
                {{ $article['difficulty'] === 'Intermediate' ? 'text-yellow-700' : '' }}
                {{ $article['difficulty'] === 'Advanced' ? 'text-red-700' : '' }}">
                {{ $article['difficulty'] }}
            </span>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ $article['title'] }}</h1>
        
        <p class="text-gray-600 mb-4">{{ $article['excerpt'] }}</p>
        
        <!-- Meta Info -->
        <div class="flex items-center gap-4 text-sm text-gray-600">
            <span>👤 {{ $article['author'] }}</span>
            <span>•</span>
            <span>📅 {{ $article['published_at'] }}</span>
            <span>•</span>
            <span>⏱️ {{ $article['reading_time'] }} menit</span>
        </div>
    </div>

    <!-- Action Buttons & Stats -->
    <div class="bg-white rounded-2xl p-4 mb-6 shadow-sm">
        <!-- Stats Bar -->
        <div class="flex items-center justify-around mb-4 pb-4 border-b border-gray-100">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($article['views']) }}</div>
                <div class="text-xs text-gray-500 mt-1">Views</div>
            </div>
            <div class="w-px h-10 bg-gray-200"></div>
            <div class="text-center">
                <div id="likesCount" class="text-2xl font-bold text-red-600">{{ number_format($article['likes']) }}</div>
                <div class="text-xs text-gray-500 mt-1">Likes</div>
            </div>
            <div class="w-px h-10 bg-gray-200"></div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ number_format($article['comments_count']) }}</div>
                <div class="text-xs text-gray-500 mt-1">Komentar</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2">
            <button type="button" 
                    onclick="toggleLike()"
                    id="likeButton"
                    class="flex-1 py-3 rounded-xl font-medium transition flex items-center justify-center gap-2
                           {{ $article['is_liked'] 
                              ? 'bg-red-50 text-red-600 border-2 border-red-200' 
                              : 'bg-gray-50 text-gray-600 border-2 border-gray-200 hover:border-red-200 hover:text-red-600' }}">
                <svg id="likeIcon" class="w-5 h-5 {{ $article['is_liked'] ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <span id="likeText" class="text-sm">{{ $article['is_liked'] ? 'Disukai' : 'Suka' }}</span>
            </button>

            <form action="{{ route('articles.bookmark', $article['id']) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" 
                        class="w-full py-3 rounded-xl font-medium transition flex items-center justify-center gap-2
                               {{ $article['is_bookmarked'] 
                                  ? 'bg-yellow-50 text-yellow-700 border-2 border-yellow-200' 
                                  : 'bg-gray-50 text-gray-600 border-2 border-gray-200 hover:border-yellow-200 hover:text-yellow-600' }}">
                    <svg class="w-5 h-5 {{ $article['is_bookmarked'] ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                    <span class="text-sm">{{ $article['is_bookmarked'] ? 'Tersimpan' : 'Simpan' }}</span>
                </button>
            </form>
            
            <button onclick="shareArticle()" 
                    class="px-4 py-3 bg-gray-50 text-gray-600 border-2 border-gray-200 rounded-xl font-medium transition hover:border-blue-200 hover:text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Article Content -->
    <div class="bg-white rounded-2xl p-6 mb-6 shadow-sm">
        <div class="prose prose-sm max-w-none">
            {!! $article['content'] !!}
        </div>
    </div>

    <!-- Tags -->
    @if(count($article['tags']) > 0)
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Tags:</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($article['tags'] as $tag)
                    <span class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-full">
                        {{ $tag['name'] }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Comments Section -->
    @if($article['allow_comments'])
        <div class="mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">
                💬 Komentar ({{ $article['comments_count'] }})
            </h2>

            <!-- Comment Form -->
            <div class="bg-white rounded-2xl p-4 mb-4 shadow-sm">
                <form action="{{ route('articles.comments.store', $article['id']) }}" method="POST" id="commentForm">
                    @csrf
                    <textarea name="content" 
                              rows="3" 
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 resize-none"
                              placeholder="Tulis komentar..." 
                              id="commentText"></textarea>
                    
                    <input type="hidden" name="sticker_id" id="stickerInput">
                    
                    <div class="flex items-center justify-between mt-3">
                        <button type="button" 
                                onclick="toggleStickerPicker()"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium active:bg-gray-200 transition">
                            😀 Stiker
                        </button>
                        
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium active:bg-blue-700 transition">
                            Kirim
                        </button>
                    </div>
                </form>

                <!-- Sticker Picker -->
                <div id="stickerPicker" class="hidden mt-4 p-4 bg-gray-50 rounded-xl">
                    @foreach($stickers as $category => $categoryStickers)
                        <div class="mb-4 last:mb-0">
                            <h4 class="text-xs font-semibold text-gray-600 uppercase mb-2">{{ ucfirst($category) }}</h4>
                            <div class="grid grid-cols-6 gap-2">
                                @foreach($categoryStickers as $sticker)
                                    <button type="button" 
                                            onclick="selectSticker({{ $sticker->id }}, '{{ $sticker->image_url }}')"
                                            class="aspect-square bg-white rounded-lg p-2 active:scale-95 transition hover:bg-gray-100"
                                            data-sticker-url="{{ $sticker->image_url }}">
                                        <img data-src="{{ $sticker->image_url }}" 
                                             alt="{{ $sticker->name }}" 
                                             class="w-full h-full object-contain lazy-sticker"
                                             title="{{ $sticker->name }}">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Comments List -->
            <div class="space-y-4">
                @forelse($articleModel->comments as $comment)
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <!-- Comment Header -->
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($comment->user->first_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 text-sm">{{ $comment->user->first_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            
                            @if($comment->canDelete(auth()->user()))
                                <form action="{{ route('comments.delete', $comment->id) }}" method="POST" 
                                      onsubmit="return confirm('Hapus komentar ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 text-sm">Hapus</button>
                                </form>
                            @endif
                        </div>

                        <!-- Comment Content -->
                        <div class="mb-2">
                            @if($comment->sticker)
                                <img src="{{ $comment->sticker->image_url }}" 
                                     alt="{{ $comment->sticker->name }}" 
                                     class="w-20 h-20 object-contain mb-2">
                            @endif
                            
                            @if($comment->content)
                                <p class="text-gray-700">{{ $comment->content }}</p>
                                @if($comment->is_edited)
                                    <span class="text-xs text-gray-400 italic">(diedit)</span>
                                @endif
                            @endif
                        </div>

                        <!-- Replies -->
                        @if($comment->replies->count() > 0)
                            <div class="ml-8 mt-3 space-y-3 border-l-2 border-gray-200 pl-4">
                                @foreach($comment->replies as $reply)
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 bg-gradient-to-br from-green-500 to-teal-500 rounded-full flex items-center justify-center text-white font-bold text-xs">
                                                    {{ strtoupper(substr($reply->user->first_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-900 text-xs">{{ $reply->user->first_name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                            
                                            @if($reply->canDelete(auth()->user()))
                                                <form action="{{ route('comments.delete', $reply->id) }}" method="POST" 
                                                      onsubmit="return confirm('Hapus balasan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 text-xs">Hapus</button>
                                                </form>
                                            @endif
                                        </div>

                                        @if($reply->sticker)
                                            <img src="{{ $reply->sticker->image_url }}" 
                                                 alt="{{ $reply->sticker->name }}" 
                                                 class="w-16 h-16 object-contain mb-2">
                                        @endif
                                        
                                        @if($reply->content)
                                            <p class="text-gray-700 text-sm">{{ $reply->content }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-gray-50 rounded-xl p-6 text-center">
                        <p class="text-gray-500">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- Related Articles -->
    @if($relatedArticles->count() > 0)
        <div class="mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">📖 Artikel Terkait</h2>
            <div class="space-y-3">
                @foreach($relatedArticles as $related)
                    <a href="{{ route('articles.show', $related['id']) }}" 
                       class="block bg-white rounded-xl p-4 shadow-sm active:scale-98 transition">
                        <h3 class="font-bold text-gray-900 mb-1 line-clamp-2">{{ $related['title'] }}</h3>
                        <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ $related['excerpt'] }}</p>
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <span>⏱️ {{ $related['reading_time'] }} menit</span>
                            <span>•</span>
                            <span>👁️ {{ $related['views'] }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Floating Action: Add to Learning Goal -->
    <button onclick="alert('Fitur Learning Goal akan datang!')" 
            class="fixed bottom-20 right-4 w-14 h-14 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-full shadow-lg flex items-center justify-center active:scale-95 transition z-30">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
        </svg>
    </button>
</div>

<script>
// Global variables
let selectedSticker = null;
let isLiked = {{ $article['is_liked'] ? 'true' : 'false' }};
let likesCount = {{ $article['likes'] }};
let stickersLoaded = false;

// Toggle Like Function
function toggleLike() {
    const button = document.getElementById('likeButton');
    const icon = document.getElementById('likeIcon');
    const text = document.getElementById('likeText');
    const likesDisplay = document.getElementById('likesCount');
    
    // Disable button during request
    button.disabled = true;
    button.style.opacity = '0.6';
    
    fetch('{{ route('articles.like', $article['id']) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            isLiked = data.liked;
            likesCount = data.likes_count;
            
            // Update button appearance
            if (isLiked) {
                button.className = 'flex-1 py-3 rounded-xl font-medium transition flex items-center justify-center gap-2 bg-red-50 text-red-600 border-2 border-red-200';
                icon.classList.add('fill-current');
                text.textContent = 'Disukai';
            } else {
                button.className = 'flex-1 py-3 rounded-xl font-medium transition flex items-center justify-center gap-2 bg-gray-50 text-gray-600 border-2 border-gray-200 hover:border-red-200 hover:text-red-600';
                icon.classList.remove('fill-current');
                text.textContent = 'Suka';
            }
            
            // Update likes count in stats bar
            likesDisplay.textContent = new Intl.NumberFormat('id-ID').format(likesCount);
            
            // Show toast notification
            showToast(data.message, 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Gagal memperbarui like', 'error');
    })
    .finally(() => {
        button.disabled = false;
        button.style.opacity = '1';
    });
}

// Sticker Picker Functions
function toggleStickerPicker() {
    const picker = document.getElementById('stickerPicker');
    picker.classList.toggle('hidden');
    
    // Lazy load stickers only when picker is opened for the first time
    if (!stickersLoaded && !picker.classList.contains('hidden')) {
        loadStickers();
        stickersLoaded = true;
    }
}

function loadStickers() {
    const lazyStickers = document.querySelectorAll('.lazy-sticker');
    lazyStickers.forEach(img => {
        const src = img.getAttribute('data-src');
        if (src) {
            img.src = src;
            img.removeAttribute('data-src');
        }
    });
}

function selectSticker(stickerId, stickerUrl) {
    selectedSticker = stickerId;
    document.getElementById('stickerInput').value = stickerId;
    
    // Show selected sticker preview
    const preview = document.getElementById('stickerPreview');
    if (!preview) {
        const previewDiv = document.createElement('div');
        previewDiv.id = 'stickerPreview';
        previewDiv.className = 'mt-2 p-2 bg-white rounded-lg border border-blue-300 inline-flex items-center gap-2';
        previewDiv.innerHTML = `
            <img src="${stickerUrl}" class="w-12 h-12 object-contain">
            <button type="button" onclick="clearSticker()" class="text-red-500 text-sm font-medium">Hapus</button>
        `;
        document.getElementById('commentForm').insertBefore(previewDiv, document.querySelector('#commentForm .flex.items-center'));
    } else {
        preview.querySelector('img').src = stickerUrl;
    }
    
    // Update placeholder
    document.getElementById('commentText').placeholder = 'Tambahkan komentar (opsional)...';
    
    // Hide picker after selection
    document.getElementById('stickerPicker').classList.add('hidden');
}

function clearSticker() {
    selectedSticker = null;
    document.getElementById('stickerInput').value = '';
    const preview = document.getElementById('stickerPreview');
    if (preview) preview.remove();
    document.getElementById('commentText').placeholder = 'Tulis komentar...';
}

// Share Function
function shareArticle() {
    if (navigator.share) {
        navigator.share({
            title: '{{ addslashes($article['title']) }}',
            text: '{{ addslashes($article['excerpt']) }}',
            url: window.location.href
        }).catch(() => {});
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href);
        showToast('Link artikel disalin ke clipboard!', 'success');
    }
}

// Toast Notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 left-1/2 transform -translate-x-1/2 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Show success/error messages on page load
@if(session('success'))
    setTimeout(() => {
        showToast('{{ session('success') }}', 'success');
    }, 100);
@endif

@if(session('error'))
    setTimeout(() => {
        showToast('{{ session('error') }}', 'error');
    }, 100);
@endif
</script>
@endsection
