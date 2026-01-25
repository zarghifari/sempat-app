<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Article;
use App\Models\ArticleReading;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\LearningGoal;
use App\Models\LearningJournal;
use App\Models\DocumentImport;
use App\Services\DocumentConverterService;
use App\Services\StudentProgressCacheService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherDashboardController extends Controller
{
    /**
     * Display teacher dashboard
     */
    public function index()
    {
        $teacher = Auth::user();

        // Overview Statistics
        $stats = [
            'total_courses' => Course::where('created_by', $teacher->id)->count(),
            'total_articles' => Article::where('created_by', $teacher->id)->count(),
            'total_students' => $this->getTotalStudents($teacher->id),
            'pending_grading' => $this->getPendingGrading($teacher->id),
        ];

        // Recent courses with enrollment count
        $recentCourses = Course::where('created_by', $teacher->id)
            ->withCount('enrollments')
            ->latest()
            ->take(5)
            ->get();

        // Recent quiz attempts needing grading (essay questions)
        $pendingQuizzes = QuizAttempt::whereHas('quiz', function($q) use ($teacher) {
                $q->where('created_by', $teacher->id);
            })
            ->where('status', 'completed')
            ->whereNull('graded_at')
            ->with(['user', 'quiz'])
            ->latest()
            ->take(5)
            ->get();

        // Student engagement overview (last 30 days)
        $engagement = $this->getStudentEngagement($teacher->id);

        // Recent activity from students
        $recentActivity = $this->getRecentStudentActivity($teacher->id);

        // Mini analytics for dashboard (last 7 days)
        $miniAnalytics = $this->getMiniAnalytics($teacher->id);

        return view('teacher.dashboard', compact(
            'stats',
            'recentCourses',
            'pendingQuizzes',
            'engagement',
            'recentActivity',
            'miniAnalytics'
        ));
    }

    /**
     * My Courses - List all courses created by teacher
     */
    public function courses(Request $request)
    {
        $teacher = Auth::user();
        
        $query = Course::where('created_by', $teacher->id)
            ->withCount(['modules', 'enrollments']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $courses = $query->latest()->paginate(12);

        $stats = [
            'total' => Course::where('created_by', $teacher->id)->count(),
            'published' => Course::where('created_by', $teacher->id)->where('status', 'published')->count(),
            'draft' => Course::where('created_by', $teacher->id)->where('status', 'draft')->count(),
            'archived' => Course::where('created_by', $teacher->id)->where('status', 'archived')->count(),
        ];

        return view('teacher.courses.index', compact('courses', 'stats'));
    }

    /**
     * Course Detail - Show single course with modules
     */
    public function showCourse($id)
    {
        $course = Course::where('id', $id)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        $modules = Module::where('course_id', $course->id)
            ->orderBy('order')
            ->get();
        
        $enrollments = Enrollment::where('course_id', $course->id)->count();
        
        return view('teacher.courses.show', compact('course', 'modules', 'enrollments'));
    }

    /**
     * My Articles - List all articles created by teacher
     */
    public function articles(Request $request)
    {
        $teacher = Auth::user();
        
        $query = Article::where('created_by', $teacher->id)
            ->with('category');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $articles = $query->latest()->paginate(12);

        $stats = [
            'total' => Article::where('created_by', $teacher->id)->count(),
            'published' => Article::where('created_by', $teacher->id)->where('status', 'published')->count(),
            'draft' => Article::where('created_by', $teacher->id)->where('status', 'draft')->count(),
            'total_views' => Article::where('created_by', $teacher->id)->sum('views_count'),
        ];

        // Get learning goals from students in teacher's courses
        $learningGoals = LearningGoal::whereHas('user.enrollments.course', function($query) use ($teacher) {
            $query->where('created_by', $teacher->id);
        })
        ->with('user')
        ->latest()
        ->paginate(10, ['*'], 'goals_page');

        // Get learning journals from students in teacher's courses  
        $learningJournals = LearningJournal::whereHas('user.enrollments.course', function($query) use ($teacher) {
            $query->where('created_by', $teacher->id);
        })
        ->with(['user', 'learning_goal'])
        ->latest('entry_date')
        ->paginate(10, ['*'], 'journals_page');

        return view('teacher.articles.index', compact('articles', 'stats', 'learningGoals', 'learningJournals'));
    }

    /**
     * My Students - List all enrolled students
     */
    public function students(Request $request)
    {
        // Just render the view - data will be loaded via optimized API
        return view('teacher.students.index');
    }

    /**
     * Student Detail - View individual student progress
     */
    public function studentDetail($studentId)
    {
        $teacher = Auth::user();
        $student = \App\Models\User::findOrFail($studentId);

        // Verify student is enrolled in teacher's courses
        $hasAccess = Enrollment::whereHas('course', function($q) use ($teacher) {
            $q->where('created_by', $teacher->id);
        })->where('user_id', $studentId)->exists();

        if (!$hasAccess) {
            abort(403, 'This student is not enrolled in your courses.');
        }

        // Student enrollments in teacher's courses
        $enrollments = Enrollment::with('course')
            ->whereHas('course', function($q) use ($teacher) {
                $q->where('created_by', $teacher->id);
            })
            ->where('user_id', $studentId)
            ->get();

        // Student's learning goals
        $learningGoals = LearningGoal::where('user_id', $studentId)
            ->latest()
            ->take(5)
            ->get();

        // Count completed learning goals
        $completedGoalsCount = LearningGoal::where('user_id', $studentId)
            ->where('status', 'completed')
            ->count();

        // Total learning goals
        $totalGoalsCount = LearningGoal::where('user_id', $studentId)->count();

        // Student's journal entries
        $learningJournals = LearningJournal::where('user_id', $studentId)
            ->with('learning_goal')
            ->latest('entry_date')
            ->take(5)
            ->get();

        // Student's quiz attempts in teacher's quizzes
        $quizAttempts = QuizAttempt::with(['quiz.course'])
            ->whereHas('quiz', function($q) use ($teacher) {
                $q->where('created_by', $teacher->id);
            })
            ->where('user_id', $studentId)
            ->latest()
            ->take(10)
            ->get();

        // Calculate comprehensive study time from all sources
        $lessonsStudyMinutes = $enrollments->sum('total_study_minutes');
        
        $articleReadingSeconds = \App\Models\ArticleReading::where('user_id', $studentId)
            ->sum('time_spent_seconds');
        $articlesStudyMinutes = floor($articleReadingSeconds / 60);
        
        $goalsStudySeconds = LearningGoal::where('user_id', $studentId)
            ->sum('total_study_seconds');
        $goalsStudyMinutes = floor($goalsStudySeconds / 60);
        
        $totalStudyMinutes = $lessonsStudyMinutes + $articlesStudyMinutes + $goalsStudyMinutes;

        // Study time breakdown for display
        $studyTimeBreakdown = [
            'lessons' => $lessonsStudyMinutes,
            'articles' => $articlesStudyMinutes,
            'goals' => $goalsStudyMinutes,
            'total' => $totalStudyMinutes,
        ];

        return view('teacher.students.show', compact(
            'student',
            'enrollments',
            'learningGoals',
            'learningJournals',
            'quizAttempts',
            'completedGoalsCount',
            'totalGoalsCount',
            'studyTimeBreakdown'
        ));
    }

    /**
     * Quiz Grading - View and grade quiz attempts
     */
    public function quizGrading(Request $request)
    {
        $teacher = Auth::user();

        $query = QuizAttempt::with(['user', 'quiz'])
            ->whereHas('quiz', function($q) use ($teacher) {
                $q->where('created_by', $teacher->id);
            })
            ->where('status', 'completed');

        // Filter pending grading (essay questions)
        if ($request->get('filter') === 'pending') {
            $query->whereNull('graded_at');
        }

        $attempts = $query->latest()->paginate(20);

        $statistics = [
            'total_attempts' => QuizAttempt::whereHas('quiz', function($q) use ($teacher) {
                $q->where('created_by', $teacher->id);
            })->count(),
            'pending_grading' => QuizAttempt::whereHas('quiz', function($q) use ($teacher) {
                $q->where('created_by', $teacher->id);
            })->whereNull('graded_at')->count(),
            'graded' => QuizAttempt::whereHas('quiz', function($q) use ($teacher) {
                $q->where('created_by', $teacher->id);
            })->whereNotNull('graded_at')->count(),
        ];

        return view('teacher.quizzes.grading', compact('attempts', 'statistics'));
    }

    /**
     * Document Imports
     */
    public function documentImports()
    {
        $teacher = Auth::user();

        $imports = DocumentImport::where('user_id', $teacher->id)
            ->latest()
            ->paginate(12);

        $statistics = [
            'total' => DocumentImport::where('user_id', $teacher->id)->count(),
            'completed' => DocumentImport::where('user_id', $teacher->id)->where('status', 'completed')->count(),
            'processing' => DocumentImport::where('user_id', $teacher->id)->where('status', 'processing')->count(),
            'failed' => DocumentImport::where('user_id', $teacher->id)->where('status', 'failed')->count(),
        ];

        return view('teacher.document-imports', compact('imports', 'statistics'));
    }

    /**
     * Analytics - Detailed analytics for teacher
     */
    public function analytics()
    {
        $teacher = Auth::user();

        // Course performance
        $coursePerformance = Course::where('created_by', $teacher->id)
            ->withCount('enrollments')
            ->withAvg('enrollments', 'progress_percentage')
            ->get();

        // Student completion rates
        $completionRates = Enrollment::whereHas('course', function($q) use ($teacher) {
                $q->where('created_by', $teacher->id);
            })
            ->select(
                DB::raw('COUNT(*) as total_enrollments'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('AVG(progress_percentage) as avg_progress')
            )
            ->first();

        // Quiz performance
        $quizPerformance = QuizAttempt::whereHas('quiz', function($q) use ($teacher) {
                $q->where('created_by', $teacher->id);
            })
            ->select(
                DB::raw('AVG(score_percentage) as avg_score'),
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('SUM(CASE WHEN passed = 1 THEN 1 ELSE 0 END) as passed_attempts')
            )
            ->first();

        // Monthly student activity (last 6 months)
        $monthlyActivity = $this->getMonthlyActivity($teacher->id);

        return view('teacher.analytics', compact(
            'coursePerformance',
            'completionRates',
            'quizPerformance',
            'monthlyActivity'
        ));
    }

    /**
     * Helper Methods
     */
    protected function getTotalStudents($teacherId)
    {
        return DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->where('courses.created_by', $teacherId)
            ->distinct('enrollments.user_id')
            ->count('enrollments.user_id');
    }

    protected function getPendingGrading($teacherId)
    {
        return QuizAttempt::whereHas('quiz', function($q) use ($teacherId) {
            $q->where('created_by', $teacherId);
        })->whereNull('graded_at')->count();
    }

    protected function getStudentEngagement($teacherId)
    {
        $thirtyDaysAgo = now()->subDays(30);

        return [
            'active_students' => Enrollment::whereHas('course', function($q) use ($teacherId) {
                    $q->where('created_by', $teacherId);
                })
                ->where('last_accessed_at', '>=', $thirtyDaysAgo)
                ->distinct('user_id')
                ->count(),
            'lesson_completions' => DB::table('lesson_completions')
                ->join('lessons', 'lesson_completions.lesson_id', '=', 'lessons.id')
                ->join('modules', 'lessons.module_id', '=', 'modules.id')
                ->join('courses', 'modules.course_id', '=', 'courses.id')
                ->where('courses.created_by', $teacherId)
                ->where('lesson_completions.created_at', '>=', $thirtyDaysAgo)
                ->count(),
            'quiz_attempts' => QuizAttempt::whereHas('quiz', function($q) use ($teacherId) {
                    $q->where('created_by', $teacherId);
                })
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->count(),
        ];
    }

    protected function getRecentStudentActivity($teacherId)
    {
        // Get recent lesson completions
        $recentCompletions = DB::table('lesson_completions')
            ->join('lessons', 'lesson_completions.lesson_id', '=', 'lessons.id')
            ->join('modules', 'lessons.module_id', '=', 'modules.id')
            ->join('courses', 'modules.course_id', '=', 'courses.id')
            ->join('users', 'lesson_completions.user_id', '=', 'users.id')
            ->where('courses.created_by', $teacherId)
            ->select(
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as student_name"),
                'courses.title as course_title',
                'lessons.title as lesson_title',
                'lesson_completions.created_at as activity_time'
            )
            ->latest('lesson_completions.created_at')
            ->take(10)
            ->get();

        return $recentCompletions;
    }

    protected function getMonthlyActivity($teacherId)
    {
        $sixMonthsAgo = now()->subMonths(6);

        return DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->where('courses.created_by', $teacherId)
            ->where('enrollments.created_at', '>=', $sixMonthsAgo)
            ->select(
                DB::raw('MONTH(enrollments.created_at) as month'),
                DB::raw('YEAR(enrollments.created_at) as year'),
                DB::raw('COUNT(*) as enrollment_count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }

    /**
     * Get mini analytics for dashboard (last 7 days)
     * Showing both learning types: Self-paced (Articles) and Facilitated (Courses)
     */
    protected function getMiniAnalytics($teacherId)
    {
        $sevenDaysAgo = now()->subDays(7)->startOfDay();
        
        // Get courses created by teacher
        $courseIds = Course::where('created_by', $teacherId)->pluck('id');
        
        // Get articles created by teacher
        $articleIds = Article::where('created_by', $teacherId)->pluck('id');

        // 1. Learning Type Breakdown (last 7 days)
        // Facilitated Self-Directed Learning (Courses)
        $courseLessonsTime = DB::table('lesson_completions')
            ->join('lessons', 'lesson_completions.lesson_id', '=', 'lessons.id')
            ->join('modules', 'lessons.module_id', '=', 'modules.id')
            ->join('courses', 'modules.course_id', '=', 'courses.id')
            ->where('courses.created_by', $teacherId)
            ->where('lesson_completions.completed_at', '>=', $sevenDaysAgo)
            ->sum('lesson_completions.time_spent_seconds');

        // Self-Paced Learning (Articles)
        $articlesTime = ArticleReading::whereIn('article_id', $articleIds)
            ->where('updated_at', '>=', $sevenDaysAgo)
            ->sum('time_spent_seconds');

        // 2. Daily trend (last 7 days) - Total time spent
        $dailyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayStart = now()->subDays($i)->startOfDay();
            $dayEnd = now()->subDays($i)->endOfDay();
            
            // Course time spent (in seconds)
            $courseTimeSeconds = DB::table('lesson_completions')
                ->join('lessons', 'lesson_completions.lesson_id', '=', 'lessons.id')
                ->join('modules', 'lessons.module_id', '=', 'modules.id')
                ->where('modules.course_id', '!=', null)
                ->whereIn('modules.course_id', $courseIds)
                ->whereBetween('lesson_completions.completed_at', [$dayStart, $dayEnd])
                ->sum('lesson_completions.time_spent_seconds');
            
            // Article time spent (in seconds)
            $articleTimeSeconds = ArticleReading::whereIn('article_id', $articleIds)
                ->whereBetween('updated_at', [$dayStart, $dayEnd])
                ->sum('time_spent_seconds');
            
            $dailyTrend[] = [
                'date' => $date,
                'day' => now()->subDays($i)->format('D'),
                'courses_seconds' => $courseTimeSeconds ?: 0,
                'articles_seconds' => $articleTimeSeconds ?: 0,
                'total_seconds' => ($courseTimeSeconds ?: 0) + ($articleTimeSeconds ?: 0),
                'courses_formatted' => $this->formatSeconds($courseTimeSeconds ?: 0),
                'articles_formatted' => $this->formatSeconds($articleTimeSeconds ?: 0),
            ];
        }

        // 3. Top 3 performing content
        $topCourses = Course::where('created_by', $teacherId)
            ->withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take(3)
            ->get(['id', 'title', 'enrollments_count']);

        $topArticles = Article::where('created_by', $teacherId)
            ->withCount('bookmarks')
            ->orderBy('bookmarks_count', 'desc')
            ->take(3)
            ->get(['id', 'title', 'bookmarks_count']);

        return [
            'learning_types' => [
                'facilitated' => [
                    'label' => 'Facilitated Learning (Courses)',
                    'time_seconds' => $courseLessonsTime,
                    'time_formatted' => $this->formatSeconds($courseLessonsTime),
                    'percentage' => ($courseLessonsTime + $articlesTime) > 0 
                        ? round(($courseLessonsTime / ($courseLessonsTime + $articlesTime)) * 100, 1) 
                        : 0,
                ],
                'self_paced' => [
                    'label' => 'Self-Paced Learning (Articles)',
                    'time_seconds' => $articlesTime,
                    'time_formatted' => $this->formatSeconds($articlesTime),
                    'percentage' => ($courseLessonsTime + $articlesTime) > 0 
                        ? round(($articlesTime / ($courseLessonsTime + $articlesTime)) * 100, 1) 
                        : 0,
                ],
                'total' => [
                    'time_seconds' => $courseLessonsTime + $articlesTime,
                    'time_formatted' => $this->formatSeconds($courseLessonsTime + $articlesTime),
                ],
            ],
            'daily_trend' => $dailyTrend,
            'top_content' => [
                'courses' => $topCourses,
                'articles' => $topArticles,
            ],
        ];
    }

    /**
     * Show form to create a new course
     */
    public function createCourse()
    {
        return view('teacher.courses.create');
    }

    /**
     * Store a new course
     */
    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'level' => 'required|in:beginner,intermediate,advanced',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:draft,published,archived',
            'is_premium' => 'required|boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $course = Course::create($validated);

        return redirect()->route('teacher.courses')
            ->with('success', 'Course created successfully!');
    }

    /**
     * Show form to edit a course
     */
    public function editCourse($id)
    {
        $course = Course::where('id', $id)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        return view('teacher.courses.create', compact('course'));
    }

    /**
     * Update a course
     */
    public function updateCourse(Request $request, $id)
    {
        $course = Course::where('id', $id)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'level' => 'required|in:beginner,intermediate,advanced',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:draft,published,archived',
            'is_premium' => 'required|boolean',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($course->thumbnail) {
                \Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $course->update($validated);

        return redirect()->route('teacher.courses')
            ->with('success', 'Course updated successfully!');
    }

    /**
     * Delete a course
     */
    public function destroyCourse($id)
    {
        $course = Course::where('id', $id)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        // Delete thumbnail if exists
        if ($course->thumbnail) {
            \Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()->route('teacher.courses')
            ->with('success', 'Course deleted successfully!');
    }

    /**
     * Show form to create a new article
     */
    public function createArticle()
    {
        return view('teacher.articles.create');
    }

    /**
     * Store a new article
     */
    public function storeArticle(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'tags' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('articles', 'public');
        }

        $article = Article::create($validated);

        return redirect()->route('teacher.articles')
            ->with('success', 'Article created successfully!');
    }

    /**
     * Show form to edit an article
     */
    public function editArticle($id)
    {
        $article = Article::where('id', $id)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        return view('teacher.articles.create', compact('article'));
    }

    /**
     * Update an article
     */
    public function updateArticle(Request $request, $id)
    {
        $article = Article::where('id', $id)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'tags' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($article->thumbnail) {
                \Storage::disk('public')->delete($article->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('articles', 'public');
        }

        $article->update($validated);

        return redirect()->route('teacher.articles')
            ->with('success', 'Article updated successfully!');
    }

    /**
     * Delete an article
     */
    public function destroyArticle($id)
    {
        $article = Article::where('id', $id)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        // Delete thumbnail if exists
        if ($article->thumbnail) {
            \Storage::disk('public')->delete($article->thumbnail);
        }

        $article->delete();

        return redirect()->route('teacher.articles')
            ->with('success', 'Article deleted successfully!');
    }

    /**
     * Module CRUD - Create module form
     */
    public function createModule($courseId)
    {
        $course = Course::where('id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        return view('teacher.modules.create', compact('course'));
    }

    public function storeModule(Request $request, $courseId)
    {
        $course = Course::where('id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:1',
            'estimated_minutes' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published',
            'is_locked' => 'nullable|boolean',
        ]);

        $validated['course_id'] = $course->id;
        $validated['created_by'] = Auth::id();
        $validated['is_locked'] = $request->has('is_locked') ? true : false;

        $module = Module::create($validated);

        return redirect()->route('teacher.courses.show', $courseId)
            ->with('success', 'Module created successfully!');
    }

    public function editModule($courseId, $moduleId)
    {
        $course = Course::where('id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        $module = Module::where('id', $moduleId)
            ->where('course_id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        return view('teacher.modules.create', compact('course', 'module'));
    }

    public function updateModule(Request $request, $courseId, $moduleId)
    {
        $course = Course::where('id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        $module = Module::where('id', $moduleId)
            ->where('course_id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:1',
            'estimated_minutes' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published',
            'is_locked' => 'nullable|boolean',
        ]);

        $validated['is_locked'] = $request->has('is_locked') ? true : false;

        $module->update($validated);

        return redirect()->route('teacher.courses.show', $courseId)
            ->with('success', 'Module updated successfully!');
    }

    public function destroyModule($courseId, $moduleId)
    {
        $course = Course::where('id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        $module = Module::where('id', $moduleId)
            ->where('course_id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        $module->delete();

        return redirect()->route('teacher.courses.show', $courseId)
            ->with('success', 'Module deleted successfully!');
    }

    /**
     * Lesson CRUD - Show module lessons
     */
    public function showModule($courseId, $moduleId)
    {
        $course = Course::where('id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        $module = Module::where('id', $moduleId)
            ->where('course_id', $courseId)
            ->where('created_by', Auth::id())
            ->with(['lessons' => function($query) {
                $query->orderBy('order');
            }])
            ->firstOrFail();
        
        return view('teacher.modules.show', compact('course', 'module'));
    }

    public function createLesson($courseId, $moduleId)
    {
        $course = Course::where('id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        $module = Module::where('id', $moduleId)
            ->where('course_id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        return view('teacher.lessons.create', compact('course', 'module'));
    }

    public function storeLesson(Request $request, $courseId, $moduleId, DocumentConverterService $converter)
    {
        $course = Course::where('id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        $module = Module::where('id', $moduleId)
            ->where('course_id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:document', // Only document allowed
            'content' => 'nullable|string',
            'order' => 'required|integer|min:1',
            'duration_minutes' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published',
            'is_preview' => 'nullable|boolean',
            'is_mandatory' => 'nullable|boolean',
            'document_file' => 'required|file|mimes:pdf,doc,docx|max:10240', // Required, 10MB max
            'convert_to_html' => 'nullable|boolean',
        ]);

        $validated['module_id'] = $module->id;
        $validated['created_by'] = Auth::id();
        $validated['is_preview'] = $request->has('is_preview');
        $validated['is_mandatory'] = $request->has('is_mandatory');
        $validated['type'] = 'document'; // Force document type

        // Handle document upload - now mandatory
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $extension = $file->getClientOriginalExtension();
            
            // Store the file
            $filePath = $converter->storeDocument($file);
            
            // Initialize attachments array
            $validated['attachments'] = ['document' => $filePath];
            
            // Convert to HTML if requested and file is docx/doc
            if ($request->has('convert_to_html') && in_array($extension, ['docx', 'doc'])) {
                try {
                    $storagePath = storage_path('app/public/' . $filePath);
                    $htmlContent = $converter->convertToHtml($storagePath);
                    $validated['content'] = $htmlContent;
                } catch (\Exception $e) {
                    return back()->withInput()->withErrors(['document_file' => 'Failed to convert document: ' . $e->getMessage()]);
                }
            }
        }

        $lesson = Lesson::create($validated);

        // Update module lessons count
        $module->increment('lessons_count');

        return redirect()->route('teacher.modules.show', [$courseId, $moduleId])
            ->with('success', 'Lesson created successfully!');
    }

    public function editLesson($courseId, $moduleId, $lessonId)
    {
        $course = Course::where('id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        $module = Module::where('id', $moduleId)
            ->where('course_id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        $lesson = Lesson::where('id', $lessonId)
            ->where('module_id', $moduleId)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        return view('teacher.lessons.create', compact('course', 'module', 'lesson'));
    }

    public function updateLesson(Request $request, $courseId, $moduleId, $lessonId, DocumentConverterService $converter)
    {
        $course = Course::where('id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        $module = Module::where('id', $moduleId)
            ->where('course_id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        $lesson = Lesson::where('id', $lessonId)
            ->where('module_id', $moduleId)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:document', // Only document
            'content' => 'nullable|string',
            'order' => 'required|integer|min:1',
            'duration_minutes' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published',
            'is_preview' => 'nullable|boolean',
            'is_mandatory' => 'nullable|boolean',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // Optional on update
            'convert_to_html' => 'nullable|boolean',
        ]);

        $validated['is_preview'] = $request->has('is_preview');
        $validated['is_mandatory'] = $request->has('is_mandatory');
        $validated['type'] = 'document'; // Force document type

        // Handle document upload for updates
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $extension = $file->getClientOriginalExtension();
            
            // Delete old document if exists
            if ($lesson->attachments && isset($lesson->attachments['document'])) {
                $converter->deleteDocument($lesson->attachments['document']);
            }
            
            // Store new file
            $filePath = $converter->storeDocument($file);
            $validated['attachments'] = ['document' => $filePath];
            
            // Convert to HTML if requested
            if ($request->has('convert_to_html') && in_array($extension, ['docx', 'doc'])) {
                try {
                    $storagePath = storage_path('app/public/' . $filePath);
                    $htmlContent = $converter->convertToHtml($storagePath);
                    $validated['content'] = $htmlContent;
                } catch (\Exception $e) {
                    return back()->withInput()->withErrors(['document_file' => 'Failed to convert document: ' . $e->getMessage()]);
                }
            }
        }

        $lesson->update($validated);

        return redirect()->route('teacher.modules.show', [$courseId, $moduleId])
            ->with('success', 'Lesson updated successfully!');
    }

    public function destroyLesson($courseId, $moduleId, $lessonId, DocumentConverterService $converter)
    {
        $course = Course::where('id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        $module = Module::where('id', $moduleId)
            ->where('course_id', $courseId)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        
        $lesson = Lesson::where('id', $lessonId)
            ->where('module_id', $moduleId)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        // Delete associated document file if exists
        if ($lesson->attachments && isset($lesson->attachments['document'])) {
            $converter->deleteDocument($lesson->attachments['document']);
        }

        $lesson->delete();

        // Update module lessons count
        $module->decrement('lessons_count');

        return redirect()->route('teacher.modules.show', [$courseId, $moduleId])
            ->with('success', 'Lesson deleted successfully!');
    }

    /**
     * Get today's progress untuk semua siswa (OPTIMIZED)
     * API endpoint untuk dashboard guru
     * 
     * GET /api/teacher/students/today-progress
     */
    public function getTodayProgress(Request $request, StudentProgressCacheService $cacheService)
    {
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search');

        // Get students (with pagination + eager load roles - NO N+1)
        $studentsQuery = User::with('roles:id,name,slug')
            ->whereHas('roles', function ($q) {
                $q->where('slug', 'student');
            })
            ->select('id', 'first_name', 'last_name', 'email', 'avatar');

        if ($search) {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $studentsQuery->paginate($perPage);
        $studentIds = $students->pluck('id')->toArray();

        // Batch get progress dari cache (SUPER FAST - Redis)
        $progressData = $cacheService->getBatchTodayProgress($studentIds);

        // Merge student info dengan progress
        $results = $students->map(function ($student) use ($progressData) {
            $progress = $progressData[$student->id] ?? [
                'total_study_time' => 0,
                'total_goals_time' => 0,
                'total_lessons_time' => 0,
                'total_articles_time' => 0,
                'sessions_count' => 0,
            ];

            return [
                'id' => $student->id,
                'name' => $student->first_name . ' ' . $student->last_name,
                'email' => $student->email,
                'avatar' => $student->avatar,
                'today_progress' => [
                    'total_time_seconds' => $progress['total_study_time'],
                    'total_time_formatted' => $this->formatSeconds($progress['total_study_time']),
                    'goals_time' => $progress['total_goals_time'],
                    'lessons_time' => $progress['total_lessons_time'],
                    'articles_time' => $progress['total_articles_time'],
                    'sessions_count' => $progress['sessions_count'],
                    'last_activity' => $progress['last_activity_at'] ?? null,
                    'is_active_today' => $progress['total_study_time'] > 0,
                ],
            ];
        });

        // Summary untuk dashboard guru
        $summary = [
            'total_students' => $students->total(),
            'active_today' => collect($results)->where('today_progress.is_active_today', true)->count(),
            'total_study_time' => collect($progressData)->sum('total_study_time'),
            'average_per_student' => $students->total() > 0 
                ? round(collect($progressData)->sum('total_study_time') / $students->total()) 
                : 0,
        ];

        return response()->json([
            'summary' => $summary,
            'students' => $results,
            'pagination' => [
                'current_page' => $students->currentPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
                'last_page' => $students->lastPage(),
            ],
        ]);
    }

    /**
     * Get detail progress untuk 1 siswa
     * Includes: today, week summary, active goals
     * 
     * GET /api/teacher/students/{userId}/progress
     */
    public function getStudentProgress(Request $request, int $userId, StudentProgressCacheService $cacheService)
    {
        // Eager load relations to prevent N+1
        $student = User::with('roles:id,name,slug')
            ->select('id', 'first_name', 'last_name', 'email', 'avatar')
            ->findOrFail($userId);

        // Get cached data (FAST)
        $todayProgress = $cacheService->getTodayProgress($userId);
        $weekSummary = $cacheService->getWeekSummary($userId);

        // Get learning goals progress
        $goalsProgress = DB::table('learning_goals')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->select(
                'id',
                'title',
                'daily_target_minutes',
                'target_days',
                'days_completed',
                'total_study_seconds'
            )
            ->get()
            ->map(function ($goal) use ($todayProgress) {
                $goalKey = "goal_{$goal->id}";
                $todayGoalTime = $todayProgress['goals_breakdown'][$goalKey] ?? 0;

                return [
                    'id' => $goal->id,
                    'title' => $goal->title,
                    'today_time_seconds' => $todayGoalTime,
                    'today_time_formatted' => $this->formatSeconds($todayGoalTime),
                    'today_target_met' => $todayGoalTime >= ($goal->daily_target_minutes * 60),
                    'progress_percentage' => $goal->target_days > 0 
                        ? round(($goal->days_completed / $goal->target_days) * 100, 1) 
                        : 0,
                    'days_completed' => $goal->days_completed,
                    'target_days' => $goal->target_days,
                ];
            });

        return response()->json([
            'student' => [
                'id' => $student->id,
                'name' => $student->first_name . ' ' . $student->last_name,
                'email' => $student->email,
                'avatar' => $student->avatar,
            ],
            'today' => [
                'total_time_seconds' => $todayProgress['total_study_time'],
                'total_time_formatted' => $this->formatSeconds($todayProgress['total_study_time']),
                'breakdown' => [
                    'goals' => $todayProgress['total_goals_time'],
                    'lessons' => $todayProgress['total_lessons_time'],
                    'articles' => $todayProgress['total_articles_time'],
                ],
                'sessions_count' => $todayProgress['sessions_count'],
                'first_activity_at' => $todayProgress['first_activity_at'],
                'last_activity_at' => $todayProgress['last_activity_at'],
            ],
            'week' => $weekSummary,
            'active_goals' => $goalsProgress,
        ]);
    }

    /**
     * Get class summary (aggregated untuk semua siswa)
     * Perfect untuk dashboard overview
     * 
     * GET /api/teacher/class/summary
     */
    public function getClassSummary(Request $request, StudentProgressCacheService $cacheService)
    {
        // Get all students IDs (single query with eager loading)
        $students = User::with('roles:id,slug')
            ->whereHas('roles', function ($q) {
                $q->where('slug', 'student');
            })
            ->select('id', 'first_name', 'last_name')
            ->get();
        
        $studentIds = $students->pluck('id')->toArray();

        // Batch get today progress (cached - SUPER FAST)
        $progressData = $cacheService->getBatchTodayProgress($studentIds);

        // Aggregate statistics
        $totalStudents = count($studentIds);
        $activeToday = collect($progressData)->where('total_study_time', '>', 0)->count();
        $totalStudyTime = collect($progressData)->sum('total_study_time');
        $avgStudyTime = $totalStudents > 0 ? round($totalStudyTime / $totalStudents) : 0;

        // Top performers today (NO additional queries - use eager loaded data)
        $topPerformers = collect($progressData)
            ->sortByDesc('total_study_time')
            ->take(5)
            ->map(function ($progress) use ($students) {
                $user = $students->firstWhere('id', $progress['user_id']);
                return [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'total_time_seconds' => $progress['total_study_time'],
                    'total_time_formatted' => $this->formatSeconds($progress['total_study_time']),
                ];
            })
            ->values();

        return response()->json([
            'date' => now()->format('Y-m-d'),
            'summary' => [
                'total_students' => $totalStudents,
                'active_today' => $activeToday,
                'active_percentage' => $totalStudents > 0 ? round(($activeToday / $totalStudents) * 100, 1) : 0,
                'total_study_time_seconds' => $totalStudyTime,
                'total_study_time_formatted' => $this->formatSeconds($totalStudyTime),
                'average_per_student_seconds' => $avgStudyTime,
                'average_per_student_formatted' => $this->formatSeconds($avgStudyTime),
            ],
            'top_performers' => $topPerformers,
        ]);
    }

    /**
     * Format seconds to human readable
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

