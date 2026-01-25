# 🎯 Features & Modules - LMS SEMPAT
## Comprehensive Feature Specification & Module Details

**Versi:** 1.0  
**Tanggal:** 12 Januari 2026  
**Target Users:** Siswa SMA/SMK, Guru, Admin

---

## 📋 Table of Contents

1. [Feature Overview](#feature-overview)
2. [User Roles & Permissions](#user-roles--permissions)
3. [FSDL Module Features](#fsdl-module-features)
4. [SPSDL Module Features](#spsdl-module-features)
5. [Document Import Features](#document-import-features)
6. [Assessment Features](#assessment-features)
7. [Self-Directed Learning Features](#self-directed-learning-features)
8. [Communication Features](#communication-features)
9. [Analytics & Reporting](#analytics--reporting)
10. [Administrative Features](#administrative-features)

---

## 🎯 Feature Overview

### Core Value Propositions

**For Students:**
- Flexible learning paths (structured & self-paced)
- Progress tracking & achievement visibility
- Self-directed learning tools (goals, journals)
- Interactive assessments with instant feedback
- Collaborative learning environment

**For Teachers:**
- Easy content creation & management
- Document import with auto-transformation
- Comprehensive student analytics
- Flexible assessment tools
- Communication & feedback channels

**For Administrators:**
- User management & access control
- System-wide analytics
- Content moderation
- Performance monitoring
- Configuration management

---

## 👥 User Roles & Permissions

**System Roles:** 3 main roles dengan hierarki permission yang jelas

### 1. Admin Role

**Capabilities:**
```
System Management:
├── Full system access
├── User management (create, edit, delete all users)
├── Role & permission management
├── System configuration
├── Database management
├── Security settings
└── Audit log access

Content Management:
├── Manage ALL courses & articles (all users)
├── Content moderation & approval
├── Category & tag management
├── Featured content selection
└── Bulk operations

User Management:
├── Create/edit/delete users
├── Assign roles to users
├── Manage student enrollments
├── View all user activities
├── Reset passwords
└── User account activation/deactivation

Analytics:
├── System-wide analytics
├── School-wide reports
├── Course completion reports
├── User engagement metrics
├── Performance metrics
└── Export all data
```

**Notes:** 
- Admin memiliki akses penuh ke semua fitur sistem
- Dapat manage konten semua user (teachers dan students)
- Bertanggung jawab atas keseluruhan sistem

---

### 2. Teacher/Guru Role

**Capabilities:**
```
Own Content Creation:
├── Create & manage OWN courses
├── Create & manage OWN articles
├── Upload & import documents (own)
├── Create modules & lessons (own)
├── Create quizzes & assessments (own)
├── Manage attachments (own)
├── Publish/unpublish own content
└── Delete own content

Student Management:
├── View enrolled students (own courses)
├── Track student progress (own courses)
├── Grade assessments (own courses)
├── Provide feedback (own students)
└── Communicate with own students

Content Discovery:
├── View all published courses (read-only)
├── View all published articles (read-only)
├── Browse course catalog
└── Access shared resources
└── Communicate with students

Analytics:
├── Course-specific analytics
├── Student progress reports
├── Assessment statistics
└── Export class reports
```

Analytics:
├── View course-specific analytics (own)
├── View student progress reports (own students)
├── View assessment statistics (own)
└── Export class reports (own)
```

**Notes:**
- Teacher hanya dapat manage konten yang mereka buat sendiri
- Tidak dapat edit/delete konten teacher lain
- Tidak dapat manage users atau system settings
- Fokus pada teaching dan student engagement

**Ownership Rules:**
- Setiap konten (course/article) memiliki `created_by` field
- Teacher hanya dapat CRUD konten dengan `created_by = user_id` mereka
- Admin dapat CRUD semua konten tanpa batasan

---

### 3. Student/Siswa Role

**Capabilities:**
```
Learning:
├── Browse course catalog
├── Enroll in courses
├── Access enrolled course content
├── Complete lessons
├── Take quizzes & assessments
├── Browse & read articles
├── Self-paced learning
└── View own learning progress

Self-Directed Learning:
├── Set personal learning goals
├── Write learning journal
├── Track study time
├── Bookmark articles & lessons
├── Create personal notes
└── View progress dashboard

Interaction:
├── Comment on content
├── Participate in discussions
├── Like & share content
├── Ask questions to teachers
├── Peer collaboration
└── Direct messaging
```

**Notes:**
- Student fokus pada learning dan self-development
- Tidak dapat create courses atau articles
- Dapat interact dengan konten dan peers
- Progress tracking otomatis

---

## � Permission Matrix Summary

| Feature | Admin | Teacher | Student |
|---------|-------|---------|--------|
| **User Management** |
| Create/Edit/Delete Users | ✅ All | ❌ | ❌ |
| Assign Roles | ✅ | ❌ | ❌ |
| View Users | ✅ All | ✅ Own Students | ❌ |
| **Content Management** |
| Create Courses/Articles | ✅ All | ✅ Own | ❌ |
| Edit Courses/Articles | ✅ All | ✅ Own Only | ❌ |
| Delete Courses/Articles | ✅ All | ✅ Own Only | ❌ |
| Publish Content | ✅ All | ✅ Own Only | ❌ |
| View Content | ✅ All | ✅ All Published | ✅ Enrolled |
| **Assessment** |
| Create Quizzes | ✅ All | ✅ Own Courses | ❌ |
| Grade Submissions | ✅ All | ✅ Own Courses | ❌ |
| Take Quizzes | ❌ | ❌ | ✅ |
| **Analytics** |
| System Analytics | ✅ | ❌ | ❌ |
| Course Analytics | ✅ All | ✅ Own | ❌ |
| Student Progress | ✅ All | ✅ Own Students | ✅ Own |
| **System** |
| Manage Settings | ✅ | ❌ | ❌ |
| View Logs | ✅ | ❌ | ❌ |
| Manage Categories/Tags | ✅ | ❌ | ❌ |

**Legend:**
- ✅ = Full Access
- ✅ Own = Access to own content only
- ✅ Own Students = Access to students in own courses only
- ❌ = No Access

---

## �📚 FSDL Module Features

### Course Management

#### 1.1 Course Creation & Structure

**Course Builder Interface:**
```
Course Creation Wizard:
Step 1: Basic Information
  ├── Title & slug
  ├── Description (rich text)
  ├── Thumbnail image
  ├── Category selection
  ├── Difficulty level (beginner/intermediate/advanced)
  └── Duration estimate

Step 2: Course Settings
  ├── Learning outcomes (list)
  ├── Prerequisites (other courses)
  ├── Enrollment settings
  │   ├── Enrollment limit
  │   ├── Enrollment period
  │   └── Auto-enrollment rules
  └── Course dates (start/end)

Step 3: Module Structure
  ├── Add modules
  ├── Define module sequence
  ├── Set module prerequisites
  └── Configure module settings

Step 4: Publish Settings
  ├── Publish immediately
  ├── Schedule publication
  └── Draft mode
```

**Course Hierarchy:**
```
Course
  └── Modules (ordered)
        └── Lessons (ordered)
              ├── Text Content (HTML)
              ├── Video Content (embedded/uploaded)
              ├── Documents (imported)
              ├── Attachments (files, images)
              └── Quizzes (optional)
```

#### 1.2 Module Management

**Module Features:**
```
Module Properties:
├── Title & slug
├── Description
├── Order/sequence
├── Prerequisites (other modules)
├── Estimated duration
├── Learning objectives
└── Publish status

Module Content:
├── Multiple lessons
├── Module quiz (summative)
├── Resources & downloads
└── Discussion forum
```

**Module Navigation:**
```
Sequential Navigation:
- Previous/Next lesson buttons
- Module progress indicator
- Lesson completion checkpoints
- Automatic unlock based on progress

Non-Sequential Options:
- Preview lessons (marked as preview)
- Downloadable resources (always accessible)
- Module overview (always accessible)
```

#### 1.3 Lesson Features

**Lesson Types:**

**A. Text Lesson**
```
Features:
├── Rich text editor (Markdown/WYSIWYG)
├── Code syntax highlighting
├── Embedded media (images, videos)
├── LaTeX math equations
├── Collapsible sections
└── Table of contents (auto-generated)

Student Features:
├── Reading progress tracking
├── Bookmark specific sections
├── Personal notes
├── Highlight text
└── Completion marking
```

**B. Video Lesson**
```
Features:
├── Video upload (MP4, WebM)
├── External embed (YouTube, Vimeo)
├── Video player controls
├── Playback speed control
├── Subtitle support (.srt, .vtt)
├── Chapter markers
└── Video transcript (searchable)

Tracking:
├── Watch progress percentage
├── Time spent watching
├── Completion threshold (80% watched)
└── Rewatch tracking
```

**C. Document Lesson**
```
Features:
├── Import .docx/.doc
├── Auto-transform to HTML
├── Preserve formatting
├── Extract images
├── Interactive viewing
└── Download original option

(See Document Import Features section for details)
```

**D. Mixed Lesson**
```
Combines:
├── Text content
├── Video embeds
├── Imported documents
├── Downloadable resources
└── Interactive elements
```

#### 1.4 Enrollment Management

**Enrollment Process:**

**Self-Enrollment:**
```
Flow:
1. Student browses course catalog
2. Views course details & preview lessons
3. Clicks "Enroll" button
4. System checks:
   - Prerequisites met?
   - Enrollment limit reached?
   - Enrollment period active?
5. Creates enrollment record
6. Grants access to course content
7. Sends confirmation notification
```

**Manual Enrollment (Teacher/Admin):**
```
Bulk Enrollment:
├── Upload CSV with student emails
├── Select students from list
├── Assign to course
├── Set enrollment date
└── Send notification

Individual Enrollment:
├── Search student
├── Select course
├── Enroll immediately
└── Optional custom message
```

**Enrollment Types:**
```
├── Active (currently learning)
├── Completed (finished all requirements)
├── Dropped (student withdrew)
└── Suspended (temporarily blocked)
```

#### 1.5 Progress Tracking

**Student Progress Dashboard:**
```
Overall Progress:
├── Courses enrolled: 5
├── Courses completed: 2
├── Courses in progress: 3
├── Total lessons completed: 87/150
├── Overall progress: 58%
└── Estimated completion: 2 weeks

Per-Course Progress:
├── Course title & thumbnail
├── Progress bar (visual)
├── Percentage complete
├── Lessons completed/total
├── Quizzes completed/total
├── Current module
├── Last accessed date
└── Continue learning button
```

**Progress Calculation Logic:**
```
Course Progress = 
  (Lessons Completed / Total Lessons) × 60% +
  (Quizzes Passed / Total Quizzes) × 40%

Module Progress =
  (Lessons Completed in Module / Total Lessons in Module) × 100%

Completion Criteria:
- All lessons completed
- All quizzes passed (score >= passing_score)
- Final assessment passed (if exists)
```

**Progress Milestones:**
```
Automatic Badges/Achievements:
├── First Lesson Complete
├── First Module Complete
├── 25% Course Complete
├── 50% Course Complete
├── 75% Course Complete
├── Course Complete
├── Perfect Quiz Score
├── 7-Day Learning Streak
└── 30-Day Learning Streak
```

---

## 📰 SPSDL Module Features

### Article Management

#### 2.1 Article Creation

**Article Editor:**
```
Editor Features:
├── Rich text WYSIWYG editor
├── Markdown support (optional)
├── Live preview
├── Auto-save drafts
├── Version history
├── Word count & reading time estimate
└── SEO optimization fields

Content Elements:
├── Headings (H1-H6)
├── Paragraphs with formatting
├── Lists (ordered, unordered)
├── Blockquotes
├── Code blocks with syntax highlighting
├── Tables
├── Images with captions
├── Embedded videos
├── Link management
└── Footnotes
```

**Article Metadata:**
```
Required:
├── Title (SEO optimized)
├── Slug (URL-friendly)
├── Content body
└── Author (auto-filled)

Optional:
├── Excerpt/summary
├── Featured image/thumbnail
├── Categories (multiple)
├── Tags (multiple)
├── Difficulty level
├── Reading time (auto-calculated)
├── Related articles
└── Publication date/time
```

#### 2.2 Content Organization

**Categories:**
```
Hierarchical Structure:
Programming
  ├── Web Development
  │     ├── Frontend
  │     ├── Backend
  │     └── Full Stack
  ├── Mobile Development
  └── Desktop Development

Mathematics
  ├── Algebra
  ├── Geometry
  └── Calculus

Science
  ├── Physics
  ├── Chemistry
  └── Biology
```

**Tagging System:**
```
Tag Features:
├── Flexible tagging (no hierarchy)
├── Auto-suggest existing tags
├── Popular tags widget
├── Tag cloud visualization
├── Tag-based search
└── Tag usage statistics

Example Tags:
- html, css, javascript, react
- tutorial, guide, reference
- beginner, advanced, expert
- tips, tricks, best-practices
```

#### 2.3 Article Discovery

**Browse & Filter:**
```
Filter Options:
├── By category
├── By tags (multiple)
├── By difficulty level
├── By reading time (short/medium/long)
├── By date (newest/oldest)
├── By popularity (views/likes)
└── By author

Sort Options:
├── Most recent
├── Most popular (views)
├── Most liked
├── Longest/shortest
└── Alphabetical
```

**Search Features:**
```
Search Capabilities:
├── Full-text search (title, excerpt, content)
├── Search within category
├── Search by tag
├── Search by author
├── Fuzzy search (typo tolerance)
├── Search suggestions
└── Search history (per user)

Search Results:
├── Highlighted keywords
├── Relevance ranking
├── Snippet preview
├── Faceted filtering
└── Pagination
```

**Personalized Recommendations:**
```
Recommendation Algorithm:
├── Based on reading history
├── Based on bookmarked articles
├── Based on completed courses
├── Based on learning goals
├── Similar content (tag matching)
├── Popular in category
└── Trending articles

Recommendation Display:
├── "Recommended for You" section
├── "Related Articles" sidebar
├── Email digest (weekly)
└── Dashboard widget
```

#### 2.4 Reading Experience

**Article Reader Features:**
```
Reading Mode:
├── Clean, distraction-free layout
├── Adjustable font size
├── Light/dark mode toggle
├── Reading progress indicator
├── Estimated time remaining
└── Table of contents (auto-generated)

Interactive Elements:
├── In-article navigation
├── Copy code button for code blocks
├── Image lightbox viewer
├── Expandable sections
├── Embedded quiz (optional)
└── Related resources
```

**Reading Progress:**
```
Progress Tracking:
├── Scroll position saved
├── Reading percentage calculated
├── Resume reading feature
├── Reading time tracked
├── Completion marked (90% scroll)
└── Reading history maintained

Progress Indicators:
├── Progress bar (top of article)
├── Percentage badge
├── "Continue Reading" bookmark
└── Reading statistics (profile page)
```

#### 2.5 Social Features

**Engagement Actions:**
```
Like/Unlike:
├── One-click like
├── Like count displayed
├── User's liked articles saved
└── Most liked ranking

Bookmark:
├── Save to reading list
├── Organize by collections
├── Offline access (future)
└── Bookmark sync across devices

Share:
├── Social media sharing (Facebook, Twitter, WhatsApp)
├── Copy link
├── QR code generation
└── Email article

Comments:
├── Threaded comments
├── Reply to comments
├── Like comments
├── Report inappropriate
└── Teacher moderation
```

---

## 📄 Document Import Features

### 3.1 Upload & Validation

**Supported Formats:**
```
Accepted Files:
├── .docx (Office 2007+)
├── .doc (Office 97-2003)
├── Maximum size: 50MB
└── Bulk upload: up to 10 files simultaneously
```

**Upload Process:**
```
1. File Selection
   ├── Drag & drop interface
   ├── File browser
   └── Clipboard paste (future)

2. Pre-Upload Validation
   ├── File type check
   ├── File size check
   ├── Virus scan (optional)
   └── Duplicate detection

3. Upload Progress
   ├── Progress bar per file
   ├── Overall progress
   ├── Cancel upload option
   └── Retry failed uploads

4. Post-Upload Actions
   ├── Attach to lesson/article
   ├── Process immediately
   ├── Schedule processing
   └── Store as draft
```

### 3.2 Document Transformation

**Parsing & Transformation Engine:**

**Content Extraction:**
```
Text Content:
├── Paragraphs with formatting
│   ├── Bold
│   ├── Italic
│   ├── Underline
│   ├── Strikethrough
│   └── Font colors
├── Headings (H1-H6)
├── Lists (ordered, unordered, nested)
├── Tables (with merged cells)
├── Blockquotes
└── Hyperlinks

Media Extraction:
├── Embedded images
│   ├── Extract to separate files
│   ├── Optimize size (resize, compress)
│   ├── Generate thumbnails
│   ├── Preserve aspect ratio
│   └── Convert to web formats (WebP, JPEG)
├── Charts (converted to images)
└── Diagrams (converted to images)

Metadata Extraction:
├── Document author
├── Creation date
├── Last modified date
├── Document title
├── Subject/keywords
├── Comments (preserved)
└── Word count
```

**HTML Conversion:**
```
Conversion Process:
1. Parse .docx/.doc structure
2. Extract content elements
3. Map to HTML elements
4. Apply styling (inline CSS or classes)
5. Process embedded media
6. Sanitize HTML (remove malicious code)
7. Optimize output (minify, clean)
8. Generate preview
```

**Conversion Rules:**
```
Word Element → HTML Element
────────────────────────────
Heading 1 → <h1>
Heading 2 → <h2>
Normal text → <p>
Bold → <strong> or <b>
Italic → <em> or <i>
Underline → <u>
Bullet list → <ul><li>
Numbered list → <ol><li>
Table → <table><tr><td>
Image → <img src="...">
Hyperlink → <a href="...">
```

**Quality Preservation:**
```
Formatting Preservation:
├── Text alignment (left, center, right, justify)
├── Line spacing
├── Indentation
├── Font sizes (relative)
├── Text colors
├── Background colors
├── Border styling
└── Page breaks (converted to section breaks)

Structural Preservation:
├── Document hierarchy (headings)
├── List nesting levels
├── Table structure
├── Image positioning
└── Caption associations
```

### 3.3 Post-Transformation Actions

**Review & Edit:**
```
Preview Mode:
├── Side-by-side view (original vs HTML)
├── Highlight differences
├── Identify conversion issues
└── Quality check report

HTML Editor:
├── Visual editor (WYSIWYG)
├── Source code editor
├── Fix formatting issues
├── Adjust styling
├── Add/remove elements
└── Re-process if needed
```

**Asset Management:**
```
Extracted Images:
├── View all extracted images
├── Edit alt text
├── Replace image
├── Delete unused images
├── Optimize further
└── Download originals

Attachments:
├── Store original .docx/.doc
├── Store converted HTML
├── Store extracted images
├── Version tracking
└── Storage optimization
```

**Publishing Options:**
```
Publish Workflow:
├── Save as draft
├── Publish immediately
├── Schedule publication
├── Attach to existing lesson
├── Create new lesson
├── Create new article
└── Export HTML (standalone)
```

### 3.4 Update & Version Control

**Document Updates:**
```
Update Process:
1. Upload new version of document
2. System detects existing document
3. Options:
   ├── Replace existing (new version)
   ├── Create duplicate
   └── Merge changes (future)
4. Re-transform if selected
5. Update linked content
6. Notify affected users (optional)
```

**Version History:**
```
Track Versions:
├── Version number (auto-increment)
├── Upload date/time
├── Uploaded by (user)
├── File size
├── Change log (if provided)
└── Restore previous version

Version Comparison:
├── Diff viewer
├── Highlight changes
├── Side-by-side comparison
└── Export differences
```

### 3.5 Error Handling

**Transformation Failures:**
```
Common Issues:
├── Corrupted file
├── Unsupported features
├── Oversized images
├── Complex tables
├── Embedded macros
└── Password-protected files

Error Response:
├── Clear error message
├── Suggested actions
├── Manual fallback option
├── Support contact
└── Log error details (for admin)

Fallback Options:
├── Upload as raw attachment
├── Manual HTML entry
├── Simplified conversion
└── Request support
```

---

## ✅ Assessment Features

### 4.1 Quiz Creation

**Quiz Builder:**
```
Quiz Configuration:
├── Title & description
├── Quiz type (formative/summative/practice)
├── Time limit (optional)
├── Passing score (percentage)
├── Max attempts allowed
├── Show results immediately?
├── Shuffle questions?
├── Show correct answers after?
└── Allow review after submission?

Question Bank:
├── Create new questions
├── Import from question bank
├── Copy questions from other quizzes
├── Randomize question order
└── Assign points per question
```

**Question Types:**

**A. Multiple Choice**
```
Features:
├── Question text (with rich formatting)
├── Multiple options (2-10)
├── Single or multiple correct answers
├── Randomize option order
├── Explanation for correct answer
└── Points weight

Example:
Question: "What is the capital of Indonesia?"
Options:
  ○ A. Bandung
  ● B. Jakarta (Correct)
  ○ C. Surabaya
  ○ D. Medan
Explanation: "Jakarta is the capital and largest city of Indonesia."
```

**B. True/False**
```
Features:
├── Statement text
├── Correct answer (True/False)
├── Explanation
└── Points weight

Example:
Statement: "HTML is a programming language."
Correct Answer: False
Explanation: "HTML is a markup language, not a programming language."
```

**C. Essay/Short Answer**
```
Features:
├── Question/prompt text
├── Expected answer (for reference)
├── Manual grading required
├── Rubric/scoring guidelines
├── Character limit (optional)
└── Points weight

Grading:
├── Teacher reviews submissions
├── Assigns score (0 to max points)
├── Provides written feedback
└── Student views score & feedback
```

**D. Fill in the Blank**
```
Features:
├── Text with blanks (_____)
├── Correct answers for each blank
├── Case-sensitive option
├── Partial credit option
└── Multiple acceptable answers

Example:
"The _____ is the capital of _____."
Answers: ["Jakarta", "Indonesia"]
```

### 4.2 Quiz Taking Experience

**Quiz Start:**
```
Pre-Quiz Screen:
├── Quiz title & description
├── Number of questions
├── Time limit (if any)
├── Passing score
├── Attempts remaining
├── Previous attempt scores
└── Start button

Quiz Timer:
├── Countdown display (if time-limited)
├── Warning at 5 minutes remaining
├── Auto-submit when time expires
└── Pause option (admin/special circumstances)
```

**During Quiz:**
```
Interface:
├── Question navigator (sidebar)
│   ├── Question numbers
│   ├── Answered status (green check)
│   ├── Flagged questions (red flag)
│   └── Current question highlighted
├── Question display area
├── Answer input (based on question type)
├── Previous/Next buttons
├── Flag for review button
├── Save & continue button
└── Submit quiz button

Features:
├── Auto-save answers (every 30 seconds)
├── Jump to any question
├── Flag questions for review
├── Answer counter (answered/total)
└── Time remaining display
```

**Quiz Submission:**
```
Submission Flow:
1. Student clicks "Submit"
2. System checks:
   ├── All questions answered?
   ├── Show warning if not
   └── Confirm submission
3. Confirmation dialog
4. Process submission:
   ├── Auto-grade objective questions
   ├── Queue essay questions for grading
   ├── Calculate score
   └── Record attempt
5. Show results (based on settings)
```

### 4.3 Grading & Results

**Auto-Grading:**
```
Automatically Graded:
├── Multiple choice
├── True/False
├── Fill in the blank (exact match)
└── Calculated immediately upon submission

Grading Logic:
- Correct answer = full points
- Incorrect answer = 0 points
- Partial credit (multiple-select questions)
- No penalty for unanswered questions
```

**Manual Grading (Essays):**
```
Grading Interface:
├── List of submissions needing grading
├── Student name & attempt info
├── Question text & expected answer
├── Student's answer
├── Score input (0 to max points)
├── Feedback text area
├── Save & next button
└── Bulk grading options

Teacher Actions:
├── Review each essay
├── Assign score based on rubric
├── Provide detailed feedback
├── Save grading
└── Notify student
```

**Results Display:**
```
Result Summary:
├── Score (points earned / total points)
├── Percentage score
├── Pass/Fail status
├── Time taken
├── Attempt number
├── Submitted at (timestamp)
└── View detailed results button

Detailed Results:
├── Question-by-question breakdown
├── Student's answer
├── Correct answer (if shown)
├── Points earned/possible
├── Explanation (if provided)
├── Teacher feedback (for essays)
└── Overall feedback
```

**Multiple Attempts:**
```
Attempt Management:
├── Track each attempt separately
├── Show attempt history
├── Best score highlighted
├── Latest score (if counting latest)
├── Average score
└── Attempts remaining

Scoring Options:
├── Highest score counts
├── Latest score counts
├── Average of all attempts
└── First attempt only
```

### 4.4 Assessment Analytics

**Student View:**
```
Performance Dashboard:
├── Quiz completion rate
├── Average score across all quizzes
├── Highest/lowest scores
├── Time trends (improving/declining)
├── Comparison to class average
└── Weak areas identified
```

**Teacher View:**
```
Quiz Analytics:
├── Average score
├── Highest/lowest scores
├── Pass rate
├── Time statistics
├── Question difficulty analysis
├── Most missed questions
└── Student ranking

Class Analytics:
├── Overall performance trends
├── Question performance breakdown
├── Time spent per quiz
├── Completion rates
└── Export to Excel/PDF
```

---

## 🎓 Self-Directed Learning Features

### 5.1 Learning Goals

**Goal Setting:**
```
Goal Creation:
├── Goal title (e.g., "Master JavaScript")
├── Description (detailed objective)
├── Target date (deadline)
├── Associated courses/articles (optional)
├── Success criteria (measurable)
└── Priority level (high/medium/low)

Goal Types:
├── Complete specific course
├── Read X articles on topic
├── Achieve score >= X on quizzes
├── Study X hours per week
└── Custom goals
```

**Goal Tracking:**
```
Progress Monitoring:
├── Progress percentage (manual or auto)
├── Days remaining
├── Milestones achieved
├── Related activities logged
└── Visual progress chart

Status Updates:
├── Active (working on it)
├── Completed (achieved)
├── Abandoned (gave up)
└── Paused (temporarily stopped)
```

**Goal Dashboard:**
```
Overview:
├── Active goals (list)
├── Progress bars for each goal
├── Upcoming deadlines
├── Recently completed goals
├── Goal achievement rate
└── Suggested next goals
```

### 5.2 Learning Journal

**Journal Entry Creation:**
```
Entry Fields:
├── Entry date (auto-filled)
├── Title (optional)
├── Content (rich text)
├── Mood indicator
│   ├── Excited 😄
│   ├── Motivated 💪
│   ├── Neutral 😐
│   ├── Struggling 😓
│   └── Frustrated 😞
├── Hours studied (decimal)
├── Associated goal (optional)
├── Topics covered (tags)
├── Privacy setting (private/public)
└── Attachments (images, files)
```

**Reflection Prompts:**
```
Guided Questions:
├── "What did I learn today?"
├── "What challenges did I face?"
├── "How did I overcome them?"
├── "What will I focus on tomorrow?"
├── "What strategies worked well?"
└── "What needs improvement?"
```

**Journal Features:**
```
Entry Management:
├── Create new entry
├── Edit existing entries
├── Delete entries
├── Search entries (by keyword, date, mood)
├── Filter by goal
└── Calendar view

Insights & Analytics:
├── Study time trends
├── Mood patterns over time
├── Most productive days
├── Topics covered frequency
├── Longest study streaks
└── Journal consistency (entries per week)
```

### 5.3 Study Time Tracking

**Automatic Tracking:**
```
Tracked Activities:
├── Lesson reading time
├── Video watching time
├── Quiz taking time
├── Article reading time
└── Active engagement time

Tracking Logic:
├── Start timer on content access
├── Pause on inactivity (5 min)
├── Resume on activity
├── Stop on content exit
└── Store time spent in database
```

**Manual Time Logging:**
```
Log Entry:
├── Date & time
├── Activity description
├── Duration (hours/minutes)
├── Course/topic (optional)
├── Notes
└── Submit
```

**Time Analytics:**
```
Time Dashboard:
├── Total study time (today/week/month/all-time)
├── Daily average
├── Study time by course
├── Study time by subject
├── Study time trends (chart)
├── Most productive hours
└── Study streak (consecutive days)
```

### 5.4 Personal Learning Path

**Path Builder:**
```
Custom Learning Path:
├── Select courses/articles
├── Define sequence
├── Set deadlines
├── Add milestones
└── Save path

Recommended Paths:
├── AI-generated based on:
│   ├── Current skill level
│   ├── Learning goals
│   ├── Completed content
│   └── Popular paths
└── Teacher-curated paths
```

### 5.5 Bookmarks & Notes

**Bookmark System:**
```
Bookmark Features:
├── One-click bookmark
├── Organize into collections
├── Add personal tags
├── Add private notes
├── Sort & filter
└── Export bookmarks

Collections:
├── To Read
├── In Progress
├── Completed
├── Reference
└── Custom collections
```

**Note-Taking:**
```
Note Features:
├── Attach notes to content
├── Rich text formatting
├── Code snippets
├── Images
├── Links
├── Tags
└── Search notes

Note Organization:
├── By course
├── By topic
├── By date
├── By tags
└── Favorites
```

---

## 💬 Communication Features

### 6.1 Discussion Forums

**Forum Structure:**
```
Course Forums:
├── General Discussion
├── Questions & Answers
├── Module-specific threads
├── Announcements
└── Off-topic

Global Forums:
├── Subject categories
├── Study groups
├── Help & Support
└── Community
```

**Thread Features:**
```
Creating Threads:
├── Title
├── Content (rich text)
├── Category/tags
├── Attachments
├── Poll (optional)
└── Notify followers

Thread Actions:
├── Reply to thread
├── Quote previous reply
├── Like/upvote posts
├── Subscribe to thread
├── Report inappropriate content
├── Pin important threads (moderator)
└── Close/lock threads (moderator)
```

**Moderation:**
```
Moderator Tools:
├── Delete posts
├── Edit posts
├── Move threads
├── Close threads
├── Ban users (temporary/permanent)
├── Mark as answered (Q&A)
└── Feature thread
```

### 6.2 Direct Messaging

**Messaging Features:**
```
Message Composition:
├── Recipient selection
├── Subject line
├── Message body (rich text)
├── Attachments
├── Priority flag
└── Read receipt request

Inbox Features:
├── Unread messages count
├── Filter (unread/sent/archived)
├── Search messages
├── Mark as read/unread
├── Archive/delete
└── Report spam
```

### 6.3 Notifications

**Notification Types:**
```
Learning Notifications:
├── Course enrollment confirmed
├── New lesson published
├── Quiz available
├── Assignment due soon
├── Grade posted
└── Certificate earned

Social Notifications:
├── New comment on your post
├── Reply to your comment
├── Someone liked your content
├── New direct message
├── Mentioned in discussion
└── New follower

System Notifications:
├── Account updates
├── Password changed
├── Login from new device
├── System maintenance
└── Policy updates
```

**Notification Channels:**
```
Delivery Methods:
├── In-app notifications
│   ├── Bell icon badge
│   ├── Notification center
│   └── Real-time updates
├── Email notifications
│   ├── Immediate
│   ├── Daily digest
│   └── Weekly summary
└── Browser push (future)

Notification Settings:
├── Enable/disable per type
├── Choose delivery channel
├── Set quiet hours
├── Email frequency
└── Priority settings
```

---

## 📊 Analytics & Reporting

### 7.1 Student Analytics

**Personal Dashboard:**
```
Dashboard Widgets:
├── Learning Summary
│   ├── Courses enrolled/completed
│   ├── Lessons completed this week
│   ├── Study hours this week
│   ├── Current streak
│   └── Achievement badges
│
├── Progress Overview
│   ├── Course progress bars
│   ├── Upcoming deadlines
│   ├── Recent activities
│   └── Next recommended action
│
├── Performance Metrics
│   ├── Average quiz scores
│   ├── Score trends (chart)
│   ├── Strengths & weaknesses
│   └── Comparison to peers
│
└── Goals & Milestones
    ├── Active goals progress
    ├── Achieved goals
    ├── Journal entries this week
    └── Study time goals
```

**Detailed Reports:**
```
Progress Report:
├── Course-by-course breakdown
├── Time spent per course
├── Completion percentages
├── Quiz performance
├── Grades summary
└── Export to PDF

Learning Analytics:
├── Study patterns (time of day)
├── Most productive days
├── Learning velocity
├── Content preferences
├── Engagement score
└── Recommendations for improvement
```

### 7.2 Teacher Analytics

**Class Overview:**
```
Class Dashboard:
├── Enrolled students count
├── Active vs inactive students
├── Overall completion rate
├── Average class score
├── Content engagement rate
└── At-risk students alert
```

**Student Performance:**
```
Per-Student View:
├── Enrollment date
├── Last activity date
├── Progress percentage
├── Lessons completed
├── Quizzes taken & scores
├── Time spent learning
├── Engagement level
└── Notes/flags (teacher comments)

Bulk View:
├── Sortable table (all students)
├── Filter by status/performance
├── Export to Excel
├── Compare students
└── Identify patterns
```

**Content Analytics:**
```
Per-Course Metrics:
├── Total enrollments
├── Active learners
├── Completion rate
├── Average completion time
├── Drop-off points (which lesson students quit)
├── Most/least accessed content
└── Student feedback/ratings

Per-Lesson Metrics:
├── View count
├── Average time spent
├── Completion rate
├── Comments/questions count
├── Video completion rate (for video lessons)
└── Difficulty rating (student feedback)
```

### 7.3 Admin Analytics

**System-Wide Metrics:**
```
User Statistics:
├── Total users (by role)
├── Active users (daily/monthly)
├── New registrations (trend)
├── User growth rate
└── User retention rate

Content Statistics:
├── Total courses
├── Total articles
├── Content published (this month)
├── Most popular content
└── Content engagement rate

Performance Metrics:
├── System uptime
├── Average response time
├── Database query performance
├── Storage usage
└── Bandwidth usage
```

**Reports:**
```
Standard Reports:
├── Monthly activity report
├── Course completion report
├── User engagement report
├── Assessment performance report
├── Content usage report
└── System performance report

Custom Reports:
├── Date range selection
├── Metric selection
├── Filter by user/course/etc.
├── Chart type selection
└── Export formats (PDF, Excel, CSV)
```

---

## 🔧 Administrative Features

### 8.1 User Management

**User CRUD:**
```
Create User:
├── Basic info (name, email, username)
├── Assign role(s)
├── Set password
├── Send welcome email
└── Activate account

Edit User:
├── Update profile info
├── Change role
├── Reset password
├── Activate/deactivate
└── View activity log

Delete User:
├── Soft delete (preserve data)
├── Anonymize data
├── Transfer content to another user
└── Permanent delete (GDPR compliance)
```

**Bulk Operations:**
```
Bulk Actions:
├── Import users from CSV
├── Export user list
├── Bulk role assignment
├── Bulk course enrollment
├── Bulk email
└── Bulk activate/deactivate
```

### 8.2 Content Management

**Content Moderation:**
```
Approval Workflow:
├── Teacher creates content
├── Content marked as "pending approval"
├── Admin reviews content
├── Admin can:
│   ├── Approve (publish)
│   ├── Request changes (with notes)
│   └── Reject (with reason)
└── Teacher receives notification
```

**Content Actions:**
```
Admin Capabilities:
├── View all content (published & unpublished)
├── Edit any content
├── Delete any content
├── Feature content (highlight)
├── Archive old content
└── Restore deleted content
```

### 8.3 System Configuration

**General Settings:**
```
Site Configuration:
├── Site name & tagline
├── Logo & favicon
├── Default language
├── Timezone
├── Date/time format
└── Contact information

Feature Toggles:
├── Enable/disable self-registration
├── Enable/disable article comments
├── Enable/disable social features
├── Enable/disable gamification
└── Maintenance mode
```

**Email Settings:**
```
Email Configuration:
├── SMTP server settings
├── Sender name & address
├── Email templates
├── Notification settings
└── Test email function
```

**Security Settings:**
```
Security Configuration:
├── Password requirements
├── Session timeout
├── Two-factor authentication
├── IP whitelist/blacklist
├── CORS settings
└── API rate limits
```

---

**Document Version:** 1.0  
**Last Updated:** 12 Januari 2026  
**Total Features:** 100+  
**Feature Categories:** 10

---

## ⏱️ Active Study Time Tracking System (Detail)

### Overview
Sistem time tracking otomatis yang hanya menghitung waktu belajar AKTIF siswa dengan monitoring:
- Tab visibility (halaman aktif/tidak)
- User activity (mouse, keyboard, scroll)
- Idle detection (3 menit tanpa aktivitas)
- Window focus state

### Database Schema

#### 1. `lesson_completions`
```sql
-- Field baru
last_time_sync TIMESTAMP NULL  -- Kapan terakhir sync ke enrollment
-- Field existing yang digunakan
time_spent_seconds INT DEFAULT 0  -- Total waktu aktif (detik)
last_accessed_at TIMESTAMP NULL
```
#### 2. `enrollments`
```sql
-- Field existing yang digunakan
total_study_minutes INT DEFAULT 0  -- Aggregate dari lesson completions
```
#### 3. `learning_goals`
```sql
-- Field baru
total_study_seconds INT DEFAULT 0   -- Total waktu belajar (detik)
last_study_at TIMESTAMP NULL        -- Terakhir belajar
```

### Data Flow

#### Lesson Time Tracking
```
1. User buka lesson → JavaScript tracker START
2. Setiap 30 detik (jika aktif) → POST /api/lessons/{id}/track-time
3. Backend: Atomic increment lesson_completions.time_spent_seconds
4. Probabilistic sync (10% chance) → Aggregate ke enrollment.total_study_minutes
5. On lesson complete → Force sync semua pending time
```
#### Learning Goal Time Tracking
```
1. User di goal page → JavaScript tracker START
2. Setiap 60 detik (jika aktif) → POST /api/learning-goals/{id}/track-time
3. Backend: Atomic increment learning_goals.total_study_seconds
4. Progress auto-recalculate berdasarkan study time vs target
```

### API Endpoints

#### Lesson Tracking
**POST /api/lessons/{lesson}/track-time**
- Body: `{ seconds: <int> }`
- Response: `{ success: true, total_time: <int> }`

#### Learning Goal Tracking
**POST /api/learning-goals/{goal}/track-time**
- Body: `{ seconds: <int> }`
- Response: `{ success: true, total_time: <int> }`

### Frontend Implementation
- JavaScript tracker aktif hanya jika tab & window aktif
- Idle >3 menit = auto-pause
- Sync otomatis & probabilistik untuk efisiensi
- Progress bar & stat cards update real-time

---
