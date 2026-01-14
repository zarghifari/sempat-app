<?php

namespace App\Http\Controllers;

use App\Models\LearningJournal;
use App\Models\Article;
use App\Models\Course;
use App\Models\LearningGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningJournalController extends Controller
{
    /**
     * Display a listing of the user's journal entries.
     */
    public function index()
    {
        $entries = Auth::user()
            ->learningJournals()
            ->with(['article', 'course', 'learningGoal'])
            ->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $stats = [
            'total' => Auth::user()->learningJournals()->count(),
            'this_week' => Auth::user()->learningJournals()
                ->where('entry_date', '>=', now()->startOfWeek())
                ->count(),
            'this_month' => Auth::user()->learningJournals()
                ->whereYear('entry_date', now()->year)
                ->whereMonth('entry_date', now()->month)
                ->count(),
            'total_study_minutes' => Auth::user()->learningJournals()->sum('study_duration_minutes') ?? 0,
        ];
        
        return view('learning-journal.index', compact('entries', 'stats'));
    }

    /**
     * Store a newly created journal entry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'entry_date' => 'required|date',
            'what_learned' => 'nullable|string|max:1000',
            'challenges_faced' => 'nullable|string',
            'next_steps' => 'nullable|string',
            'mood' => 'nullable|in:excited,confident,neutral,challenged,frustrated',
            'study_duration_minutes' => 'nullable|integer|min:1',
            'tags' => 'nullable|array',
            'article_id' => 'nullable|exists:articles,id',
            'course_id' => 'nullable|exists:courses,id',
            'learning_goal_id' => 'nullable|exists:learning_goals,id',
        ]);
        
        $validated['user_id'] = Auth::id();
        
        $journal = LearningJournal::create($validated);
        
        // Auto-update learning goal if linked
        if ($journal->learning_goal_id) {
            $goal = LearningGoal::find($journal->learning_goal_id);
            if ($goal) {
                $goal->updateStudyStats();
            }
        }
        
        return redirect()->route('learning-journal.index')
            ->with('success', 'Journal entry created successfully!');
    }

    /**
     * Update the specified journal entry.
     */
    public function update(Request $request, LearningJournal $learningJournal)
    {
        // Ensure user owns this entry
        if ($learningJournal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'entry_date' => 'required|date',
            'what_learned' => 'nullable|string',
            'challenges_faced' => 'nullable|string',
            'next_steps' => 'nullable|string',
            'mood' => 'nullable|in:excited,confident,neutral,challenged,frustrated',
            'study_duration_minutes' => 'nullable|integer|min:0',
            'tags' => 'nullable|array',
            'article_id' => 'nullable|exists:articles,id',
            'course_id' => 'nullable|exists:courses,id',
            'learning_goal_id' => 'nullable|exists:learning_goals,id',
        ]);
        
        $learningJournal->update($validated);
        
        // Auto-update learning goal if linked
        if ($learningJournal->learning_goal_id) {
            $goal = LearningGoal::find($learningJournal->learning_goal_id);
            if ($goal) {
                $goal->updateStudyStats();
            }
        }
        
        return back()->with('success', 'Journal entry updated successfully!');
    }

    /**
     * Remove the specified journal entry
     */
    public function destroy(LearningJournal $learningJournal)
    {
        // Ensure user owns this entry
        if ($learningJournal->user_id !== Auth::id()) {
            abort(403);
        }
        
        $learningJournal->delete();
        
        return redirect()->route('learning-journal.index')
            ->with('success', 'Journal entry deleted successfully!');
    }
}
