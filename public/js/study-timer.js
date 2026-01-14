/**
 * Study Timer Component
 * Manages study session timing with localStorage persistence and backend sync
 */
class StudyTimer {
    constructor(goalId, goalTitle, dailyTargetMinutes) {
        this.goalId = goalId;
        this.goalTitle = goalTitle;
        this.dailyTargetMinutes = dailyTargetMinutes;
        this.storageKey = 'study_timer';
        this.timerInterval = null;
        this.autoSaveInterval = null;
        this.startTime = null;
        this.elapsedSeconds = 0;
        this.isRunning = false;
        this.date = this.getCurrentDate();
        
        // Load existing timer state
        this.loadFromStorage();
        
        // Check for day change
        this.checkDayChange();
    }

    /**
     * Get current date in YYYY-MM-DD format
     */
    getCurrentDate() {
        const now = new Date();
        return now.toISOString().split('T')[0];
    }

    /**
     * Load timer state from localStorage
     */
    loadFromStorage() {
        const stored = localStorage.getItem(this.storageKey);
        if (!stored) return;

        try {
            const state = JSON.parse(stored);
            
            // Only load if it's for the same goal
            if (state.goal_id === this.goalId) {
                this.startTime = state.start_time;
                this.elapsedSeconds = state.elapsed_seconds || 0;
                this.isRunning = state.is_running || false;
                this.date = state.date;
                
                // If timer was running, calculate elapsed time since last save
                if (this.isRunning && this.startTime) {
                    const now = Date.now();
                    const savedStart = new Date(this.startTime).getTime();
                    const additionalSeconds = Math.floor((now - savedStart) / 1000);
                    this.elapsedSeconds += additionalSeconds;
                    this.startTime = new Date().toISOString();
                }
            }
        } catch (e) {
            console.error('Error loading timer state:', e);
        }
    }

    /**
     * Save timer state to localStorage
     */
    saveToStorage() {
        const state = {
            goal_id: this.goalId,
            start_time: this.startTime,
            elapsed_seconds: this.elapsedSeconds,
            is_running: this.isRunning,
            date: this.date
        };
        localStorage.setItem(this.storageKey, JSON.stringify(state));
    }

    /**
     * Check if day has changed and auto-save if needed
     */
    checkDayChange() {
        const currentDate = this.getCurrentDate();
        if (this.date !== currentDate && this.elapsedSeconds > 0) {
            // Day changed, save previous day's progress
            this.saveToBackend(this.date, Math.floor(this.elapsedSeconds / 60));
            // Reset for new day
            this.elapsedSeconds = 0;
            this.date = currentDate;
            this.saveToStorage();
        }
    }

    /**
     * Start the timer
     */
    start() {
        if (this.isRunning) return;

        this.isRunning = true;
        this.startTime = new Date().toISOString();
        this.saveToStorage();

        // Update UI every second
        this.timerInterval = setInterval(() => {
            this.elapsedSeconds++;
            this.saveToStorage();
            this.updateUI();
            this.checkDayChange();
        }, 1000);

        // Auto-save to backend every 5 minutes
        this.autoSaveInterval = setInterval(() => {
            if (this.elapsedSeconds > 0) {
                this.saveToBackend(this.date, Math.floor(this.elapsedSeconds / 60));
            }
        }, 5 * 60 * 1000); // 5 minutes

        this.updateUI();
        this.triggerEvent('started');
    }

    /**
     * Pause the timer
     */
    pause() {
        if (!this.isRunning) return;

        this.isRunning = false;
        clearInterval(this.timerInterval);
        clearInterval(this.autoSaveInterval);
        this.saveToStorage();
        this.updateUI();
        this.triggerEvent('paused');
    }

    /**
     * Stop the timer and save to backend
     */
    async stop() {
        this.pause();
        
        if (this.elapsedSeconds > 0) {
            const minutes = Math.floor(this.elapsedSeconds / 60);
            if (minutes > 0) {
                await this.saveToBackend(this.date, minutes);
            }
        }

        // Reset timer
        this.reset();
        this.triggerEvent('stopped');
    }

    /**
     * Reset timer state
     */
    reset() {
        this.elapsedSeconds = 0;
        this.startTime = null;
        this.isRunning = false;
        this.date = this.getCurrentDate();
        localStorage.removeItem(this.storageKey);
        this.updateUI();
    }

    /**
     * Save session to backend
     */
    async saveToBackend(date, minutes) {
        try {
            const response = await fetch('/study-timer/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    goal_id: this.goalId,
                    date: date,
                    duration_minutes: minutes
                })
            });

            if (!response.ok) {
                throw new Error('Failed to save timer session');
            }

            const data = await response.json();
            
            // Update UI with backend response
            this.triggerEvent('saved', data);
            
            return data;
        } catch (error) {
            console.error('Error saving timer session:', error);
            this.triggerEvent('error', { message: 'Failed to save session' });
            throw error;
        }
    }

    /**
     * Get current status from backend
     */
    async fetchStatus() {
        try {
            const response = await fetch(`/study-timer/status/${this.goalId}`);
            if (!response.ok) {
                throw new Error('Failed to fetch status');
            }
            const data = await response.json();
            this.triggerEvent('statusLoaded', data);
            return data;
        } catch (error) {
            console.error('Error fetching status:', error);
            throw error;
        }
    }

    /**
     * Get session logs from backend
     */
    async fetchLogs() {
        try {
            const response = await fetch(`/study-timer/logs/${this.goalId}`);
            if (!response.ok) {
                throw new Error('Failed to fetch logs');
            }
            const data = await response.json();
            this.triggerEvent('logsLoaded', data);
            return data;
        } catch (error) {
            console.error('Error fetching logs:', error);
            throw error;
        }
    }

    /**
     * Format seconds to MM:SS
     */
    formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    }

    /**
     * Get elapsed time in minutes
     */
    getElapsedMinutes() {
        return Math.floor(this.elapsedSeconds / 60);
    }

    /**
     * Get remaining time until target in minutes
     */
    getRemainingMinutes() {
        const elapsed = this.getElapsedMinutes();
        return Math.max(0, this.dailyTargetMinutes - elapsed);
    }

    /**
     * Check if target is reached
     */
    isTargetReached() {
        return this.getElapsedMinutes() >= this.dailyTargetMinutes;
    }

    /**
     * Get progress percentage
     */
    getProgressPercentage() {
        const elapsed = this.getElapsedMinutes();
        return Math.min(100, Math.round((elapsed / this.dailyTargetMinutes) * 100));
    }

    /**
     * Update UI elements (to be implemented by consumer)
     */
    updateUI() {
        // Dispatch custom event for UI updates
        this.triggerEvent('update', {
            elapsedSeconds: this.elapsedSeconds,
            elapsedMinutes: this.getElapsedMinutes(),
            formattedTime: this.formatTime(this.elapsedSeconds),
            remainingMinutes: this.getRemainingMinutes(),
            progressPercentage: this.getProgressPercentage(),
            isRunning: this.isRunning,
            isTargetReached: this.isTargetReached()
        });
    }

    /**
     * Trigger custom event
     */
    triggerEvent(eventName, data = {}) {
        const event = new CustomEvent(`studyTimer:${eventName}`, {
            detail: {
                goalId: this.goalId,
                goalTitle: this.goalTitle,
                ...data
            }
        });
        window.dispatchEvent(event);
    }

    /**
     * Cleanup resources
     */
    destroy() {
        this.pause();
        clearInterval(this.timerInterval);
        clearInterval(this.autoSaveInterval);
    }
}

// Export for use in modules or global scope
if (typeof module !== 'undefined' && module.exports) {
    module.exports = StudyTimer;
} else {
    window.StudyTimer = StudyTimer;
}
