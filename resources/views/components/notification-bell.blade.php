<!-- Notification Bell Component -->
<div x-data="notificationBell" class="relative">
    <!-- Bell Button -->
    <button 
        @click="toggleDropdown" 
        class="p-2 rounded-lg active:bg-gray-100 relative text-gray-700 transition-colors hover:bg-gray-50"
        :class="{ 'bg-gray-100': open }"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        
        <!-- Badge -->
        <span 
            x-show="unreadCount > 0" 
            x-text="unreadCount > 99 ? '99+' : unreadCount"
            class="absolute top-1 right-1 min-w-[18px] h-[18px] bg-red-500 text-white rounded-full text-[10px] font-bold flex items-center justify-center px-1"
            x-transition
        ></span>
    </button>

    <!-- Dropdown Panel -->
    <div 
        x-show="open" 
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden z-50"
        style="display: none;"
    >
        <!-- Header -->
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Notifikasi</h3>
            <div class="flex items-center gap-2">
                <!-- Refresh Button -->
                <button 
                    @click="fetchNotifications(); fetchUnreadCount()"
                    class="text-xs text-gray-600 hover:text-gray-900 font-medium p-1 rounded hover:bg-gray-100 transition-colors"
                    title="Refresh notifikasi"
                >
                    <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
                <!-- Mark All as Read -->
                <button 
                    @click="markAllAsRead"
                    x-show="unreadCount > 0"
                    class="text-xs text-blue-600 hover:text-blue-700 font-medium"
                >
                    Tandai semua sudah dibaca
                </button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            <template x-if="loading">
                <div class="p-8 text-center">
                    <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Memuat notifikasi...</p>
                </div>
            </template>

            <template x-if="!loading && notifications.length === 0">
                <div class="p-8 text-center">
                    <div class="text-5xl mb-3">🔔</div>
                    <p class="text-gray-500 text-sm">Tidak ada notifikasi</p>
                </div>
            </template>

            <template x-if="!loading && notifications.length > 0">
                <div>
                    <template x-for="notification in notifications" :key="notification.id">
                        <div 
                            @click="handleNotificationClick(notification)"
                            class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors"
                            :class="{ 'bg-blue-50': !notification.read_at }"
                        >
                            <div class="flex items-start gap-3">
                                <!-- Icon -->
                                <div class="text-2xl flex-shrink-0" x-text="notification.data.icon || '📬'"></div>
                                
                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 mb-0.5" x-text="notification.data.title"></p>
                                    <p class="text-xs text-gray-600 line-clamp-2" x-text="notification.data.message"></p>
                                    <p class="text-xs text-gray-400 mt-1" x-text="formatTime(notification.created_at)"></p>
                                </div>
                                
                                <!-- Unread Indicator -->
                                <div x-show="!notification.read_at" class="flex-shrink-0">
                                    <span class="w-2 h-2 bg-blue-600 rounded-full block"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 text-center">
            <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                Lihat Semua Notifikasi
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('notificationBell', () => ({
        open: false,
        loading: false,
        notifications: [],
        unreadCount: 0,
        refreshInterval: null,

        init() {
            console.log('🔔 Notification bell initializing...');
            this.fetchNotifications();
            this.fetchUnreadCount();
            
            // Auto-refresh unread count every 10 seconds for real-time updates
            this.refreshInterval = setInterval(() => {
                this.fetchUnreadCount();
                // Also refresh notifications if dropdown is open
                if (this.open) {
                    this.fetchNotifications();
                }
            }, 10000); // 10 seconds
            
            console.log('✅ Notification bell initialized!');
        },

        destroy() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
            }
        },

        toggleDropdown() {
            this.open = !this.open;
            // Always fetch fresh notifications when opening dropdown
            if (this.open) {
                this.fetchNotifications();
            }
        },

        async fetchNotifications() {
            this.loading = true;
            try {
                const response = await fetch('/api/notifications/recent?limit=5');
                const data = await response.json();
                
                if (data.success) {
                    this.notifications = data.data;
                    this.unreadCount = data.unread_count;
                }
            } catch (error) {
                console.error('Error fetching notifications:', error);
            } finally {
                this.loading = false;
            }
        },

        async fetchUnreadCount() {
            try {
                const response = await fetch('/api/notifications/unread-count');
                const data = await response.json();
                
                console.log('📊 Unread count response:', data);
                
                if (data.success) {
                    this.unreadCount = data.count;
                    console.log('✅ Badge updated:', this.unreadCount);
                }
            } catch (error) {
                console.error('❌ Error fetching unread count:', error);
            }
        },

        async markAllAsRead() {
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
                    this.notifications = this.notifications.map(n => ({
                        ...n,
                        read_at: new Date().toISOString()
                    }));
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        },

        async handleNotificationClick(notification) {
            // Mark as read if unread
            if (!notification.read_at) {
                try {
                    await fetch(`/api/notifications/${notification.id}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    notification.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                }
            }
            
            // Navigate to URL
            if (notification.data.url) {
                window.location.href = notification.data.url;
            }
        },

        formatTime(timestamp) {
            const now = new Date();
            const notifTime = new Date(timestamp);
            const diff = Math.floor((now - notifTime) / 1000); // seconds

            if (diff < 60) return 'Baru saja';
            if (diff < 3600) return Math.floor(diff / 60) + ' menit yang lalu';
            if (diff < 86400) return Math.floor(diff / 3600) + ' jam yang lalu';
            if (diff < 604800) return Math.floor(diff / 86400) + ' hari yang lalu';
            
            return notifTime.toLocaleDateString('id-ID', { 
                day: 'numeric', 
                month: 'short' 
            });
        }
    }));
});
</script>
