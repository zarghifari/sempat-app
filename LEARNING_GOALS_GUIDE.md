# 🎯 Learning Goals - Panduan Lengkap Pembuatan Goal Baru

## 📋 Overview

Sistem Learning Goals memungkinkan siswa membuat tujuan pembelajaran self-directed dengan fitur:
- ✅ **Milestones** - Checkpoint terstruktur (simple checkbox atau dengan bukti capaian)
- ✅ **Daily Study Target** - Target waktu belajar harian (auto-tracked dari journal)
- ✅ **Final Project** - Project akhir sebagai deliverable
- ✅ **Auto-Progress Tracking** - Progress dihitung otomatis dari milestone completion

## 🚀 Cara Membuat Learning Goal Baru

### 1. Akses Form Create Goal
- Buka `/learning-goals`
- Klik tombol **"+ New Goal"**

### 2. Isi Informasi Dasar

#### **Goal Title** (Required)
Judul singkat dan jelas tentang apa yang ingin dicapai.
```
Contoh:
✅ "Menguasai HTML & CSS"
✅ "Membaca 10 Artikel Produktivitas"
✅ "Konsisten Belajar 30 Hari Berturut"
```

#### **Description** (Optional)
Deskripsi detail tentang goal dan apa yang ingin dicapai.
```
Contoh:
"Mempelajari fundamental HTML dan CSS untuk membuat website 
responsif. Target: bisa membuat landing page dari scratch."
```

#### **Category** (Required)
Pilih kategori goal:
- 🛠️ **Skill** - Keterampilan teknis (programming, design, etc)
- 📚 **Knowledge** - Pengetahuan (membaca, riset, teori)
- 💼 **Career** - Tujuan karir (sertifikasi, portfolio)
- 🌱 **Personal** - Pengembangan diri (habit building, soft skills)
- 🎓 **Academic** - Tujuan akademis (ujian, tugas, course)

#### **Priority** (Required)
Tingkat prioritas goal:
- **Low** - Goal yang bisa dikerjakan nanti
- **Medium** - Goal penting tapi tidak urgent
- **High** - Goal yang harus segera diselesaikan

#### **Target Date** (Optional)
Tanggal target penyelesaian goal.

---

### 3. 🎯 Set Milestones (Optional tapi Disarankan)

Milestone adalah **checkpoint terstruktur** untuk track progress goal.

#### **Cara Menambahkan Milestone:**
1. Klik **"+ Add Milestone"**
2. Isi **Milestone Title** (required)
3. Isi **Description** (optional)
4. Centang **"Membutuhkan bukti capaian"** jika ingin milestone dengan evidence

#### **2 Tipe Milestone:**

**A. Simple Milestone (Checkbox Only)**
- Untuk milestone konseptual/teori
- Tinggal centang untuk mark complete
- Contoh: "Belajar HTML Basics", "Pahami CSS Selectors"

**B. Evidence-Based Milestone (Checkbox + Proof)**
- Untuk milestone praktek/hands-on
- Membutuhkan **text description** + optional **file upload**
- Contoh: "Buat Landing Page", "Deploy Website ke Netlify"

#### **Best Practices Milestone:**
```
Goal: "Menguasai HTML & CSS"

Milestone 1: "HTML Fundamentals" 
   - Type: Simple
   - Description: Belajar tags, elements, attributes

Milestone 2: "CSS Styling Basics"
   - Type: Simple
   - Description: Selectors, colors, fonts, box model

Milestone 3: "Buat Layout dengan Flexbox"
   - Type: Evidence ✓
   - Description: Praktek membuat layout responsive

Milestone 4: "Project: Landing Page"
   - Type: Evidence ✓
   - Description: Buat landing page lengkap

Milestone 5: "Deploy ke Netlify"
   - Type: Evidence ✓
   - Description: Deploy project online
```

**Tips:**
- 4-6 milestones per goal (tidak terlalu banyak)
- Urutkan dari mudah ke sulit
- Milestone terakhir bisa final project
- Gunakan evidence untuk milestone praktek

---

### 4. ⏱️ Set Daily Study Target (Optional)

Untuk **habit-building goals**, aktifkan daily study target:

1. Centang **"Set Daily Study Target"**
2. Isi **Minutes/Day** (contoh: 30, 60, 120)
3. Isi **Total Days** (contoh: 30, 90, 180)

**Bagaimana Cara Kerjanya?**
- Setiap kali buat **Learning Journal** dengan `learning_goal_id` dan `study_duration_minutes`
- System otomatis update `days_completed`
- Progress dihitung: `(days_completed / target_days) × 100%`

**Contoh:**
```
Goal: "Konsisten Belajar 30 Menit Setiap Hari"
Daily Target: 30 minutes
Total Days: 90

Day 1: Buat journal, study 30 min → days_completed = 1
Day 2: Buat journal, study 45 min → days_completed = 2
...
Day 20: → Progress = 20/90 = 22%
```

**Best Use Cases:**
- Habit building (konsistensi belajar)
- Reading goals (baca 30 menit per hari)
- Practice goals (coding 1 jam per hari)

---

### 5. 📁 Plan Final Project (Optional)

Jika goal memiliki **deliverable akhir**, set final project info:

1. Centang **"Plan Final Project"**
2. Isi **Project Title** (contoh: "Portfolio Website")
3. Isi **Project Description** (deskripsi project)

**Note:** URL dan file upload akan dilakukan nanti saat **submit final project**.

**Contoh:**
```
Final Project Title: "Personal Portfolio Website"
Description: "Website portfolio dengan 5 section: Home, About, 
Skills, Projects, Contact. Menggunakan HTML, CSS, dan JavaScript."
```

---

## 📊 Setelah Goal Dibuat

### **Progress Tracking**
Progress dihitung otomatis dari:
1. **Milestone Completion** 
   - Formula: `(completed_milestones / total_milestones) × 100%`
   - Contoh: 3/5 completed = 60%

2. **Daily Target** (jika ada)
   - Formula: `(days_completed / target_days) × 100%`
   - Auto-update dari journal entries

### **Mengerjakan Milestones**

#### **Simple Milestone:**
1. Buka detail goal (`/learning-goals/{id}`)
2. Klik checkbox pada milestone
3. Done! ✅

#### **Evidence-Based Milestone:**
1. Buka detail goal
2. Klik checkbox pada milestone yang butuh evidence
3. Modal akan muncul
4. Isi **Deskripsi Capaian** (min 50 karakter)
5. Upload **File** (optional, max 5MB)
6. Klik **"Submit & Complete"**

**Contoh Evidence:**
```
Milestone: "Buat Landing Page dengan Flexbox"

Deskripsi Capaian:
"Saya sudah membuat landing page responsif dengan 3 section 
(header, features, footer) menggunakan Flexbox. Layout otomatis 
menyesuaikan di mobile dan desktop. Sudah praktek flex-direction, 
justify-content, dan align-items."

File: landing-page-screenshot.jpg
```

### **Submit Final Project**

Ketika goal sudah selesai:
1. Buka detail goal
2. Scroll ke section **"Final Project"**
3. Klik **"Add Final Project"** atau **"Edit Final Project"**
4. Isi form:
   - Project Title
   - Description
   - Project URL (GitHub, live demo, etc)
   - Upload File (PDF dokumentasi, ZIP source code, etc)
5. Klik **"Submit Project"**

**Contoh:**
```
Title: "Portfolio Website"
Description: "Website portfolio dengan React dan Tailwind CSS"
URL: https://myportfolio.netlify.app
File: portfolio-source-code.zip (source code)
```

---

## 🔗 Integrasi dengan Learning Journal

### **Auto-Update dari Journal**

Ketika membuat journal entry:
```
Title: "Belajar CSS Flexbox"
Content: "Hari ini praktek Flexbox..."
Study Duration: 45 minutes
Learning Goal: [Pilih goal yang sesuai]
```

System otomatis:
- ✅ Update `days_completed` (jika daily target aktif)
- ✅ Track total study time
- ✅ Bisa link ke milestone completion

### **Link Journal ke Milestone**

Opsi 1: **Manual Link**
- Buat journal entry dengan goal_id
- Progress auto-update

Opsi 2: **Evidence Link** (Future)
- Saat submit milestone evidence, journal otomatis tercreate

---

## 🎨 UI Features

### **Create Form Features:**
- ✅ Dynamic milestone builder (add/remove)
- ✅ Toggle daily target fields
- ✅ Toggle final project fields
- ✅ Milestone dengan evidence checkbox
- ✅ Real-time milestone numbering

### **Detail Page Features:**
- ✅ Progress bar dengan percentage
- ✅ Daily target stats (jika ada)
- ✅ Interactive milestone checklist
- ✅ Evidence display (text + file download)
- ✅ Final project section dengan upload
- ✅ Related journal entries list

---

## 📝 Validation Rules

### **Goal Creation:**
```php
- title: required, max 255
- description: optional
- category: required (skill|knowledge|career|personal|academic)
- priority: required (low|medium|high)
- target_date: optional, must be future date
- daily_target_minutes: optional, min 1
- target_days: optional, min 1
- final_project_title: optional, max 255
```

### **Milestone:**
```php
- title: required, max 255
- description: optional
- requires_evidence: optional, boolean
- evidence_text: required if evidence needed, max 1000
- evidence_file: optional, max 5MB, accepted: pdf,doc,jpg,png,zip
```

### **Final Project:**
```php
- final_project_title: required, max 255
- final_project_description: optional
- final_project_url: optional, valid URL
- final_project_file: optional, max 10MB
```

---

## 💡 Tips & Best Practices

### **1. Goal Setting**
- Gunakan **SMART goals** (Specific, Measurable, Achievable, Relevant, Time-bound)
- Mulai dengan 2-3 active goals (jangan terlalu banyak)
- Set deadline realistis

### **2. Milestones**
- 4-6 milestones optimal per goal
- Milestone pertama harus mudah (quick win)
- Milestone terakhir bisa final project
- Gunakan evidence untuk skill-based milestones

### **3. Daily Targets**
- Mulai dengan target kecil (30 menit)
- Konsistensi > durasi
- Link journal setiap hari untuk auto-tracking

### **4. Evidence Submission**
- Jelaskan **apa yang sudah dipelajari**
- Screenshot/code snippet sangat membantu
- Minimal 50 karakter untuk deskripsi

### **5. Final Project**
- Submit saat goal 100% selesai
- Include live demo URL jika ada
- Upload source code/dokumentasi

---

## 🚫 Hal yang TIDAK Bisa Dilakukan

### **SPSDL ≠ FSDL**

Learning Goals adalah **Self-Paced Self-Directed Learning (SPSDL)**, bukan FSDL (Formal Structured...).

**Artinya:**
- ❌ Milestone TIDAK bisa di-link dengan Course (FSDL)
- ❌ Milestone TIDAK bisa di-link dengan Quiz
- ❌ Milestone TIDAK bisa di-link dengan Lesson
- ✅ Milestone adalah checkpoint **self-defined**
- ✅ Progress ditentukan oleh **diri sendiri**
- ✅ Validation melalui **evidence submission**

**Contoh Salah:**
```
❌ Milestone: "Selesaikan Quiz HTML Basics"
   → Ini mengacu ke FSDL feature
```

**Contoh Benar:**
```
✅ Milestone: "Pahami HTML Elements & Attributes"
   → Self-defined checkpoint
✅ Milestone: "Buat 3 Halaman Web Sederhana"
   → Evidence-based validation
```

---

## 🎯 Contoh Complete Goal Setup

```
═══════════════════════════════════════════════
GOAL: "Menguasai HTML & CSS Fundamental"
═══════════════════════════════════════════════

📝 Basic Info:
- Category: Skill
- Priority: High
- Target Date: 60 hari dari sekarang

⏱️ Daily Target:
- Minutes/Day: 60
- Total Days: 60

🎯 Milestones:
1. "Belajar HTML Basics" (Simple)
   - Pahami struktur HTML, tags, elements
   
2. "Belajar CSS Styling" (Simple)
   - Selectors, colors, fonts, box model
   
3. "CSS Layout: Flexbox" (Evidence)
   - Praktek membuat layout dengan Flexbox
   
4. "CSS Layout: Grid" (Evidence)
   - Praktek CSS Grid untuk complex layout
   
5. "Responsive Design" (Evidence)
   - Buat website yang responsive di semua device
   
6. "Final Project: Landing Page" (Evidence)
   - Buat landing page lengkap dari scratch

📁 Final Project:
- Title: "Portfolio Landing Page"
- Description: "Landing page portfolio dengan 5 section, 
  fully responsive, menggunakan HTML, CSS, dan Flexbox/Grid"

═══════════════════════════════════════════════
```

---

## 📊 Progress Calculation

### **Formula:**

```javascript
// Jika ada milestones
progress = (completed_milestones / total_milestones) × 100

// Contoh:
3/5 completed = 60%

// Jika ada daily target
daily_progress = (days_completed / target_days) × 100

// Contoh:
20/90 days = 22%

// Combined (if both exist)
overall_progress = (milestone_progress + daily_progress) / 2
```

### **Auto-Update Triggers:**

1. **Milestone Completed**
   ```php
   $milestone->markCompleted();
   → $goal->recalculateProgress();
   → Progress updated ✅
   ```

2. **Journal Entry Created**
   ```php
   LearningJournal::create([
       'learning_goal_id' => $goal->id,
       'study_duration_minutes' => 45,
       // ...
   ]);
   → $goal->updateStudyStats();
   → days_completed updated ✅
   ```

---

## 🎉 Completion & Rewards

Ketika goal selesai 100%:
- ✅ Status otomatis → **Completed**
- ✅ Badge completion di UI
- ✅ Final project bisa di-submit
- ✅ Goal masuk history

**Future Enhancement:**
- 🏆 Badges & achievements
- 🔥 Streak tracking
- 📊 Statistics & analytics
- 🎊 Celebration animations

---

**Happy Learning! 🚀**
