# 🗄️ Database Design - LMS SEMPAT
## Comprehensive Database Schema & Design Patterns

**Versi:** 1.0  
**Tanggal:** 12 Januari 2026  
**Database:** MySQL 8.0+  
**Charset:** utf8mb4_unicode_ci

---

## 📋 Table of Contents

1. [Database Design Principles](#database-design-principles)
2. [Entity Relationship Overview](#entity-relationship-overview)
3. [Core Tables](#core-tables)
4. [Module Tables](#module-tables)
5. [Indexing Strategy](#indexing-strategy)
6. [Data Integrity Rules](#data-integrity-rules)
7. [Performance Considerations](#performance-considerations)

---

## 🎯 Database Design Principles

### Design Philosophy
1. **Normalization** - 3NF (Third Normal Form) untuk reduce redundancy
2. **Referential Integrity** - Foreign keys dengan proper cascade rules
3. **Data Consistency** - Constraints dan validation rules
4. **Performance** - Strategic indexing dan query optimization
5. **Scalability** - Partitioning ready untuk future growth
6. **Audit Trail** - Timestamps dan soft deletes untuk semua tables

### Naming Conventions
- **Tables:** Plural, snake_case - `users`, `learning_modules`, `quiz_questions`
- **Columns:** Snake_case - `first_name`, `created_at`, `is_active`
- **Primary Keys:** `id` (BIGINT UNSIGNED AUTO_INCREMENT)
- **Foreign Keys:** `{table}_id` - `user_id`, `module_id`, `course_id`
- **Pivot Tables:** Alphabetically ordered - `course_user`, `module_tag`
- **Timestamps:** `created_at`, `updated_at`, `deleted_at`

### Common Column Standards
```
id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
uuid                CHAR(36) UNIQUE (untuk external references)
created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
deleted_at          TIMESTAMP NULL (soft delete)
created_by          BIGINT UNSIGNED (foreign key ke users)
updated_by          BIGINT UNSIGNED (foreign key ke users)
is_active           BOOLEAN DEFAULT TRUE
status              ENUM atau VARCHAR
```

---

## 🔗 Entity Relationship Overview

### High-Level ER Diagram

```
                    ┌─────────┐
                    │  Users  │
                    └─────────┘
                         │
        ┌────────────────┼────────────────┐
        │                │                │
        ▼                ▼                ▼
  ┌─────────┐      ┌─────────┐     ┌─────────┐
  │  Roles  │      │Profiles │     │Sessions │
  └─────────┘      └─────────┘     └─────────┘
        │
        │
        ▼
┌─────────────────────────────────────────────┐
│           Learning Content                   │
├─────────────┬─────────────┬─────────────────┤
│   FSDL      │   SPSDL     │   Shared        │
├─────────────┼─────────────┼─────────────────┤
│  Courses    │  Articles   │  Documents      │
│  Modules    │  Resources  │  Attachments    │
│  Lessons    │  Tags       │  Media          │
│  Quizzes    │  Categories │  Comments       │
└─────────────┴─────────────┴─────────────────┘
        │
        ▼
┌─────────────────────────────────────────────┐
│         Progress & Analytics                 │
├─────────────────────────────────────────────┤
│  Enrollments  │  Progress  │  Assessments  │
│  Activities   │  Logs      │  Reports      │
└─────────────────────────────────────────────┘
```

---

## 👥 Core Tables

### 1. Users Table
**Purpose:** Central user management untuk semua roles (Admin, Guru, Siswa)

```sql
TABLE: users
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── uuid                  CHAR(36) UNIQUE
├── username              VARCHAR(50) UNIQUE NOT NULL
├── email                 VARCHAR(255) UNIQUE NOT NULL
├── email_verified_at     TIMESTAMP NULL
├── password              VARCHAR(255) NOT NULL
├── remember_token        VARCHAR(100) NULL
├── first_name            VARCHAR(100) NOT NULL
├── last_name             VARCHAR(100) NOT NULL
├── phone_number          VARCHAR(20) NULL
├── avatar                VARCHAR(255) NULL
├── is_active             BOOLEAN DEFAULT TRUE
├── last_login_at         TIMESTAMP NULL
├── last_login_ip         VARCHAR(45) NULL
├── created_at            TIMESTAMP
├── updated_at            TIMESTAMP
└── deleted_at            TIMESTAMP NULL

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (uuid)
- UNIQUE INDEX (username)
- UNIQUE INDEX (email)
- INDEX (is_active)
- INDEX (deleted_at)

RELATIONSHIPS:
- Has many roles (many-to-many via role_user)
- Has one profile
- Has many enrollments
- Has many activities
- Has many created contents
```

### 2. Roles Table
**Purpose:** Role-based access control (RBAC)

```sql
TABLE: roles
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── name                  VARCHAR(50) UNIQUE NOT NULL
├── slug                  VARCHAR(50) UNIQUE NOT NULL
├── description           TEXT NULL
├── is_system             BOOLEAN DEFAULT FALSE
├── created_at            TIMESTAMP
└── updated_at            TIMESTAMP

PREDEFINED ROLES:
- Super Admin (full access)
- Admin (school management)
- Teacher/Guru (content creator, mentor)
- Student/Siswa (learner)

RELATIONSHIPS:
- Belongs to many users (many-to-many via role_user)
- Has many permissions (many-to-many via permission_role)
```

### 3. Role_User Pivot Table
**Purpose:** Many-to-many relationship antara users dan roles

```sql
TABLE: role_user
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── user_id               BIGINT UNSIGNED NOT NULL
├── role_id               BIGINT UNSIGNED NOT NULL
├── assigned_by           BIGINT UNSIGNED NULL
├── assigned_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP
└── expires_at            TIMESTAMP NULL

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (user_id, role_id)
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
- FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL
```

### 4. Permissions Table
**Purpose:** Granular permission management

```sql
TABLE: permissions
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── name                  VARCHAR(100) UNIQUE NOT NULL
├── slug                  VARCHAR(100) UNIQUE NOT NULL
├── description           TEXT NULL
├── module                VARCHAR(50) NOT NULL
├── created_at            TIMESTAMP
└── updated_at            TIMESTAMP

EXAMPLES:
- courses.create
- courses.edit
- courses.delete
- courses.publish
- students.view
- reports.export

RELATIONSHIPS:
- Belongs to many roles (many-to-many via permission_role)
```

### 5. User_Profiles Table
**Purpose:** Extended user information (one-to-one dengan users)

```sql
TABLE: user_profiles
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── user_id               BIGINT UNSIGNED UNIQUE NOT NULL
├── bio                   TEXT NULL
├── date_of_birth         DATE NULL
├── gender                ENUM('male', 'female', 'other') NULL
├── address               TEXT NULL
├── city                  VARCHAR(100) NULL
├── province              VARCHAR(100) NULL
├── postal_code           VARCHAR(10) NULL
├── school_name           VARCHAR(255) NULL (untuk siswa)
├── school_npsn           VARCHAR(20) NULL (Nomor Pokok Sekolah)
├── grade_level           VARCHAR(20) NULL (X, XI, XII)
├── major                 VARCHAR(100) NULL (IPA, IPS, etc.)
├── nis                   VARCHAR(50) NULL (Nomor Induk Siswa)
├── nip                   VARCHAR(50) NULL (Nomor Induk Pegawai - untuk guru)
├── preferences           JSON NULL (user settings)
├── created_at            TIMESTAMP
└── updated_at            TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (user_id)
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
- INDEX (school_npsn)
- INDEX (grade_level)
```

---

## 📚 FSDL (Facilitated Self-Directed Learning) Tables

### 6. Courses Table
**Purpose:** Top-level container untuk structured learning

```sql
TABLE: courses
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── uuid                  CHAR(36) UNIQUE
├── title                 VARCHAR(255) NOT NULL
├── slug                  VARCHAR(255) UNIQUE NOT NULL
├── description           TEXT NULL
├── thumbnail             VARCHAR(255) NULL
├── level                 ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner'
├── category              VARCHAR(100) NULL
├── prerequisites         JSON NULL (array of course IDs)
├── learning_outcomes     JSON NULL (array of strings)
├── duration_hours        INT UNSIGNED NULL
├── is_published          BOOLEAN DEFAULT FALSE
├── published_at          TIMESTAMP NULL
├── enrollment_limit      INT UNSIGNED NULL
├── enrollment_start      TIMESTAMP NULL
├── enrollment_end        TIMESTAMP NULL
├── start_date            TIMESTAMP NULL
├── end_date              TIMESTAMP NULL
├── created_by            BIGINT UNSIGNED NOT NULL
├── updated_by            BIGINT UNSIGNED NULL
├── created_at            TIMESTAMP
├── updated_at            TIMESTAMP
└── deleted_at            TIMESTAMP NULL

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (uuid)
- UNIQUE INDEX (slug)
- INDEX (is_published, published_at)
- INDEX (category)
- INDEX (created_by)
- INDEX (deleted_at)
- FOREIGN KEY (created_by) REFERENCES users(id)
- FULLTEXT INDEX (title, description)

RELATIONSHIPS:
- Belongs to user (creator)
- Has many modules
- Has many enrollments
- Belongs to many tags (via course_tag)
```

### 7. Modules Table
**Purpose:** Structural units dalam course

```sql
TABLE: modules
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── uuid                  CHAR(36) UNIQUE
├── course_id             BIGINT UNSIGNED NOT NULL
├── title                 VARCHAR(255) NOT NULL
├── slug                  VARCHAR(255) NOT NULL
├── description           TEXT NULL
├── order                 INT UNSIGNED NOT NULL
├── is_published          BOOLEAN DEFAULT FALSE
├── published_at          TIMESTAMP NULL
├── created_by            BIGINT UNSIGNED NOT NULL
├── created_at            TIMESTAMP
├── updated_at            TIMESTAMP
└── deleted_at            TIMESTAMP NULL

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (uuid)
- UNIQUE INDEX (course_id, slug)
- INDEX (course_id, order)
- INDEX (is_published)
- FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
- FOREIGN KEY (created_by) REFERENCES users(id)

RELATIONSHIPS:
- Belongs to course
- Has many lessons
- Has many quizzes
```

### 8. Lessons Table
**Purpose:** Individual learning content dalam module

```sql
TABLE: lessons
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── uuid                  CHAR(36) UNIQUE
├── module_id             BIGINT UNSIGNED NOT NULL
├── title                 VARCHAR(255) NOT NULL
├── slug                  VARCHAR(255) NOT NULL
├── content_type          ENUM('text', 'video', 'document', 'mixed') DEFAULT 'text'
├── content               LONGTEXT NULL (HTML content)
├── video_url             VARCHAR(500) NULL
├── duration_minutes      INT UNSIGNED NULL
├── order                 INT UNSIGNED NOT NULL
├── is_published          BOOLEAN DEFAULT FALSE
├── is_preview            BOOLEAN DEFAULT FALSE (dapat dilihat tanpa enroll)
├── published_at          TIMESTAMP NULL
├── created_by            BIGINT UNSIGNED NOT NULL
├── created_at            TIMESTAMP
├── updated_at            TIMESTAMP
└── deleted_at            TIMESTAMP NULL

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (uuid)
- UNIQUE INDEX (module_id, slug)
- INDEX (module_id, order)
- INDEX (is_published)
- FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
- FOREIGN KEY (created_by) REFERENCES users(id)
- FULLTEXT INDEX (title, content)

RELATIONSHIPS:
- Belongs to module
- Has many attachments
- Has many comments
- Has many progress records
```

### 9. Quizzes Table
**Purpose:** Assessments terkait module atau lesson

```sql
TABLE: quizzes
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── uuid                  CHAR(36) UNIQUE
├── module_id             BIGINT UNSIGNED NULL
├── lesson_id             BIGINT UNSIGNED NULL
├── title                 VARCHAR(255) NOT NULL
├── description           TEXT NULL
├── quiz_type             ENUM('formative', 'summative', 'practice') DEFAULT 'formative'
├── passing_score         DECIMAL(5,2) DEFAULT 70.00
├── time_limit_minutes    INT UNSIGNED NULL
├── max_attempts          INT UNSIGNED DEFAULT 3
├── show_results          BOOLEAN DEFAULT TRUE
├── shuffle_questions     BOOLEAN DEFAULT FALSE
├── is_published          BOOLEAN DEFAULT FALSE
├── published_at          TIMESTAMP NULL
├── created_by            BIGINT UNSIGNED NOT NULL
├── created_at            TIMESTAMP
├── updated_at            TIMESTAMP
└── deleted_at            TIMESTAMP NULL

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (uuid)
- INDEX (module_id)
- INDEX (lesson_id)
- INDEX (is_published)
- FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
- FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
- FOREIGN KEY (created_by) REFERENCES users(id)

CONSTRAINTS:
- CHECK: module_id IS NOT NULL OR lesson_id IS NOT NULL
```

### 10. Quiz_Questions Table
**Purpose:** Individual questions dalam quiz

```sql
TABLE: quiz_questions
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── uuid                  CHAR(36) UNIQUE
├── quiz_id               BIGINT UNSIGNED NOT NULL
├── question_text         TEXT NOT NULL
├── question_type         ENUM('multiple_choice', 'true_false', 'essay', 'fill_blank') NOT NULL
├── options               JSON NULL (untuk multiple choice)
├── correct_answer        TEXT NULL (untuk auto-grade types)
├── points                DECIMAL(5,2) DEFAULT 1.00
├── explanation           TEXT NULL (penjelasan jawaban)
├── order                 INT UNSIGNED NOT NULL
├── created_at            TIMESTAMP
└── updated_at            TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (uuid)
- INDEX (quiz_id, order)
- FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
```

### 11. Enrollments Table
**Purpose:** Student enrollment ke courses

```sql
TABLE: enrollments
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── uuid                  CHAR(36) UNIQUE
├── user_id               BIGINT UNSIGNED NOT NULL
├── course_id             BIGINT UNSIGNED NOT NULL
├── enrolled_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP
├── completed_at          TIMESTAMP NULL
├── progress_percentage   DECIMAL(5,2) DEFAULT 0.00
├── status                ENUM('active', 'completed', 'dropped', 'suspended') DEFAULT 'active'
├── enrollment_source     VARCHAR(50) NULL (manual, self-enroll, etc.)
├── enrolled_by           BIGINT UNSIGNED NULL
├── created_at            TIMESTAMP
└── updated_at            TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (uuid)
- UNIQUE INDEX (user_id, course_id)
- INDEX (user_id, status)
- INDEX (course_id, status)
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
- FOREIGN KEY (enrolled_by) REFERENCES users(id) ON DELETE SET NULL

RELATIONSHIPS:
- Belongs to user
- Belongs to course
- Has many lesson completions
- Has many quiz attempts
```

### 12. Lesson_Completions Table
**Purpose:** Track lesson completion per student

```sql
TABLE: lesson_completions
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── user_id               BIGINT UNSIGNED NOT NULL
├── lesson_id             BIGINT UNSIGNED NOT NULL
├── enrollment_id         BIGINT UNSIGNED NOT NULL
├── completed_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
├── time_spent_seconds    INT UNSIGNED DEFAULT 0
├── notes                 TEXT NULL (student notes)
└── created_at            TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (user_id, lesson_id)
- INDEX (enrollment_id)
- INDEX (completed_at)
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
- FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE
```

### 13. Quiz_Attempts Table
**Purpose:** Track quiz attempts dan scores

```sql
TABLE: quiz_attempts
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── uuid                  CHAR(36) UNIQUE
├── user_id               BIGINT UNSIGNED NOT NULL
├── quiz_id               BIGINT UNSIGNED NOT NULL
├── enrollment_id         BIGINT UNSIGNED NULL
├── attempt_number        INT UNSIGNED NOT NULL
├── started_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP
├── submitted_at          TIMESTAMP NULL
├── score                 DECIMAL(5,2) NULL
├── max_score             DECIMAL(5,2) NOT NULL
├── percentage            DECIMAL(5,2) NULL
├── passed                BOOLEAN NULL
├── answers               JSON NULL (student answers)
├── feedback              TEXT NULL (teacher feedback)
├── created_at            TIMESTAMP
└── updated_at            TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (uuid)
- UNIQUE INDEX (user_id, quiz_id, attempt_number)
- INDEX (user_id, quiz_id)
- INDEX (enrollment_id)
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
- FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE
```

---

## 📰 SPSDL (Self-Paced Self-Directed Learning) Tables

### 14. Articles Table
**Purpose:** Self-paced learning content (tidak terstruktur seperti course)

```sql
TABLE: articles
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── uuid                  CHAR(36) UNIQUE
├── title                 VARCHAR(255) NOT NULL
├── slug                  VARCHAR(255) UNIQUE NOT NULL
├── excerpt               TEXT NULL
├── content               LONGTEXT NOT NULL (HTML content)
├── thumbnail             VARCHAR(255) NULL
├── reading_time_minutes  INT UNSIGNED NULL
├── difficulty_level      ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner'
├── is_published          BOOLEAN DEFAULT FALSE
├── published_at          TIMESTAMP NULL
├── view_count            INT UNSIGNED DEFAULT 0
├── like_count            INT UNSIGNED DEFAULT 0
├── featured              BOOLEAN DEFAULT FALSE
├── created_by            BIGINT UNSIGNED NOT NULL
├── updated_by            BIGINT UNSIGNED NULL
├── created_at            TIMESTAMP
├── updated_at            TIMESTAMP
└── deleted_at            TIMESTAMP NULL

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (uuid)
- UNIQUE INDEX (slug)
- INDEX (is_published, published_at)
- INDEX (featured, published_at)
- INDEX (created_by)
- INDEX (deleted_at)
- FOREIGN KEY (created_by) REFERENCES users(id)
- FULLTEXT INDEX (title, excerpt, content)

RELATIONSHIPS:
- Belongs to user (author)
- Has many attachments
- Has many comments
- Belongs to many categories
- Belongs to many tags
- Has many reading progress records
```

### 15. Article_Categories Table
**Purpose:** Kategorisasi articles

```sql
TABLE: article_categories
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── name                  VARCHAR(100) NOT NULL
├── slug                  VARCHAR(100) UNIQUE NOT NULL
├── description           TEXT NULL
├── parent_id             BIGINT UNSIGNED NULL (untuk nested categories)
├── order                 INT UNSIGNED DEFAULT 0
├── is_active             BOOLEAN DEFAULT TRUE
├── created_at            TIMESTAMP
└── updated_at            TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (slug)
- INDEX (parent_id)
- FOREIGN KEY (parent_id) REFERENCES article_categories(id) ON DELETE SET NULL
```

### 16. Article_Category Pivot Table
**Purpose:** Many-to-many relationship articles dan categories

```sql
TABLE: article_category
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── article_id            BIGINT UNSIGNED NOT NULL
├── category_id           BIGINT UNSIGNED NOT NULL
├── created_at            TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (article_id, category_id)
- FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
- FOREIGN KEY (category_id) REFERENCES article_categories(id) ON DELETE CASCADE
```

### 17. Tags Table
**Purpose:** Flexible tagging untuk articles dan courses

```sql
TABLE: tags
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── name                  VARCHAR(50) UNIQUE NOT NULL
├── slug                  VARCHAR(50) UNIQUE NOT NULL
├── usage_count           INT UNSIGNED DEFAULT 0
├── created_at            TIMESTAMP
└── updated_at            TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (slug)
- INDEX (usage_count DESC) (untuk popular tags)
```

### 18. Article_Tag Pivot Table
**Purpose:** Many-to-many relationship articles dan tags

```sql
TABLE: article_tag
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── article_id            BIGINT UNSIGNED NOT NULL
├── tag_id                BIGINT UNSIGNED NOT NULL
├── created_at            TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (article_id, tag_id)
- FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
- FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
```

### 19. Article_Reading_Progress Table
**Purpose:** Track reading progress untuk self-paced learning

```sql
TABLE: article_reading_progress
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── user_id               BIGINT UNSIGNED NOT NULL
├── article_id            BIGINT UNSIGNED NOT NULL
├── progress_percentage   DECIMAL(5,2) DEFAULT 0.00
├── scroll_position       INT UNSIGNED DEFAULT 0
├── completed             BOOLEAN DEFAULT FALSE
├── completed_at          TIMESTAMP NULL
├── time_spent_seconds    INT UNSIGNED DEFAULT 0
├── last_read_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
├── created_at            TIMESTAMP
└── updated_at            TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (user_id, article_id)
- INDEX (user_id, completed)
- INDEX (article_id, completed)
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
```

### 20. Learning_Goals Table
**Purpose:** Student-defined learning goals (SDL feature)

```sql
TABLE: learning_goals
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── uuid                  CHAR(36) UNIQUE
├── user_id               BIGINT UNSIGNED NOT NULL
├── title                 VARCHAR(255) NOT NULL
├── description           TEXT NULL
├── target_date           DATE NULL
├── status                ENUM('active', 'completed', 'abandoned') DEFAULT 'active'
├── progress_percentage   DECIMAL(5,2) DEFAULT 0.00
├── completed_at          TIMESTAMP NULL
├── created_at            TIMESTAMP
└── updated_at            TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (uuid)
- INDEX (user_id, status)
- INDEX (target_date)
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
```

### 21. Learning_Journal Table
**Purpose:** Reflective learning journal (SDL feature)

```sql
TABLE: learning_journal
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── uuid                  CHAR(36) UNIQUE
├── user_id               BIGINT UNSIGNED NOT NULL
├── goal_id               BIGINT UNSIGNED NULL
├── entry_date            DATE NOT NULL
├── title                 VARCHAR(255) NULL
├── content               TEXT NOT NULL
├── mood                  ENUM('excited', 'motivated', 'neutral', 'struggling', 'frustrated') NULL
├── hours_studied         DECIMAL(4,2) NULL
├── is_private            BOOLEAN DEFAULT TRUE
├── created_at            TIMESTAMP
└── updated_at            TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (uuid)
- INDEX (user_id, entry_date DESC)
- INDEX (goal_id)
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY (goal_id) REFERENCES learning_goals(id) ON DELETE SET NULL
```

---

## 📎 Shared/Common Tables

### 22. Documents Table
**Purpose:** Imported documents (.docx/.doc) yang ditransformasi

```sql
TABLE: documents
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── uuid                  CHAR(36) UNIQUE
├── documentable_type     VARCHAR(50) NOT NULL (polymorphic)
├── documentable_id       BIGINT UNSIGNED NOT NULL (polymorphic)
├── original_filename     VARCHAR(255) NOT NULL
├── stored_filename       VARCHAR(255) NOT NULL
├── file_path             VARCHAR(500) NOT NULL
├── file_size_bytes       BIGINT UNSIGNED NOT NULL
├── mime_type             VARCHAR(100) NOT NULL
├── file_extension        VARCHAR(10) NOT NULL
├── transformed_html      LONGTEXT NULL
├── transformation_status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending'
├── transformation_error  TEXT NULL
├── metadata              JSON NULL (author, created date, etc.)
├── version               INT UNSIGNED DEFAULT 1
├── uploaded_by           BIGINT UNSIGNED NOT NULL
├── created_at            TIMESTAMP
└── updated_at            TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (uuid)
- INDEX (documentable_type, documentable_id)
- INDEX (transformation_status)
- INDEX (uploaded_by)
- FOREIGN KEY (uploaded_by) REFERENCES users(id)
- FULLTEXT INDEX (transformed_html)

RELATIONSHIPS:
- Morphs to lesson, article, or other content types
- Has many attachments (extracted images/files)
- Belongs to user (uploader)
```

### 23. Attachments Table
**Purpose:** File attachments (images, PDFs, etc.)

```sql
TABLE: attachments
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── uuid                  CHAR(36) UNIQUE
├── attachable_type       VARCHAR(50) NOT NULL (polymorphic)
├── attachable_id         BIGINT UNSIGNED NOT NULL (polymorphic)
├── document_id           BIGINT UNSIGNED NULL (if extracted from document)
├── filename              VARCHAR(255) NOT NULL
├── stored_filename       VARCHAR(255) NOT NULL
├── file_path             VARCHAR(500) NOT NULL
├── file_size_bytes       BIGINT UNSIGNED NOT NULL
├── mime_type             VARCHAR(100) NOT NULL
├── file_type             VARCHAR(50) NOT NULL (image, pdf, video, etc.)
├── thumbnail_path        VARCHAR(500) NULL
├── alt_text              VARCHAR(255) NULL
├── caption               TEXT NULL
├── order                 INT UNSIGNED DEFAULT 0
├── uploaded_by           BIGINT UNSIGNED NOT NULL
├── created_at            TIMESTAMP
└── updated_at            TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (uuid)
- INDEX (attachable_type, attachable_id)
- INDEX (document_id)
- INDEX (file_type)
- FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE
- FOREIGN KEY (uploaded_by) REFERENCES users(id)

RELATIONSHIPS:
- Morphs to lesson, article, document, etc.
- Optionally belongs to document
```

### 24. Comments Table
**Purpose:** Discussion/comments pada content

```sql
TABLE: comments
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── uuid                  CHAR(36) UNIQUE
├── commentable_type      VARCHAR(50) NOT NULL (polymorphic)
├── commentable_id        BIGINT UNSIGNED NOT NULL (polymorphic)
├── parent_id             BIGINT UNSIGNED NULL (untuk nested comments)
├── user_id               BIGINT UNSIGNED NOT NULL
├── content               TEXT NOT NULL
├── is_approved           BOOLEAN DEFAULT TRUE
├── is_pinned             BOOLEAN DEFAULT FALSE
├── like_count            INT UNSIGNED DEFAULT 0
├── created_at            TIMESTAMP
├── updated_at            TIMESTAMP
└── deleted_at            TIMESTAMP NULL

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (uuid)
- INDEX (commentable_type, commentable_id)
- INDEX (parent_id)
- INDEX (user_id)
- INDEX (created_at DESC)
- FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
```

### 25. Notifications Table
**Purpose:** In-app notifications

```sql
TABLE: notifications
├── id                    CHAR(36) PRIMARY KEY (UUID)
├── type                  VARCHAR(255) NOT NULL
├── notifiable_type       VARCHAR(50) NOT NULL (polymorphic - usually User)
├── notifiable_id         BIGINT UNSIGNED NOT NULL
├── data                  JSON NOT NULL
├── read_at               TIMESTAMP NULL
├── created_at            TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- INDEX (notifiable_type, notifiable_id, read_at)
- INDEX (created_at DESC)
```

### 26. Activity_Logs Table
**Purpose:** Comprehensive activity tracking

```sql
TABLE: activity_logs
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── log_name              VARCHAR(50) NULL
├── description           TEXT NOT NULL
├── subject_type          VARCHAR(50) NULL (polymorphic)
├── subject_id            BIGINT UNSIGNED NULL
├── causer_type           VARCHAR(50) NULL (polymorphic - usually User)
├── causer_id             BIGINT UNSIGNED NULL
├── properties            JSON NULL (old/new values)
├── ip_address            VARCHAR(45) NULL
├── user_agent            VARCHAR(255) NULL
├── created_at            TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- INDEX (log_name)
- INDEX (subject_type, subject_id)
- INDEX (causer_type, causer_id)
- INDEX (created_at DESC)
```

### 27. Sessions Table
**Purpose:** User session management

```sql
TABLE: sessions
├── id                    VARCHAR(255) PRIMARY KEY
├── user_id               BIGINT UNSIGNED NULL
├── ip_address            VARCHAR(45) NULL
├── user_agent            TEXT NULL
├── payload               LONGTEXT NOT NULL
├── last_activity         INT UNSIGNED NOT NULL

INDEXES:
- PRIMARY KEY (id)
- INDEX (user_id)
- INDEX (last_activity)
```

### 28. Cache Table
**Purpose:** Database-based cache storage (optional)

```sql
TABLE: cache
├── key                   VARCHAR(255) PRIMARY KEY
├── value                 MEDIUMTEXT NOT NULL
└── expiration            INT UNSIGNED NOT NULL

INDEXES:
- PRIMARY KEY (key)
- INDEX (expiration)
```

### 29. Jobs Table
**Purpose:** Queue job storage

```sql
TABLE: jobs
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── queue                 VARCHAR(255) NOT NULL
├── payload               LONGTEXT NOT NULL
├── attempts              TINYINT UNSIGNED NOT NULL
├── reserved_at           INT UNSIGNED NULL
├── available_at          INT UNSIGNED NOT NULL
├── created_at            INT UNSIGNED NOT NULL

INDEXES:
- PRIMARY KEY (id)
- INDEX (queue, reserved_at)
```

### 30. Failed_Jobs Table
**Purpose:** Failed queue jobs untuk retry

```sql
TABLE: failed_jobs
├── id                    BIGINT UNSIGNED PRIMARY KEY
├── uuid                  CHAR(36) UNIQUE
├── connection            TEXT NOT NULL
├── queue                 TEXT NOT NULL
├── payload               LONGTEXT NOT NULL
├── exception             LONGTEXT NOT NULL
├── failed_at             TIMESTAMP DEFAULT CURRENT_TIMESTAMP

INDEXES:
- PRIMARY KEY (id)
- UNIQUE INDEX (uuid)
- INDEX (failed_at)
```

---

## 🔍 Indexing Strategy

### Primary Indexes
- **Primary Keys:** Auto-increment BIGINT UNSIGNED pada semua tables
- **UUIDs:** Unique indexes untuk external references

### Foreign Key Indexes
- Semua foreign key columns di-index otomatis
- Composite indexes untuk pivot tables

### Performance Indexes
```sql
-- High-frequency queries
INDEX (user_id, created_at DESC) -- untuk user activities
INDEX (course_id, is_published) -- untuk published course contents
INDEX (is_active, deleted_at) -- untuk active records
```

### Full-Text Indexes
```sql
-- Search optimization
FULLTEXT (title, content) ON articles
FULLTEXT (title, description) ON courses
FULLTEXT (transformed_html) ON documents
```

### Composite Indexes
```sql
-- Multi-column queries
INDEX (user_id, status, created_at) ON enrollments
INDEX (course_id, order) ON modules
INDEX (module_id, order) ON lessons
```

---

## 🔒 Data Integrity Rules

### Foreign Key Constraints

**ON DELETE CASCADE** (data dependent sepenuhnya):
- `modules.course_id` → courses
- `lessons.module_id` → modules
- `quiz_questions.quiz_id` → quizzes
- `enrollments` → users, courses
- `comments.parent_id` → comments

**ON DELETE SET NULL** (data bisa standalone):
- `documents.uploaded_by` → users
- `articles.updated_by` → users
- `learning_goals.completed_by` → users

**ON DELETE RESTRICT** (harus dihapus manual):
- Tidak digunakan, lebih prefer soft deletes

### Check Constraints
```sql
-- Logical validations
CHECK (passing_score >= 0 AND passing_score <= 100)
CHECK (progress_percentage >= 0 AND progress_percentage <= 100)
CHECK (max_attempts >= 1)
CHECK (time_limit_minutes > 0 OR time_limit_minutes IS NULL)
CHECK (end_date IS NULL OR end_date >= start_date)
```

### Unique Constraints
```sql
-- Prevent duplicates
UNIQUE (user_id, course_id) ON enrollments
UNIQUE (user_id, lesson_id) ON lesson_completions
UNIQUE (user_id, article_id) ON article_reading_progress
UNIQUE (course_id, slug) ON modules
UNIQUE (module_id, slug) ON lessons
```

---

## ⚡ Performance Considerations

### Query Optimization Strategies

1. **Eager Loading**
   - Load related models dalam single query
   - Prevent N+1 query problems
   - Use Laravel's `with()` method

2. **Pagination**
   - Always paginate list queries
   - Default: 20 items per page
   - Use cursor pagination untuk large datasets

3. **Selective Column Loading**
   - Only select needed columns
   - Avoid `SELECT *` when possible
   - Use Laravel's `select()` method

4. **Caching Strategy**
   - Cache frequently accessed data (courses list, popular articles)
   - Cache duration: 1 hour for content, 5 minutes for user data
   - Tag-based cache invalidation

### Table Partitioning (Future)

**Partitioning Candidates:**
```sql
-- By date range (monthly/yearly)
activity_logs (by created_at)
notifications (by created_at)
sessions (by last_activity)

-- By status
enrollments (by status)
quiz_attempts (by submitted_at)
```

### Archive Strategy

**Tables to Archive:**
- `activity_logs` older than 1 year
- `sessions` older than 30 days
- `notifications` older than 90 days
- Completed `enrollments` older than 2 years

**Archive Method:**
- Move to separate archive database
- Keep last 2 years in main database
- Automated monthly archiving job

---

## 📊 Database Statistics & Monitoring

### Key Metrics to Monitor

1. **Table Size**
   - Monitor table growth
   - Alert if exceeds threshold
   - Plan for scaling

2. **Query Performance**
   - Slow query log (> 1 second)
   - Most frequent queries
   - Index usage statistics

3. **Connection Pool**
   - Active connections
   - Connection wait time
   - Connection errors

4. **Replication Lag** (if using replication)
   - Seconds behind master
   - Replication errors

### Maintenance Tasks

**Daily:**
- Monitor slow queries
- Check error logs
- Verify backups

**Weekly:**
- Analyze table statistics
- Update query execution plans
- Review index usage

**Monthly:**
- Optimize tables (OPTIMIZE TABLE)
- Archive old data
- Review and update indexes

---

## 🔄 Migration Strategy

### Migration Files Organization

```
database/migrations/
├── 2026_01_01_000000_create_users_table.php
├── 2026_01_01_000001_create_roles_table.php
├── 2026_01_01_000002_create_permissions_table.php
├── 2026_01_01_000003_create_user_profiles_table.php
├── 2026_01_02_000000_create_courses_table.php
├── 2026_01_02_000001_create_modules_table.php
├── 2026_01_02_000002_create_lessons_table.php
├── 2026_01_02_000003_create_quizzes_table.php
├── 2026_01_03_000000_create_articles_table.php
├── 2026_01_03_000001_create_article_categories_table.php
├── 2026_01_03_000002_create_tags_table.php
├── 2026_01_04_000000_create_documents_table.php
├── 2026_01_04_000001_create_attachments_table.php
├── 2026_01_04_000002_create_comments_table.php
└── 2026_01_05_000000_create_activity_logs_table.php
```

### Seeder Strategy

**Master Data Seeders:**
- Roles & Permissions
- Default Admin User
- Article Categories
- Sample Tags

**Development Seeders:**
- Test Users (Guru, Siswa)
- Sample Courses & Modules
- Sample Articles
- Sample Quizzes

**Production Seeders:**
- Only essential master data
- No test/dummy data

---

## 📋 Database Backup Plan

### Backup Schedule

**Full Backups:**
- Frequency: Daily at 2:00 AM
- Retention: 30 days
- Storage: Off-site backup server

**Incremental Backups:**
- Frequency: Every 6 hours
- Retention: 7 days
- Storage: Local backup server

**Transaction Log Backups:**
- Frequency: Every hour
- Retention: 24 hours
- Storage: Local backup server

### Disaster Recovery

**RTO (Recovery Time Objective):** 4 hours
**RPO (Recovery Point Objective):** 1 hour
**Backup Testing:** Monthly restoration test

---

**Document Version:** 1.0  
**Last Updated:** 12 Januari 2026  
**Total Tables:** 30+  
**Estimated Database Size:** 50GB (first year, 10,000 active users)

---
