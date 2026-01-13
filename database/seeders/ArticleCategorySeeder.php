<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use Illuminate\Database\Seeder;

class ArticleCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Teknologi & Pemrograman',
                'description' => 'Artikel tentang teknologi terkini, pemrograman, dan software development',
                'icon' => '💻',
                'color' => 'blue',
                'order' => 1,
            ],
            [
                'name' => 'Ilmu Pengetahuan',
                'description' => 'Eksplorasi sains, fisika, kimia, biologi, dan matematika',
                'icon' => '🔬',
                'color' => 'green',
                'order' => 2,
            ],
            [
                'name' => 'Pengembangan Diri',
                'description' => 'Tips produktivitas, soft skills, dan pengembangan pribadi',
                'icon' => '🌱',
                'color' => 'purple',
                'order' => 3,
            ],
            [
                'name' => 'Karir & Bisnis',
                'description' => 'Panduan karir, entrepreneurship, dan dunia kerja',
                'icon' => '💼',
                'color' => 'yellow',
                'order' => 4,
            ],
            [
                'name' => 'Pendidikan & Belajar',
                'description' => 'Metode belajar efektif, tips ujian, dan strategi pembelajaran',
                'icon' => '📚',
                'color' => 'red',
                'order' => 5,
            ],
            [
                'name' => 'Hobi & Kreativitas',
                'description' => 'Seni, musik, desain, dan aktivitas kreatif',
                'icon' => '🎨',
                'color' => 'pink',
                'order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            ArticleCategory::create($category);
        }
    }
}
