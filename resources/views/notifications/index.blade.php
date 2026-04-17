<x-app-layout>
    <div class="absolute inset-0 top-14 bottom-16 bg-gray-50 overflow-y-auto" x-data="notificationsPage">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 sticky top-0 z-40">
            <div class="max-w-4xl mx-auto px-4 py-4">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Notifikasi</h1>
                
                <!-- Filter Tabs -->
                <div class="flex gap-2 border-b border-gray-200">
                    <a href="{{ route('notifications.index', ['filter' => 'all']) }}" 
                       class="px-4 py-2 font-medium text-sm border-b-2 transition-colors {{ $filter === 'all' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                        Semua @if($filter === 'all')({{ $notifications->total() }})@endif
                    </a>
                    <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
                       class="px-4 py-2 font-medium text-sm border-b-2 transition-colors {{ $filter === 'unread' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                        Belum Dibaca 
                        @if($unreadCount > 0)
                            <span class="ml-1 px-2 py-0.5 text-xs bg-red-500 text-white rounded-full">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('notifications.index', ['filter' => 'read']) }}" 
                       class="px-4 py-2 font-medium text-sm border-b-2 transition-colors {{ $filter === 'read' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                        Sudah Dibaca @if($filter === 'read')({{ $notifications->total() }})@endif
                    </a>
                </div>
                
                <!-- Actions -->
                @if($filter !== 'read' && $unreadCount > 0)
                <div class="mt-3">
                    <button 
                        @click="markAllAsRead"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                    >
                        Tandai semua sudah dibaca
                    </button>
                </div>
                @endif
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-w-4xl mx-auto px-4 py-6">
            @if($notifications->isEmpty())
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <div class="text-6xl mb-4">
                        @if($filter === 'unread')
                            🎉
                        @elseif($filter === 'read')
                            📭
                        @else
                            🔔
                        @endif
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        @if($filter === 'unread')
                            Tidak ada notifikasi baru
                        @elseif($filter === 'read')
                            Belum ada notifikasi yang dibaca
                        @else
                            Belum ada notifikasi
                        @endif
                    </h3>
                    <p class="text-gray-600">
                        @if($filter === 'unread')
                            Kamu sudah membaca semua notifikasi
                        @else
                            Notifikasi belajarmu akan muncul di sini
                        @endif
                    </p>
                </div>
            @else
                <!-- Notifications Grid -->
                <div class="space-y-2">
                    @foreach($notifications as $notification)
                        <div 
                            @click="handleNotificationClick('{{ $notification->id }}', '{{ $notification->data['url'] ?? '' }}', {{ $notification->read_at ? 'true' : 'false' }})"
                            class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow cursor-pointer {{ $notification->read_at ? '' : 'border-l-4 border-blue-500 bg-blue-50/30' }}"
                        >
                            <div class="flex items-start gap-4">
                                <!-- Icon -->
                                <div class="text-3xl flex-shrink-0">
                                    {{ $notification->data['icon'] ?? '📬' }}
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-3 mb-1">
                                        <h3 class="font-semibold text-gray-900 {{ $notification->read_at ? '' : 'text-blue-900' }}">
                                            {{ $notification->data['title'] ?? 'Notification' }}
                                        </h3>
                                        @if(!$notification->read_at)
                                            <span class="w-2 h-2 bg-blue-600 rounded-full flex-shrink-0 mt-1.5"></span>
                                        @endif
                                    </div>
                                    
                                    <p class="text-gray-700 text-sm mb-2">
                                        {{ $notification->data['message'] ?? '' }}
                                    </p>
                                    
                                    <div class="flex items-center gap-4 text-xs text-gray-500">
                                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                                        @if($notification->read_at)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Dibaca {{ $notification->read_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex-shrink-0" @click.stop>
                                    <button 
                                        @click="deleteNotification('{{ $notification->id }}')"
                                        class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                                        title="Hapus notifikasi"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($notifications->hasPages())
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
                @endif
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('notificationsPage', () => ({
                async handleNotificationClick(notificationId, url, isRead) {
                    // Mark as read first
                    if (!isRead) {
                        try {
                            await fetch(`/api/notifications/${notificationId}/read`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                        } catch (error) {
                            console.error('Error marking notification as read:', error);
                        }
                    }
                    
                    // Navigate to URL
                    if (url) {
                        window.location.href = url;
                    } else {
                        // Reload to update read status
                        window.location.reload();
                    }
                },

                async markAllAsRead() {
                    if (!confirm('Tandai semua notifikasi sebagai sudah dibaca?')) {
                        return;
                    }

                    try {
                        const response = await fetch('/api/notifications/read-all', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Error marking all as read:', error);
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                },

                async deleteNotification(notificationId) {
                    if (!confirm('Hapus notifikasi ini?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`/api/notifications/${notificationId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Error deleting notification:', error);
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                }
            }));
        });
    </script>
</x-app-layout>
