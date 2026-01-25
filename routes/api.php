<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Api\TimeTrackingController;
use App\Http\Controllers\TeacherDashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/auth/register', [RegisterController::class, 'register']);
    Route::post('/auth/login', [LoginController::class, 'login']);
    
    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [LogoutController::class, 'logout']);
        Route::get('/auth/me', [LoginController::class, 'me']);
        Route::patch('/auth/profile', [LoginController::class, 'updateProfile']);
    });
});

// Time Tracking API (MOVED TO web.php for session support)
// These routes are now in web.php with 'api' prefix
/*
Route::middleware('auth')->group(function () {
    // Lesson time tracking
    Route::post('/lessons/{lesson}/track-time', [TimeTrackingController::class, 'trackLessonTime']);
    Route::get('/lessons/{lesson}/time', [TimeTrackingController::class, 'getLessonTime']);
    
    // Learning goal time tracking
    Route::post('/learning-goals/{goal}/track-time', [TimeTrackingController::class, 'trackGoalTime']);
    Route::get('/learning-goals/{goal}/time', [TimeTrackingController::class, 'getGoalTime']);
    
    // Article time tracking
    Route::post('/articles/{article}/track-time', [TimeTrackingController::class, 'trackArticleTime']);
    Route::get('/articles/{article}/time', [TimeTrackingController::class, 'getArticleTime']);
    
    // User study statistics
    Route::get('/user/study-stats', [TimeTrackingController::class, 'getUserStats']);
});
*/

// Note: Teacher API routes moved to web.php for session authentication support

