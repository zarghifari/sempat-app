# 🛠️ Technical Implementation Strategy - LMS SEMPAT
## Development Guidelines & Best Practices (Without Code)

**Versi:** 1.0  
**Tanggal:** 12 Januari 2026  
**Framework:** Laravel 12.x + MySQL 8.0+

---

## 📋 Table of Contents

1. [Development Approach](#development-approach)
2. [Laravel Project Structure](#laravel-project-structure)
3. [Service Layer Architecture](#service-layer-architecture)
4. [Repository Pattern](#repository-pattern)
5. [Event-Driven Architecture](#event-driven-architecture)
6. [Queue & Job System](#queue--job-system)
7. [File Storage Strategy](#file-storage-strategy)
8. [Frontend Implementation](#frontend-implementation)
9. [Testing Strategy](#testing-strategy)
10. [Deployment Strategy](#deployment-strategy)

---

## 🎯 Development Approach

### Agile Methodology

**Sprint Structure:**
```
Sprint Duration: 2 weeks

Sprint Cycle:
Week 1:
├── Day 1-2: Sprint Planning & Design
├── Day 3-5: Core Development
└── Day 6-7: Feature Implementation

Week 2:
├── Day 8-10: Testing & Bug Fixes
├── Day 11-12: Code Review & Refactoring
├── Day 13: Documentation
└── Day 14: Sprint Review & Retrospective
```

### Development Phases

**Phase 1: Foundation (Month 1-2)**
```
Priorities:
├── Database setup & migrations
├── Authentication & authorization
├── User management
├── Basic module structure
└── Core services foundation
```

**Phase 2: Core Features (Month 3-5)**
```
Priorities:
├── FSDL module (courses, modules, lessons)
├── SPSDL module (articles)
├── Document import system
├── Quiz & assessment engine
└── Progress tracking
```

**Phase 3: Enhanced Features (Month 6-8)**
```
Priorities:
├── Self-directed learning features
├── Communication features
├── Analytics & reporting
├── Notifications system
└── Admin dashboard
```

**Phase 4: Polish & Deploy (Month 9-10)**
```
Priorities:
├── Performance optimization
├── Security hardening
├── UI/UX refinements
├── Comprehensive testing
└── Production deployment
```

---

## 📁 Laravel Project Structure

### Application Directory Organization

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/                    # API controllers
│   │   │   ├── V1/
│   │   │   │   ├── AuthController
│   │   │   │   ├── CourseController
│   │   │   │   ├── ArticleController
│   │   │   │   ├── QuizController
│   │   │   │   └── ...
│   │   │   └── V2/ (future)
│   │   │
│   │   ├── Web/                    # Web controllers
│   │   │   ├── DashboardController
│   │   │   ├── CourseController
│   │   │   └── ...
│   │   │
│   │   └── Admin/                  # Admin controllers
│   │       ├── UserManagementController
│   │       ├── ContentModerationController
│   │       └── ...
│   │
│   ├── Middleware/
│   │   ├── CheckRole
│   │   ├── CheckPermission
│   │   ├── LogActivity
│   │   ├── TrackLearningTime
│   │   └── EnsureEnrolled
│   │
│   ├── Requests/
│   │   ├── Auth/
│   │   │   ├── LoginRequest
│   │   │   └── RegisterRequest
│   │   ├── Course/
│   │   │   ├── CreateCourseRequest
│   │   │   └── UpdateCourseRequest
│   │   └── ...
│   │
│   └── Resources/
│       ├── CourseResource
│       ├── ArticleResource
│       ├── UserResource
│       └── ...
│
├── Services/
│   ├── AuthService                 # Authentication logic
│   ├── CourseService               # Course business logic
│   ├── ModuleService
│   ├── LessonService
│   ├── ArticleService
│   ├── QuizService
│   ├── DocumentTransformService    # Document import logic
│   ├── ProgressTrackingService
│   ├── EnrollmentService
│   ├── NotificationService
│   └── AnalyticsService
│
├── Repositories/
│   ├── Contracts/                  # Repository interfaces
│   │   ├── CourseRepositoryInterface
│   │   ├── UserRepositoryInterface
│   │   └── ...
│   │
│   └── Eloquent/                   # Eloquent implementations
│       ├── CourseRepository
│       ├── UserRepository
│       └── ...
│
├── Models/
│   ├── User
│   ├── Role
│   ├── Permission
│   ├── Course
│   ├── Module
│   ├── Lesson
│   ├── Quiz
│   ├── Article
│   ├── Document
│   ├── Enrollment
│   └── ...
│
├── Events/
│   ├── User/
│   │   ├── UserRegistered
│   │   └── UserLoggedIn
│   ├── Course/
│   │   ├── CoursePublished
│   │   ├── StudentEnrolled
│   │   └── LessonCompleted
│   ├── Quiz/
│   │   ├── QuizStarted
│   │   └── QuizSubmitted
│   └── Document/
│       ├── DocumentUploaded
│       └── DocumentTransformed
│
├── Listeners/
│   ├── SendWelcomeEmail
│   ├── LogUserLogin
│   ├── NotifyEnrollment
│   ├── UpdateProgress
│   ├── ProcessDocumentTransformation
│   └── SendQuizResultNotification
│
├── Jobs/
│   ├── ProcessDocumentJob         # Background document processing
│   ├── SendEmailNotificationJob
│   ├── GenerateReportJob
│   ├── CalculateAnalyticsJob
│   └── CleanupOldDataJob
│
├── Mail/
│   ├── WelcomeEmail
│   ├── EnrollmentConfirmation
│   ├── QuizResultEmail
│   └── WeeklyDigestEmail
│
├── Notifications/
│   ├── NewCoursePublished
│   ├── AssignmentDueSoon
│   ├── QuizGraded
│   └── NewCommentNotification
│
├── Policies/
│   ├── CoursePolicy
│   ├── ArticlePolicy
│   ├── QuizPolicy
│   └── CommentPolicy
│
├── Traits/
│   ├── HasUuid
│   ├── HasSlug
│   ├── Loggable
│   └── SoftDeletesWithRestore
│
└── Helpers/
    ├── DateHelper
    ├── FileHelper
    ├── TextHelper
    └── UrlHelper
```

---

## 🔧 Service Layer Architecture

### Service Pattern Implementation

**Purpose:**
- Encapsulate business logic
- Keep controllers thin
- Promote code reusability
- Facilitate testing

**Service Structure Example (CourseService):**

```
CourseService Responsibilities:
├── createCourse(data)
│   ├── Validate data
│   ├── Generate slug
│   ├── Generate UUID
│   ├── Create course record
│   ├── Fire CourseCreated event
│   └── Return course instance
│
├── updateCourse(course, data)
│   ├── Validate data
│   ├── Update course record
│   ├── Update slug if title changed
│   ├── Fire CourseUpdated event
│   └── Clear related cache
│
├── publishCourse(course)
│   ├── Validate course completeness
│   ├── Check all modules published
│   ├── Set published status
│   ├── Set published_at timestamp
│   ├── Fire CoursePublished event
│   └── Send notifications to followers
│
├── enrollStudent(course, user)
│   ├── Check enrollment eligibility
│   ├── Check prerequisites
│   ├── Check enrollment limit
│   ├── Create enrollment record
│   ├── Fire StudentEnrolled event
│   └── Send enrollment confirmation
│
├── calculateProgress(enrollment)
│   ├── Get completed lessons
│   ├── Get total lessons
│   ├── Get quiz scores
│   ├── Calculate percentage
│   ├── Update enrollment progress
│   └── Check if course completed
│
└── getCourseAnalytics(course)
    ├── Get enrollment stats
    ├── Get completion rates
    ├── Get average scores
    ├── Get time statistics
    └── Return analytics data
```

**Service Dependencies:**

```
CourseService depends on:
├── CourseRepository (data access)
├── ModuleRepository
├── EnrollmentRepository
├── ProgressTrackingService
├── NotificationService
└── CacheService

Injected via Constructor:
- Laravel's dependency injection container
- Automatic resolution of dependencies
- Easy to mock for testing
```

---

## 🗄️ Repository Pattern

### Purpose & Benefits

**Why Repository Pattern:**
```
Benefits:
├── Abstract database operations
├── Centralize query logic
├── Easy to swap data sources
├── Simplify testing (mock repositories)
├── Consistency across application
└── Reduce code duplication
```

### Repository Structure

**Interface (Contract):**
```
CourseRepositoryInterface defines:
├── all(filters = [])
├── find(id)
├── findBySlug(slug)
├── findByUuid(uuid)
├── create(data)
├── update(id, data)
├── delete(id)
├── getPublished(filters = [])
├── getByCategory(category)
├── searchByTitle(query)
└── withRelations(relations = [])
```

**Implementation (Eloquent):**
```
CourseRepository implements CourseRepositoryInterface:

all(filters):
  ├── Start with Course query
  ├── Apply filters (category, level, published)
  ├── Apply sorting
  ├── Apply pagination
  └── Return paginated collection

find(id):
  ├── Find course by ID
  ├── Load default relations (creator)
  ├── Return course or throw exception

getPublished(filters):
  ├── Query only published courses
  ├── Apply filters
  ├── Cache results (1 hour)
  └── Return cached or fresh data

searchByTitle(query):
  ├── Use full-text search
  ├── Or use LIKE query
  ├── Order by relevance
  └── Return results
```

**Repository Binding:**
```
Service Provider registration:
- Bind CourseRepositoryInterface to CourseRepository
- Allows automatic dependency injection
- Easy to swap implementations
- Can use different implementations per environment
```

---

## 📡 Event-Driven Architecture

### Event System Flow

**Event Lifecycle:**
```
1. Action occurs (e.g., user enrolls in course)
   ↓
2. Event is fired (StudentEnrolled)
   ↓
3. Event contains relevant data (enrollment, user, course)
   ↓
4. Registered listeners are triggered
   ↓
5. Each listener performs its task
   ├── Send enrollment email
   ├── Log activity
   ├── Update statistics
   └── Clear cache
```

### Key Events & Listeners

**User Events:**
```
UserRegistered Event:
├── Data: User instance
├── Listeners:
│   ├── SendWelcomeEmail
│   ├── CreateUserProfile
│   ├── LogUserRegistration
│   └── SendAdminNotification (if configured)

UserLoggedIn Event:
├── Data: User instance, IP, timestamp
├── Listeners:
│   ├── LogLoginActivity
│   ├── UpdateLastLoginInfo
│   └── CheckSuspiciousActivity
```

**Course Events:**
```
CoursePublished Event:
├── Data: Course instance
├── Listeners:
│   ├── NotifyFollowers
│   ├── ClearCourseListCache
│   ├── UpdateCourseStatistics
│   └── IndexForSearch

StudentEnrolled Event:
├── Data: Enrollment instance, User, Course
├── Listeners:
│   ├── SendEnrollmentConfirmation
│   ├── LogEnrollmentActivity
│   ├── UpdateCourseEnrollmentCount
│   └── CreateProgressRecord

LessonCompleted Event:
├── Data: Completion instance, User, Lesson
├── Listeners:
│   ├── UpdateEnrollmentProgress
│   ├── CheckModuleCompletion
│   ├── AwardBadges (if milestone)
│   └── UnlockNextContent
```

**Document Events:**
```
DocumentUploaded Event:
├── Data: Document instance
├── Listeners:
│   ├── QueueDocumentTransformation
│   ├── ScanForVirus
│   └── LogDocumentUpload

DocumentTransformed Event:
├── Data: Document instance, Transformation result
├── Listeners:
│   ├── NotifyUploader
│   ├── UpdateContentPreview
│   └── IndexTransformedContent
```

**Quiz Events:**
```
QuizSubmitted Event:
├── Data: Quiz attempt instance, User, Quiz
├── Listeners:
│   ├── AutoGradeObjectiveQuestions
│   ├── QueueEssayGrading (if has essays)
│   ├── CalculateFinalScore
│   ├── UpdateProgress
│   └── SendResultNotification (if enabled)
```

### Event Broadcasting (Real-Time)

**Real-Time Features:**
```
Broadcasting Events:
├── New notification (show immediately)
├── Quiz timer updates
├── Live discussion updates
├── Progress updates
└── System announcements

Broadcasting Channels:
├── Private channels (user-specific)
├── Presence channels (online users)
└── Public channels (announcements)

Technology Options:
├── Pusher (hosted service)
├── Laravel WebSockets (self-hosted)
└── Socket.io (Node.js integration)
```

---

## ⚙️ Queue & Job System

### Background Job Processing

**Queue Architecture:**
```
Job Flow:
1. Action triggers job dispatch
   ↓
2. Job added to queue (Redis/Database)
   ↓
3. Queue worker picks up job
   ↓
4. Job executes in background
   ↓
5. On success: Mark completed
   On failure: Retry (max 3 attempts)
   ↓
6. If all retries fail: Move to failed_jobs table
```

### Important Background Jobs

**Document Processing Job:**
```
ProcessDocumentJob:
├── Receives: Document ID
├── Steps:
│   1. Load document from storage
│   2. Parse .docx/.doc structure
│   3. Extract text content
│   4. Extract and save images
│   5. Convert to HTML
│   6. Sanitize HTML
│   7. Save transformed HTML
│   8. Update document status
│   9. Fire DocumentTransformed event
│   └── Clean up temporary files
├── Timeout: 5 minutes
├── Max Attempts: 3
└── Queue: document-processing (dedicated worker)
```

**Email Notification Job:**
```
SendEmailNotificationJob:
├── Receives: User, Email type, Data
├── Steps:
│   1. Load user preferences
│   2. Check if email enabled for this type
│   3. Render email template
│   4. Send via mail service
│   5. Log email sent
│   └── Update user notification log
├── Timeout: 30 seconds
├── Max Attempts: 5 (important to deliver)
└── Queue: emails
```

**Analytics Calculation Job:**
```
CalculateAnalyticsJob:
├── Receives: Date range, Metrics to calculate
├── Steps:
│   1. Query relevant data
│   2. Perform aggregations
│   3. Calculate statistics
│   4. Store results in cache
│   5. Update analytics tables
│   └── Generate charts data
├── Timeout: 10 minutes
├── Max Attempts: 2
└── Queue: analytics (low priority)
```

**Report Generation Job:**
```
GenerateReportJob:
├── Receives: Report type, Filters, User ID
├── Steps:
│   1. Query data based on filters
│   2. Process and format data
│   3. Generate PDF/Excel
│   4. Save to storage
│   5. Send download link to user
│   └── Schedule cleanup after 7 days
├── Timeout: 15 minutes
├── Max Attempts: 2
└── Queue: reports
```

### Queue Management

**Queue Configuration:**
```
Queue Drivers:
├── Development: Database (simple setup)
├── Staging: Redis (better performance)
└── Production: Redis (scalable)

Queue Priorities:
├── High: Critical emails, notifications
├── Default: Standard operations
└── Low: Analytics, cleanup tasks

Queue Workers:
├── Main worker (all queues)
├── Document worker (document-processing only)
├── Email worker (emails only)
└── Analytics worker (analytics, reports)
```

**Failed Job Handling:**
```
On Job Failure:
├── Store in failed_jobs table
├── Capture exception details
├── Send alert to admin (if critical)
├── Retry manually or via command
└── Delete if permanently failed

Retry Strategy:
├── Retry 1: Immediately
├── Retry 2: After 5 minutes
├── Retry 3: After 30 minutes
└── Failed: Move to failed_jobs
```

---

## 📦 File Storage Strategy

### Storage Organization

**Storage Directory Structure:**
```
storage/
├── app/
│   ├── public/                    # Publicly accessible files
│   │   ├── avatars/
│   │   │   └── {user_id}/{filename}
│   │   │
│   │   ├── courses/
│   │   │   ├── thumbnails/
│   │   │   │   └── {course_id}/{filename}
│   │   │   └── attachments/
│   │   │       └── {course_id}/{filename}
│   │   │
│   │   ├── articles/
│   │   │   ├── thumbnails/
│   │   │   └── images/
│   │   │       └── {article_id}/{filename}
│   │   │
│   │   └── documents/
│   │       └── {document_id}/
│   │           ├── original/
│   │           │   └── {filename}.docx
│   │           ├── images/
│   │           │   ├── {image_name}.jpg
│   │           │   └── thumbnails/
│   │           │       └── {image_name}_thumb.jpg
│   │           └── transformed/
│   │               └── content.html
│   │
│   └── private/                   # Non-public files
│       ├── documents/original/    # Original uploaded docs
│       ├── reports/               # Generated reports
│       └── exports/               # Data exports
│
├── framework/
│   ├── cache/
│   ├── sessions/
│   └── views/
│
└── logs/
    └── laravel.log
```

### File Upload Handling

**Upload Process:**
```
File Upload Flow:
1. Validate file:
   ├── Check file type
   ├── Check file size
   ├── Check filename
   └── Virus scan (optional)

2. Generate secure filename:
   ├── Generate UUID
   ├── Preserve extension
   ├── Example: 550e8400-e29b-41d4-a716-446655440000.docx

3. Determine storage path:
   ├── Based on file type
   ├── Based on entity type
   └── Example: documents/{document_id}/original/

4. Store file:
   ├── Local filesystem (development)
   ├── S3/Cloud Storage (production)
   └── Store metadata in database

5. Process if needed:
   ├── Generate thumbnails (images)
   ├── Queue document transformation
   └── Optimize file size

6. Return file information:
   ├── File ID
   ├── Public URL
   ├── File size
   └── File type
```

### Image Processing

**Image Optimization:**
```
Image Upload → Optimization Pipeline:
├── Original upload (preserve)
├── Resize for web display:
│   ├── Large: 1200px width
│   ├── Medium: 800px width
│   └── Small: 400px width
├── Generate thumbnail: 200x200px
├── Compress (80% quality)
├── Convert to WebP (optional)
└── Store all variants
```

**Responsive Image Serving:**
```
Frontend Implementation:
- Use <picture> element
- Provide multiple sizes
- Let browser choose best size
- Lazy load images
- Use CDN for delivery
```

---

## 🎨 Frontend Implementation

### Mobile-First Architecture

**Design Philosophy:**
```
Development Flow:
Mobile (320px) → Tablet (768px) → Desktop (1024px+)
     ↓                ↓                  ↓
  Primary         Enhanced           Full Features
  Essential       Features           Desktop UI
```

**Key Principles:**
1. **Touch-First**: Semua interaksi optimized untuk touch
2. **Progressive Enhancement**: Mulai dari mobile, tambahkan fitur untuk layar besar
3. **Performance**: Optimasi untuk koneksi mobile/3G
4. **Accessibility**: WCAG 2.1 AA compliance

### Core UI Components

**Fixed Navigation Components:**
```
App Structure:
┌─────────────────────────────────┐
│  App Bar (h-14 / 56px)          │ ← Fixed Top
│  - Back button (conditional)    │
│  - App title                    │
│  - Notifications                │
│  - Profile menu                 │
├─────────────────────────────────┤
│                                 │
│  Scrollable Content Area        │ ← pt-14 pb-20
│  - Cards                        │
│  - Lists                        │
│  - Forms                        │
│                                 │
├─────────────────────────────────┤
│  Bottom Navigation (h-16/64px)  │ ← Fixed Bottom
│  [Home][Learn][Progress][Chat]  │
│  [Profile]                      │
└─────────────────────────────────┘
```

**1. App Bar Component:**
```
Location: resources/views/layouts/app.blade.php

Features:
├── Fixed positioning (top-0)
├── Gradient background (blue-600 to blue-700)
├── Z-index 50 (always on top)
├── Safe area inset support
├── Responsive height
└── Touch-optimized buttons

Elements:
├── Back Button (conditional @if showBack)
├── App Title/Logo
├── Notification Bell (with badge)
└── Profile Avatar (with dropdown)
```

**2. Bottom Navigation Component:**
```
Location: resources/views/layouts/app.blade.php

Features:
├── Fixed positioning (bottom-0)
├── 5-tab navigation system
├── Active state highlighting
├── Icon + label display
└── Touch feedback (active:scale-95)

Tabs:
├── Home     → route('dashboard')
├── Learn    → route('courses.index')
├── Progress → route('progress.index')
├── Chat     → route('messages.index')
└── Profile  → route('profile.show')
```

**3. Card Components:**
```
Card Types:
├── Welcome Card (gradient with avatar)
├── Stat Cards (2x2 grid, icon + number)
├── List Cards (horizontal scroll)
└── Content Cards (vertical stack)

Common Features:
├── rounded-xl borders
├── shadow-sm elevation
├── padding p-4 or p-5
├── active:scale-95 feedback
└── Responsive sizing
```

### Technology Stack

**Core Technologies:**
```
├── Blade Templates (Server-side rendering)
│   ├── Layout templates (app, guest)
│   ├── Component templates (cards, forms)
│   ├── Partial views (modals, dropdowns)
│   └── Mobile-first structure
│
├── Tailwind CSS (Utility-first)
│   ├── Mobile-first breakpoints
│   ├── Touch-optimized spacing
│   ├── Custom design tokens
│   └── JIT compilation
│
├── Alpine.js (Optional - for enhanced interactivity)
│   ├── Simple interactions
│   ├── Form handling
│   ├── Modals & dropdowns
│   └── Data binding
│
└── Vite (Build tool)
    ├── Fast HMR
    ├── CSS preprocessing
    ├── JS bundling
    └── Asset optimization
```

**CSS Framework:**
```
Tailwind CSS Configuration:
├── Mobile-first breakpoints:
│   ├── sm: 640px   (landscape phones)
│   ├── md: 768px   (tablets)
│   ├── lg: 1024px  (laptops)
│   └── xl: 1280px  (desktops)
│
├── Custom spacing:
│   ├── Touch targets: min-h-[44px]
│   ├── Safe areas: env(safe-area-inset-*)
│   └── App clearance: pt-14, pb-20
│
├── Design tokens:
│   ├── Colors: blue-{50-900}
│   ├── Fonts: Inter family
│   ├── Shadows: shadow-sm to shadow-2xl
│   └── Borders: rounded-xl, rounded-2xl
│
└── Custom utilities:
    ├── scrollbar-hide
    ├── active:scale-95
    └── -webkit-tap-highlight
```

### Component Structure

**Blade Components:**
```
resources/views/components/
├── layouts/
│   ├── app.blade.php          # Mobile-first main layout
│   ├── guest.blade.php        # Mobile-first auth layout
│   └── admin.blade.php        # Admin dashboard layout
│
├── navigation/
│   ├── app-bar.blade.php      # Top navigation
│   ├── bottom-nav.blade.php   # Bottom tabs
│   └── breadcrumbs.blade.php  # Optional desktop
│
├── cards/
│   ├── welcome.blade.php      # Gradient welcome card
│   ├── stat.blade.php         # Stat card with icon
│   ├── course.blade.php       # Course card
│   └── progress.blade.php     # Progress card
│
├── forms/
│   ├── input.blade.php        # Touch-friendly input
│   ├── textarea.blade.php     # Auto-resize textarea
│   ├── select.blade.php       # Native select
│   └── button.blade.php       # Gradient button
│
└── ui/
    ├── modal.blade.php        # Bottom sheet style
    ├── alert.blade.php        # Toast notification
    ├── badge.blade.php        # Status badge
    └── loading.blade.php      # Loading spinner
```

### Touch Interaction Patterns

**1. Touch Targets:**
```
Minimum Sizes:
├── Buttons: min-h-[44px] py-3 px-4
├── Icon buttons: w-12 h-12
├── Form inputs: px-4 py-3
└── List items: min-h-[56px]

Spacing:
├── Gap between elements: gap-3 (12px)
├── Touch safe margin: m-2 (8px)
└── Padding from edges: px-4 (16px)
```

**2. Visual Feedback:**
```
Active States:
├── Scale: active:scale-95
├── Background: active:bg-blue-700
├── Opacity: active:opacity-80
└── Combined: active:scale-95 active:bg-blue-700

Transitions:
├── Duration: transition-all duration-150
├── Easing: ease-in-out
└── Transform: transition-transform
```

**3. Scrolling:**
```
CSS Properties:
├── Smooth scroll: scroll-behavior: smooth
├── Momentum: -webkit-overflow-scrolling: touch
├── Overscroll: overscroll-behavior-y: contain
└── Hide scrollbar: scrollbar-hide utility

Horizontal Scroll:
├── Container: flex overflow-x-auto gap-3
├── Items: flex-shrink-0 w-64
├── Padding: -mx-4 px-4 (bleed effect)
└── Snap: scroll-snap-type: x mandatory
```

### Responsive Patterns

**Grid Layouts:**
```
Mobile → Tablet → Desktop:
├── 1 column → 2 columns → 3 columns
│   grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3
│
├── Full width → Half → Third
│   w-full sm:w-1/2 lg:w-1/3
│
└── Stack → Side-by-side
    flex-col sm:flex-row
```

**Navigation Patterns:**
```
Mobile:
├── App Bar (always visible)
├── Bottom Navigation (5 tabs)
└── Hamburger menu (if needed)

Tablet (768px+):
├── App Bar (with more actions)
├── Bottom Nav OR Side Nav
└── Breadcrumbs appear

Desktop (1024px+):
├── Full top navigation
├── Sidebar navigation
└── Breadcrumbs + search
```

### Performance Optimization

**1. Image Handling:**
```
Techniques:
├── Lazy loading: loading="lazy"
├── Responsive images: srcset + sizes
├── WebP format: picture element fallback
├── Placeholder: blur-up technique
└── CDN delivery: for production
```

**2. CSS Optimization:**
```
Tailwind Purging:
├── PurgeCSS enabled in production
├── Only used classes included
├── Result: ~10KB gzipped CSS
└── Critical CSS inlined in <head>
```

**3. JavaScript:**
```
Loading Strategy:
├── Defer non-critical: <script defer>
├── Async third-party: <script async>
├── Code splitting: dynamic imports
└── Module bundling: Vite optimization
```

### Accessibility Implementation

**WCAG 2.1 AA Compliance:**
```
Requirements:
├── Touch Targets:
│   └── Minimum 44x44px for all interactive elements
│
├── Color Contrast:
│   ├── Text: min 4.5:1 ratio
│   └── UI components: min 3:1 ratio
│
├── Focus Management:
│   ├── Visible focus indicators
│   ├── Logical tab order
│   └── Skip links for navigation
│
├── Semantic HTML:
│   ├── Proper heading hierarchy (h1-h6)
│   ├── Landmark roles (nav, main, aside)
│   └── ARIA labels where needed
│
└── Form Accessibility:
    ├── Associated labels for all inputs
    ├── Error messages linked with aria-describedby
    ├── Required fields marked (*)
    └── Clear validation feedback
```

### Interactive Features

**Alpine.js Implementation:**
```
Use Cases:
├── Dropdown menus
├── Modal dialogs
├── Tabs & accordions
├── Form validation
├── Dynamic filters
├── Search autocomplete
└── Shopping cart-like features

Example: Toggle Dropdown
- x-data for component state
- @click for event handling
- x-show for conditional display
- x-transition for animations
```

**Livewire Implementation (Alternative):**
```
Use Cases:
├── Real-time search
├── Pagination without page reload
├── Form submissions with validation
├── Dynamic content loading
├── Shopping cart
└── Chat/messaging

Example: Search Component
- Component class handles logic
- Blade template for UI
- Automatic reactivity
- No JavaScript needed
```

---

## 🧪 Testing Strategy

### Testing Pyramid

```
Testing Layers:
         ▲
        / \
       /   \
      / E2E \     (10%) - End-to-End Tests
     /-------\
    /         \
   / Integration\  (30%) - Integration Tests
  /-------------\
 /               \
/ Unit Tests (60%)\
-------------------
```

### Unit Testing

**What to Test:**
```
Unit Tests for:
├── Service methods
│   ├── Business logic
│   ├── Data manipulation
│   └── Calculations
│
├── Repository methods
│   ├── Query building
│   ├── Data retrieval
│   └── Data persistence
│
├── Helper functions
│   ├── String manipulation
│   ├── Date formatting
│   └── Utility methods
│
└── Model methods
    ├── Accessors/mutators
    ├── Relationships
    └── Scopes
```

**Testing Approach:**
```
Unit Test Structure:
├── Arrange: Set up test data
├── Act: Execute the method
├── Assert: Verify the result
└── Clean up: Reset state

Mocking:
├── Mock dependencies (repositories, services)
├── Mock external APIs
├── Mock file system
└── Mock database (use in-memory)
```

### Integration Testing

**What to Test:**
```
Integration Tests for:
├── API endpoints
│   ├── Request/response
│   ├── Authentication
│   └── Authorization
│
├── Database operations
│   ├── CRUD operations
│   ├── Relationships
│   └── Transactions
│
├── Event/Listener flow
│   ├── Event firing
│   ├── Listener execution
│   └── Side effects
│
└── Job processing
    ├── Job dispatch
    ├── Job execution
    └── Job failure handling
```

### Feature/E2E Testing

**What to Test:**
```
Feature Tests for:
├── User registration flow
├── Login/logout flow
├── Course enrollment flow
├── Lesson completion flow
├── Quiz taking flow
├── Document upload flow
└── Payment flow (if applicable)

Browser Testing (Laravel Dusk):
├── UI interactions
├── JavaScript behavior
├── Form submissions
├── Page navigation
└── Responsive design
```

### Test Data Management

**Factories & Seeders:**
```
Factory Usage:
├── Create test data on-demand
├── Define realistic data
├── Randomize where appropriate
├── Support relationships
└── Fast test execution

Seeder Usage:
├── Populate development database
├── Create demo data
├── Set up roles & permissions
└── Create sample content
```

---

## 🚀 Deployment Strategy

### Environment Setup

**Environments:**
```
├── Development (local)
│   ├── XAMPP (Windows)
│   ├── Debug enabled
│   ├── Local file storage
│   └── Database seeded
│
├── Staging (test server)
│   ├── Linux server
│   ├── Mirror production config
│   ├── Test data
│   └── Integration testing
│
└── Production (live server)
    ├── Linux server
    ├── Optimized config
    ├── Real data
    └── Monitoring enabled
```

### Deployment Process

**Manual Deployment (Initial):**
```
Deployment Steps:
1. Prepare server:
   ├── Install PHP 8.4+
   ├── Install MySQL 8.0+
   ├── Install Redis
   ├── Install Composer
   ├── Install Node.js
   └── Configure Nginx/Apache

2. Deploy code:
   ├── Clone repository
   ├── Run composer install --optimize-autoloader --no-dev
   ├── Run npm install && npm run build
   ├── Copy .env.example to .env
   ├── Configure environment variables
   ├── Generate application key
   └── Set file permissions

3. Database setup:
   ├── Create database
   ├── Run migrations
   ├── Run seeders (master data only)
   └── Backup database

4. Optimize:
   ├── Route caching
   ├── Config caching
   ├── View caching
   └── Opcache setup

5. Configure services:
   ├── Queue workers (Supervisor)
   ├── Scheduler (cron)
   ├── SSL certificate
   └── Firewall rules

6. Test:
   ├── Smoke tests
   ├── Health check
   └── Performance test
```

**Automated Deployment (CI/CD - Future):**
```
CI/CD Pipeline:
1. Git push to main branch
   ↓
2. GitHub Actions/GitLab CI triggered
   ↓
3. Run tests
   ├── Unit tests
   ├── Integration tests
   └── Feature tests
   ↓
4. Build assets
   ├── npm run build
   └── Optimize images
   ↓
5. Deploy to staging
   ├── SSH to staging server
   ├── Pull latest code
   ├── Run migrations
   ├── Restart services
   └── Run smoke tests
   ↓
6. Manual approval (for production)
   ↓
7. Deploy to production
   ├── Zero-downtime deployment
   ├── Health check
   └── Rollback if failed
```

### Zero-Downtime Deployment

**Blue-Green Deployment:**
```
Strategy:
├── Maintain two identical environments (Blue & Green)
├── Blue is currently live
├── Deploy to Green (currently idle)
├── Test Green thoroughly
├── Switch traffic from Blue to Green
├── Green becomes live, Blue becomes idle
└── Rollback to Blue if issues
```

### Post-Deployment

**Verification Checklist:**
```
✓ Application accessible
✓ Database connected
✓ Queue workers running
✓ Scheduler configured
✓ SSL certificate valid
✓ File uploads working
✓ Emails sending
✓ Logs writing
✓ Backups configured
✓ Monitoring active
```

---

**Document Version:** 1.0  
**Last Updated:** 12 Januari 2026  
**Development Methodology:** Agile  
**Estimated Timeline:** 10 months

---
