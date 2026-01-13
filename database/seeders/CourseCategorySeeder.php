<?php

namespace Database\Seeders;

use App\Models\CourseCategory;
use Illuminate\Database\Seeder;

class CourseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Matematika',
                'description' => 'Pelajari konsep matematika dasar hingga lanjutan',
                'icon' => '📐',
                'color' => 'blue',
                'order' => 1,
            ],
            [
                'name' => 'Sains & Teknologi',
                'description' => 'Eksplorasi dunia sains dan teknologi modern',
                'icon' => '🔬',
                'color' => 'green',
                'order' => 2,
            ],
            [
                'name' => 'Bahasa',
                'description' => 'Kuasai berbagai bahasa dengan pembelajaran terstruktur',
                'icon' => '📚',
                'color' => 'purple',
                'order' => 3,
            ],
            [
                'name' => 'Bisnis & Ekonomi',
                'description' => 'Pelajari dunia bisnis dan manajemen',
                'icon' => '💼',
                'color' => 'yellow',
                'order' => 4,
            ],
            [
                'name' => 'Pengembangan Diri',
                'description' => 'Tingkatkan keterampilan pribadi dan profesional',
                'icon' => '🎯',
                'color' => 'red',
                'order' => 5,
            ],
            [
                'name' => 'Seni & Kreativitas',
                'description' => 'Kembangkan bakat seni dan kreativitas Anda',
                'icon' => '🎨',
                'color' => 'pink',
                'order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            CourseCategory::create($category);
        }
    }
}
