<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::with('creator')->get();

        foreach ($courses as $course) {
            $modules = $this->getModulesForCourse($course, $course->creator->id);
            
            foreach ($modules as $index => $moduleData) {
                Module::create(array_merge($moduleData, [
                    'course_id' => $course->id,
                    'order' => $index + 1,
                    'created_by' => $course->created_by,
                ]));
            }
        }
    }

    private function getModulesForCourse($course, $teacherId): array
    {
        $modulesMap = [
            'Matematika Dasar untuk Pemula' => [
                [
                    'title' => 'Operasi Aritmatika Dasar',
                    'description' => 'Pelajari penjumlahan, pengurangan, perkalian, dan pembagian',
                    'estimated_minutes' => 180,
                    'status' => 'published',
                ],
                [
                    'title' => 'Pecahan dan Desimal',
                    'description' => 'Memahami konsep pecahan dan desimal serta operasinya',
                    'estimated_minutes' => 240,
                    'status' => 'published',
                ],
                [
                    'title' => 'Aljabar Sederhana',
                    'description' => 'Pengenalan variabel, persamaan, dan penyelesaiannya',
                    'estimated_minutes' => 300,
                    'status' => 'published',
                ],
                [
                    'title' => 'Geometri Dasar',
                    'description' => 'Mengenal bangun datar dan ruang sederhana',
                    'estimated_minutes' => 270,
                    'status' => 'published',
                ],
            ],
            'Pengantar Ilmu Komputer' => [
                [
                    'title' => 'Pengenalan Komputer',
                    'description' => 'Memahami komponen komputer dan cara kerjanya',
                    'estimated_minutes' => 200,
                    'status' => 'published',
                ],
                [
                    'title' => 'Sistem Operasi',
                    'description' => 'Belajar tentang Windows, Linux, dan macOS',
                    'estimated_minutes' => 250,
                    'status' => 'published',
                ],
                [
                    'title' => 'Algoritma dan Pemrograman',
                    'description' => 'Dasar-dasar logika pemrograman dan algoritma',
                    'estimated_minutes' => 400,
                    'status' => 'published',
                ],
                [
                    'title' => 'Internet dan Jaringan',
                    'description' => 'Memahami cara kerja internet dan jaringan komputer',
                    'estimated_minutes' => 300,
                    'status' => 'published',
                ],
            ],
            'Bahasa Inggris Percakapan Sehari-hari' => [
                [
                    'title' => 'Greetings dan Self Introduction',
                    'description' => 'Cara memperkenalkan diri dan menyapa dalam bahasa Inggris',
                    'estimated_minutes' => 180,
                    'status' => 'published',
                ],
                [
                    'title' => 'Daily Conversations',
                    'description' => 'Percakapan sehari-hari di berbagai situasi',
                    'estimated_minutes' => 300,
                    'status' => 'published',
                ],
                [
                    'title' => 'Shopping dan Dining',
                    'description' => 'Bahasa Inggris untuk berbelanja dan makan di restoran',
                    'estimated_minutes' => 240,
                    'status' => 'published',
                ],
                [
                    'title' => 'Travel English',
                    'description' => 'Vocabulary dan frasa untuk traveling',
                    'estimated_minutes' => 280,
                    'status' => 'published',
                ],
            ],
            'Dasar-dasar Kewirausahaan' => [
                [
                    'title' => 'Mindset Entrepreneur',
                    'description' => 'Membangun pola pikir wirausahawan',
                    'estimated_minutes' => 150,
                    'status' => 'published',
                ],
                [
                    'title' => 'Menemukan Ide Bisnis',
                    'description' => 'Cara mengidentifikasi peluang bisnis',
                    'estimated_minutes' => 200,
                    'status' => 'published',
                ],
                [
                    'title' => 'Business Model Canvas',
                    'description' => 'Merancang model bisnis dengan BMC',
                    'estimated_minutes' => 250,
                    'status' => 'published',
                ],
                [
                    'title' => 'Marketing dan Branding',
                    'description' => 'Strategi pemasaran dan membangun brand',
                    'estimated_minutes' => 300,
                    'status' => 'published',
                ],
            ],
            'Manajemen Waktu dan Produktivitas' => [
                [
                    'title' => 'Prinsip Manajemen Waktu',
                    'description' => 'Memahami pentingnya manajemen waktu',
                    'estimated_minutes' => 120,
                    'status' => 'published',
                ],
                [
                    'title' => 'Teknik Prioritas',
                    'description' => 'Eisenhower Matrix dan metode prioritas lainnya',
                    'estimated_minutes' => 180,
                    'status' => 'published',
                ],
                [
                    'title' => 'Mengatasi Prokrastinasi',
                    'description' => 'Strategi efektif menghilangkan kebiasaan menunda',
                    'estimated_minutes' => 200,
                    'status' => 'published',
                ],
                [
                    'title' => 'Membangun Kebiasaan Produktif',
                    'description' => 'Habit stacking dan sistem produktivitas jangka panjang',
                    'estimated_minutes' => 220,
                    'status' => 'published',
                ],
            ],
        ];

        return $modulesMap[$course->title] ?? [];
    }
}
