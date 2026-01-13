<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $modules = Module::with('course.creator')->get();

        foreach ($modules as $module) {
            $lessons = $this->getLessonsForModule($module);
            
            foreach ($lessons as $index => $lessonData) {
                Lesson::create(array_merge($lessonData, [
                    'module_id' => $module->id,
                    'order' => $index + 1,
                    'created_by' => $module->course->created_by,
                ]));
            }
        }
    }

    private function getLessonsForModule($module): array
    {
        // Sample lessons based on module title
        $lessonTemplates = [
            'Operasi Aritmatika Dasar' => [
                [
                    'title' => 'Pengenalan Penjumlahan dan Pengurangan',
                    'type' => 'video',
                    'description' => 'Video pembelajaran tentang konsep dasar penjumlahan dan pengurangan',
                    'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                    'duration_minutes' => 25,
                    'status' => 'published',
                    'is_preview' => true,
                ],
                [
                    'title' => 'Latihan Penjumlahan',
                    'type' => 'text',
                    'description' => 'Latihan soal penjumlahan dengan berbagai tingkat kesulitan',
                    'content' => '<h2>Latihan Penjumlahan</h2><p>Kerjakan soal-soal berikut ini:</p><ol><li>25 + 17 = ?</li><li>143 + 89 = ?</li><li>567 + 234 = ?</li></ol>',
                    'duration_minutes' => 20,
                    'status' => 'published',
                ],
                [
                    'title' => 'Perkalian Dasar',
                    'type' => 'video',
                    'description' => 'Memahami konsep perkalian sebagai penjumlahan berulang',
                    'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                    'duration_minutes' => 30,
                    'status' => 'published',
                ],
                [
                    'title' => 'Tabel Perkalian 1-10',
                    'type' => 'document',
                    'description' => 'PDF tabel perkalian yang bisa diunduh',
                    'content' => '<p>Unduh dan hafalkan tabel perkalian untuk memudahkan perhitungan.</p>',
                    'attachments' => [['name' => 'tabel_perkalian.pdf', 'url' => '#']],
                    'duration_minutes' => 15,
                    'status' => 'published',
                ],
                [
                    'title' => 'Pembagian Sederhana',
                    'type' => 'video',
                    'description' => 'Konsep pembagian dan hubungannya dengan perkalian',
                    'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                    'duration_minutes' => 30,
                    'status' => 'published',
                ],
                [
                    'title' => 'Kuis: Operasi Aritmatika',
                    'type' => 'quiz',
                    'description' => 'Uji pemahaman Anda tentang operasi aritmatika',
                    'duration_minutes' => 20,
                    'status' => 'published',
                    'requires_quiz' => true,
                    'min_quiz_score' => 70,
                ],
            ],
            'default' => [
                [
                    'title' => 'Pengenalan Materi',
                    'type' => 'video',
                    'description' => 'Video pengenalan materi modul ini',
                    'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                    'duration_minutes' => 20,
                    'status' => 'published',
                    'is_preview' => true,
                ],
                [
                    'title' => 'Konsep Dasar',
                    'type' => 'text',
                    'description' => 'Penjelasan konsep dasar dalam bentuk teks',
                    'content' => '<h2>Konsep Dasar</h2><p>Berikut adalah konsep-konsep dasar yang perlu Anda pahami...</p>',
                    'duration_minutes' => 25,
                    'status' => 'published',
                ],
                [
                    'title' => 'Contoh Penerapan',
                    'type' => 'video',
                    'description' => 'Video demonstrasi penerapan konsep',
                    'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                    'duration_minutes' => 30,
                    'status' => 'published',
                ],
                [
                    'title' => 'Latihan Soal',
                    'type' => 'text',
                    'description' => 'Kumpulan latihan soal untuk memperdalam pemahaman',
                    'content' => '<h2>Latihan Soal</h2><p>Kerjakan latihan berikut untuk menguji pemahaman Anda.</p>',
                    'duration_minutes' => 35,
                    'status' => 'published',
                ],
                [
                    'title' => 'Studi Kasus',
                    'type' => 'text',
                    'description' => 'Analisis studi kasus nyata',
                    'content' => '<h2>Studi Kasus</h2><p>Mari kita pelajari penerapan konsep dalam kasus nyata...</p>',
                    'duration_minutes' => 40,
                    'status' => 'published',
                ],
                [
                    'title' => 'Rangkuman Modul',
                    'type' => 'text',
                    'description' => 'Rangkuman poin-poin penting dalam modul ini',
                    'content' => '<h2>Rangkuman</h2><ul><li>Poin 1</li><li>Poin 2</li><li>Poin 3</li></ul>',
                    'duration_minutes' => 15,
                    'status' => 'published',
                ],
                [
                    'title' => 'Kuis Akhir Modul',
                    'type' => 'quiz',
                    'description' => 'Kuis untuk menguji pemahaman keseluruhan modul',
                    'duration_minutes' => 20,
                    'status' => 'published',
                    'requires_quiz' => true,
                    'min_quiz_score' => 75,
                ],
            ],
        ];

        return $lessonTemplates[$module->title] ?? $lessonTemplates['default'];
    }
}
