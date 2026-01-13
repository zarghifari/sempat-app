<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $teacher1 = User::where('email', 'teacher@sempat.test')->first();
        $teacher2 = User::where('email', 'teacher2@sempat.test')->first();
        
        $categories = [
            'tech' => ArticleCategory::where('slug', 'teknologi-pemrograman')->first(),
            'science' => ArticleCategory::where('slug', 'ilmu-pengetahuan')->first(),
            'personal' => ArticleCategory::where('slug', 'pengembangan-diri')->first(),
            'career' => ArticleCategory::where('slug', 'karir-bisnis')->first(),
            'education' => ArticleCategory::where('slug', 'pendidikan-belajar')->first(),
        ];

        $tutorialTag = Tag::where('slug', 'tutorial')->first();
        $tipsTag = Tag::where('slug', 'tips-trik')->first();
        $beginnerTag = Tag::where('slug', 'pemula')->first();
        $practicalTag = Tag::where('slug', 'praktis')->first();

        $articles = [
            [
                'category_id' => $categories['tech']->id,
                'created_by' => $teacher1->id,
                'title' => 'Pengenalan Git dan GitHub untuk Pemula',
                'excerpt' => 'Panduan lengkap memulai version control dengan Git dan berkolaborasi menggunakan GitHub.',
                'content' => '<h2>Apa itu Git?</h2><p>Git adalah sistem version control terdistribusi yang memungkinkan developer melacak perubahan kode.</p><h3>Mengapa Git Penting?</h3><ul><li>Melacak history perubahan</li><li>Kolaborasi tim yang efektif</li><li>Branching dan merging yang mudah</li><li>Backup otomatis</li></ul><h2>Instalasi Git</h2><p>Download Git dari situs resmi dan install sesuai sistem operasi Anda...</p>',
                'difficulty_level' => 'beginner',
                'reading_time_minutes' => 15,
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now()->subDays(10),
                'tags' => [$tutorialTag->id, $beginnerTag->id],
            ],
            [
                'category_id' => $categories['personal']->id,
                'created_by' => $teacher2->id,
                'title' => '10 Teknik Pomodoro untuk Meningkatkan Fokus Belajar',
                'excerpt' => 'Pelajari cara menggunakan teknik Pomodoro untuk memaksimalkan sesi belajar Anda.',
                'content' => '<h2>Apa itu Teknik Pomodoro?</h2><p>Teknik Pomodoro adalah metode manajemen waktu yang membagi pekerjaan menjadi interval 25 menit dengan istirahat 5 menit.</p><h3>Cara Kerja Pomodoro</h3><ol><li>Pilih satu tugas</li><li>Set timer 25 menit</li><li>Fokus penuh sampai timer berbunyi</li><li>Istirahat 5 menit</li><li>Ulangi 4 kali, lalu istirahat panjang 15-30 menit</li></ol>',
                'difficulty_level' => 'beginner',
                'reading_time_minutes' => 10,
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now()->subDays(8),
                'tags' => [$tipsTag->id, $practicalTag->id],
            ],
            [
                'category_id' => $categories['science']->id,
                'created_by' => $teacher1->id,
                'title' => 'Memahami Hukum Newton dengan Contoh Sehari-hari',
                'excerpt' => 'Eksplorasi tiga hukum Newton tentang gerak dengan contoh yang mudah dipahami.',
                'content' => '<h2>Hukum I Newton (Inersia)</h2><p>Benda akan tetap diam atau bergerak lurus beraturan kecuali ada gaya yang bekerja padanya.</p><p><strong>Contoh:</strong> Saat bus rem mendadak, badan kita terdorong ke depan karena inersia.</p><h2>Hukum II Newton (F = ma)</h2><p>Percepatan benda berbanding lurus dengan gaya dan berbanding terbalik dengan massa.</p>',
                'difficulty_level' => 'intermediate',
                'reading_time_minutes' => 20,
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(7),
                'tags' => [$tutorialTag->id],
            ],
            [
                'category_id' => $categories['career']->id,
                'created_by' => $teacher2->id,
                'title' => 'Cara Membuat CV yang Menarik Perhatian HRD',
                'excerpt' => 'Tips praktis menyusun CV profesional yang meningkatkan peluang diterima kerja.',
                'content' => '<h2>Struktur CV yang Efektif</h2><ul><li>Data pribadi dan kontak</li><li>Ringkasan profesional</li><li>Pengalaman kerja</li><li>Pendidikan</li><li>Keterampilan</li><li>Sertifikat dan penghargaan</li></ul><h3>Tips Penulisan</h3><p>Gunakan bahasa yang jelas, hindari typo, dan sesuaikan dengan posisi yang dilamar.</p>',
                'difficulty_level' => 'beginner',
                'reading_time_minutes' => 12,
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(6),
                'tags' => [$tipsTag->id, $practicalTag->id],
            ],
            [
                'category_id' => $categories['education']->id,
                'created_by' => $teacher1->id,
                'title' => 'Metode Feynman: Belajar dengan Mengajarkan',
                'excerpt' => 'Teknik belajar yang terbukti efektif dengan cara menjelaskan materi kepada orang lain.',
                'content' => '<h2>4 Langkah Metode Feynman</h2><ol><li>Pilih topik yang ingin dipelajari</li><li>Jelaskan dengan bahasa sederhana seolah mengajar anak kecil</li><li>Identifikasi gap pemahaman dan pelajari ulang</li><li>Sederhanakan dan gunakan analogi</li></ol><p>Metode ini memaksa kita memahami materi secara mendalam.</p>',
                'difficulty_level' => 'beginner',
                'reading_time_minutes' => 8,
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now()->subDays(5),
                'tags' => [$tipsTag->id, $beginnerTag->id],
            ],
            [
                'category_id' => $categories['tech']->id,
                'created_by' => $teacher2->id,
                'title' => 'Dasar-dasar HTML untuk Membuat Website Pertama',
                'excerpt' => 'Panduan step-by-step membuat halaman web sederhana dengan HTML.',
                'content' => '<h2>Struktur HTML Dasar</h2><pre>&lt;!DOCTYPE html&gt;\n&lt;html&gt;\n  &lt;head&gt;\n    &lt;title&gt;Judul Halaman&lt;/title&gt;\n  &lt;/head&gt;\n  &lt;body&gt;\n    &lt;h1&gt;Hello World!&lt;/h1&gt;\n  &lt;/body&gt;\n&lt;/html&gt;</pre><h3>Tag HTML Penting</h3><ul><li>h1-h6: Heading</li><li>p: Paragraph</li><li>a: Link</li><li>img: Image</li><li>div: Container</li></ul>',
                'difficulty_level' => 'beginner',
                'reading_time_minutes' => 18,
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(4),
                'tags' => [$tutorialTag->id, $beginnerTag->id],
            ],
            [
                'category_id' => $categories['personal']->id,
                'created_by' => $teacher1->id,
                'title' => 'Growth Mindset vs Fixed Mindset: Mindset untuk Sukses',
                'excerpt' => 'Memahami perbedaan dan mengembangkan growth mindset untuk pembelajaran yang lebih baik.',
                'content' => '<h2>Fixed Mindset</h2><p>Percaya bahwa kemampuan bersifat tetap dan tidak bisa dikembangkan.</p><h2>Growth Mindset</h2><p>Percaya bahwa kemampuan dapat dikembangkan melalui usaha dan pembelajaran.</p><h3>Cara Mengembangkan Growth Mindset</h3><ul><li>Terima tantangan</li><li>Belajar dari kritik</li><li>Fokus pada proses, bukan hasil</li><li>Lihat kegagalan sebagai pembelajaran</li></ul>',
                'difficulty_level' => 'beginner',
                'reading_time_minutes' => 10,
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(3),
                'tags' => [$tipsTag->id],
            ],
            [
                'category_id' => $categories['science']->id,
                'created_by' => $teacher2->id,
                'title' => 'Fotosintesis: Proses Ajaib Tumbuhan Menghasilkan Makanan',
                'excerpt' => 'Penjelasan lengkap proses fotosintesis dan perannya dalam ekosistem.',
                'content' => '<h2>Apa itu Fotosintesis?</h2><p>Fotosintesis adalah proses tumbuhan mengubah cahaya matahari, air, dan CO2 menjadi glukosa dan oksigen.</p><h3>Persamaan Fotosintesis</h3><p>6CO2 + 6H2O + Cahaya → C6H12O6 + 6O2</p><h3>Dua Tahap Fotosintesis</h3><ol><li>Reaksi terang: Terjadi di tilakoid</li><li>Reaksi gelap (Siklus Calvin): Terjadi di stroma</li></ol>',
                'difficulty_level' => 'intermediate',
                'reading_time_minutes' => 15,
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(2),
                'tags' => [$tutorialTag->id],
            ],
            [
                'category_id' => $categories['career']->id,
                'created_by' => $teacher1->id,
                'title' => 'Soft Skills yang Dicari Perusahaan di Era Digital',
                'excerpt' => 'Keterampilan non-teknis yang wajib dikuasai untuk sukses di dunia kerja.',
                'content' => '<h2>Top 10 Soft Skills</h2><ol><li>Komunikasi efektif</li><li>Kerja sama tim</li><li>Problem solving</li><li>Adaptabilitas</li><li>Critical thinking</li><li>Time management</li><li>Leadership</li><li>Kreativitas</li><li>Emotional intelligence</li><li>Growth mindset</li></ol><h3>Cara Mengembangkan Soft Skills</h3><p>Ikut organisasi, volunteer, ambil tanggung jawab dalam proyek kelompok.</p>',
                'difficulty_level' => 'beginner',
                'reading_time_minutes' => 12,
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDay(),
                'tags' => [$tipsTag->id, $practicalTag->id],
            ],
            [
                'category_id' => $categories['education']->id,
                'created_by' => $teacher2->id,
                'title' => 'Active Recall: Teknik Belajar Paling Efektif Menurut Sains',
                'excerpt' => 'Metode belajar dengan mengingat aktif terbukti lebih efektif daripada membaca ulang.',
                'content' => '<h2>Apa itu Active Recall?</h2><p>Active recall adalah teknik belajar dengan aktif mengingat informasi tanpa melihat catatan.</p><h3>Mengapa Efektif?</h3><p>Otak dipaksa untuk "retrieve" informasi, memperkuat jalur neural dan meningkatkan retensi jangka panjang.</p><h3>Cara Menerapkan</h3><ul><li>Gunakan flashcards</li><li>Buat pertanyaan sendiri</li><li>Jelaskan materi tanpa melihat buku</li><li>Practice tests</li></ul>',
                'difficulty_level' => 'intermediate',
                'reading_time_minutes' => 10,
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now(),
                'tags' => [$tipsTag->id, $practicalTag->id],
            ],
        ];

        foreach ($articles as $articleData) {
            $tags = $articleData['tags'];
            unset($articleData['tags']);
            
            $article = Article::create($articleData);
            $article->tags()->attach($tags);
        }
    }
}
