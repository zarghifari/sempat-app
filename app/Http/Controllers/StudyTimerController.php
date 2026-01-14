<?php

namespace App\Http\Controllers;

use App\Models\StudySession;
use App\Models\LearningGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudyTimerController extends Controller
{
    /**
     * Save study session from timer
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

        // Get or create study session for this goal
        $session = StudySession::firstOrCreate([
            'user_id' => Auth::id(),
            'learning_goal_id' => $goal->id,
        ], [
            'session_logs' => [],
        ]);

        // Add study minutes (with cap at daily_target_minutes)
        $totalMinutes = $session->addStudyMinutes(
            $validated['date'],
            $validated['duration_minutes']
        );

        return response()->json([
            'success' => true,
            'total_minutes_today' => $totalMinutes,
            'target_reached' => $session->isTodayTargetReached(),
            'days_completed' => $goal->fresh()->days_completed,
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

        $session = StudySession::where('user_id', Auth::id())
            ->where('learning_goal_id', $goalId)
            ->first();

        $todayMinutes = $session ? $session->getTodayMinutes() : 0;
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

        $session = StudySession::where('user_id', Auth::id())
            ->where('learning_goal_id', $goalId)
            ->first();

        return response()->json([
            'session_logs' => $session ? $session->session_logs : [],
            'days_completed' => $goal->days_completed,
            'target_days' => $goal->target_days,
        ]);
    }
}
