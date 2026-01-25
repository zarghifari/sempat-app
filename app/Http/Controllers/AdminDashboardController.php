<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Article;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\LearningGoal;
use App\Models\LearningJournal;
use App\Models\DocumentImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AdminDashboardController extends Controller
{

    /**
     * Display admin dashboard
     */
    public function index()
    {
        // System-wide statistics
        $stats = [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'total_articles' => Article::count(),
            'total_enrollments' => Enrollment::count(),
            'active_students' => $this->getActiveStudents(),
            'total_quizzes' => Quiz::count(),
        ];

        // User distribution by role
        $usersByRole = $this->getUsersByRole();

        // Recent registrations
        $recentUsers = User::latest()->take(10)->get();

        // Course statistics
        $courseStats = [
            'published' => Course::where('status', 'published')->count(),
            'draft' => Course::where('status', 'draft')->count(),
            'archived' => Course::where('status', 'archived')->count(),
        ];

        // System activity (last 30 days)
        $systemActivity = $this->getSystemActivity();

        // Top performing courses
        $topCourses = Course::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take(5)
            ->get();

        // Top teachers by student count
        $topTeachers = $this->getTopTeachers();

        return view('admin.dashboard', compact(
            'stats',
            'usersByRole',
            'recentUsers',
            'courseStats',
            'systemActivity',
            'topCourses',
            'topTeachers'
        ));
    }

    /**
     * User Management
     */
    public function users(Request $request)
    {
        $query = User::with('roles');

        // Filter by role
        if ($request->has('role')) {
            $query->role($request->role);
        }

        // Search
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(20);

        $statistics = [
            'total' => User::count(),
            'students' => User::role('student')->count(),
            'teachers' => User::role('teacher')->count(),
            'admins' => User::role('admin')->count(),
        ];

        return view('admin.users.index', compact('users', 'statistics'));
    }

    /**
     * Show user detail
     */
    public function userDetail($userId)
    {
        $user = User::with('roles')->findOrFail($userId);

        // User activity statistics
        $userStats = [];
        
        if ($user->hasRole('student')) {
            $userStats = [
                'enrollments' => Enrollment::where('user_id', $userId)->count(),
                'completed_courses' => Enrollment::where('user_id', $userId)->where('status', 'completed')->count(),
                'quiz_attempts' => QuizAttempt::where('user_id', $userId)->count(),
                'learning_goals' => LearningGoal::where('user_id', $userId)->count(),
                'journal_entries' => LearningJournal::where('user_id', $userId)->count(),
            ];
        } elseif ($user->hasRole('teacher')) {
            $userStats = [
                'courses_created' => Course::where('created_by', $userId)->count(),
                'articles_created' => Article::where('created_by', $userId)->count(),
                'total_students' => $this->getTeacherStudentCount($userId),
                'document_imports' => DocumentImport::where('user_id', $userId)->count(),
            ];
        }

        // Recent activity
        $recentActivity = $this->getUserRecentActivity($userId);

        return view('admin.users.show', compact('user', 'userStats', 'recentActivity'));
    }

    /**
     * All Courses Management
     */
    public function courses(Request $request)
    {
        $query = Course::with('creator')->withCount(['modules', 'enrollments']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $courses = $query->latest()->paginate(12);

        $statistics = [
            'total' => Course::count(),
            'published' => Course::where('status', 'published')->count(),
            'draft' => Course::where('status', 'draft')->count(),
            'archived' => Course::where('status', 'archived')->count(),
        ];

        return view('admin.courses.index', compact('courses', 'statistics'));
    }

    /**
     * All Articles Management
     */
    public function articles(Request $request)
    {
        $query = Article::with(['creator', 'category']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $articles = $query->latest()->paginate(12);

        $statistics = [
            'total' => Article::count(),
            'published' => Article::where('status', 'published')->count(),
            'draft' => Article::where('status', 'draft')->count(),
            'total_views' => Article::sum('views_count'),
        ];

        return view('admin.articles.index', compact('articles', 'statistics'));
    }

    /**
     * All Students Overview
     */
    public function students(Request $request)
    {
        $query = User::role('student')
            ->withCount('enrollments');

        // Search
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $students = $query->latest()->paginate(20);

        return view('admin.students.index', compact('students'));
    }

    /**
     * Student Detail
     */
    public function studentDetail($studentId)
    {
        $student = User::role('student')->findOrFail($studentId);

        // Student statistics
        $enrollments = Enrollment::with('course')
            ->where('user_id', $studentId)
            ->get();

        $learningGoals = LearningGoal::where('user_id', $studentId)
            ->latest()
            ->take(10)
            ->get();

        $journalEntries = LearningJournal::where('user_id', $studentId)
            ->latest()
            ->take(10)
            ->get();

        $quizAttempts = QuizAttempt::with('quiz')
            ->where('user_id', $studentId)
            ->latest()
            ->take(15)
            ->get();

        return view('admin.students.show', compact(
            'student',
            'enrollments',
            'learningGoals',
            'journalEntries',
            'quizAttempts'
        ));
    }

    /**
     * All Quiz Attempts
     */
    public function quizAttempts(Request $request)
    {
        $query = QuizAttempt::with(['user', 'quiz'])
            ->where('status', 'completed');

        // Filter by status
        if ($request->get('filter') === 'pending') {
            $query->whereNull('graded_at');
        } elseif ($request->get('filter') === 'graded') {
            $query->whereNotNull('graded_at');
        }

        $attempts = $query->latest()->paginate(20);

        $statistics = [
            'total_attempts' => QuizAttempt::count(),
            'pending_grading' => QuizAttempt::whereNull('graded_at')->count(),
            'graded' => QuizAttempt::whereNotNull('graded_at')->count(),
            'avg_score' => QuizAttempt::where('status', 'completed')->avg('score_percentage'),
        ];

        return view('admin.quizzes.attempts', compact('attempts', 'statistics'));
    }

    /**
     * System Analytics
     */
    public function analytics()
    {
        // User growth (last 12 months)
        $userGrowth = $this->getUserGrowth();

        // Course enrollment trends
        $enrollmentTrends = $this->getEnrollmentTrends();

        // Course completion rates
        $completionRates = Enrollment::select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('AVG(progress_percentage) as avg_progress')
            )
            ->first();

        // Quiz performance overview
        $quizPerformance = QuizAttempt::select(
                DB::raw('AVG(score_percentage) as avg_score'),
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('SUM(CASE WHEN passed = 1 THEN 1 ELSE 0 END) as passed')
            )
            ->first();

        // Most active students
        $activeStudents = $this->getMostActiveStudents();

        // Content creation trends
        $contentTrends = $this->getContentCreationTrends();

        return view('admin.analytics', compact(
            'userGrowth',
            'enrollmentTrends',
            'completionRates',
            'quizPerformance',
            'activeStudents',
            'contentTrends'
        ));
    }

    /**
     * System Settings & Configuration
     */
    public function settings()
    {
        // System health checks
        $systemHealth = [
            'database' => $this->checkDatabaseConnection(),
            'storage' => $this->checkStorageSpace(),
            'queue' => $this->checkQueueStatus(),
        ];

        // Recent errors or issues
        $recentErrors = $this->getRecentErrors();

        return view('admin.settings', compact('systemHealth', 'recentErrors'));
    }

    /**
     * Document Imports (All)
     */
    public function documentImports()
    {
        $imports = DocumentImport::with('user')
            ->latest()
            ->paginate(12);

        $statistics = [
            'total' => DocumentImport::count(),
            'completed' => DocumentImport::where('status', 'completed')->count(),
            'processing' => DocumentImport::where('status', 'processing')->count(),
            'failed' => DocumentImport::where('status', 'failed')->count(),
        ];

        return view('admin.document-imports', compact('imports', 'statistics'));
    }

    /**
     * Helper Methods
     */
    protected function getActiveStudents()
    {
        return User::role('student')
            ->whereHas('enrollments', function($q) {
                $q->where('last_accessed_at', '>=', now()->subDays(30));
            })
            ->count();
    }

    protected function getUsersByRole()
    {
        return [
            'students' => User::role('student')->count(),
            'teachers' => User::role('teacher')->count(),
            'admins' => User::role('admin')->count(),
        ];
    }

    protected function getSystemActivity()
    {
        $thirtyDaysAgo = now()->subDays(30);

        return [
            'new_users' => User::where('created_at', '>=', $thirtyDaysAgo)->count(),
            'new_courses' => Course::where('created_at', '>=', $thirtyDaysAgo)->count(),
            'new_enrollments' => Enrollment::where('created_at', '>=', $thirtyDaysAgo)->count(),
            'quiz_attempts' => QuizAttempt::where('created_at', '>=', $thirtyDaysAgo)->count(),
        ];
    }

    protected function getTopTeachers()
    {
        return User::role('teacher')
            ->withCount(['createdCourses as total_courses'])
            ->get()
            ->map(function($teacher) {
                $studentCount = DB::table('enrollments')
                    ->join('courses', 'enrollments.course_id', '=', 'courses.id')
                    ->where('courses.created_by', $teacher->id)
                    ->distinct('enrollments.user_id')
                    ->count();
                    
                $teacher->student_count = $studentCount;
                return $teacher;
            })
            ->sortByDesc('student_count')
            ->take(5);
    }

    protected function getTeacherStudentCount($teacherId)
    {
        return DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->where('courses.created_by', $teacherId)
            ->distinct('enrollments.user_id')
            ->count();
    }

    protected function getUserRecentActivity($userId)
    {
        $user = User::find($userId);
        
        if ($user->hasRole('student')) {
            return DB::table('lesson_completions')
                ->join('lessons', 'lesson_completions.lesson_id', '=', 'lessons.id')
                ->where('lesson_completions.user_id', $userId)
                ->select('lessons.title', 'lesson_completions.created_at as activity_time')
                ->latest('lesson_completions.created_at')
                ->take(10)
                ->get();
        } elseif ($user->hasRole('teacher')) {
            return Course::where('created_by', $userId)
                ->select('title', 'created_at as activity_time')
                ->latest()
                ->take(10)
                ->get();
        }

        return collect();
    }

    protected function getUserGrowth()
    {
        $twelveMonthsAgo = now()->subMonths(12);

        return User::where('created_at', '>=', $twelveMonthsAgo)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as user_count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }

    protected function getEnrollmentTrends()
    {
        $sixMonthsAgo = now()->subMonths(6);

        return Enrollment::where('created_at', '>=', $sixMonthsAgo)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as enrollment_count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }

    protected function getMostActiveStudents()
    {
        return User::role('student')
            ->select('users.*')
            ->join('enrollments', 'users.id', '=', 'enrollments.user_id')
            ->groupBy('users.id')
            ->orderByRaw('SUM(enrollments.total_study_minutes) DESC')
            ->take(10)
            ->get();
    }

    protected function getContentCreationTrends()
    {
        $threeMonthsAgo = now()->subMonths(3);

        $courses = Course::where('created_at', '>=', $threeMonthsAgo)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->get();

        $articles = Article::where('created_at', '>=', $threeMonthsAgo)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->get();

        return [
            'courses' => $courses,
            'articles' => $articles,
        ];
    }

    protected function checkDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Database connection is working'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function checkStorageSpace()
    {
        $path = storage_path();
        $freeSpace = disk_free_space($path);
        $totalSpace = disk_total_space($path);
        $usedPercentage = (($totalSpace - $freeSpace) / $totalSpace) * 100;

        return [
            'status' => $usedPercentage < 90 ? 'healthy' : 'warning',
            'free_space' => $this->formatBytes($freeSpace),
            'total_space' => $this->formatBytes($totalSpace),
            'used_percentage' => round($usedPercentage, 2),
        ];
    }

    protected function checkQueueStatus()
    {
        $pendingJobs = DB::table('jobs')->count();
        $failedJobs = DB::table('failed_jobs')->count();

        return [
            'status' => $failedJobs > 10 ? 'warning' : 'healthy',
            'pending_jobs' => $pendingJobs,
            'failed_jobs' => $failedJobs,
        ];
    }

    protected function getRecentErrors()
    {
        return DB::table('failed_jobs')
            ->latest()
            ->take(5)
            ->get();
    }

    protected function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Admin Course CRUD - Create new course
     */
    public function createCourse()
    {
        return view('admin.courses.create');
    }

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

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);
        $validated['created_by'] = Auth::id();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $course = Course::create($validated);

        return redirect()->route('admin.courses')
            ->with('success', 'Course created successfully!');
    }

    /**
     * Admin Course CRUD - Edit any course
     */
    public function editCourse($id)
    {
        $course = Course::findOrFail($id);
        return view('admin.courses.create', compact('course'));
    }

    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);

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

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                \Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $course->update($validated);

        return redirect()->route('admin.courses')
            ->with('success', 'Course updated successfully!');
    }

    public function destroyCourse($id)
    {
        $course = Course::findOrFail($id);

        if ($course->thumbnail) {
            \Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()->route('admin.courses')
            ->with('success', 'Course deleted successfully!');
    }

    /**
     * Admin Article CRUD - Create new article
     */
    public function createArticle()
    {
        return view('admin.articles.create');
    }

    public function storeArticle(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'tags' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);
        $validated['author_id'] = Auth::id();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('articles', 'public');
        }

        $article = Article::create($validated);

        return redirect()->route('admin.articles')
            ->with('success', 'Article created successfully!');
    }

    /**
     * Admin Article CRUD - Edit any article
     */
    public function editArticle($id)
    {
        $article = Article::findOrFail($id);
        return view('admin.articles.create', compact('article'));
    }

    public function updateArticle(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'tags' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);

        if ($request->hasFile('thumbnail')) {
            if ($article->thumbnail) {
                \Storage::disk('public')->delete($article->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('articles', 'public');
        }

        $article->update($validated);

        return redirect()->route('admin.articles')
            ->with('success', 'Article updated successfully!');
    }

    public function destroyArticle($id)
    {
        $article = Article::findOrFail($id);

        if ($article->thumbnail) {
            \Storage::disk('public')->delete($article->thumbnail);
        }

        $article->delete();

        return redirect()->route('admin.articles')
            ->with('success', 'Article deleted successfully!');
    }
}
