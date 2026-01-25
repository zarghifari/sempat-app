/**
 * Active Study Time Tracker
 * 
 * Tracks actual study time by monitoring:
 * - Tab visibility (page focus)
 * - User activity (mouse, keyboard, scroll)
 * - Window focus state
 * 
 * Only counts time when user is actively engaged.
 */

class StudyTimeTracker {
    constructor(config = {}) {
        // Configuration
        this.resourceType = config.resourceType; // 'lesson' or 'learning-goal'
        this.resourceId = config.resourceId;
        this.apiEndpoint = config.apiEndpoint;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        // Tracking state
        this.startTime = Date.now();
        this.totalActiveTime = 0;
        this.lastSync = Date.now();
        this.isTracking = true;
        this.isIdle = false;
        this.lastActivity = Date.now();
        this.isInitialized = false; // Flag to prevent display spike before server data loads
        this.isOnline = navigator.onLine; // Track network status
        this.retryCount = 0; // Track failed sync attempts for exponential backoff
        this.lastDisplayUpdate = 0; // For throttling display updates
        this.pauseStartTime = null; // Track when paused to calculate elapsed time on resume
        this.accumulatedTime = 0; // Accumulated time while tab was hidden
        
        // Configuration options
        this.IDLE_THRESHOLD = config.idleThreshold || 3 * 60 * 1000; // 3 minutes
        this.SYNC_INTERVAL = config.syncInterval || 60 * 1000; // 60 seconds (reduced DB writes)
        this.IDLE_CHECK_INTERVAL = config.idleCheckInterval || 15 * 1000; // 15 seconds (reduced checks)
        this.MIN_SYNC_SECONDS = config.minSyncSeconds || 10; // Minimum 10 seconds before syncing
        this.MAX_RETRY_DELAY = 5 * 60 * 1000; // Max 5 minutes between retries
        
        // Display element
        if (config.displayElement) {
            this.displayElement = typeof config.displayElement === 'string' 
                ? document.getElementById(config.displayElement)
                : config.displayElement;
        } else {
            this.displayElement = null;
        }
        
        // Total seconds (can be set from server)
        this.totalSeconds = 0;
        
        // Timers
        this.idleCheckTimer = null;
        this.syncTimer = null;
        
        // Initialize
        this.init();
    }
    
    /**
     * Initialize all event listeners and timers
     */
    init() {
        console.log(`[StudyTracker] Initialized for ${this.resourceType} #${this.resourceId}`);
        
        // 1. Track tab visibility (most important)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pause('Tab tidak aktif');
            } else {
                this.resume('Tab aktif kembali');
            }
        });
        
        // 2. Track network status (don't sync when offline)
        window.addEventListener('online', () => {
            console.log('[StudyTracker] 🌐 Back online');
            this.isOnline = true;
            this.retryCount = 0; // Reset retry counter
            // Try to sync any accumulated time
            if (this.getActiveSeconds() >= this.MIN_SYNC_SECONDS) {
                this.sync(true);
            }
        });
        
        window.addEventListener('offline', () => {
            console.log('[StudyTracker] 📡 Offline - syncing paused');
            this.isOnline = false;
        });
        
        // 3. Track window focus
        window.addEventListener('blur', () => {
            this.pause('Browser kehilangan focus');
        });
        
        window.addEventListener('focus', () => {
            this.resume('Browser mendapat focus');
        });
        
        // 4. Track user activity (with throttling to reduce overhead)
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        let activityThrottle = null;
        
        events.forEach(event => {
            document.addEventListener(event, () => {
                if (!activityThrottle) {
                    this.onUserActivity();
                    activityThrottle = setTimeout(() => {
                        activityThrottle = null;
                    }, 1000); // Throttle to once per second max
                }
            }, { passive: true });
        });
        
        // 5. Check idle state periodically
        this.idleCheckTimer = setInterval(() => this.checkIdle(), this.IDLE_CHECK_INTERVAL);
        
        // 6. Sync to server periodically
        this.syncTimer = setInterval(() => this.sync(), this.SYNC_INTERVAL);
        
        // 7. Final sync on page unload
        window.addEventListener('beforeunload', (e) => {
            // Capture active seconds before any async operations
            const finalSeconds = this.getActiveSeconds();
            
            // IMMEDIATELY reset accumulated time to prevent double counting
            this.accumulatedTime = 0;
            this.lastSync = Date.now();
            
            // Send final sync if there's time to report
            if (finalSeconds > 0) {
                console.log(`[StudyTracker] 📤 Unload sync: ${finalSeconds}s`);
                
                // Use fetch with keepalive for reliable delivery
                fetch(this.apiEndpoint, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ 
                        seconds: finalSeconds,
                        is_active: false // Mark as inactive on unload
                    }),
                    keepalive: true // Critical for beforeunload
                }).catch(err => {
                    console.error('[StudyTracker] Unload sync failed:', err);
                });
            }
        });
        
        // 8. Handle page visibility change for mobile
        if (typeof document.hidden !== 'undefined') {
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    this.onUserActivity();
                }
            });
        }
        
        // 9. DON'T load from localStorage - server is the source of truth
        // Will be initialized after fetch completes in the view
        
        // 10. Start display update if element provided
        if (this.displayElement) {
            // Use requestAnimationFrame for smoother, more efficient updates
            const updateLoop = () => {
                if (!document.hidden) { // Only update when tab is visible
                    this.updateDisplay();
                }
                requestAnimationFrame(updateLoop);
            };
            requestAnimationFrame(updateLoop);
        }
    }
    
    /**
     * Handle user activity
     */
    onUserActivity() {
        this.lastActivity = Date.now();
        
        if (this.isIdle) {
            this.isIdle = false;
            this.resume('User aktif kembali');
        }
    }
    
    /**
     * Check if user is idle
     */
    checkIdle() {
        const idleTime = Date.now() - this.lastActivity;
        
        if (idleTime > this.IDLE_THRESHOLD && !this.isIdle && this.isTracking) {
            this.isIdle = true;
            this.pause('User idle (tidak ada aktivitas 3 menit)');
        }
    }
    
    /**
     * Pause tracking
     */
    pause(reason) {
        if (this.isTracking) {
            console.log(`[StudyTracker] ⏸️ PAUSED: ${reason}`);
            
            // Only accumulate time if already initialized (prevents spike during page load)
            if (this.isInitialized) {
                const elapsedSinceLastSync = Math.floor((Date.now() - this.lastSync) / 1000);
                if (elapsedSinceLastSync > 0) {
                    this.accumulatedTime += elapsedSinceLastSync;
                    console.log(`[StudyTracker] 💾 Accumulated ${elapsedSinceLastSync}s (Total accumulated: ${this.accumulatedTime}s)`);
                }
            }
            
            this.isTracking = false;
            this.pauseStartTime = Date.now();
            this.lastSync = Date.now(); // Reset lastSync to current time
        }
    }
    
    /**
     * Resume tracking
     */
    resume(reason) {
        if (!this.isTracking && !document.hidden) {
            console.log(`[StudyTracker] ▶️ RESUMED: ${reason}`);
            
            // Don't add time that passed while paused - that's correct behavior
            // But we keep the accumulated time from before the pause
            
            this.isTracking = true;
            this.lastActivity = Date.now();
            this.lastSync = Date.now(); // Reset sync timer to now
            this.pauseStartTime = null;
        }
    }
    
    /**
     * Get active seconds since last sync
     */
    getActiveSeconds() {
        // Don't count time before initialized (prevents spike on page load)
        if (!this.isInitialized) {
            return 0;
        }
        
        let activeSeconds = 0;
        
        // Add accumulated time from before pause
        activeSeconds += this.accumulatedTime;
        
        // Add current session time if tracking
        if (this.isTracking) {
            const currentSessionTime = Math.floor((Date.now() - this.lastSync) / 1000);
            activeSeconds += currentSessionTime;
        }
        
        return activeSeconds;
    }
    
    /**
     * Sync time to server
     */
    async sync(force = false) {
        const activeSeconds = this.getActiveSeconds();
        
        // Don't sync if below minimum threshold (unless forced)
        if (!force && activeSeconds < this.MIN_SYNC_SECONDS) {
            return;
        }
        
        // Skip sync if offline (save battery and avoid failed requests)
        if (!this.isOnline && !force) {
            console.log('[StudyTracker] 📡 Offline - skipping sync');
            return;
        }
        
        if (activeSeconds > 0) {
            try {
                const response = await fetch(this.apiEndpoint, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ 
                        seconds: activeSeconds,
                        is_active: this.isTracking
                    }),
                    // Use keepalive for beforeunload
                    keepalive: force
                });
                
                if (response.ok) {
                    const data = await response.json();
                    
                    if (data.success) {
                        // Update totalSeconds from server (source of truth)
                        // PRIORITY: Use today's shared timer (course/global), NOT individual resource time
                        if (data.today_course_time !== undefined) {
                            this.totalSeconds = data.today_course_time; // For lessons - shared course timer
                        } else if (data.today_global_time !== undefined) {
                            this.totalSeconds = data.today_global_time; // For articles - shared global timer
                        } else if (data.total_seconds !== undefined) {
                            this.totalSeconds = data.total_seconds; // Fallback for goals
                        }
                        
                        // RESET accumulated time after successful sync
                        this.accumulatedTime = 0;
                        this.totalActiveTime = 0;
                        this.lastSync = Date.now();
                        this.retryCount = 0; // Reset retry counter on success
                        
                        // Only log sync every 60 seconds to reduce console spam
                        if (activeSeconds >= 60 || force) {
                            console.log(`[StudyTracker] ✅ Synced ${activeSeconds}s (Total: ${this.totalSeconds}s)`);
                        }
                        
                        // Trigger custom event for other components
                        window.dispatchEvent(new CustomEvent('study-time-updated', {
                            detail: {
                                seconds: this.totalSeconds, // Use server total, not totalActiveTime
                                formatted: this.getFormattedTime()
                            }
                        }));
                    }
                } else {
                    console.error('[StudyTracker] ❌ Sync failed:', response.status);
                    
                    // Implement exponential backoff for retries
                    this.retryCount++;
                    const retryDelay = Math.min(
                        this.SYNC_INTERVAL * Math.pow(2, this.retryCount),
                        this.MAX_RETRY_DELAY
                    );
                    
                    console.log(`[StudyTracker] 🔄 Will retry in ${Math.round(retryDelay / 1000)}s (attempt ${this.retryCount})`);
                }
            } catch (error) {
                console.error('[StudyTracker] ❌ Sync error:', error);
                
                // Implement exponential backoff for retries
                this.retryCount++;
                const retryDelay = Math.min(
                    this.SYNC_INTERVAL * Math.pow(2, this.retryCount),
                    this.MAX_RETRY_DELAY
                );
                
                console.log(`[StudyTracker] 🔄 Will retry in ${Math.round(retryDelay / 1000)}s (attempt ${this.retryCount})`);
                
                // Don't reset lastSync on failure - accumulated time will retry next sync
                // But prevent overflow: if > 5 minutes accumulated, reset to prevent data loss
                if (activeSeconds > 300) {
                    console.warn('[StudyTracker] ⚠️ Too much unsync\'d time (>5min), resetting to prevent overflow');
                    this.lastSync = Date.now();
                    this.totalActiveTime = 0;
                }
            }
        }
    }
    
    /**
     * Get formatted time string
     */
    getFormattedTime() {
        // FIXED: Use totalSeconds (from server) + active seconds (including accumulated)
        const activeSeconds = this.getActiveSeconds();
        const total = this.totalSeconds + activeSeconds;
        
        const hours = Math.floor(total / 3600);
        const minutes = Math.floor((total % 3600) / 60);
        const seconds = total % 60;
        
        if (hours > 0) {
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
        
        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
    
    /**
     * Update display element
     */
    updateDisplay() {
        if (this.displayElement) {
            // Throttle display updates to once per second (save CPU)
            const now = Date.now();
            if (now - this.lastDisplayUpdate < 1000) {
                return;
            }
            this.lastDisplayUpdate = now;
            
            // Don't update display until initialized (prevents showing wrong values during load)
            if (!this.isInitialized) {
                this.displayElement.textContent = '00:00';
                return;
            }
            
            const formatted = this.getFormattedTime();
            this.displayElement.textContent = formatted;
            
            // Update status indicator if exists
            const statusEl = document.getElementById('tracker-status');
            if (statusEl) {
                if (this.isTracking && !this.isIdle) {
                    statusEl.className = 'tracker-status tracker-active';
                    statusEl.title = 'Tracking active';
                } else if (this.isIdle) {
                    statusEl.className = 'tracker-status tracker-idle';
                    statusEl.title = 'Idle';
                } else {
                    statusEl.className = 'tracker-status tracker-paused';
                    statusEl.title = 'Paused';
                }
            }
        }
    }
    
    // localStorage removed - server is single source of truth
    // No need for client-side persistence that can cause race conditions
    
    /**
     * Stop tracking and clean up
     */
    destroy() {
        console.log('[StudyTracker] Stopping tracker...');
        
        // Final sync
        this.sync(true);
        
        // Clear timers
        if (this.idleCheckTimer) clearInterval(this.idleCheckTimer);
        if (this.syncTimer) clearInterval(this.syncTimer);
        
        console.log('[StudyTracker] ⏹️ Stopped');
    }
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = StudyTimeTracker;
}
