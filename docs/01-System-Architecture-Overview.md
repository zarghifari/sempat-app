# 📐 System Architecture Overview - LMS SEMPAT
## Learning Management System untuk SMA/SMK

**Versi:** 1.0  
**Tanggal:** 12 Januari 2026  
**Status:** Blueprint - Technical Specification

---

## 🎯 Executive Summary

LMS SEMPAT (Self-Paced and Mentored Academic Training) adalah platform pembelajaran digital yang dirancang khusus untuk siswa SMA dan SMK di Indonesia. Sistem ini mendukung dua mode pembelajaran utama:

1. **FSDL (Facilitated Self-Directed Learning)** - Pembelajaran terstruktur dengan bimbingan
2. **SPSDL (Self-Paced Self-Directed Learning)** - Pembelajaran mandiri berbasis artikel

### Keunggulan Utama
- 🎓 Dual learning mode untuk fleksibilitas maksimal
- 📄 Import dokumen Word (.docx/.doc) dengan transformasi otomatis ke HTML
- 🎯 Fitur self-directed learning yang komprehensif
- ⚡ Optimisasi tingkat enterprise untuk performa maksimal
- 🔒 Keamanan berlapis sesuai standar industri
- 📱 Responsive design untuk akses multi-device

---

## 🏗️ High-Level Architecture

### Arsitektur 3-Layer

```
┌─────────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                        │
│  (Web Interface - Blade Templates + Alpine.js/Livewire)     │
└─────────────────────────────────────────────────────────────┘
                              ↕
┌─────────────────────────────────────────────────────────────┐
│                   APPLICATION LAYER                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Services   │  │ Repositories │  │  Controllers │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │    Jobs      │  │   Events     │  │  Middleware  │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                              ↕
┌─────────────────────────────────────────────────────────────┐
│                      DATA LAYER                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │    MySQL     │  │    Redis     │  │  File System │      │
│  │  (Primary DB)│  │   (Cache)    │  │  (Storage)   │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔧 Technology Stack

### Backend Framework
- **Laravel 12.x**
  - Framework PHP modern dengan ecosystem lengkap
  - Built-in authentication, authorization, validation
  - Queue system untuk background processing
  - Event-driven architecture
  - Eloquent ORM untuk database abstraction
  - Blade templating engine

### Database
- **MySQL 8.0+**
  - Primary database untuk data relational
  - Support untuk JSON columns
  - Full-text search capability
  - Transaction support dengan ACID compliance
  - Indexing strategy untuk optimisasi query

### Caching Layer
- **Redis**
  - Cache untuk query results
  - Session management
  - Queue driver untuk background jobs
  - Real-time data caching
  - Rate limiting storage

### Storage
- **Local File System** (Development)
  - Storage untuk uploaded files
  - Document attachments
  - Transformed HTML files
  
- **Cloud Storage** (Production - Optional)
  - Amazon S3 / Google Cloud Storage
  - Scalable dan reliable
  - CDN integration ready

### Frontend Technologies

**Mobile-First Architecture:**
- **Modern App-Like Interface**
  - Fixed app bar (top navigation)
  - Bottom navigation (5 tabs: Home, Learn, Progress, Chat, Profile)
  - Touch-optimized interactions
  - Responsive untuk semua ukuran layar
  - Safe area support untuk notch devices
  - Progressive Web App (PWA) ready

- **Blade Templates**
  - Server-side rendering
  - SEO friendly
  - Fast initial page load
  - Component-based structure
  
- **Alpine.js / Laravel Livewire** (Optional for enhanced interactivity)
  - Interactive UI components
  - Reactive data binding
  - Minimal JavaScript footprint
  - Real-time updates tanpa full page reload

- **Tailwind CSS**
  - Utility-first CSS framework
  - Mobile-first responsive design
  - Consistent design system
  - Custom component library
  - Touch-friendly UI patterns

**Design Philosophy:**
```
Mobile First → Tablet → Desktop
Touch Optimized → Keyboard Enhanced
Offline Capable → Network Resilient
```

**Key UI Components:**
- App Bar: Fixed top navigation dengan gradient background
- Bottom Nav: 5-tab navigation untuk akses cepat
- Cards: Content organization dengan shadow dan rounded corners
- Forms: Touch-friendly inputs (min 44x44px touch targets)
- Buttons: Active states dengan scale feedback
- Modals: Bottom sheet style untuk mobile

### Additional Tools
- **PHPWord / PHPOffice**
  - Document import engine
  - .docx/.doc parsing
  - HTML transformation
  
- **DOMPurifier / HTML Purifier**
  - HTML sanitization
  - XSS protection
  - Safe HTML rendering
  
- **Laravel Telescope** (Development)
  - Request debugging
  - Query analysis
  - Performance monitoring
  
- **Laravel Horizon**
  - Queue monitoring
  - Job management
  - Failed job handling

---

## 📦 Core Modules

### 1. User Management Module
**Responsibility:** Mengelola authentikasi, authorization, dan profil pengguna

**Sub-Components:**
- Authentication Service
- Role & Permission Manager
- User Profile Manager
- Session Manager

**Key Features:**
- Multi-role support (Admin, Guru, Siswa)
- Permission-based access control
- User profile dengan custom fields
- Activity logging
- Password reset & email verification

---

### 2. FSDL (Facilitated Self-Directed Learning) Module
**Responsibility:** Pembelajaran terstruktur dengan modul dan bimbingan

**Sub-Components:**
- Module Manager Service
- Lesson Content Service
- Progress Tracking Service
- Assessment Engine
- Assignment Manager

**Key Features:**
- Hierarchical module structure (Course → Module → Lesson)
- Sequential learning path
- Learning objectives per module
- Prerequisite management
- Progress tracking dengan percentage
- Deadline management
- Mentor/Teacher feedback system

**Data Flow:**
```
Teacher Creates Module
    ↓
System Structures Content
    ↓
Student Enrolls
    ↓
Sequential Access Based on Progress
    ↓
Completion Tracking
    ↓
Assessment & Feedback
```

---

### 3. SPSDL (Self-Paced Self-Directed Learning) Module
**Responsibility:** Pembelajaran mandiri berbasis artikel dan resources

**Sub-Components:**
- Article Management Service
- Resource Library Service
- Personal Learning Path Builder
- Self-Assessment Tools

**Key Features:**
- Non-sequential article access
- Tagging & categorization system
- Personal bookmarks & notes
- Reading progress tracking
- Self-assessment quizzes
- Learning journal
- Resource recommendations based on interests

**Data Flow:**
```
Content Creator Uploads Article
    ↓
System Categorizes & Tags
    ↓
Student Browses/Searches
    ↓
Self-Paced Access (Any Order)
    ↓
Personal Notes & Bookmarks
    ↓
Optional Self-Assessment
```

---

### 4. Document Import & Transformation Module
**Responsibility:** Import dan transformasi dokumen Word ke HTML

**Sub-Components:**
- Document Upload Handler
- Format Parser Service (PHPWord)
- HTML Transformation Engine
- Attachment Extractor
- Storage Manager

**Key Features:**
- Support .docx dan .doc format
- Automatic HTML conversion
- Image extraction dan storage
- Table preservation
- Text formatting retention (bold, italic, lists, etc.)
- Link preservation
- Metadata extraction
- File versioning
- Attachment management (view, delete, update)

**Technical Process:**
```
1. Upload Validation
   - File type check (.docx/.doc)
   - Size validation (max 50MB)
   - Virus scanning (optional)

2. Parsing Phase
   - Extract document content using PHPWord
   - Parse paragraphs, images, tables, lists
   - Extract embedded media
   - Parse metadata (author, created date, etc.)

3. Transformation Phase
   - Convert content to clean HTML
   - Optimize images (resize, compress)
   - Store images in file system
   - Generate image URLs
   - Preserve document structure

4. Sanitization Phase
   - Clean HTML using HTML Purifier
   - Remove malicious scripts
   - Validate HTML structure
   - Ensure safe rendering

5. Storage Phase
   - Store HTML in database or file system
   - Store images in organized directories
   - Create file references
   - Generate previews/thumbnails

6. Indexing Phase
   - Full-text indexing for search
   - Metadata indexing
   - Tag association
```

---

### 5. Assessment & Evaluation Module
**Responsibility:** Sistem penilaian, kuis, dan ujian

**Sub-Components:**
- Question Bank Service
- Quiz Builder
- Exam Engine
- Grading Service
- Result Analytics

**Key Features:**
- Multiple question types (multiple choice, essay, true/false)
- Randomized questions
- Time-limited assessments
- Auto-grading untuk objective questions
- Manual grading untuk essays
- Result analytics & reporting
- Performance tracking over time

---

### 6. Communication & Collaboration Module
**Responsibility:** Fasilitas komunikasi antara siswa, guru, dan peers

**Sub-Components:**
- Discussion Forum Service
- Direct Messaging System
- Announcement Manager
- Notification Service

**Key Features:**
- Thread-based discussions
- Direct messaging
- Announcement broadcasting
- Real-time notifications
- Email notifications
- Comment & reply system
- Moderation tools

---

### 7. Analytics & Reporting Module
**Responsibility:** Monitoring, reporting, dan insights

**Sub-Components:**
- Learning Analytics Engine
- Progress Report Generator
- Activity Logger
- Dashboard Service

**Key Features:**
- Student progress dashboard
- Teacher analytics dashboard
- Learning time tracking
- Completion rates
- Assessment performance trends
- Engagement metrics
- Export reports (PDF, Excel)

---

## 🔄 System Integration Points

### Internal Integration
```
┌─────────────────────────────────────────────────────┐
│                    API Gateway                       │
│               (Laravel Routes)                       │
└─────────────────────────────────────────────────────┘
        ↓           ↓           ↓           ↓
┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐
│  FSDL    │  │  SPSDL   │  │ Document │  │  User    │
│  Module  │  │  Module  │  │  Import  │  │  Module  │
└──────────┘  └──────────┘  └──────────┘  └──────────┘
        ↓           ↓           ↓           ↓
┌─────────────────────────────────────────────────────┐
│            Shared Services Layer                     │
│  (Auth, Cache, Storage, Queue, Notification)        │
└─────────────────────────────────────────────────────┘
        ↓           ↓           ↓           ↓
┌─────────────────────────────────────────────────────┐
│              Data Persistence Layer                  │
│         (MySQL, Redis, File System)                  │
└─────────────────────────────────────────────────────┘
```

### External Integration (Future)
- **Payment Gateway** (untuk premium features)
- **Video Conferencing API** (Zoom, Google Meet)
- **Cloud Storage** (S3, Google Cloud)
- **Email Service** (SendGrid, Mailgun)
- **SMS Gateway** (untuk notifications)
- **SSO Integration** (Google, Microsoft)

---

## 🔐 Security Architecture

### Authentication & Authorization
- **Laravel Sanctum** untuk API token authentication
- **Laravel Permission** (Spatie) untuk role-based access control
- Session-based authentication untuk web interface
- CSRF protection pada semua forms
- Rate limiting untuk prevent brute force

### Data Security
- **Encrypted Database Fields** untuk sensitive data
- **HTTPS Only** di production
- **SQL Injection Prevention** via Eloquent ORM
- **XSS Prevention** via Blade escaping dan HTML Purifier
- **File Upload Validation** untuk prevent malicious files

### Infrastructure Security
- **Environment Variables** untuk credentials
- **Firewall Rules** untuk database access
- **Regular Backups** dengan encryption
- **Access Logs** untuk audit trail
- **Two-Factor Authentication** (optional) untuk admin

---

## ⚡ Performance Optimization Strategy

### Database Optimization
1. **Indexing Strategy**
   - Primary keys pada semua tables
   - Foreign keys untuk relations
   - Composite indexes untuk frequent queries
   - Full-text indexes untuk search
   
2. **Query Optimization**
   - Eager loading untuk prevent N+1 queries
   - Query result caching
   - Pagination untuk large datasets
   - Database query monitoring via Telescope

3. **Database Scaling**
   - Read replicas untuk heavy read operations
   - Connection pooling
   - Query queue untuk long-running queries

### Caching Strategy
1. **Application Level Caching**
   - Cache database query results (Redis)
   - Cache rendered views
   - Cache API responses
   - Session storage di Redis
   
2. **Cache Invalidation**
   - Time-based expiration
   - Event-based invalidation
   - Tag-based cache clearing
   - Cache warming strategy

### File & Asset Optimization
1. **Static Assets**
   - Asset minification (CSS, JS)
   - Image optimization (compression, lazy loading)
   - CDN untuk static files (production)
   - Browser caching headers
   
2. **Document Storage**
   - Chunked file uploads untuk large files
   - Background processing untuk document transformation
   - Thumbnail generation
   - Garbage collection untuk unused files

### Application Performance
1. **Queue System**
   - Background jobs untuk heavy operations
   - Email sending via queue
   - Document processing via queue
   - Report generation via queue
   
2. **Code Optimization**
   - Opcache untuk PHP bytecode caching
   - Autoload optimization
   - Route caching
   - Config caching
   
3. **API Optimization**
   - Response compression (gzip)
   - Pagination untuk list endpoints
   - Field filtering (sparse fieldsets)
   - Rate limiting untuk fair usage

---

## 📊 Scalability Considerations

### Horizontal Scaling
- **Load Balancer** untuk distribute traffic
- **Stateless Application Servers** untuk easy scaling
- **Centralized Session Storage** (Redis)
- **Distributed Queue Workers**

### Vertical Scaling
- **Database Server** dapat di-upgrade resources
- **Application Server** dapat di-upgrade resources
- **Redis Server** dapat di-upgrade memory

### Microservices Preparation (Future)
- **Service-Oriented Architecture** di application layer
- **API Gateway** ready untuk service splitting
- **Event-Driven Communication** antar services
- **Independent Database** per service (future)

---

## 🚀 Deployment Architecture

### Development Environment
```
Local Machine (XAMPP)
  ├── Apache Web Server
  ├── MySQL Database
  ├── PHP 8.4+
  ├── Redis (optional)
  └── Node.js (untuk asset compilation)
```

### Production Environment (Linux Server)
```
Production Stack
  ├── Nginx Web Server (reverse proxy)
  ├── PHP-FPM (application server)
  ├── MySQL 8.0+ (database server)
  ├── Redis (cache & queue)
  ├── Supervisor (queue worker management)
  └── SSL Certificate (Let's Encrypt)
```

### CI/CD Pipeline (Future)
```
Git Push → GitHub Actions/GitLab CI
    ↓
Automated Tests (PHPUnit)
    ↓
Build Assets (npm run build)
    ↓
Deploy to Staging Server
    ↓
Manual Approval
    ↓
Deploy to Production Server
    ↓
Database Migration
    ↓
Cache Clearing
    ↓
Health Check
```

---

## 📈 Monitoring & Maintenance

### Application Monitoring
- **Laravel Telescope** untuk development debugging
- **Laravel Horizon** untuk queue monitoring
- **Error Tracking** (Sentry, Bugsnag)
- **Performance Monitoring** (New Relic, DataDog - optional)
- **Uptime Monitoring** (Pingdom, UptimeRobot)

### Database Monitoring
- **Query Performance Analysis**
- **Slow Query Log**
- **Database Size Monitoring**
- **Connection Pool Monitoring**

### Server Monitoring
- **CPU & Memory Usage**
- **Disk Space Monitoring**
- **Network Traffic**
- **SSL Certificate Expiration**

### Backup Strategy
- **Daily Database Backups** (automated)
- **Weekly File Storage Backups**
- **Off-site Backup Storage**
- **Backup Testing** (monthly)
- **Disaster Recovery Plan**

---

## 🎨 User Interface Philosophy

### Design Principles
1. **User-Centric Design**
   - Intuitive navigation
   - Consistent UI patterns
   - Clear call-to-action buttons
   - Helpful error messages

2. **Accessibility**
   - WCAG 2.1 Level AA compliance
   - Keyboard navigation support
   - Screen reader friendly
   - Color contrast standards

3. **Responsive Design**
   - Mobile-first approach
   - Tablet optimization
   - Desktop experience
   - Progressive enhancement

4. **Performance**
   - Fast page load (<2 seconds)
   - Smooth animations
   - Lazy loading
   - Progressive image loading

### UI Component Structure
```
Layout Components
  ├── Navigation Bar (role-based menu)
  ├── Sidebar (contextual navigation)
  ├── Breadcrumbs (location indicator)
  └── Footer (links & info)

Page Components
  ├── Dashboard (personalized overview)
  ├── Content Viewer (article/module display)
  ├── Forms (consistent styling)
  └── Data Tables (sortable, filterable)

Interactive Components
  ├── Modal Dialogs
  ├── Dropdown Menus
  ├── Tooltips
  ├── Progress Indicators
  └── Notifications/Alerts
```

---

## 🔄 Data Flow Patterns

### Request Lifecycle
```
1. User Request
   ↓
2. Route Matching
   ↓
3. Middleware Processing
   - Authentication
   - Authorization
   - Rate Limiting
   - Logging
   ↓
4. Controller Action
   ↓
5. Service Layer Processing
   ↓
6. Repository/Data Access
   ↓
7. Business Logic
   ↓
8. Response Formatting
   ↓
9. View Rendering / JSON Response
   ↓
10. Response to User
```

### Background Job Flow
```
1. Event Triggered (e.g., Document Upload)
   ↓
2. Job Dispatched to Queue
   ↓
3. Queue Worker Picks Job
   ↓
4. Job Processing
   - Document Parsing
   - HTML Transformation
   - Image Processing
   ↓
5. Data Persistence
   ↓
6. Event Fired (Job Completed)
   ↓
7. Notification Sent to User
```

---

## 📝 Development Standards

### Code Organization
```
app/
├── Http/
│   ├── Controllers/          # Route handlers
│   ├── Middleware/           # Request filters
│   └── Requests/             # Form validations
├── Services/                 # Business logic
├── Repositories/             # Data access layer
├── Models/                   # Eloquent models
├── Events/                   # Event classes
├── Listeners/                # Event handlers
├── Jobs/                     # Queue jobs
├── Mail/                     # Email classes
└── Policies/                 # Authorization logic
```

### Naming Conventions
- **Controllers:** `StudentController`, `ModuleController`
- **Models:** Singular `Student`, `Module`, `Lesson`
- **Tables:** Plural `students`, `modules`, `lessons`
- **Routes:** Kebab-case `/student-progress`, `/learning-modules`
- **Methods:** camelCase `getUserProgress()`, `calculateScore()`
- **Constants:** UPPER_SNAKE_CASE `MAX_FILE_SIZE`, `DEFAULT_ROLE`

### Documentation Standards
- PHPDoc blocks untuk semua methods
- Inline comments untuk complex logic
- README per module
- API documentation (OpenAPI/Swagger)
- Database schema documentation

---

## 🎯 Success Metrics

### Technical Metrics
- **Response Time:** < 200ms (95th percentile)
- **Uptime:** > 99.9%
- **Database Query Time:** < 50ms (average)
- **Page Load Time:** < 2 seconds
- **API Throughput:** > 1000 requests/minute

### Business Metrics
- **User Engagement:** Daily active users
- **Course Completion Rate:** > 70%
- **User Satisfaction:** > 4.5/5 rating
- **Content Upload Success Rate:** > 99%
- **System Availability:** 24/7 access

---

## 🔮 Future Enhancements

### Phase 2 Features
- Mobile application (Flutter/React Native)
- Video content support
- Live virtual classroom
- Gamification (badges, points, leaderboard)
- Advanced analytics with ML

### Phase 3 Features
- Multi-language support
- AI-powered content recommendations
- Adaptive learning paths
- Integration dengan sistem pemerintah (SIMPKB, Dapodik)
- Blockchain-based certificates

---

## 📚 Reference Architecture Patterns

### Design Patterns Used
1. **Repository Pattern** - Data access abstraction
2. **Service Pattern** - Business logic encapsulation
3. **Observer Pattern** - Event-driven architecture
4. **Factory Pattern** - Object creation
5. **Strategy Pattern** - Algorithm selection
6. **Singleton Pattern** - Shared resources

### Architectural Principles
- **SOLID Principles** untuk maintainable code
- **DRY (Don't Repeat Yourself)** untuk code reusability
- **KISS (Keep It Simple, Stupid)** untuk simplicity
- **YAGNI (You Aren't Gonna Need It)** untuk avoid over-engineering
- **Separation of Concerns** untuk modularity

---

## 📞 Technical Support & Maintenance

### Documentation Maintenance
- Update documentation setiap release
- Version control untuk documentation
- Keep architecture diagrams current
- Document breaking changes

### Knowledge Transfer
- Code walkthroughs untuk new developers
- Architecture decision records (ADR)
- Video tutorials untuk key features
- Developer onboarding guide

---

**Document Version:** 1.0  
**Last Updated:** 12 Januari 2026  
**Next Review:** Setiap major release  
**Maintained By:** Development Team

---

