<?php

namespace App\Http\Controllers;

use App\Models\LearningGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningGoalController extends Controller
{
    /**
     * Display a listing of the user's learning goals.
     */
    public function index()
    {
        $goals = Auth::user()
            ->learningGoals()
            ->orderBy('target_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $stats = [
            'total' => $goals->count(),
            'completed' => $goals->where('status', 'completed')->count(),
            'active' => $goals->where('status', 'active')->count(),
            'abandoned' => $goals->where('status', 'abandoned')->count(),
        ];
        
        return view('learning-goals.index', compact('goals', 'stats'));
    }

    /**
     * Store a newly created learning goal.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:skill,knowledge,career,personal,academic',
            'priority' => 'required|in:low,medium,high',
            'target_date' => 'nullable|date|after:today',
        ]);
        
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'active';
        
        $goal = LearningGoal::create($validated);
        
        return redirect()->route('learning-goals.index')
            ->with('success', 'Learning goal created successfully!');
    }

    /**
     * Update the specified learning goal.
     */
    public function update(Request $request, LearningGoal $learningGoal)
    {
        // Ensure user owns this goal
        if ($learningGoal->user_id !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:skill,knowledge,career,personal,academic',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:active,completed,abandoned',
            'target_date' => 'nullable|date',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
        ]);
        
        $learningGoal->update($validated);
        
        return redirect()->route('learning-goals.index')
            ->with('success', 'Learning goal updated successfully!');
    }

    /**
     * Update goal status quickly
     */
    public function updateStatus(Request $request, LearningGoal $learningGoal)
    {
        // Ensure user owns this goal
        if ($learningGoal->user_id !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'status' => 'required|in:active,completed,abandoned',
        ]);
        
        $learningGoal->update($validated);
        
        return back()->with('success', 'Goal status updated!');
    }

    /**
     * Update goal progress
     */
    public function updateProgress(Request $request, LearningGoal $learningGoal)
    {
        // Ensure user owns this goal
        if ($learningGoal->user_id !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
        ]);
        
        $learningGoal->update($validated);
        
        // Auto-update status based on progress
        if ($learningGoal->progress_percentage == 100) {
            $learningGoal->update(['status' => 'completed']);
        } elseif ($learningGoal->progress_percentage > 0 && $learningGoal->status !== 'completed') {
            $learningGoal->update(['status' => 'active']);
        }
        
        return back()->with('success', 'Progress updated!');
    }

    /**
     * Remove the specified learning goal.
     */
    public function destroy(LearningGoal $learningGoal)
    {
        // Ensure user owns this goal
        if ($learningGoal->user_id !== Auth::id()) {
            abort(403);
        }
        
        $learningGoal->delete();
        
        return redirect()->route('learning-goals.index')
            ->with('success', 'Learning goal deleted successfully!');
    }
}
