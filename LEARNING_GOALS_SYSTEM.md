# Learning Goals & Milestone System

## 📌 Overview

Sistem Learning Goals dengan Milestone adalah fitur self-assessment yang memungkinkan siswa untuk:
- Menetapkan tujuan pembelajaran sendiri
- Melacak progress dengan milestone yang terstruktur
- Mencatat study time harian
- Menyimpan final project sebagai bukti pencapaian
- Otomatis update progress dari journal entries

## 🎯 Fitur Utama

### 1. **Learning Goals**
Target pembelajaran yang ingin dicapai dengan berbagai kategori:
- **Skill**: Keterampilan teknis (HTML, CSS, Programming, dll)
- **Knowledge**: Pengetahuan (membaca artikel, belajar konsep)
- **Career**: Tujuan karir
- **Personal**: Pengembangan diri
- **Academic**: Tujuan akademis

**Priority Levels**: High, Medium, Low

**Status**: Active, Completed, Abandoned

### 2. **Milestones** 
Checkpoint terstruktur untuk mencapai goal:
- Terurut (order 1, 2, 3, dst)
- Bisa dicentang (mark complete/incomplete)
- Otomatis recalculate goal progress
- Bisa di-link dengan journal entries

### 3. **Daily Study Target**
Untuk habit-building goals:
- `daily_target_minutes`: Target menit per hari
- `target_days`: Total hari target (misal: 90 hari)
- `days_completed`: Hari yang sudah tercapai
- Otomatis update dari journal entries

### 4. **Final Project**
Deliverable sebagai bukti pencapaian:
- Title & Description
- Project URL (GitHub, portfolio, etc)
- File upload (PDF, DOC, ZIP, max 10MB)
- Submission timestamp

### 5. **Auto-Update dari Journal**
Ketika membuat/update journal entry dengan `learning_goal_id`:
- Update `days_completed` (unique entry_date count)
- Recalculate progress dari milestone completion
- Track total study minutes

## 📊 Database Schema

### `learning_goals` Table
```sql
id, user_id, title, description, category, priority, status,
target_date, completed_at, progress_percentage, progress_notes,
related_article_ids,
-- New fields:
daily_target_minutes, target_days, days_completed,
final_project_title, final_project_description, 
final_project_url, final_project_file, final_project_submitted_at
```

### `learning_goal_milestones` Table
```sql
id, learning_goal_id, title, description, order,
is_completed, completed_at, completed_by_journal_id
```

## 🔄 Workflow

### Skenario 1: Skill Goal dengan Milestones
```
Goal: "Menguasai HTML & CSS"
├─ Milestone 1: HTML Basics ✓
├─ Milestone 2: CSS Fundamentals ✓
├─ Milestone 3: Flexbox Layout ✓
├─ Milestone 4: Responsive Design (in progress)
└─ Milestone 5: Final Project: Portfolio

Progress: 60% (3/5 completed)
```

### Skenario 2: Habit Goal dengan Daily Target
```
Goal: "Belajar 30 Menit Setiap Hari"
Daily Target: 30 minutes
Target Days: 90
Days Completed: 20
Progress: 22%

→ Setiap kali buat journal entry dengan study_duration_minutes,
  otomatis update days_completed dan progress
```

### Skenario 3: Project-Based Goal
```
Goal: "Menyelesaikan Course Matematika"
├─ Milestone 1: Selesaikan 5 Module ✓
├─ Milestone 2: Lulus Semua Quiz ✓
└─ Milestone 3: Submit Final Project ✓

Final Project:
- Title: "Kumpulan Soal Matematika"
- File: matematika-rangkuman.pdf
- Submitted: 3 days ago
Status: COMPLETED ✓
```

## 🚀 Usage

### Create Goal
```php
LearningGoal::create([
    'user_id' => Auth::id(),
    'title' => 'Menguasai Laravel',
    'category' => 'skill',
    'priority' => 'high',
    'status' => 'active',
    'daily_target_minutes' => 60,
    'target_days' => 30,
]);
```

### Add Milestones
```php
LearningGoalMilestone::create([
    'learning_goal_id' => $goal->id,
    'title' => 'Belajar Routing & Controllers',
    'order' => 1,
]);
```

### Mark Milestone Complete
```php
$milestone->markCompleted($journalId = null);
// → Otomatis recalculate parent goal progress
```

### Update Goal from Journal
```php
$journal = LearningJournal::create([
    'learning_goal_id' => $goal->id,
    'study_duration_minutes' => 45,
    // ...
]);

$goal->updateStudyStats();
// → Update days_completed, progress_percentage
```

### Submit Final Project
```php
$goal->update([
    'final_project_title' => 'My Portfolio',
    'final_project_url' => 'https://myportfolio.com',
    'final_project_submitted_at' => now(),
]);
```

## 🎨 UI Components

### Goal Detail Page (`/learning-goals/{id}`)
- Header dengan progress bar
- Daily target stats (jika ada)
- Milestone checklist (interactive toggle)
- Final project section (upload form)
- Related journal entries

### Goal Index Page (`/learning-goals`)
- Goal cards dengan status badge
- Quick actions: View Details, Update Progress, Mark Complete
- Filter by status (Active, Completed, Abandoned)

## 📝 Routes

```php
// Learning Goals
GET    /learning-goals                  → index
GET    /learning-goals/{id}             → show (detail with milestones)
POST   /learning-goals                  → store
PUT    /learning-goals/{id}             → update
DELETE /learning-goals/{id}             → destroy

// Quick Updates
PATCH  /learning-goals/{id}/status      → updateStatus
PATCH  /learning-goals/{id}/progress    → updateProgress

// Milestones
PATCH  /milestones/{id}/toggle          → toggleMilestone

// Final Project
POST   /learning-goals/{id}/final-project → storeFinalProject
```

## 🔗 Synergy dengan Learning Journal

Learning Goals dan Learning Journal ter-integrasi:

**Planning → Execution → Reflection**

```
Learning Goals (Planning)          Learning Journal (Execution)
├─ What I want to achieve    ←─→  ├─ What I did today
├─ Milestones to track       ←─→  ├─ Study duration
├─ Daily target minutes      ←─→  ├─ Entry date
└─ Final project goal        ←─→  └─ What I learned

Auto-sync via learning_goal_id foreign key
```

## 🧪 Testing

Gunakan seeder untuk membuat sample data:
```bash
php artisan db:seed --class=LearningGoalSeeder
```

Ini akan membuat:
- 6 learning goals dengan berbagai status
- 8 milestones untuk 2 goals
- 1 completed goal dengan final project
- 1 habit goal dengan daily target

## 📱 Mobile-First Design

Semua UI responsif dengan Tailwind CSS:
- Touch-friendly buttons
- Swipe gestures ready
- Modal bottom sheet untuk mobile
- Compact cards untuk small screens

## 🎉 Auto-Celebration

Ketika milestone completed:
```
"Milestone completed! 🎉"
```

Ketika goal completed:
```
Status: COMPLETED ✓
Progress: 100%
```

## 💡 Best Practices

1. **Goal Setting**: Gunakan SMART goals (Specific, Measurable, Achievable, Relevant, Time-bound)
2. **Milestones**: 4-6 milestones per goal (tidak terlalu banyak)
3. **Daily Targets**: Realistis (30-60 menit untuk pemula)
4. **Journal Integration**: Selalu link journal dengan goal untuk auto-tracking
5. **Final Project**: Submit sebagai bukti konkrit pencapaian

## 🔮 Future Enhancements

- [ ] Auto-suggest milestones based on course content
- [ ] Milestone templates by category
- [ ] Gamification: badges, streaks, leaderboards
- [ ] Goal reminders & notifications
- [ ] Progress visualization charts
- [ ] Peer review for final projects
- [ ] AI-powered reflection analysis
- [ ] Goal sharing & collaboration

---

**Happy Learning! 🚀**
