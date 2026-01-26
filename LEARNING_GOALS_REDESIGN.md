# Learning Goals System Redesign

## 🎯 Konsep Perubahan

### Sebelumnya (❌ Salah Konsep)
Learning Goals **dianggap sebagai materi belajar** yang melacak waktu belajar sendiri:
- `learning_goals` table memiliki `total_study_seconds` dan `last_study_at`
- Endpoint `/learning-goals/{goal}/track-time` untuk tracking waktu
- StudyTimeTracker diinisialisasi di halaman Learning Goals

**Masalah:**
- Learning Goals bukan konten/materi yang dipelajari
- Duplikasi tracking dengan lessons dan articles
- Membebani server dengan tracking redundan
- User bingung: "Apa yang sedang saya pelajari di halaman ini?"

### Sekarang (✅ Konsep Benar)
Learning Goals adalah **tempat siswa menentukan target belajar harian**:
- Hanya menyimpan `daily_target_minutes` (target harian dalam menit)
- Progress dihitung dari waktu belajar di **Lessons** dan **Articles**
- Single source of truth: `daily_study_sessions` table

**Keuntungan:**
- ✅ Konsep jelas: Goals = Target Setting, bukan materi
- ✅ Tidak ada duplikasi tracking
- ✅ Optimal: single query ke DailyStudySession
- ✅ User experience lebih baik: lihat gabungan progress dari semua aktivitas

---

## 📋 Perubahan Database

### Migration: `2026_01_26_000001_remove_time_tracking_from_learning_goals.php`

```php
Schema::table('learning_goals', function (Blueprint $table) {
    $table->dropColumn('total_study_seconds');
    $table->dropColumn('last_study_at');
});
```

**Fields yang dihapus:**
- ❌ `total_study_seconds` - Tidak diperlukan lagi
- ❌ `last_study_at` - Tidak diperlukan lagi

**Fields yang dipertahankan:**
- ✅ `daily_target_minutes` - Target harian (misal: 60 menit/hari)
- ✅ `target_days` - Berapa hari ingin mencapai target
- ✅ `days_completed` - Berapa hari sudah mencapai target
- ✅ `progress_percentage` - Progress keseluruhan

---

## 🔄 Perubahan Model

### `app/Models/LearningGoal.php`

#### 1. Hapus Fields dari $fillable dan $casts

```php
// REMOVED
protected $fillable = [
    // ...
    'total_study_seconds',  // ❌ Removed
    'last_study_at',        // ❌ Removed
];

protected $casts = [
    // ...
    'last_study_at' => 'datetime',  // ❌ Removed
];
```

#### 2. Method Baru: `getTodayProgress()`

**Purpose:** Mendapatkan progress belajar hari ini dari lessons + articles

```php
public function getTodayProgress()
{
    $session = DailyStudySession::getTodaySession($this->user_id);
    
    $lessonsTime = $session->total_lessons_time ?? 0;
    $articlesTime = $session->total_articles_time ?? 0;
    $totalTime = $lessonsTime + $articlesTime;
    
    $todayMinutes = floor($totalTime / 60);
    $targetMinutes = $this->daily_target_minutes ?? 0;
    
    return [
        'today_minutes' => $todayMinutes,
        'target_minutes' => $targetMinutes,
        'remaining_minutes' => max(0, $targetMinutes - $todayMinutes),
        'percentage' => $targetMinutes > 0 ? min(100, round(($todayMinutes / $targetMinutes) * 100)) : 0,
        'target_reached' => $todayMinutes >= $targetMinutes,
        'breakdown' => [
            'lessons_minutes' => floor($lessonsTime / 60),
            'articles_minutes' => floor($articlesTime / 60),
        ],
    ];
}
```

**Return Format:**
```json
{
    "today_minutes": 45,
    "target_minutes": 60,
    "remaining_minutes": 15,
    "percentage": 75,
    "target_reached": false,
    "breakdown": {
        "lessons_minutes": 25,
        "articles_minutes": 20
    }
}
```

#### 3. Method Baru: `updateDaysCompletedFromDailySessions()`

**Purpose:** Menghitung berapa hari sudah mencapai target (untuk progress calculation)

```php
public function updateDaysCompletedFromDailySessions()
{
    $targetMinutes = $this->daily_target_minutes ?? 0;
    
    if ($targetMinutes === 0) {
        $this->days_completed = 0;
        return;
    }
    
    $targetSeconds = $targetMinutes * 60;
    
    $completedDays = DailyStudySession::where('user_id', $this->user_id)
        ->where('total_study_time', '>=', $targetSeconds)
        ->count();
    
    $this->days_completed = $completedDays;
}
```

#### 4. Update `recalculateProgress()`

**Sebelumnya:**
```php
// 30% dari total waktu belajar
$studyTimeWeight = 0.30;
$studyTimeProgress = min(100, ($this->total_study_seconds / $totalTargetSeconds) * 100);
```

**Sekarang:**
```php
// Tidak ada komponen study time, fokus pada days completed
$daysWeight = 0.40;  // ⬆️ Increased from 0.30
$milestonesWeight = 0.40;
$finalWeight = 0.20;

// Tidak ada $studyTimeWeight lagi
```

---

## 🛣️ Perubahan Routes & Controller

### Routes (`routes/web.php`)

**Dihapus:**
```php
// ❌ REMOVED - Learning goals tidak track time sendiri
Route::post('/learning-goals/{goal}/track-time', ...);
```

**Dipertahankan:**
```php
// ✅ KEPT - Untuk status check
Route::get('/learning-goals/{goal}/time', [StudyTimerController::class, 'getStatus']);
```

### `app/Http/Controllers/StudyTimerController.php`

#### Method: `getStatus($goalId)`

**Sebelumnya:**
```php
$session = DailyStudySession::getTodaySession(Auth::id());
$todaySeconds = $session->getTodayTimeFor('goal', $goalId);
```

**Sekarang:**
```php
$goal = LearningGoal::findOrFail($goalId);
$progress = $goal->getTodayProgress();
return response()->json($progress);
```

#### Method: `getLogs($goalId)`

**Sebelumnya:**
```php
$sessionLogs = DailyStudySession::getGoalHistory(Auth::id(), $goalId);
```

**Sekarang:**
```php
$sessions = DailyStudySession::where('user_id', Auth::id())
    ->where('study_date', '>=', now()->subDays(30))
    ->get()
    ->map(function ($session) use ($targetSeconds) {
        return [
            'date' => $session->study_date,
            'total_minutes' => floor($session->total_study_time / 60),
            'lessons_minutes' => floor($session->total_lessons_time / 60),
            'articles_minutes' => floor($session->total_articles_time / 60),
            'target_reached' => $session->total_study_time >= $targetSeconds,
        ];
    });
```

---

## 🎨 Perubahan Frontend

### `resources/views/learning-goals/show.blade.php`

#### 1. Hapus "Study Time" Card dari Header

**Sebelumnya:** 4 cards (Category, Priority, Study Time, Target Date)

**Sekarang:** 3 cards (Category, Priority, Target Date)

```blade
<!-- Goal Info -->
<div class="grid grid-cols-3 gap-2">  <!-- Changed from grid-cols-4 -->
    <div>Category</div>
    <div>Priority</div>
    <!-- REMOVED: Study Time card -->
    <div>Target Date</div>
</div>
```

#### 2. Update Info Banner

**Sebelumnya:**
> "Auto-tracked when you read articles in the library"

**Sekarang:**
> "Auto-tracked from your learning activities: 📚 **Lessons** and 📄 **Articles**"

#### 3. Update Tracking Status

**Sebelumnya:**
```html
<span>✓ Auto-tracking from articles</span>
```

**Sekarang:**
```html
<span>✓ Auto-tracking from lessons & articles</span>
```

#### 4. Hapus StudyTimeTracker Instance

**Sebelumnya:**
```javascript
const goalTracker = new StudyTimeTracker({
    resourceType: 'learning-goal',
    resourceId: {{ $learningGoal->id }},
    apiEndpoint: '/api/learning-goals/{goal}/track-time',
    displayElement: 'goal-study-timer',
});
```

**Sekarang:**
```javascript
// ❌ REMOVED - Learning goals tidak track time sendiri
// Hanya fetch progress dari endpoint /learning-goals/{goal}/time
```

#### 5. Update fetchDailyStudyTime()

**Sebelumnya:**
```javascript
fetch('/api/daily-study-time')  // Global endpoint
    .then(data => {
        todayGlobalSeconds = data.today_total_seconds;
    });
```

**Sekarang:**
```javascript
fetch('/learning-goals/{{ $learningGoal->id }}/time')  // Goal-specific endpoint
    .then(data => {
        todayGlobalSeconds = (data.today_minutes || 0) * 60;
        console.log('Lessons:', data.breakdown.lessons_minutes + 'm');
        console.log('Articles:', data.breakdown.articles_minutes + 'm');
    });
```

---

## 📊 Flow Diagram

### Old Flow (❌)
```
Learning Goals Page
    ↓
StudyTimeTracker (track time on page)
    ↓
POST /learning-goals/{goal}/track-time
    ↓
learning_goals.total_study_seconds += time
    ↓
Progress calculated from learning_goals table
```

### New Flow (✅)
```
Student sets daily target
    ↓
learning_goals.daily_target_minutes = 60 (misal)
    ↓
Student studies:
    - Lessons → daily_study_sessions.total_lessons_time
    - Articles → daily_study_sessions.total_articles_time
    ↓
GET /learning-goals/{goal}/time
    ↓
Check today's session:
    total_study_time = lessons_time + articles_time
    ↓
Compare with target:
    IF total_study_time >= daily_target_minutes * 60
    THEN target_reached = true
    ↓
Display progress on Learning Goals page
```

---

## 🎯 Use Case Example

### Scenario: Sarah menetapkan target belajar 60 menit/hari

1. **Set Goal:**
   ```
   Sarah membuat Learning Goal "Master JavaScript"
   Daily Target: 60 minutes/day
   Target Days: 30 days
   ```

2. **Day 1 Activities:**
   - Belajar Lesson "Variables" → 25 menit
   - Baca Article "Closures" → 20 menit
   - Baca Article "Async/Await" → 18 menit
   - **Total:** 63 menit ✅ Target tercapai!

3. **Data Flow:**
   ```
   DailyStudySession (2026-01-26):
   - total_lessons_time: 1500 seconds (25 min)
   - total_articles_time: 2280 seconds (38 min)
   - total_study_time: 3780 seconds (63 min)
   ```

4. **Learning Goals Page Display:**
   ```
   📊 Today's Progress
   63m / 60m target ✅
   
   Breakdown:
   📚 Lessons: 25m
   📄 Articles: 38m
   
   🎯 Target Achieved!
   ```

5. **Progress Calculation:**
   ```
   days_completed = 1
   progress_percentage = (40% × 1/30) + (40% × 0) + (20% × 0)
                       = 1.33%
   ```

---

## 🚀 Benefits

### 1. **Konsep yang Jelas**
- ✅ Learning Goals = Target Setting Tool
- ✅ Lessons/Articles = Actual Study Activities
- ✅ DailyStudySession = Progress Tracking

### 2. **Performance Optimization**
- ✅ Single query ke DailyStudySession (1 query vs N queries)
- ✅ No redundant tracking endpoints
- ✅ Reduced server load

### 3. **Better User Experience**
- ✅ User tidak bingung "apa yang sedang dipelajari" di halaman Learning Goals
- ✅ Progress terlihat dari semua aktivitas belajar (lessons + articles)
- ✅ Breakdown jelas: berapa dari lessons, berapa dari articles

### 4. **Data Integrity**
- ✅ Single source of truth: DailyStudySession
- ✅ No data duplication
- ✅ Easier to maintain and debug

---

## 📝 Testing Checklist

- [ ] Migration berhasil: `total_study_seconds` dan `last_study_at` terhapus
- [ ] Learning Goals page tidak error saat dibuka
- [ ] Progress hari ini tampil dengan benar (lessons + articles)
- [ ] Breakdown menampilkan waktu dari lessons dan articles
- [ ] Target achievement notification muncul saat mencapai target
- [ ] `days_completed` update otomatis berdasarkan DailyStudySession
- [ ] Progress percentage dihitung dengan benar (tanpa study time component)
- [ ] API endpoint `/learning-goals/{goal}/time` return data yang benar
- [ ] Tidak ada console errors di browser
- [ ] Halaman responsive di mobile

---

## 🔧 Maintenance Notes

### Jika ingin menambah sumber belajar baru (misal: Videos)

1. **Update DailyStudySession:**
   ```php
   Schema::table('daily_study_sessions', function (Blueprint $table) {
       $table->integer('total_videos_time')->default(0);
   });
   ```

2. **Update tracking saat video watched:**
   ```php
   $session->total_videos_time += $watchTime;
   $session->total_study_time += $watchTime;
   $session->save();
   ```

3. **Update getTodayProgress() breakdown:**
   ```php
   'breakdown' => [
       'lessons_minutes' => floor($lessonsTime / 60),
       'articles_minutes' => floor($articlesTime / 60),
       'videos_minutes' => floor($videosTime / 60),  // ✅ Added
   ],
   ```

4. **Done!** Otomatis ter-track di Learning Goals

---

## 📚 Related Files

- `database/migrations/2026_01_26_000001_remove_time_tracking_from_learning_goals.php`
- `app/Models/LearningGoal.php`
- `app/Http/Controllers/StudyTimerController.php`
- `resources/views/learning-goals/show.blade.php`
- `routes/web.php`

---

**Tanggal Update:** 26 Januari 2026  
**Status:** ✅ Implemented & Migrated
