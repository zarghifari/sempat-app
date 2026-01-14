<?php

namespace App\Http\Controllers;

use App\Models\LearningGoal;
use App\Models\LearningGoalMilestone;
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
            'daily_target_minutes' => 'nullable|integer|min:1',
            'target_days' => 'nullable|integer|min:1',
            'completion_type' => 'required|in:final_project,final_assessment',
            'final_project_title' => 'nullable|string|max:255',
            'final_project_description' => 'nullable|string',
            'milestones' => 'nullable|array',
            'milestones.*.title' => 'required|string|max:255',
            'milestones.*.description' => 'nullable|string',
            'milestones.*.requires_evidence' => 'nullable|boolean',
        ]);
        
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'active';
        
        $goal = LearningGoal::create($validated);
        
        // Create milestones if provided
        if (!empty($validated['milestones'])) {
            foreach ($validated['milestones'] as $index => $milestoneData) {
                $goal->milestones()->create([
                    'title' => $milestoneData['title'],
                    'description' => $milestoneData['description'] ?? null,
                    'order' => $index + 1,
                    'requires_evidence' => $milestoneData['requires_evidence'] ?? false,
                ]);
            }
        }
        
        // Calculate initial progress based on daily targets, milestones, and final completion
        $goal->recalculateProgress();
        
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
            // progress_percentage is auto-calculated, not editable
        ]);
        
        $learningGoal->update($validated);
        
        // Recalculate progress to ensure accuracy
        $learningGoal->recalculateProgress();
        
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
     * @deprecated This method is no longer used. Progress is now automatically calculated
     * from daily targets, milestones, and final completion (project/assessment).
     * See LearningGoal::recalculateProgress() method.
     */
    public function updateProgress(Request $request, LearningGoal $learningGoal)
    {
        // This method is deprecated but kept for backward compatibility
        // Progress is now automatically calculated based on:
        // - Daily target completion (40% weight)
        // - Milestones completion (40% weight)  
        // - Final completion - project or assessment (20% weight)
        
        return redirect()->route('learning-goals.index')
            ->with('info', 'Progress is now calculated automatically based on your daily targets, milestones, and final completion.');
        
        /* Original code removed - manual progress updates no longer supported
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
        */
        
        return back()->with('info', 'Progress is now calculated automatically based on your daily targets, milestones, and final completion.');
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

    /**
     * Show goal details with milestones
     */
    public function show(LearningGoal $learningGoal)
    {
        // Ensure user owns this goal
        if ($learningGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $learningGoal->load(['milestones' => function($query) {
            $query->orderBy('order');
        }, 'journalEntries']);

        return view('learning-goals.show', compact('learningGoal'));
    }

    /**
     * Toggle milestone completion
     */
    public function toggleMilestone(Request $request, LearningGoalMilestone $milestone)
    {
        // Ensure user owns this milestone's goal
        if ($milestone->learningGoal->user_id !== Auth::id()) {
            abort(403);
        }

        if ($milestone->is_completed) {
            $milestone->markIncomplete();
            $message = 'Milestone marked as incomplete.';
        } else {
            // If requires evidence, validate
            if ($milestone->requires_evidence) {
                $validated = $request->validate([
                    'evidence_text' => 'nullable|string|max:1000',
                    'evidence_file' => 'nullable|file|max:5120', // 5MB max
                ]);

                // Handle file upload
                if ($request->hasFile('evidence_file')) {
                    $file = $request->file('evidence_file');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('milestone-evidence', $filename, 'public');
                    $milestone->evidence_file = $filename;
                }

                $milestone->evidence_text = $validated['evidence_text'] ?? null;
                $milestone->save();
            }

            $milestone->markCompleted();
            $message = 'Milestone completed! 🎉';
        }

        return back()->with('success', $message);
    }

    /**
     * Store final project
     */
    public function storeFinalProject(Request $request, LearningGoal $learningGoal)
    {
        // Ensure user owns this goal
        if ($learningGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'final_project_title' => 'required|string|max:255',
            'final_project_description' => 'nullable|string',
            'final_project_url' => 'nullable|url',
            'final_project_file' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Handle file upload
        if ($request->hasFile('final_project_file')) {
            $file = $request->file('final_project_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('final-projects', $filename, 'public');
            $validated['final_project_file'] = $filename;
        }

        $validated['final_project_submitted_at'] = now();
        $learningGoal->update($validated);
        
        // Recalculate progress
        $learningGoal->recalculateProgress();

        return back()->with('success', 'Final project submitted successfully! 🎉');
    }
    
    /**
     * Submit final assessment
     */
    public function submitAssessment(Request $request, LearningGoal $learningGoal)
    {
        // Ensure user owns this goal
        if ($learningGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'assessment_what_learned' => 'required|string',
            'assessment_how_applied' => 'required|string',
            'assessment_challenges' => 'required|string',
            'assessment_next_steps' => 'required|string',
        ]);

        $validated['assessment_submitted_at'] = now();
        $learningGoal->update($validated);
        
        // Recalculate progress
        $learningGoal->recalculateProgress();
        
        // Check if goal is fully completed
        if ($learningGoal->isFullyCompleted()) {
            $learningGoal->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
            return back()->with('success', 'Congratulations! You have completed this learning goal! 🎉🎊');
        }

        return back()->with('success', 'Assessment submitted successfully! 🎉');
    }
}
