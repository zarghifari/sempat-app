@extends('layouts.app')

@section('title', 'Messages')

@section('content')
    <!-- Page Header -->
    <div class="mb-6 px-4">
        <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
        <p class="text-sm text-gray-600 mt-1">Chat with teachers and classmates</p>
    </div>

    <!-- Search Bar -->
    <div class="mb-6 px-4">
        <div class="relative">
            <input type="text" 
                   placeholder="Search conversations..." 
                   class="w-full pl-10 pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    <!-- Conversations List -->
    <div class="space-y-3 pb-24 px-4">
        @foreach($conversations as $conversation)
            <a href="{{ route('messages.show', $conversation['id']) }}" 
               class="block bg-white rounded-xl p-4 shadow-sm active:scale-[0.98] transition-transform">
                <div class="flex items-start gap-4">
                    <!-- Avatar -->
                    <div class="relative flex-shrink-0">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ substr($conversation['name'], 0, 1) }}
                        </div>
                        @if($conversation['online'] === true)
                            <div class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                        @endif
                        @if(isset($conversation['is_group']) && $conversation['is_group'])
                            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-gray-600 rounded-full flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 mb-1.5">
                            <h3 class="font-semibold text-gray-900 truncate text-base">{{ $conversation['name'] }}</h3>
                            <span class="text-xs text-gray-500 flex-shrink-0">{{ $conversation['last_message_time'] }}</span>
                        </div>
                        <p class="text-sm text-gray-600 line-clamp-2 leading-relaxed">{{ $conversation['last_message'] }}</p>
                    </div>

                    <!-- Unread Badge -->
                    @if($conversation['unread_count'] > 0)
                        <div class="flex-shrink-0 self-center">
                            <div class="min-w-[1.5rem] h-6 px-2 bg-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-xs font-bold text-white">{{ $conversation['unread_count'] }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </a>
        @endforeach
    </div>

    <!-- Empty State -->
    @if(count($conversations) === 0)
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Messages Yet</h3>
            <p class="text-sm text-gray-600">Start a conversation with your teachers</p>
        </div>
    @endif

    <!-- New Message Button (Floating) -->
    <button class="fixed bottom-20 right-4 w-14 h-14 bg-gradient-to-br from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-full shadow-2xl flex items-center justify-center active:scale-90 transition-all duration-200 z-50">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
        </svg>
    </button>
@endsection
