<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Tutorial', 'color' => 'blue'],
            ['name' => 'Panduan', 'color' => 'green'],
            ['name' => 'Tips & Trik', 'color' => 'yellow'],
            ['name' => 'Pemula', 'color' => 'purple'],
            ['name' => 'Advanced', 'color' => 'red'],
            ['name' => 'Praktis', 'color' => 'pink'],
            ['name' => 'Teori', 'color' => 'indigo'],
            ['name' => 'Studi Kasus', 'color' => 'orange'],
            ['name' => 'Inspirasi', 'color' => 'teal'],
            ['name' => 'Produktivitas', 'color' => 'cyan'],
            ['name' => 'Motivasi', 'color' => 'emerald'],
            ['name' => 'Teknologi', 'color' => 'blue'],
            ['name' => 'Coding', 'color' => 'slate'],
            ['name' => 'Desain', 'color' => 'pink'],
            ['name' => 'Karir', 'color' => 'amber'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
