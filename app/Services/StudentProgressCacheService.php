<?php

namespace App\Services;

use App\Models\DailyStudySession;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

/**
 * Cache service untuk mempercepat akses data progres siswa
 * Mengurangi beban database dengan caching data hari ini
 */
class StudentProgressCacheService
{
    /**
     * Cache TTL - expire di akhir hari (midnight)
     */
    private function getCacheTTL(): int
    {
        return now()->endOfDay()->diffInSeconds(now());
    }

    /**
     * Get today's study progress untuk 1 siswa (cached)
     * SUPER CEPAT: Redis lookup, no database query jika ada di cache
     */
    public function getTodayProgress(int $userId): array
    {
        $cacheKey = "student:{$userId}:today:" . now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, $this->getCacheTTL(), function () use ($userId) {
            $session = DailyStudySession::getTodaySession($userId);
            
            return [
                'user_id' => $userId,
                'date' => now()->format('Y-m-d'),
                'total_study_time' => $session->total_study_time,
                'total_goals_time' => $session->total_goals_time,
                'total_lessons_time' => $session->total_lessons_time,
                'total_articles_time' => $session->total_articles_time,
                'sessions_count' => $session->sessions_count,
                'first_activity_at' => $session->first_activity_at?->toISOString(),
                'last_activity_at' => $session->last_activity_at?->toISOString(),
                'goals_breakdown' => $session->goals_breakdown ?? [],
                'cached_at' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get today's progress untuk BANYAK siswa sekaligus
     * EFISIEN: Batch query, parallel cache lookup
     * 
     * @param array $userIds Array of user IDs
     * @return array Keyed by user_id
     */
    public function getBatchTodayProgress(array $userIds): array
    {
        $results = [];
        $missingIds = [];
        
        // Step 1: Cek cache dulu (FAST)
        foreach ($userIds as $userId) {
            $cacheKey = "student:{$userId}:today:" . now()->format('Y-m-d');
            $cached = Cache::get($cacheKey);
            
            if ($cached) {
                $results[$userId] = $cached;
            } else {
                $missingIds[] = $userId;
            }
        }
        
        // Step 2: Query yang belum ada di cache (BATCH QUERY)
        if (!empty($missingIds)) {
            $today = now()->format('Y-m-d');
            $sessions = DailyStudySession::whereIn('user_id', $missingIds)
                ->where('study_date', $today)
                ->get()
                ->keyBy('user_id');
            
            foreach ($missingIds as $userId) {
                $session = $sessions->get($userId);
                
                if (!$session) {
                    // Create empty session jika belum ada
                    $session = new DailyStudySession([
                        'user_id' => $userId,
                        'study_date' => $today,
                        'total_study_time' => 0,
                        'total_goals_time' => 0,
                        'total_lessons_time' => 0,
                        'total_articles_time' => 0,
                        'sessions_count' => 0,
                    ]);
                }
                
                $data = [
                    'user_id' => $userId,
                    'date' => $today,
                    'total_study_time' => $session->total_study_time,
                    'total_goals_time' => $session->total_goals_time,
                    'total_lessons_time' => $session->total_lessons_time,
                    'total_articles_time' => $session->total_articles_time,
                    'sessions_count' => $session->sessions_count,
                    'first_activity_at' => $session->first_activity_at?->toISOString(),
                    'last_activity_at' => $session->last_activity_at?->toISOString(),
                    'goals_breakdown' => $session->goals_breakdown ?? [],
                    'cached_at' => now()->toISOString(),
                ];
                
                // Cache untuk next request
                $cacheKey = "student:{$userId}:today:{$today}";
                Cache::put($cacheKey, $data, $this->getCacheTTL());
                
                $results[$userId] = $data;
            }
        }
        
        return $results;
    }

    /**
     * Invalidate cache ketika ada update
     * Dipanggil oleh TimeTrackingService setelah tracking
     */
    public function invalidateTodayCache(int $userId): void
    {
        $cacheKey = "student:{$userId}:today:" . now()->format('Y-m-d');
        Cache::forget($cacheKey);
    }

    /**
     * Get week summary untuk siswa (cached 1 hour)
     * Untuk dashboard guru - lihat trend 7 hari terakhir
     */
    public function getWeekSummary(int $userId): array
    {
        $cacheKey = "student:{$userId}:week:" . now()->format('Y-W');
        
        return Cache::remember($cacheKey, 3600, function () use ($userId) {
            $startDate = now()->subDays(6)->startOfDay();
            $endDate = now()->endOfDay();
            
            $sessions = DailyStudySession::where('user_id', $userId)
                ->whereBetween('study_date', [$startDate, $endDate])
                ->orderBy('study_date', 'desc')
                ->get();
            
            $dailyData = [];
            $totalTime = 0;
            $totalDays = 0;
            
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $session = $sessions->firstWhere('study_date', $date);
                
                $dayTime = $session ? $session->total_study_time : 0;
                $totalTime += $dayTime;
                if ($dayTime > 0) $totalDays++;
                
                $dailyData[] = [
                    'date' => $date,
                    'total_time' => $dayTime,
                    'formatted_time' => $this->formatSeconds($dayTime),
                ];
            }
            
            return [
                'user_id' => $userId,
                'week' => now()->format('Y-W'),
                'daily_data' => $dailyData,
                'total_time' => $totalTime,
                'total_days_active' => $totalDays,
                'average_per_day' => $totalDays > 0 ? round($totalTime / 7) : 0,
            ];
        });
    }

    /**
     * Format seconds ke human readable
     */
    private function formatSeconds(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        if ($hours > 0) {
            return sprintf('%d jam %d menit', $hours, $minutes);
        }
        
        return sprintf('%d menit', $minutes);
    }
}
