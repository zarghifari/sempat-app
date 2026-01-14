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
        ];

        foreach ($articles as $articleData) {
            $tags = $articleData['tags'];
            unset($articleData['tags']);
            
            $article = Article::create($articleData);
            $article->tags()->attach($tags);
        }
    }
}
