# Teacher & Admin Dashboard Implementation Guide

## Overview

This guide documents the complete Teacher and Admin Dashboard system for SEMPAT LMS. The system provides role-based dashboards with content management capabilities, student monitoring, quiz grading, and analytics.

## Table of Contents

1. [Architecture](#architecture)
2. [Features](#features)
3. [Installation](#installation)
4. [Usage](#usage)
5. [API Reference](#api-reference)
6. [Additional Feature Ideas](#additional-feature-ideas)

---

## Architecture

### Role-Based Access Control

The system uses a three-tier role structure:

- **Admin**: Full system access, manage all users and content
- **Teacher**: Manage own content, view enrolled students, grade quizzes
- **Student**: Access enrolled courses, submit work, view own progress

### Dashboard Routing

When users log in, they are automatically redirected to the appropriate dashboard:

```php
Route::get('/dashboard', function () {
    $user = Auth::user();
    
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }
    
    if ($user->hasRole('teacher')) {
        return redirect()->route('teacher.dashboard');
    }
    
    // Default: Student dashboard
    return view('dashboard', ...);
});
```

### Controllers

#### **TeacherDashboardController**
- Location: `app/Http/Controllers/TeacherDashboardController.php`
- Purpose: Handle all teacher-specific dashboard operations
- Authorization: Requires `teacher` role

#### **AdminDashboardController**
- Location: `app/Http/Controllers/AdminDashboardController.php`
- Purpose: Handle all admin-specific dashboard operations
- Authorization: Requires `admin` role

### Content Ownership Model

**Teachers:**
- Can only CRUD content they created (`created_by = user_id`)
- Can only view students enrolled in their courses
- Can only grade quizzes from their courses

**Admins:**
- Can CRUD all content regardless of creator
- Can view all users and students
- Can access system-wide statistics and settings

---

## Features

### Teacher Dashboard

#### 📊 Dashboard Overview
**Route:** `/teacher/dashboard`  
**Controller:** `TeacherDashboardController@index`

**Statistics Cards:**
- Total courses created
- Total articles written
- Total students in courses
- Pending quiz grading

**Widgets:**
- Recent courses with enrollment counts
- Student engagement metrics (last 30 days)
  - Active students
  - Lesson completions
  - Quiz attempts
- Pending quiz grading list
- Recent student activity feed

**Quick Actions:**
- Create new course
- Write new article
- Import document
- View analytics

#### 📚 Course Management
**Route:** `/teacher/courses`  
**Controller:** `TeacherDashboardController@courses`

**Features:**
- List all courses created by teacher
- Filter by status (published, draft, archived)
- Search by title
- View enrollment count and module count
- Statistics overview

**Restrictions:**
- Teachers can only see courses where `created_by = their user_id`

#### 📝 Article Management
**Route:** `/teacher/articles`  
**Controller:** `TeacherDashboardController@articles`

**Features:**
- List all articles created by teacher
- Filter by status (published, draft)
- View total views count
- Statistics overview

**Restrictions:**
- Teachers can only see articles where `created_by = their user_id`

#### 👥 Student Management
**Route:** `/teacher/students`  
**Controller:** `TeacherDashboardController@students`

**Features:**
- View all students enrolled in teacher's courses
- See enrollment count per student
- View average progress percentage
- See total study time

**Student Detail View:**
**Route:** `/teacher/students/{studentId}`  
**Controller:** `TeacherDashboardController@studentDetail`

**Features:**
- Course enrollments with progress
- Learning goals (last 5)
- Journal entries (last 5)
- Quiz attempts (last 10) with scores

**Privacy:**
- Teachers can only view students enrolled in their courses
- Access is verified before displaying data

#### ✍️ Quiz Grading
**Route:** `/teacher/quiz-grading`  
**Controller:** `TeacherDashboardController@quizGrading`

**Features:**
- View all quiz attempts from teacher's quizzes
- Filter: All / Pending Grading
- See student name, quiz title, and attempt time
- Statistics: Total attempts, pending grading, graded

**Note:** This shows the list. Actual grading interface requires separate implementation.

#### 📄 Document Imports
**Route:** `/teacher/document-imports`  
**Controller:** `TeacherDashboardController@documentImports`

**Features:**
- View all document imports by teacher
- Statistics: total, completed, processing, failed
- Integration with existing Document Import system

#### 📈 Analytics
**Route:** `/teacher/analytics`  
**Controller:** `TeacherDashboardController@analytics`

**Metrics:**
- Course performance (enrollments, average progress)
- Student completion rates
- Quiz performance (average score, pass rate)
- Monthly student activity (last 6 months)

---

### Admin Dashboard

#### 🛡️ Dashboard Overview
**Route:** `/admin/dashboard`  
**Controller:** `AdminDashboardController@index`

**System Statistics:**
- Total users
- Total courses
- Total enrollments
- Active students (last 30 days)
- Total articles
- Total quizzes

**User Distribution:**
- Students count
- Teachers count
- Admins count

**Widgets:**
- System activity (last 30 days)
  - New users
  - New courses
  - New enrollments
  - Quiz attempts
- Top performing courses (by enrollment)
- Top teachers (by student count)
- Recent user registrations
- Course statistics (published/draft/archived)

**Quick Actions:**
- Manage users
- Manage courses
- View analytics
- System settings

#### 👥 User Management
**Route:** `/admin/users`  
**Controller:** `AdminDashboardController@users`

**Features:**
- List all users with roles
- Filter by role (student, teacher, admin)
- Search by name or email
- View registration date
- Statistics by role

**User Detail View:**
**Route:** `/admin/users/{userId}`  
**Controller:** `AdminDashboardController@userDetail`

**For Students:**
- Enrollment count and completion rate
- Quiz attempts count
- Learning goals count
- Journal entries count
- Recent activity

**For Teachers:**
- Courses created
- Articles created
- Total students taught
- Document imports

#### 📚 Course Management (Admin)
**Route:** `/admin/courses`  
**Controller:** `AdminDashboardController@courses`

**Features:**
- View ALL courses regardless of creator
- Filter by status
- Search by title
- View creator, enrollment count, module count
- Statistics overview

#### 📝 Article Management (Admin)
**Route:** `/admin/articles`  
**Controller:** `AdminDashboardController@articles`

**Features:**
- View ALL articles regardless of creator
- Filter by status
- View creator, category, views count
- Statistics overview

#### 👨‍🎓 Student Overview
**Route:** `/admin/students`  
**Controller:** `AdminDashboardController@students`

**Features:**
- List all users with student role
- Search by name or email
- View enrollment count per student

**Student Detail:**
**Route:** `/admin/students/{studentId}`  
**Controller:** `AdminDashboardController@studentDetail`

**Full student profile:**
- All course enrollments with progress
- All learning goals (last 10)
- All journal entries (last 10)
- All quiz attempts (last 15) with scores

#### 📝 Quiz Attempts Overview
**Route:** `/admin/quiz-attempts`  
**Controller:** `AdminDashboardController@quizAttempts`

**Features:**
- View ALL quiz attempts system-wide
- Filter: All / Pending Grading / Graded
- Statistics:
  - Total attempts
  - Pending grading count
  - Graded count
  - Average score percentage

#### 📄 Document Imports (Admin)
**Route:** `/admin/document-imports`  
**Controller:** `AdminDashboardController@documentImports`

**Features:**
- View ALL document imports system-wide
- Statistics: total, completed, processing, failed

#### 📊 System Analytics
**Route:** `/admin/analytics`  
**Controller:** `AdminDashboardController@analytics`

**Comprehensive Metrics:**
- User growth (last 12 months)
- Course enrollment trends (last 6 months)
- Course completion rates
- Quiz performance overview (average score, pass rate)
- Most active students (by study time)
- Content creation trends (last 3 months)

#### ⚙️ System Settings
**Route:** `/admin/settings`  
**Controller:** `AdminDashboardController@settings`

**System Health Checks:**
- Database connection status
- Storage space availability
- Queue status (pending/failed jobs)

**Recent System Errors:**
- Last 5 failed jobs from `failed_jobs` table

---

## Installation

### 1. Routes

The routes are already configured in `routes/web.php`:

```php
// Teacher Dashboard Routes
Route::prefix('teacher')->name('teacher.')->middleware('role:teacher')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::get('/courses', [TeacherDashboardController::class, 'courses'])->name('courses');
    Route::get('/articles', [TeacherDashboardController::class, 'articles'])->name('articles');
    Route::get('/students', [TeacherDashboardController::class, 'students'])->name('students');
    Route::get('/students/{studentId}', [TeacherDashboardController::class, 'studentDetail'])->name('students.show');
    Route::get('/quiz-grading', [TeacherDashboardController::class, 'quizGrading'])->name('quiz-grading');
    Route::get('/document-imports', [TeacherDashboardController::class, 'documentImports'])->name('document-imports');
    Route::get('/analytics', [TeacherDashboardController::class, 'analytics'])->name('analytics');
});

// Admin Dashboard Routes
Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
    Route::get('/users/{userId}', [AdminDashboardController::class, 'userDetail'])->name('users.show');
    Route::get('/courses', [AdminDashboardController::class, 'courses'])->name('courses');
    Route::get('/articles', [AdminDashboardController::class, 'articles'])->name('articles');
    Route::get('/students', [AdminDashboardController::class, 'students'])->name('students');
    Route::get('/students/{studentId}', [AdminDashboardController::class, 'studentDetail'])->name('students.show');
    Route::get('/quiz-attempts', [AdminDashboardController::class, 'quizAttempts'])->name('quiz-attempts');
    Route::get('/document-imports', [AdminDashboardController::class, 'documentImports'])->name('document-imports');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
});
```

### 2. Controllers

Both controllers are already created:
- `app/Http/Controllers/TeacherDashboardController.php`
- `app/Http/Controllers/AdminDashboardController.php`

### 3. Views

Dashboard views are created:
- `resources/views/teacher/dashboard.blade.php`
- `resources/views/admin/dashboard.blade.php`

**Additional views needed** (to be created as needed):
- Teacher:
  - `resources/views/teacher/courses/index.blade.php`
  - `resources/views/teacher/articles/index.blade.php`
  - `resources/views/teacher/students/index.blade.php`
  - `resources/views/teacher/students/show.blade.php`
  - `resources/views/teacher/quizzes/grading.blade.php`
  - `resources/views/teacher/analytics.blade.php`
  
- Admin:
  - `resources/views/admin/users/index.blade.php`
  - `resources/views/admin/users/show.blade.php`
  - `resources/views/admin/courses/index.blade.php`
  - `resources/views/admin/articles/index.blade.php`
  - `resources/views/admin/students/index.blade.php`
  - `resources/views/admin/students/show.blade.php`
  - `resources/views/admin/quizzes/attempts.blade.php`
  - `resources/views/admin/analytics.blade.php`
  - `resources/views/admin/settings.blade.php`

### 4. Middleware

Ensure role middleware is configured in `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    // ...
    'role' => \App\Http\Middleware\CheckRole::class,
];
```

If not exists, create `app/Http/Middleware/CheckRole.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check() || !Auth::user()->hasRole($role)) {
            abort(403, "Access denied. {$role} role required.");
        }

        return $next($request);
    }
}
```

---

## Usage

### For Teachers

1. **Login** with teacher credentials
2. Automatically redirected to `/teacher/dashboard`
3. View statistics and recent activity
4. Navigate using sidebar or quick actions:
   - **My Courses**: Manage your courses
   - **My Articles**: Manage your articles
   - **My Students**: View enrolled students
   - **Quiz Grading**: Grade pending quizzes
   - **Analytics**: View detailed performance metrics

### For Admins

1. **Login** with admin credentials
2. Automatically redirected to `/admin/dashboard`
3. View system-wide statistics
4. Navigate using sidebar or quick actions:
   - **Manage Users**: View all users, assign roles
   - **Manage Courses**: Moderate all courses
   - **View Analytics**: System-wide analytics
   - **System Settings**: Monitor system health

### For Students

1. **Login** with student credentials
2. See regular student dashboard at `/dashboard`
3. Access courses, articles, progress tracking

---

## API Reference

### TeacherDashboardController Methods

```php
// Dashboard overview
public function index()

// List teacher's courses
public function courses(Request $request)

// List teacher's articles
public function articles(Request $request)

// List students in teacher's courses
public function students(Request $request)

// View individual student detail
public function studentDetail($studentId)

// List quiz attempts needing grading
public function quizGrading(Request $request)

// List teacher's document imports
public function documentImports()

// View analytics for teacher
public function analytics()
```

### AdminDashboardController Methods

```php
// Dashboard overview
public function index()

// User management
public function users(Request $request)
public function userDetail($userId)

// Course management
public function courses(Request $request)

// Article management
public function articles(Request $request)

// Student overview
public function students(Request $request)
public function studentDetail($studentId)

// Quiz attempts
public function quizAttempts(Request $request)

// System analytics
public function analytics()

// System settings
public function settings()

// Document imports (all)
public function documentImports()
```

---

## Additional Feature Ideas

### For Teachers

#### 1. **Announcements System**
- Broadcast messages to students enrolled in specific courses
- Schedule announcements for future dates
- Track read/unread status

#### 2. **Assignment Management**
- Create assignments with deadlines
- Accept file submissions from students
- Provide feedback and grades
- Support for peer review

#### 3. **Gradebook**
- Comprehensive grade management
- Weighted categories (quizzes, assignments, participation)
- Export grades to CSV/Excel
- Grade curve tools

#### 4. **Content Analytics**
- Track which lessons students struggle with (low completion, long time)
- Identify popular/unpopular content
- Heat maps of student engagement

#### 5. **Live Sessions**
- Schedule and conduct live video sessions
- Record sessions for later viewing
- Attendance tracking
- Q&A during sessions

#### 6. **Bulk Operations**
- Bulk grade adjustments
- Bulk student communications
- Bulk content publishing/archiving

#### 7. **Templates & Reusability**
- Save courses as templates
- Duplicate lessons/modules
- Import content from previous courses

#### 8. **Student Groups**
- Create study groups within courses
- Assign group projects
- Group discussions and collaboration

#### 9. **Calendar Integration**
- Course schedule view
- Assignment due dates
- Quiz availability windows
- Export to Google Calendar/Outlook

#### 10. **Feedback Collection**
- Course evaluation surveys
- Lesson feedback forms
- Anonymous suggestion box

### For Admins

#### 1. **Advanced User Management**
- Bulk user import (CSV)
- User account suspension/activation
- Password reset for users
- User activity logs
- IP-based access restrictions

#### 2. **System Monitoring Dashboard**
- Real-time active users
- Server resource usage (CPU, memory, disk)
- API response times
- Error rate monitoring
- Queue performance metrics

#### 3. **Content Moderation**
- Review flagged content
- Approve/reject course publications
- Remove inappropriate comments
- Content compliance checks

#### 4. **Backup & Restore**
- Automated database backups
- Manual backup triggers
- Restore from backup points
- Export user data for GDPR compliance

#### 5. **Email Campaign Manager**
- Send newsletters to all users or by role
- Drip email campaigns for new users
- Re-engagement emails for inactive users
- Course completion certificates via email

#### 6. **Reports & Exports**
- Custom report builder
- Scheduled report generation
- Export data to PDF/Excel
- Financial reports (if payment system exists)

#### 7. **Role & Permission Management**
- Create custom roles
- Granular permission assignment
- Role-based content access
- Temporary role assignments

#### 8. **Audit Logs**
- Track all admin actions
- User login/logout logs
- Content modification history
- Data access logs

#### 9. **System Configuration**
- Site-wide settings (name, logo, timezone)
- Email SMTP configuration
- Storage provider selection (local/S3/etc.)
- Cache management
- Maintenance mode toggle

#### 10. **API Management**
- API key generation for integrations
- Rate limiting configuration
- API usage analytics
- Webhook management

#### 11. **Certificate Management**
- Design certificate templates
- Auto-generate on course completion
- Digital signature/verification
- Certificate revocation

#### 12. **Gamification Settings**
- Configure badges and achievements
- Set point values for activities
- Leaderboard visibility settings
- Reward thresholds

#### 13. **Integration Management**
- LTI integration for external LMS
- SSO/OAuth configuration
- Zoom/Google Meet integration
- Slack/Discord notifications

#### 14. **Resource Management**
- Disk usage by user/course
- Media library management
- Bulk file operations
- CDN configuration

---

## Security Considerations

### Content Ownership Verification

Always verify ownership before allowing actions:

```php
// Good: Verify ownership
$course = Course::where('id', $id)
    ->where('created_by', Auth::id())
    ->firstOrFail();

// Bad: No verification
$course = Course::findOrFail($id);
```

### Student Access Verification

Verify student is enrolled before showing data:

```php
$hasAccess = Enrollment::whereHas('course', function($q) use ($teacherId) {
    $q->where('created_by', $teacherId);
})->where('user_id', $studentId)->exists();

if (!$hasAccess) {
    abort(403, 'This student is not enrolled in your courses.');
}
```

### Admin vs Teacher Permissions

- **Teachers**: Use `created_by = Auth::id()` filters
- **Admins**: No ownership filters, full access

---

## Performance Optimization

### Database Indexes

Ensure indexes exist on frequently queried columns:

```sql
-- Courses
CREATE INDEX idx_courses_created_by ON courses(created_by);
CREATE INDEX idx_courses_status ON courses(status);

-- Articles
CREATE INDEX idx_articles_created_by ON articles(created_by);
CREATE INDEX idx_articles_status ON articles(status);

-- Enrollments
CREATE INDEX idx_enrollments_user_course ON enrollments(user_id, course_id);
CREATE INDEX idx_enrollments_last_accessed ON enrollments(last_accessed_at);

-- Quiz Attempts
CREATE INDEX idx_quiz_attempts_user ON quiz_attempts(user_id);
CREATE INDEX idx_quiz_attempts_graded ON quiz_attempts(graded_at);
```

### Eager Loading

Always use eager loading to avoid N+1 queries:

```php
// Good
$courses = Course::with(['creator', 'modules'])
    ->where('created_by', $teacherId)
    ->get();

// Bad
$courses = Course::where('created_by', $teacherId)->get();
foreach ($courses as $course) {
    $creator = $course->creator; // N+1 query
}
```

### Caching

Cache expensive queries:

```php
$stats = Cache::remember("teacher.{$teacherId}.stats", 3600, function() use ($teacherId) {
    return [
        'total_courses' => Course::where('created_by', $teacherId)->count(),
        'total_students' => $this->getTotalStudents($teacherId),
        // ...
    ];
});
```

---

## Testing

### Teacher Dashboard Tests

```php
/** @test */
public function teacher_can_view_dashboard()
{
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    
    $this->actingAs($teacher)
        ->get('/teacher/dashboard')
        ->assertStatus(200)
        ->assertSee('Teacher Dashboard');
}

/** @test */
public function teacher_cannot_see_others_courses()
{
    $teacher1 = User::factory()->create();
    $teacher1->assignRole('teacher');
    
    $teacher2 = User::factory()->create();
    $teacher2->assignRole('teacher');
    
    $course = Course::factory()->create(['created_by' => $teacher2->id]);
    
    $this->actingAs($teacher1)
        ->get('/teacher/courses')
        ->assertDontSee($course->title);
}
```

### Admin Dashboard Tests

```php
/** @test */
public function admin_can_view_all_courses()
{
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    
    $course1 = Course::factory()->create();
    $course2 = Course::factory()->create();
    
    $this->actingAs($admin)
        ->get('/admin/courses')
        ->assertSee($course1->title)
        ->assertSee($course2->title);
}

/** @test */
public function non_admin_cannot_access_admin_dashboard()
{
    $student = User::factory()->create();
    
    $this->actingAs($student)
        ->get('/admin/dashboard')
        ->assertStatus(403);
}
```

---

## Troubleshooting

### Dashboard Not Loading

**Issue:** Blank page or 500 error  
**Solution:**
1. Check logs: `storage/logs/laravel.log`
2. Verify database connection
3. Ensure user has correct role: `$user->hasRole('teacher')`

### No Students Showing

**Issue:** Teacher's student list is empty  
**Solution:**
1. Verify students are enrolled: Check `enrollments` table
2. Ensure `created_by` field matches teacher's ID in `courses` table
3. Check SQL query in `getTeacherStudents()` method

### Permission Denied Errors

**Issue:** 403 Forbidden errors  
**Solution:**
1. Check role assignment: `$user->roles`
2. Verify middleware is applied: Check route definition
3. Ensure `CheckRole` middleware is registered in Kernel

---

## Future Enhancements

1. **Real-time Notifications**: Use WebSockets for live updates
2. **Mobile App**: Native iOS/Android apps with push notifications
3. **AI-Powered Insights**: Suggest interventions for struggling students
4. **Advanced Search**: Full-text search across all content
5. **Multi-language Support**: i18n for international users
6. **Accessibility**: WCAG 2.1 AA compliance
7. **White-label**: Allow institutions to customize branding
8. **LTI Integration**: Connect with Canvas, Blackboard, Moodle

---

## Conclusion

The Teacher & Admin Dashboard system provides comprehensive management capabilities for SEMPAT LMS. Teachers can manage their content and monitor student progress, while admins have full system oversight and configuration abilities.

For additional support or feature requests, please contact the development team.

**Version:** 1.0.0  
**Last Updated:** January 2025  
**Author:** SEMPAT LMS Development Team

---

## ⏱️ Study Time Integration in Teacher Dashboard (Detail)

### Status: FULLY INTEGRATED
Halaman `/teacher/students` dan detail siswa kini menampilkan waktu belajar dari SEMUA sumber:
- Lessons (enrollments)
- Articles (article readings)
- Learning Goals (study sessions)

### Tampilan di Dashboard

#### 1. List Students (`/teacher/students`)
- **Total Study Time Card**: Menampilkan total waktu (HH:MM) dan breakdown per sumber
- **Contoh:**
```
┌─────────────────────────────┐
│ Total Study Time           │
│   03:45                    │  ← Total dari semua sumber
│ 📚 120m 📰 45m 🎯 60m        │  ← Breakdown per sumber
└─────────────────────────────┘
```

#### 2. Student Detail (`/teacher/students/{id}`)
- **Stats Card**: Total waktu belajar prominent, tombol "View Details →"
- **Breakdown Panel**: Breakdown lessons, articles, goals, distribusi waktu (bar chart)
- **Contoh:**
```
┌──────────────────────────────────────────────────────────────┐
│ 📊 Study Time Breakdown                    ✕ Close          │
├──────────────────────────────────────────────────────────────┤
│  📚 Lessons        📰 Articles       🎯 Goals                │
│    02:00            00:45            01:00                  │
│  120 minutes      45 minutes      60 minutes                │
│                                                            │
│  💎 Total Time: 03:45 (225 minutes)                        │
│                                                            │
│  Time Distribution:                                        │
│  ████████████░░░░░░░░░░░░░░░░░░░░░░░░░░░░░                │
│  53.3% Lessons | 20% Articles | 26.7% Goals                │
└──────────────────────────────────────────────────────────────┘
```

### Backend Processing (TeacherDashboardController)
- Query base: lessons time dari enrollments
- Tambah: articles time dari article_readings
- Tambah: goals time dari learning_goals
- Semua waktu di-convert ke menit/detik untuk display
- API endpoint: `/teacher/api/students/today-progress`, `/teacher/api/students/{id}/progress`

### Frontend
- Stat cards, breakdown, dan distribusi waktu update otomatis
- Data diambil via AJAX/fetch dari API
- Responsive & real-time
