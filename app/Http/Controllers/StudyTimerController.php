<?php

namespace App\Http\Controllers;

use App\Models\DailyStudySession;
use App\Models\LearningGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudyTimerController extends Controller
{
    /**
     * Save study session from timer
     * @deprecated - Use TimeTrackingController::trackGoalTime instead
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'goal_id' => 'required|exists:learning_goals,id',
            'date' => 'required|date_format:Y-m-d',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        // Verify goal belongs to user
        $goal = LearningGoal::where('id', $validated['goal_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // This is now handled by TimeTrackingService
        // But keep for backward compatibility
        
        return response()->json([
            'success' => true,
            'message' => 'Please use /api/learning-goals/{goal}/track-time endpoint',
        ]);
    }

    /**
     * Get current session status for a goal
     */
    public function getStatus(Request $request, $goalId)
    {
        $goal = LearningGoal::where('id', $goalId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Get today's goal time from daily_study_sessions
        $session = DailyStudySession::getTodaySession(Auth::id());
        $todaySeconds = $session->getTodayTimeFor('goal', $goalId);
        $todayMinutes = floor($todaySeconds / 60);
        
        $targetMinutes = $goal->daily_target_minutes ?? 0;

        return response()->json([
            'today_minutes' => $todayMinutes,
            'target_minutes' => $targetMinutes,
            'remaining_minutes' => max(0, $targetMinutes - $todayMinutes),
            'target_reached' => $todayMinutes >= $targetMinutes,
        ]);
    }

    /**
     * Get session logs for a goal
     */
    public function getLogs(Request $request, $goalId)
    {
        $goal = LearningGoal::where('id', $goalId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Get goal history from daily_study_sessions
        $sessionLogs = DailyStudySession::getGoalHistory(Auth::id(), $goalId);
        
        // Calculate days completed
        $daysCompleted = 0;
        $targetMinutes = $goal->daily_target_minutes ?? 0;
        
        if ($targetMinutes > 0) {
            $daysCompleted = DailyStudySession::countGoalTargetDays(
                Auth::id(),
                $goalId,
                $targetMinutes
            );
        }

        return response()->json([
            'session_logs' => $sessionLogs,
            'days_completed' => $daysCompleted,
            'target_days' => $goal->target_days,
        ]);
    }
}
