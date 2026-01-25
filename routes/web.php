<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DocumentImportController;
use App\Http\Controllers\LearningGoalController;
use App\Http\Controllers\LearningJournalController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\TeacherDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // If user is already authenticated, redirect to dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    
    // Show landing page for guests
    return view('landing');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    
    // Redirect to role-specific dashboard
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }
    
    if ($user->hasRole('teacher')) {
        return redirect()->route('teacher.dashboard');
    }
    
    // Student Dashboard
    // Get user's enrollments with courses
    $enrollments = $user->enrollments()->with('course')->active()->get();
    
    // Calculate real streak using ProgressController logic
    $progressController = new \App\Http\Controllers\ProgressController();
    $currentStreak = $progressController->calculateUserStreak($user);
    
    $stats = [
        'enrolled_courses' => $enrollments->count(),
        'completed_courses' => $user->enrollments()->completed()->count(),
        'total_study_hours' => (int)($user->enrollments()->sum('total_study_minutes') / 60),
        'current_streak' => $currentStreak
    ];
    
    $courses = $enrollments->take(4)->map(function($enrollment) {
        return [
            'id' => $enrollment->course->id,
            'title' => $enrollment->course->title,
            'progress' => $enrollment->progress_percentage
        ];
    });
    
    // Get recent activity (bookmarks + lesson completions)
    $recentActivities = collect();
    
    // Get recent bookmarks
    $recentBookmarks = \DB::table('article_bookmarks')
        ->join('articles', 'article_bookmarks.article_id', '=', 'articles.id')
        ->where('article_bookmarks.user_id', $user->id)
        ->select(
            'articles.id',
            'articles.title',
            'article_bookmarks.created_at',
            \DB::raw("'bookmark' as type"),
            \DB::raw("'article' as content_type")
        )
        ->orderBy('article_bookmarks.created_at', 'desc')
        ->limit(10)
        ->get();
    
    foreach ($recentBookmarks as $bookmark) {
        $recentActivities->push([
            'type' => 'bookmark',
            'icon' => '🔖',
            'color' => 'blue',
            'title' => $bookmark->title,
            'description' => 'Bookmarked article',
            'link' => route('articles.show', $bookmark->id),
            'time' => \Carbon\Carbon::parse($bookmark->created_at)->diffForHumans(),
            'timestamp' => $bookmark->created_at,
        ]);
    }
    
    // Get recent lesson completions
    $recentCompletions = $user->lessonCompletions()
        ->with('lesson.module.course')
        ->orderBy('completed_at', 'desc')
        ->limit(10)
        ->get();
    
    foreach ($recentCompletions as $completion) {
        $recentActivities->push([
            'type' => 'completion',
            'icon' => '✅',
            'color' => 'green',
            'title' => $completion->lesson->title,
            'description' => 'Completed lesson in ' . $completion->lesson->module->course->title,
            'link' => route('lessons.show', $completion->lesson->id),
            'time' => $completion->completed_at->diffForHumans(),
            'timestamp' => $completion->completed_at,
        ]);
    }
    
    // Get recent quiz attempts
    $recentQuizAttempts = $user->quizAttempts()
        ->with('quiz.lesson.module.course')
        ->where('status', 'completed')
        ->orderBy('completed_at', 'desc')
        ->limit(5)
        ->get();
    
    foreach ($recentQuizAttempts as $attempt) {
        $recentActivities->push([
            'type' => 'quiz',
            'icon' => '📝',
            'color' => 'purple',
            'title' => $attempt->quiz->title,
            'description' => ($attempt->passed ? '✓ Passed' : '✗ Not passed') . ' with ' . number_format($attempt->score_percentage, 0) . '% in ' . $attempt->quiz->lesson->module->course->title,
            'link' => route('quizzes.result', $attempt->id),
            'time' => $attempt->completed_at->diffForHumans(),
            'timestamp' => $attempt->completed_at,
        ]);
    }
    
    // Get recent journal entries
    $recentJournals = $user->learningJournals()
        ->orderBy('entry_date', 'desc')
        ->limit(5)
        ->get();
    
    foreach ($recentJournals as $journal) {
        $recentActivities->push([
            'type' => 'journal',
            'icon' => '📓',
            'color' => 'yellow',
            'title' => $journal->title,
            'description' => 'Reflected on learning experience',
            'link' => route('learning-journal.index'),
            'time' => \Carbon\Carbon::parse($journal->entry_date)->diffForHumans(),
            'timestamp' => $journal->entry_date,
        ]);
    }
    
    // Sort by timestamp and take 5 most recent
    $recentActivity = $recentActivities->sortByDesc('timestamp')->take(5)->values();
    
    return view('dashboard', compact('stats', 'courses', 'recentActivity'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Courses Routes
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');
    Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
    
    // Lessons Routes
    Route::get('/lessons/{id}', [LessonController::class, 'show'])->name('lessons.show');
    Route::post('/lessons/{id}/complete', [LessonController::class, 'complete'])->name('lessons.complete');
    
    // Quiz Routes
    Route::get('/lessons/{lessonId}/quizzes/{quizId}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quizzes/{quizId}/start', [QuizController::class, 'start'])->name('quizzes.start');
    Route::get('/quiz-attempts/{attemptId}', [QuizController::class, 'take'])->name('quizzes.take');
    Route::post('/quiz-attempts/{attemptId}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('/quiz-attempts/{attemptId}/result', [QuizController::class, 'result'])->name('quizzes.result');
    
    // Articles Routes (SPSDL)
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/{id}', [ArticleController::class, 'show'])->name('articles.show');
    Route::post('/articles/{id}/bookmark', [ArticleController::class, 'toggleBookmark'])->name('articles.bookmark');
    Route::post('/articles/{id}/like', [ArticleController::class, 'toggleLike'])->name('articles.like');
    Route::post('/articles/{id}/comments', [ArticleController::class, 'storeComment'])->name('articles.comments.store');
    Route::put('/comments/{commentId}', [ArticleController::class, 'updateComment'])->name('comments.update');
    Route::delete('/comments/{commentId}', [ArticleController::class, 'deleteComment'])->name('comments.delete');
    Route::post('/articles/{id}/like', [ArticleController::class, 'toggleLike'])->name('articles.like');
    Route::post('/articles/{id}/comments', [ArticleController::class, 'storeComment'])->name('articles.comments.store');
    Route::put('/articles/comments/{commentId}', [ArticleController::class, 'updateComment'])->name('articles.comments.update');
    Route::delete('/articles/comments/{commentId}', [ArticleController::class, 'deleteComment'])->name('articles.comments.delete');
    
    // Learning Goals Routes
    Route::get('/learning-goals', [LearningGoalController::class, 'index'])->name('learning-goals.index');
    Route::get('/learning-goals/{learningGoal}', [LearningGoalController::class, 'show'])->name('learning-goals.show');
    Route::post('/learning-goals', [LearningGoalController::class, 'store'])->name('learning-goals.store');
    Route::put('/learning-goals/{learningGoal}', [LearningGoalController::class, 'update'])->name('learning-goals.update');
    Route::patch('/learning-goals/{learningGoal}/status', [LearningGoalController::class, 'updateStatus'])->name('learning-goals.update-status');
    // Progress is now auto-calculated from daily targets, milestones, and final completion
    // Route::patch('/learning-goals/{learningGoal}/progress', [LearningGoalController::class, 'updateProgress'])->name('learning-goals.update-progress');
    Route::delete('/learning-goals/{learningGoal}', [LearningGoalController::class, 'destroy'])->name('learning-goals.destroy');
    
    // Learning Goal Milestones Routes
    Route::patch('/milestones/{milestone}/toggle', [LearningGoalController::class, 'toggleMilestone'])->name('milestones.toggle');
    Route::post('/learning-goals/{learningGoal}/final-project', [LearningGoalController::class, 'storeFinalProject'])->name('learning-goals.final-project');
    Route::post('/learning-goals/{learningGoal}/submit-assessment', [LearningGoalController::class, 'submitAssessment'])->name('learning-goals.submit-assessment');
    
    // Study Timer Routes
    Route::post('/study-timer/save', [App\Http\Controllers\StudyTimerController::class, 'save'])->name('study-timer.save');
    Route::get('/study-timer/status/{goalId}', [App\Http\Controllers\StudyTimerController::class, 'getStatus'])->name('study-timer.status');
    Route::get('/study-timer/logs/{goalId}', [App\Http\Controllers\StudyTimerController::class, 'getLogs'])->name('study-timer.logs');
    
    // Learning Journal Routes
    Route::get('/learning-journal', [LearningJournalController::class, 'index'])->name('learning-journal.index');
    Route::post('/learning-journal', [LearningJournalController::class, 'store'])->name('learning-journal.store');
    Route::put('/learning-journal/{learningJournal}', [LearningJournalController::class, 'update'])->name('learning-journal.update');
    Route::delete('/learning-journal/{learningJournal}', [LearningJournalController::class, 'destroy'])->name('learning-journal.destroy');
    
    // Document Import Routes
    Route::get('/document-imports', [DocumentImportController::class, 'index'])->name('document-imports.index');
    Route::get('/document-imports/create', [DocumentImportController::class, 'create'])->name('document-imports.create');
    Route::post('/document-imports', [DocumentImportController::class, 'store'])->name('document-imports.store');
    Route::get('/document-imports/{documentImport}', [DocumentImportController::class, 'show'])->name('document-imports.show');
    Route::delete('/document-imports/{documentImport}', [DocumentImportController::class, 'destroy'])->name('document-imports.destroy');
    Route::post('/document-imports/{documentImport}/retry', [DocumentImportController::class, 'retry'])->name('document-imports.retry');
    Route::post('/document-imports/{documentImport}/create-lesson', [DocumentImportController::class, 'createLesson'])->name('document-imports.create-lesson');
    Route::get('/document-imports/{documentImport}/status', [DocumentImportController::class, 'status'])->name('document-imports.status');
    
    // Progress Routes
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
    
    // Teacher Dashboard Routes
    Route::prefix('teacher')->name('teacher.')->middleware('role:teacher')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        
        // Courses Management
        Route::get('/courses', [TeacherDashboardController::class, 'courses'])->name('courses');
        Route::get('/courses/create', [TeacherDashboardController::class, 'createCourse'])->name('courses.create');
        Route::post('/courses', [TeacherDashboardController::class, 'storeCourse'])->name('courses.store');
        Route::get('/courses/{id}', [TeacherDashboardController::class, 'showCourse'])->name('courses.show');
        Route::get('/courses/{id}/edit', [TeacherDashboardController::class, 'editCourse'])->name('courses.edit');
        Route::put('/courses/{id}', [TeacherDashboardController::class, 'updateCourse'])->name('courses.update');
        Route::delete('/courses/{id}', [TeacherDashboardController::class, 'destroyCourse'])->name('courses.destroy');
        
        // Articles Management
        Route::get('/articles', [TeacherDashboardController::class, 'articles'])->name('articles');
        Route::get('/articles/create', [TeacherDashboardController::class, 'createArticle'])->name('articles.create');
        Route::post('/articles', [TeacherDashboardController::class, 'storeArticle'])->name('articles.store');
        Route::get('/articles/{id}/edit', [TeacherDashboardController::class, 'editArticle'])->name('articles.edit');
        Route::put('/articles/{id}', [TeacherDashboardController::class, 'updateArticle'])->name('articles.update');
        Route::delete('/articles/{id}', [TeacherDashboardController::class, 'destroyArticle'])->name('articles.destroy');
        
        // Modules Management
        Route::get('/courses/{courseId}/modules/create', [TeacherDashboardController::class, 'createModule'])->name('modules.create');
        Route::post('/courses/{courseId}/modules', [TeacherDashboardController::class, 'storeModule'])->name('modules.store');
        Route::get('/courses/{courseId}/modules/{moduleId}', [TeacherDashboardController::class, 'showModule'])->name('modules.show');
        Route::get('/courses/{courseId}/modules/{moduleId}/edit', [TeacherDashboardController::class, 'editModule'])->name('modules.edit');
        Route::put('/courses/{courseId}/modules/{moduleId}', [TeacherDashboardController::class, 'updateModule'])->name('modules.update');
        Route::delete('/courses/{courseId}/modules/{moduleId}', [TeacherDashboardController::class, 'destroyModule'])->name('modules.destroy');
        
        // Lessons Management
        Route::get('/courses/{courseId}/modules/{moduleId}/lessons/create', [TeacherDashboardController::class, 'createLesson'])->name('lessons.create');
        Route::post('/courses/{courseId}/modules/{moduleId}/lessons', [TeacherDashboardController::class, 'storeLesson'])->name('lessons.store');
        Route::get('/courses/{courseId}/modules/{moduleId}/lessons/{lessonId}/edit', [TeacherDashboardController::class, 'editLesson'])->name('lessons.edit');
        Route::put('/courses/{courseId}/modules/{moduleId}/lessons/{lessonId}', [TeacherDashboardController::class, 'updateLesson'])->name('lessons.update');
        Route::delete('/courses/{courseId}/modules/{moduleId}/lessons/{lessonId}', [TeacherDashboardController::class, 'destroyLesson'])->name('lessons.destroy');
        
        // Student Activity Monitoring (OPTIMIZED with Redis Cache)
        Route::get('/students', [TeacherDashboardController::class, 'students'])->name('students');
        Route::get('/students/{studentId}', [TeacherDashboardController::class, 'studentDetail'])->name('students.show');
        
        // API endpoints for student monitoring (moved from api.php untuk web session support)
        Route::get('/api/class/summary', [TeacherDashboardController::class, 'getClassSummary']);
        Route::get('/api/students/today-progress', [TeacherDashboardController::class, 'getTodayProgress']);
        Route::get('/api/students/{userId}/progress', [TeacherDashboardController::class, 'getStudentProgress']);
        
        // Quiz Grading (uses existing method)
        Route::get('/quiz-grading', [TeacherDashboardController::class, 'quizGrading'])->name('quiz-grading');
        
        Route::get('/document-imports', [TeacherDashboardController::class, 'documentImports'])->name('document-imports');
        Route::get('/analytics', [TeacherDashboardController::class, 'analytics'])->name('analytics');
    });
    
    // Admin Dashboard Routes
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
        Route::get('/users/{userId}', [AdminDashboardController::class, 'userDetail'])->name('users.show');
        
        // Courses Management (Admin can edit any course)
        Route::get('/courses', [AdminDashboardController::class, 'courses'])->name('courses');
        Route::get('/courses/create', [AdminDashboardController::class, 'createCourse'])->name('courses.create');
        Route::post('/courses', [AdminDashboardController::class, 'storeCourse'])->name('courses.store');
        Route::get('/courses/{id}/edit', [AdminDashboardController::class, 'editCourse'])->name('courses.edit');
        Route::put('/courses/{id}', [AdminDashboardController::class, 'updateCourse'])->name('courses.update');
        Route::delete('/courses/{id}', [AdminDashboardController::class, 'destroyCourse'])->name('courses.destroy');
        
        // Articles Management (Admin can edit any article)
        Route::get('/articles', [AdminDashboardController::class, 'articles'])->name('articles');
        Route::get('/articles/create', [AdminDashboardController::class, 'createArticle'])->name('articles.create');
        Route::post('/articles', [AdminDashboardController::class, 'storeArticle'])->name('articles.store');
        Route::get('/articles/{id}/edit', [AdminDashboardController::class, 'editArticle'])->name('articles.edit');
        Route::put('/articles/{id}', [AdminDashboardController::class, 'updateArticle'])->name('articles.update');
        Route::delete('/articles/{id}', [AdminDashboardController::class, 'destroyArticle'])->name('articles.destroy');
        
        Route::get('/students', [AdminDashboardController::class, 'students'])->name('students');
        Route::get('/students/{studentId}', [AdminDashboardController::class, 'studentDetail'])->name('students.show');
        Route::get('/quiz-attempts', [AdminDashboardController::class, 'quizAttempts'])->name('quiz-attempts');
        Route::get('/document-imports', [AdminDashboardController::class, 'documentImports'])->name('document-imports');
        Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
        Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
    });
    
    // Messages Routes
    Route::get('/messages', function () {
        $conversations = [
            ['id' => 1, 'name' => 'John Doe', 'last_message' => 'Thanks for your help with the assignment!', 'last_message_time' => '2m ago', 'unread_count' => 2, 'online' => true],
            ['id' => 2, 'name' => 'Jane Smith', 'last_message' => 'The quiz deadline has been extended', 'last_message_time' => '1h ago', 'unread_count' => 0, 'online' => false],
            ['id' => 3, 'name' => 'Web Dev Class', 'last_message' => 'Alice: See you in the next session!', 'last_message_time' => '3h ago', 'unread_count' => 5, 'online' => false, 'is_group' => true],
        ];
        return view('messages.index', compact('conversations'));
    })->name('messages.index');
    
    Route::get('/messages/{id}', function ($id) {
        return view('messages.show', ['id' => $id]);
    })->name('messages.show');
});

// Time Tracking API Routes (in web middleware for session support)
Route::middleware('auth')->prefix('api')->group(function () {
    $controller = \App\Http\Controllers\Api\TimeTrackingController::class;
    
    // Lesson time tracking
    Route::post('/lessons/{lesson}/track-time', [$controller, 'trackLessonTime']);
    Route::get('/lessons/{lesson}/time', [$controller, 'getLessonTime']);
    
    // Learning goal time tracking
    Route::post('/learning-goals/{goal}/track-time', [$controller, 'trackGoalTime']);
    Route::get('/learning-goals/{goal}/time', [$controller, 'getGoalTime']);
    
    // Article time tracking
    Route::post('/articles/{article}/track-time', [$controller, 'trackArticleTime']);
    Route::get('/articles/{article}/time', [$controller, 'getArticleTime']);
    
    // Daily study time (for learning goals progress)
    Route::get('/daily-study-time', [$controller, 'getDailyStudyTime']);
    
    // User study statistics
    Route::get('/user/study-stats', [$controller, 'getUserStats']);
    Route::get('/user/today-stats', [$controller, 'getTodayStats']);
});

require __DIR__.'/auth.php';
