# 🚀 API Design & Optimization - LMS SEMPAT
## RESTful API Architecture & Performance Best Practices

**Versi:** 1.0  
**Tanggal:** 12 Januari 2026  
**API Style:** RESTful with JSON responses  
**Base URL (Dev):** `http://localhost/sempat-app/public/api/v1`

---

## 📋 Table of Contents

1. [API Design Principles](#api-design-principles)
2. [Authentication & Authorization](#authentication--authorization)
3. [API Endpoints](#api-endpoints)
4. [Request/Response Format](#requestresponse-format)
5. [Error Handling](#error-handling)
6. [Optimization Strategies](#optimization-strategies)
7. [Rate Limiting](#rate-limiting)
8. [Caching Strategy](#caching-strategy)

---

## 🎯 API Design Principles

### RESTful Standards

1. **Resource-Based URLs**
   - Use nouns, not verbs: `/users` not `/getUsers`
   - Plural naming: `/courses`, `/articles`, `/modules`
   - Nested resources: `/courses/{id}/modules`

2. **HTTP Methods Semantically**
   - `GET` - Retrieve resources (safe, idempotent)
   - `POST` - Create new resources
   - `PUT` - Full update (replace entire resource)
   - `PATCH` - Partial update (modify specific fields)
   - `DELETE` - Remove resources (idempotent)

3. **Stateless Architecture**
   - No server-side session storage
   - Each request contains all needed information
   - Token-based authentication (Laravel Sanctum)

4. **Versioning**
   - URL-based versioning: `/api/v1/`, `/api/v2/`
   - Major version changes only
   - Backward compatibility within versions

5. **HATEOAS Principles** (future enhancement)
   - Include links to related resources
   - Self-documenting API
   - Discoverability

---

## 🔐 Authentication & Authorization

### Authentication Flow

```
1. User Login
   POST /api/v1/auth/login
   Body: { email, password }
   ↓
2. Server Validates Credentials
   ↓
3. Generate Token (Laravel Sanctum)
   ↓
4. Return Token
   Response: { token, user, expires_at }
   ↓
5. Client Stores Token
   ↓
6. Subsequent Requests Include Token
   Header: Authorization: Bearer {token}
```

### Auth Endpoints

```
Authentication Endpoints:
├── POST   /api/v1/auth/register          # User registration
├── POST   /api/v1/auth/login             # User login
├── POST   /api/v1/auth/logout            # User logout
├── POST   /api/v1/auth/refresh           # Refresh token
├── POST   /api/v1/auth/forgot-password   # Request password reset
├── POST   /api/v1/auth/reset-password    # Reset password
├── GET    /api/v1/auth/me                # Get current user
└── PATCH  /api/v1/auth/profile           # Update profile
```

### Authorization Levels

**1. Public Endpoints** (no authentication required)
- Course catalog browsing
- Article listing (published only)
- Public content preview

**2. Authenticated Endpoints** (valid token required)
- User profile access
- Enrollment management
- Content access for enrolled users

**3. Role-Based Endpoints** (specific role required)
- Admin: User management, system settings
- Teacher: Content creation, grading
- Student: Learning content access

**4. Permission-Based Endpoints** (specific permission required)
- Granular access control
- Module-specific permissions
- Feature flags

---

## 📡 API Endpoints

### 1. User Management API

#### User Endpoints
```
GET    /api/v1/users                      # List all users (admin)
POST   /api/v1/users                      # Create user (admin)
GET    /api/v1/users/{id}                 # Get user details
PATCH  /api/v1/users/{id}                 # Update user
DELETE /api/v1/users/{id}                 # Delete user (soft delete)
GET    /api/v1/users/{id}/enrollments     # Get user enrollments
GET    /api/v1/users/{id}/progress        # Get learning progress
GET    /api/v1/users/{id}/activities      # Get activity log
```

**Query Parameters (GET /users):**
- `?page=1` - Pagination
- `?per_page=20` - Items per page
- `?role=student` - Filter by role
- `?search=john` - Search by name/email
- `?sort_by=created_at&order=desc` - Sorting

**Example Request:**
```http
GET /api/v1/users?page=1&per_page=20&role=student&search=ahmad
Authorization: Bearer {token}
```

**Example Response:**
```json
{
  "status": "success",
  "message": "Users retrieved successfully",
  "data": {
    "users": [
      {
        "id": 1,
        "uuid": "550e8400-e29b-41d4-a716-446655440000",
        "username": "ahmad.student",
        "email": "ahmad@example.com",
        "first_name": "Ahmad",
        "last_name": "Fauzi",
        "roles": ["student"],
        "is_active": true,
        "last_login_at": "2026-01-12T10:30:00Z",
        "created_at": "2026-01-01T00:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 150,
      "total_pages": 8,
      "has_more": true
    }
  },
  "meta": {
    "timestamp": "2026-01-12T14:30:00Z",
    "version": "1.0"
  }
}
```

---

### 2. FSDL (Course Management) API

#### Course Endpoints
```
GET    /api/v1/courses                    # List all courses
POST   /api/v1/courses                    # Create course (teacher)
GET    /api/v1/courses/{id}               # Get course details
PATCH  /api/v1/courses/{id}               # Update course
DELETE /api/v1/courses/{id}               # Delete course
GET    /api/v1/courses/{id}/modules       # Get course modules
POST   /api/v1/courses/{id}/enroll        # Enroll student
DELETE /api/v1/courses/{id}/enroll        # Unenroll student
GET    /api/v1/courses/{id}/students      # Get enrolled students
GET    /api/v1/courses/{id}/analytics     # Get course analytics
POST   /api/v1/courses/{id}/publish       # Publish course
```

**Query Parameters (GET /courses):**
- `?status=published` - Filter by status
- `?category=mathematics` - Filter by category
- `?level=beginner` - Filter by difficulty
- `?created_by={user_id}` - Filter by creator
- `?enrolled=true` - Show only enrolled courses (current user)
- `?include=modules,lessons` - Include related data
- `?fields=id,title,thumbnail` - Sparse fieldsets

**Example Request with Includes:**
```http
GET /api/v1/courses/123?include=modules,modules.lessons
Authorization: Bearer {token}
```

**Example Response:**
```json
{
  "status": "success",
  "data": {
    "course": {
      "id": 123,
      "uuid": "550e8400-e29b-41d4-a716-446655440001",
      "title": "Introduction to Web Development",
      "slug": "introduction-to-web-development",
      "description": "Learn the fundamentals of web development",
      "thumbnail": "/storage/courses/thumbnails/intro-web-dev.jpg",
      "level": "beginner",
      "duration_hours": 40,
      "is_published": true,
      "published_at": "2026-01-10T08:00:00Z",
      "enrollment_count": 250,
      "created_by": {
        "id": 5,
        "name": "Prof. Budi Santoso",
        "avatar": "/storage/avatars/budi.jpg"
      },
      "modules": [
        {
          "id": 1,
          "title": "HTML Basics",
          "order": 1,
          "lessons_count": 5,
          "lessons": [
            {
              "id": 1,
              "title": "Introduction to HTML",
              "duration_minutes": 30,
              "is_preview": true
            }
          ]
        }
      ],
      "stats": {
        "total_modules": 8,
        "total_lessons": 42,
        "total_quizzes": 10,
        "completion_rate": 68.5
      }
    }
  }
}
```

#### Module Endpoints
```
GET    /api/v1/modules/{id}               # Get module details
POST   /api/v1/modules                    # Create module
PATCH  /api/v1/modules/{id}               # Update module
DELETE /api/v1/modules/{id}               # Delete module
PATCH  /api/v1/modules/{id}/reorder       # Reorder module
GET    /api/v1/modules/{id}/lessons       # Get module lessons
```

#### Lesson Endpoints
```
GET    /api/v1/lessons/{id}               # Get lesson details
POST   /api/v1/lessons                    # Create lesson
PATCH  /api/v1/lessons/{id}               # Update lesson
DELETE /api/v1/lessons/{id}               # Delete lesson
POST   /api/v1/lessons/{id}/complete      # Mark lesson complete
GET    /api/v1/lessons/{id}/next          # Get next lesson
GET    /api/v1/lessons/{id}/previous      # Get previous lesson
```

**Lesson Completion Request:**
```http
POST /api/v1/lessons/42/complete
Authorization: Bearer {token}
Content-Type: application/json

{
  "time_spent_seconds": 1800,
  "notes": "Completed HTML basics lesson, very helpful!"
}
```

**Lesson Completion Response:**
```json
{
  "status": "success",
  "message": "Lesson marked as complete",
  "data": {
    "completion": {
      "lesson_id": 42,
      "completed_at": "2026-01-12T15:30:00Z",
      "time_spent_seconds": 1800,
      "progress_percentage": 12.5
    },
    "next_lesson": {
      "id": 43,
      "title": "HTML Elements",
      "url": "/api/v1/lessons/43"
    },
    "module_progress": {
      "completed_lessons": 5,
      "total_lessons": 8,
      "percentage": 62.5
    }
  }
}
```

---

### 3. Quiz & Assessment API

#### Quiz Endpoints
```
GET    /api/v1/quizzes/{id}               # Get quiz details
POST   /api/v1/quizzes                    # Create quiz (teacher)
PATCH  /api/v1/quizzes/{id}               # Update quiz
DELETE /api/v1/quizzes/{id}               # Delete quiz
POST   /api/v1/quizzes/{id}/start         # Start quiz attempt
POST   /api/v1/quizzes/{id}/submit        # Submit quiz answers
GET    /api/v1/quizzes/{id}/attempts      # Get user attempts
GET    /api/v1/quizzes/{id}/results/{attemptId}  # Get attempt results
```

**Start Quiz Request:**
```http
POST /api/v1/quizzes/15/start
Authorization: Bearer {token}
```

**Start Quiz Response:**
```json
{
  "status": "success",
  "data": {
    "attempt": {
      "id": 1234,
      "uuid": "550e8400-e29b-41d4-a716-446655440002",
      "quiz_id": 15,
      "attempt_number": 1,
      "started_at": "2026-01-12T15:45:00Z",
      "time_limit_minutes": 30,
      "expires_at": "2026-01-12T16:15:00Z"
    },
    "questions": [
      {
        "id": 101,
        "question_text": "What does HTML stand for?",
        "question_type": "multiple_choice",
        "options": [
          {
            "key": "A",
            "value": "Hyper Text Markup Language"
          },
          {
            "key": "B",
            "value": "High Tech Modern Language"
          },
          {
            "key": "C",
            "value": "Home Tool Markup Language"
          }
        ],
        "points": 1
      }
    ]
  }
}
```

**Submit Quiz Request:**
```http
POST /api/v1/quizzes/15/submit
Authorization: Bearer {token}
Content-Type: application/json

{
  "attempt_id": 1234,
  "answers": [
    {
      "question_id": 101,
      "answer": "A"
    },
    {
      "question_id": 102,
      "answer": "The <body> tag"
    }
  ]
}
```

**Submit Quiz Response:**
```json
{
  "status": "success",
  "message": "Quiz submitted successfully",
  "data": {
    "result": {
      "attempt_id": 1234,
      "submitted_at": "2026-01-12T16:10:00Z",
      "score": 8.5,
      "max_score": 10,
      "percentage": 85,
      "passed": true,
      "time_taken_seconds": 1500,
      "correct_answers": 9,
      "total_questions": 10
    },
    "feedback": "Great job! You passed the quiz."
  }
}
```

---

### 4. SPSDL (Article) API

#### Article Endpoints
```
GET    /api/v1/articles                   # List all articles
POST   /api/v1/articles                   # Create article (teacher)
GET    /api/v1/articles/{id}              # Get article details
PATCH  /api/v1/articles/{id}              # Update article
DELETE /api/v1/articles/{id}              # Delete article
POST   /api/v1/articles/{id}/like         # Like article
DELETE /api/v1/articles/{id}/like         # Unlike article
POST   /api/v1/articles/{id}/bookmark     # Bookmark article
DELETE /api/v1/articles/{id}/bookmark     # Remove bookmark
PATCH  /api/v1/articles/{id}/progress     # Update reading progress
GET    /api/v1/articles/recommended       # Get personalized recommendations
```

**Query Parameters (GET /articles):**
- `?category=programming` - Filter by category
- `?tags=html,css` - Filter by tags (comma-separated)
- `?difficulty=beginner` - Filter by difficulty
- `?featured=true` - Get featured articles
- `?popular=true` - Get popular articles (most views)
- `?recent=true` - Get recent articles

**Example Request:**
```http
GET /api/v1/articles?category=programming&tags=html,javascript&difficulty=beginner&page=1
Authorization: Bearer {token}
```

**Example Response:**
```json
{
  "status": "success",
  "data": {
    "articles": [
      {
        "id": 45,
        "uuid": "550e8400-e29b-41d4-a716-446655440003",
        "title": "Getting Started with HTML5",
        "slug": "getting-started-with-html5",
        "excerpt": "Learn the basics of HTML5 and modern web development",
        "thumbnail": "/storage/articles/html5-intro.jpg",
        "reading_time_minutes": 8,
        "difficulty_level": "beginner",
        "view_count": 1250,
        "like_count": 89,
        "published_at": "2026-01-05T10:00:00Z",
        "author": {
          "id": 5,
          "name": "Prof. Budi Santoso",
          "avatar": "/storage/avatars/budi.jpg"
        },
        "categories": ["Programming", "Web Development"],
        "tags": ["html", "html5", "web", "beginner"],
        "user_progress": {
          "progress_percentage": 60,
          "completed": false,
          "bookmarked": true,
          "liked": false
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 87,
      "total_pages": 5
    }
  }
}
```

**Update Reading Progress:**
```http
PATCH /api/v1/articles/45/progress
Authorization: Bearer {token}
Content-Type: application/json

{
  "progress_percentage": 75,
  "scroll_position": 2500,
  "time_spent_seconds": 420
}
```

---

### 5. Document Import API

#### Document Endpoints
```
POST   /api/v1/documents/upload           # Upload document (.docx/.doc)
GET    /api/v1/documents/{id}             # Get document details
GET    /api/v1/documents/{id}/html        # Get transformed HTML
DELETE /api/v1/documents/{id}             # Delete document
PATCH  /api/v1/documents/{id}             # Update document metadata
GET    /api/v1/documents/{id}/attachments # Get extracted attachments
POST   /api/v1/documents/{id}/transform   # Re-transform document
```

**Upload Document Request:**
```http
POST /api/v1/documents/upload
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "file": [binary file data],
  "documentable_type": "lesson",
  "documentable_id": 42,
  "auto_transform": true
}
```

**Upload Response:**
```json
{
  "status": "success",
  "message": "Document uploaded successfully",
  "data": {
    "document": {
      "id": 789,
      "uuid": "550e8400-e29b-41d4-a716-446655440004",
      "original_filename": "lesson-content.docx",
      "file_size_bytes": 2458624,
      "mime_type": "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
      "transformation_status": "processing",
      "uploaded_at": "2026-01-12T16:30:00Z",
      "processing_job_id": "job-12345"
    }
  }
}
```

**Transformation Complete Webhook/Notification:**
```json
{
  "event": "document.transformation.completed",
  "document_id": 789,
  "transformation_status": "completed",
  "html_url": "/api/v1/documents/789/html",
  "attachments_count": 5,
  "completed_at": "2026-01-12T16:32:00Z"
}
```

**Get Transformed HTML:**
```http
GET /api/v1/documents/789/html
Authorization: Bearer {token}
```

**HTML Response:**
```json
{
  "status": "success",
  "data": {
    "document_id": 789,
    "original_filename": "lesson-content.docx",
    "html_content": "<h1>Lesson Title</h1><p>Content here...</p><img src=\"/storage/documents/789/images/image1.jpg\" />",
    "metadata": {
      "author": "John Doe",
      "created_date": "2026-01-10",
      "modified_date": "2026-01-11",
      "word_count": 1250
    },
    "attachments": [
      {
        "id": 1,
        "type": "image",
        "filename": "image1.jpg",
        "url": "/storage/documents/789/images/image1.jpg",
        "thumbnail_url": "/storage/documents/789/images/thumbnails/image1.jpg"
      }
    ]
  }
}
```

---

### 6. Learning Analytics API

#### Analytics Endpoints
```
GET    /api/v1/analytics/dashboard        # User dashboard summary
GET    /api/v1/analytics/progress         # Detailed progress report
GET    /api/v1/analytics/time-tracking    # Learning time analysis
GET    /api/v1/analytics/assessments      # Assessment performance
GET    /api/v1/analytics/engagement       # Engagement metrics
GET    /api/v1/analytics/export           # Export reports (PDF/Excel)
```

**Dashboard Summary Request:**
```http
GET /api/v1/analytics/dashboard?time_range=30d
Authorization: Bearer {token}
```

**Dashboard Response:**
```json
{
  "status": "success",
  "data": {
    "summary": {
      "total_courses_enrolled": 5,
      "courses_completed": 2,
      "courses_in_progress": 3,
      "total_lessons_completed": 87,
      "total_hours_learned": 42.5,
      "current_streak_days": 7,
      "longest_streak_days": 15,
      "average_quiz_score": 82.5
    },
    "recent_activity": [
      {
        "type": "lesson_completed",
        "title": "JavaScript Basics",
        "course": "Web Development Fundamentals",
        "timestamp": "2026-01-12T14:30:00Z"
      }
    ],
    "upcoming_deadlines": [
      {
        "type": "assignment",
        "title": "Module 3 Project",
        "course": "Python Programming",
        "due_date": "2026-01-15T23:59:59Z",
        "days_remaining": 3
      }
    ],
    "recommendations": [
      {
        "type": "course",
        "id": 78,
        "title": "Advanced JavaScript",
        "reason": "Based on your completed courses"
      }
    ]
  }
}
```

---

### 7. Self-Directed Learning Features API

#### Learning Goals Endpoints
```
GET    /api/v1/goals                      # List user goals
POST   /api/v1/goals                      # Create goal
GET    /api/v1/goals/{id}                 # Get goal details
PATCH  /api/v1/goals/{id}                 # Update goal
DELETE /api/v1/goals/{id}                 # Delete goal
POST   /api/v1/goals/{id}/complete        # Mark goal complete
```

#### Learning Journal Endpoints
```
GET    /api/v1/journal/entries            # List journal entries
POST   /api/v1/journal/entries            # Create entry
GET    /api/v1/journal/entries/{id}       # Get entry
PATCH  /api/v1/journal/entries/{id}       # Update entry
DELETE /api/v1/journal/entries/{id}       # Delete entry
GET    /api/v1/journal/insights           # Get learning insights
```

**Create Journal Entry:**
```http
POST /api/v1/journal/entries
Authorization: Bearer {token}
Content-Type: application/json

{
  "entry_date": "2026-01-12",
  "title": "Great progress on JavaScript",
  "content": "Today I learned about async/await...",
  "mood": "motivated",
  "hours_studied": 3.5,
  "goal_id": 5,
  "is_private": true
}
```

---

### 8. Communication API

#### Comments Endpoints
```
GET    /api/v1/comments                   # List comments (for resource)
POST   /api/v1/comments                   # Create comment
PATCH  /api/v1/comments/{id}              # Update comment
DELETE /api/v1/comments/{id}              # Delete comment
POST   /api/v1/comments/{id}/like         # Like comment
POST   /api/v1/comments/{id}/reply        # Reply to comment
```

#### Notifications Endpoints
```
GET    /api/v1/notifications              # List notifications
GET    /api/v1/notifications/unread       # Get unread notifications
PATCH  /api/v1/notifications/{id}/read    # Mark as read
PATCH  /api/v1/notifications/read-all     # Mark all as read
DELETE /api/v1/notifications/{id}         # Delete notification
```

---

## 📝 Request/Response Format

### Standard Request Headers
```http
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
Accept-Language: id-ID
X-Request-ID: {unique-id}
```

### Standard Response Structure

**Success Response:**
```json
{
  "status": "success",
  "message": "Operation completed successfully",
  "data": {
    // Response payload
  },
  "meta": {
    "timestamp": "2026-01-12T16:30:00Z",
    "version": "1.0",
    "request_id": "req-12345"
  }
}
```

**Error Response:**
```json
{
  "status": "error",
  "message": "Operation failed",
  "error": {
    "code": "VALIDATION_ERROR",
    "details": [
      {
        "field": "email",
        "message": "The email field is required."
      }
    ]
  },
  "meta": {
    "timestamp": "2026-01-12T16:30:00Z",
    "version": "1.0",
    "request_id": "req-12345"
  }
}
```

---

## ⚠️ Error Handling

### HTTP Status Codes

**2xx Success:**
- `200 OK` - Successful GET, PATCH, DELETE
- `201 Created` - Successful POST
- `204 No Content` - Successful DELETE with no response body

**4xx Client Errors:**
- `400 Bad Request` - Invalid request format
- `401 Unauthorized` - Missing or invalid token
- `403 Forbidden` - Valid token but insufficient permissions
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation errors
- `429 Too Many Requests` - Rate limit exceeded

**5xx Server Errors:**
- `500 Internal Server Error` - Unexpected server error
- `503 Service Unavailable` - Server maintenance

### Error Codes

```
VALIDATION_ERROR          # Request validation failed
AUTHENTICATION_FAILED     # Invalid credentials
UNAUTHORIZED_ACCESS       # Insufficient permissions
RESOURCE_NOT_FOUND        # Requested resource doesn't exist
DUPLICATE_RESOURCE        # Resource already exists
RATE_LIMIT_EXCEEDED       # Too many requests
INTERNAL_SERVER_ERROR     # Unexpected error
SERVICE_UNAVAILABLE       # Maintenance or overload
DOCUMENT_TRANSFORM_FAILED # Document processing error
ENROLLMENT_CLOSED         # Course enrollment not available
QUIZ_TIME_EXPIRED         # Quiz time limit exceeded
INVALID_FILE_TYPE         # Unsupported file format
FILE_TOO_LARGE            # File size exceeds limit
```

### Validation Error Example
```json
{
  "status": "error",
  "message": "Validation failed",
  "error": {
    "code": "VALIDATION_ERROR",
    "details": [
      {
        "field": "email",
        "message": "The email must be a valid email address.",
        "rule": "email"
      },
      {
        "field": "password",
        "message": "The password must be at least 8 characters.",
        "rule": "min:8"
      }
    ]
  }
}
```

---

## ⚡ Optimization Strategies

### 1. Query Optimization

**Eager Loading (Prevent N+1 Queries):**
```
Request: GET /api/v1/courses/123?include=modules,modules.lessons

Backend (Laravel):
- Course::with(['modules.lessons'])->find(123)
- Single query with joins instead of multiple queries
- Reduces database round trips from N+1 to 1-2 queries
```

**Selective Field Loading:**
```
Request: GET /api/v1/users?fields=id,name,email

Backend:
- User::select(['id', 'name', 'email'])->get()
- Reduces data transfer
- Faster query execution
```

**Pagination:**
```
Request: GET /api/v1/articles?page=1&per_page=20

Backend:
- Article::paginate(20)
- Cursor pagination untuk large datasets
- Prevents loading all records
```

### 2. Caching Strategy

**Cache Layers:**

**1. Application Cache (Redis)**
```
Cached Data:
- Course catalog (1 hour)
- Popular articles (30 minutes)
- User permissions (5 minutes)
- Static content (24 hours)

Cache Key Pattern:
- courses:list:page:{page}:filters:{hash}
- article:{id}:details
- user:{id}:permissions
```

**2. HTTP Cache Headers**
```http
Response Headers:
Cache-Control: public, max-age=3600
ETag: "33a64df551425fcc55e4d42a148795d9f25f89d4"
Last-Modified: Thu, 12 Jan 2026 14:30:00 GMT
```

**3. CDN Caching**
```
Static Assets:
- Images: 30 days
- CSS/JS: 1 year (with versioning)
- Documents: 7 days
```

**Cache Invalidation Strategy:**
```
Event-Based Invalidation:
- Course updated → clear course:{id}:* cache
- Article published → clear articles:list:* cache
- User role changed → clear user:{id}:permissions

Tag-Based Invalidation:
- Clear all caches with specific tag
- Cache::tags(['courses', 'featured'])->flush()
```

### 3. Database Optimization

**Indexing:**
```
Indexed Columns:
- Foreign keys (automatic)
- Frequently filtered columns (status, is_published)
- Sorted columns (created_at, order)
- Searched columns (FULLTEXT on title, content)

Composite Indexes:
- (user_id, status, created_at) for user-specific queries
- (course_id, is_published) for course content queries
```

**Query Result Caching:**
```
Cache frequent queries:
- Popular courses
- Featured articles
- User enrollments
- Course statistics

Cache Duration:
- Static content: 24 hours
- Dynamic content: 5-15 minutes
- User-specific: 1-5 minutes
```

### 4. API Response Optimization

**Response Compression:**
```http
Request Header:
Accept-Encoding: gzip, deflate, br

Response Header:
Content-Encoding: gzip
```

**Partial Responses:**
```
Request: GET /api/v1/courses?fields=id,title,thumbnail

Benefits:
- Reduced payload size (50-80% smaller)
- Faster JSON parsing
- Lower bandwidth usage
```

**Batch Operations:**
```
Instead of:
POST /api/v1/lessons/1/complete
POST /api/v1/lessons/2/complete
POST /api/v1/lessons/3/complete

Use:
POST /api/v1/lessons/complete-batch
Body: { "lesson_ids": [1, 2, 3] }

Benefits:
- Single HTTP request
- Reduced overhead
- Atomic operations
```

### 5. Background Processing

**Queue Jobs for Heavy Operations:**

```
Queued Operations:
├── Document Transformation
│   - .docx/.doc parsing
│   - HTML generation
│   - Image extraction
│   - Duration: 5-30 seconds
│
├── Email Sending
│   - Enrollment confirmations
│   - Notifications
│   - Duration: 1-2 seconds
│
├── Report Generation
│   - PDF export
│   - Excel export
│   - Duration: 10-60 seconds
│
└── Analytics Processing
    - Aggregate statistics
    - Learning insights
    - Duration: 30-120 seconds
```

**Job Processing Flow:**
```
1. API receives request
   ↓
2. Validate and create job
   ↓
3. Return job ID immediately
   Response: { "job_id": "123", "status": "processing" }
   ↓
4. Process job in background
   ↓
5. Notify user when complete
   - Real-time notification
   - Email notification
```

**Job Status Checking:**
```
GET /api/v1/jobs/{job_id}/status

Response:
{
  "job_id": "123",
  "status": "processing",
  "progress_percentage": 45,
  "estimated_completion": "2026-01-12T16:35:00Z"
}
```

---

## 🚦 Rate Limiting

### Rate Limit Strategy

**Tier-Based Limits:**

```
Public/Unauthenticated:
- 60 requests per hour
- IP-based tracking
- Applies to: Public content browsing

Authenticated Users:
- 1000 requests per hour
- User-based tracking
- Applies to: General API access

Premium/Teacher Accounts:
- 5000 requests per hour
- Account-based tracking
- Higher limits for content creators

Admin Accounts:
- 10000 requests per hour
- Account-based tracking
- System management operations
```

**Endpoint-Specific Limits:**

```
Heavy Operations:
├── POST /api/v1/documents/upload
│   - 10 uploads per hour
│   - Prevents abuse
│
├── POST /api/v1/quizzes/{id}/submit
│   - Based on quiz max_attempts
│   - Prevents cheating
│
└── GET /api/v1/analytics/export
    - 5 exports per hour
    - Prevents server overload
```

**Rate Limit Headers:**
```http
Response Headers:
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 997
X-RateLimit-Reset: 1673539200
Retry-After: 3600
```

**Rate Limit Exceeded Response:**
```json
{
  "status": "error",
  "message": "Rate limit exceeded",
  "error": {
    "code": "RATE_LIMIT_EXCEEDED",
    "details": {
      "limit": 1000,
      "reset_at": "2026-01-12T17:00:00Z",
      "retry_after_seconds": 1800
    }
  }
}
```

---

## 📊 Performance Metrics

### Target Performance

**Response Time:**
- Simple GET requests: < 100ms
- Complex queries (with includes): < 300ms
- Document upload: < 500ms (sync) + background processing
- Quiz submission: < 200ms

**Throughput:**
- 1000 requests/minute (sustained)
- 5000 requests/minute (burst)

**Database Queries:**
- Average: 2-3 queries per request
- Max: 10 queries per complex request
- Query time: < 50ms average

**Cache Hit Rate:**
- Target: > 80% for cached endpoints
- Course catalog: > 90%
- User permissions: > 85%

---

## 🔧 API Testing & Monitoring

### Testing Strategy

**Unit Tests:**
- Controller methods
- Service layer logic
- Validation rules

**Integration Tests:**
- API endpoint responses
- Database interactions
- Queue jobs

**Performance Tests:**
- Load testing (1000 concurrent users)
- Stress testing (breaking point)
- Endurance testing (24-hour sustained load)

### Monitoring

**Metrics to Track:**
- Response time percentiles (p50, p95, p99)
- Error rate per endpoint
- Request volume per minute
- Cache hit/miss ratio
- Queue job processing time
- Failed job count

**Alerting Thresholds:**
- Response time > 1 second (P95)
- Error rate > 5%
- Cache hit rate < 70%
- Queue jobs delayed > 10 minutes
- Failed jobs > 50 per hour

---

**Document Version:** 1.0  
**Last Updated:** 12 Januari 2026  
**Total Endpoints:** 100+  
**API Style:** RESTful JSON API

---
