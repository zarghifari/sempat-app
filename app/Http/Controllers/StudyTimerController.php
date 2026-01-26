<?php

namespace App\Http\Controllers;

use App\Models\DailyStudySession;
use App\Models\LearningGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudyTimerController extends Controller
{
    /**
     * Get current session status for a goal
     * Progress dihitung dari total waktu belajar di lessons + articles hari ini
     */
    public function getStatus(Request $request, $goalId)
    {
        $goal = LearningGoal::where('id', $goalId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Get today's progress dari DailyStudySession
        $progress = $goal->getTodayProgress();

        return response()->json($progress);
    }

    /**
     * Get study logs/history
     * Menampilkan histori hari-hari yang mencapai target
     */
    public function getLogs(Request $request, $goalId)
    {
        $goal = LearningGoal::where('id', $goalId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $targetMinutes = $goal->daily_target_minutes ?? 0;
        $targetSeconds = $targetMinutes * 60;
        
        // Get last 30 days study sessions
        $sessions = DailyStudySession::where('user_id', Auth::id())
            ->where('study_date', '>=', now()->subDays(30))
            ->orderBy('study_date', 'desc')
            ->get()
            ->map(function ($session) use ($targetSeconds) {
                $totalMinutes = floor($session->total_study_time / 60);
                $targetReached = $targetSeconds > 0 && $session->total_study_time >= $targetSeconds;
                
                return [
                    'date' => $session->study_date->format('Y-m-d'),
                    'total_minutes' => $totalMinutes,
                    'lessons_minutes' => floor($session->total_lessons_time / 60),
                    'articles_minutes' => floor($session->total_articles_time / 60),
                    'target_reached' => $targetReached,
                ];
            });

        // Count days completed
        $daysCompleted = $sessions->where('target_reached', true)->count();

        return response()->json([
            'session_logs' => $sessions,
            'days_completed' => $daysCompleted,
            'target_days' => $goal->target_days,
            'target_minutes' => $targetMinutes,
        ]);
    }
}
