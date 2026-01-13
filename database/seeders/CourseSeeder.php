<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $teacher1 = User::where('email', 'teacher@sempat.test')->first();
        $teacher2 = User::where('email', 'teacher2@sempat.test')->first();
        
        $matematika = CourseCategory::where('slug', 'matematika')->first();
        $sains = CourseCategory::where('slug', 'sains-teknologi')->first();
        $bahasa = CourseCategory::where('slug', 'bahasa')->first();
        $bisnis = CourseCategory::where('slug', 'bisnis-ekonomi')->first();
        $pengembangan = CourseCategory::where('slug', 'pengembangan-diri')->first();

        $courses = [
            [
                'title' => 'Matematika Dasar untuk Pemula',
                'description' => 'Pelajari konsep matematika fundamental mulai dari operasi dasar, pecahan, desimal, hingga aljabar sederhana. Cocok untuk siswa yang ingin memperkuat fondasi matematika.',
                'objectives' => [
                    'Memahami operasi aritmatika dasar',
                    'Menguasai konsep pecahan dan desimal',
                    'Mampu menyelesaikan persamaan aljabar sederhana',
                    'Memahami konsep geometri dasar'
                ],
                'prerequisites' => ['Kemampuan membaca dan menulis', 'Motivasi belajar'],
                'level' => 'beginner',
                'status' => 'published',
                'language' => 'id',
                'estimated_hours' => 20,
                'is_free' => true,
                'is_featured' => true,
                'max_students' => 100,
                'created_by' => $teacher1->id,
                'published_at' => now()->subDays(30),
                'category' => $matematika,
            ],
            [
                'title' => 'Pengantar Ilmu Komputer',
                'description' => 'Kursus komprehensif tentang dasar-dasar ilmu komputer, mulai dari cara kerja komputer, algoritma, hingga pemrograman dasar. Sempurna untuk memulai karir di bidang teknologi.',
                'objectives' => [
                    'Memahami konsep dasar komputer dan sistem operasi',
                    'Mengenal algoritma dan struktur data',
                    'Mampu menulis program sederhana',
                    'Memahami internet dan jaringan komputer'
                ],
                'prerequisites' => ['Pengalaman menggunakan komputer'],
                'level' => 'beginner',
                'status' => 'published',
                'language' => 'id',
                'estimated_hours' => 30,
                'price' => 299000,
                'is_free' => false,
                'is_featured' => true,
                'max_students' => 50,
                'created_by' => $teacher1->id,
                'published_at' => now()->subDays(20),
                'category' => $sains,
            ],
            [
                'title' => 'Bahasa Inggris Percakapan Sehari-hari',
                'description' => 'Kuasai bahasa Inggris untuk komunikasi sehari-hari dengan metode praktis dan interaktif. Fokus pada listening, speaking, dan vocabulary yang sering digunakan.',
                'objectives' => [
                    'Mampu berkomunikasi dalam situasi sehari-hari',
                    'Menguasai 500+ vocabulary penting',
                    'Memahami grammar dasar untuk percakapan',
                    'Meningkatkan kepercayaan diri berbahasa Inggris'
                ],
                'prerequisites' => ['Memahami alfabet dan angka dalam bahasa Inggris'],
                'level' => 'intermediate',
                'status' => 'published',
                'language' => 'id',
                'estimated_hours' => 25,
                'price' => 399000,
                'is_free' => false,
                'is_featured' => true,
                'max_students' => 75,
                'created_by' => $teacher2->id,
                'published_at' => now()->subDays(15),
                'category' => $bahasa,
            ],
            [
                'title' => 'Dasar-dasar Kewirausahaan',
                'description' => 'Pelajari langkah-langkah memulai bisnis dari nol, mulai dari ide bisnis, riset pasar, hingga strategi pemasaran. Dilengkapi dengan studi kasus nyata.',
                'objectives' => [
                    'Mampu mengidentifikasi peluang bisnis',
                    'Membuat business plan sederhana',
                    'Memahami dasar marketing dan branding',
                    'Mengelola keuangan bisnis kecil'
                ],
                'prerequisites' => ['Minat dalam berwirausaha'],
                'level' => 'beginner',
                'status' => 'published',
                'language' => 'id',
                'estimated_hours' => 18,
                'is_free' => true,
                'is_featured' => false,
                'max_students' => 100,
                'created_by' => $teacher2->id,
                'published_at' => now()->subDays(10),
                'category' => $bisnis,
            ],
            [
                'title' => 'Manajemen Waktu dan Produktivitas',
                'description' => 'Tingkatkan produktivitas dengan teknik manajemen waktu yang terbukti efektif. Pelajari cara menetapkan prioritas, menghilangkan prokrastinasi, dan mencapai work-life balance.',
                'objectives' => [
                    'Menguasai teknik time blocking dan Pomodoro',
                    'Mampu menetapkan prioritas dengan metode Eisenhower',
                    'Mengatasi prokrastinasi dan distraksi',
                    'Membangun kebiasaan produktif jangka panjang'
                ],
                'prerequisites' => ['Keinginan untuk berubah lebih baik'],
                'level' => 'beginner',
                'status' => 'published',
                'language' => 'id',
                'estimated_hours' => 15,
                'price' => 199000,
                'is_free' => false,
                'is_featured' => false,
                'max_students' => 150,
                'created_by' => $teacher1->id,
                'published_at' => now()->subDays(5),
                'category' => $pengembangan,
            ],
        ];

        foreach ($courses as $courseData) {
            $category = $courseData['category'];
            unset($courseData['category']);
            
            $course = Course::create($courseData);
            $course->categories()->attach($category->id);
        }
    }
}
