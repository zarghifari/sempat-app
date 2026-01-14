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
            ['name' => 'Studi Kasus', 'color' => 'orange'],
            ['name' => 'Produktivitas', 'color' => 'cyan'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
