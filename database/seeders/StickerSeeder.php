<?php

namespace Database\Seeders;

use App\Models\Sticker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StickerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stickers = [
            // Reaction stickers
            [
                'name' => 'Thumbs Up',
                'code' => 'thumbs_up',
                'image_url' => 'stickers/reactions/thumbs_up.png',
                'category' => 'reaction',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'Thumbs Down',
                'code' => 'thumbs_down',
                'image_url' => 'stickers/reactions/thumbs_down.png',
                'category' => 'reaction',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'Heart',
                'code' => 'heart',
                'image_url' => 'stickers/reactions/heart.png',
                'category' => 'reaction',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'name' => 'Fire',
                'code' => 'fire',
                'image_url' => 'stickers/reactions/fire.png',
                'category' => 'reaction',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'name' => 'Clap',
                'code' => 'clap',
                'image_url' => 'stickers/reactions/clap.png',
                'category' => 'reaction',
                'is_active' => true,
                'order' => 5,
            ],
            [
                'name' => '100',
                'code' => 'hundred',
                'image_url' => 'stickers/reactions/hundred.png',
                'category' => 'reaction',
                'is_active' => true,
                'order' => 6,
            ],
            [
                'name' => 'Star Eyes',
                'code' => 'star_eyes',
                'image_url' => 'stickers/reactions/star_eyes.png',
                'category' => 'reaction',
                'is_active' => true,
                'order' => 7,
            ],
            [
                'name' => 'Mind Blown',
                'code' => 'mind_blown',
                'image_url' => 'stickers/reactions/mind_blown.png',
                'category' => 'reaction',
                'is_active' => true,
                'order' => 8,
            ],

            // Emotion stickers
            [
                'name' => 'Laugh',
                'code' => 'laugh',
                'image_url' => 'stickers/emotions/laugh.png',
                'category' => 'emotion',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'Cry',
                'code' => 'cry',
                'image_url' => 'stickers/emotions/cry.png',
                'category' => 'emotion',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'Shock',
                'code' => 'shock',
                'image_url' => 'stickers/emotions/shock.png',
                'category' => 'emotion',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'name' => 'Angry',
                'code' => 'angry',
                'image_url' => 'stickers/emotions/angry.png',
                'category' => 'emotion',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'name' => 'Thinking',
                'code' => 'thinking',
                'image_url' => 'stickers/emotions/thinking.png',
                'category' => 'emotion',
                'is_active' => true,
                'order' => 5,
            ],
            [
                'name' => 'Cool',
                'code' => 'cool',
                'image_url' => 'stickers/emotions/cool.png',
                'category' => 'emotion',
                'is_active' => true,
                'order' => 6,
            ],
            [
                'name' => 'Confused',
                'code' => 'confused',
                'image_url' => 'stickers/emotions/confused.png',
                'category' => 'emotion',
                'is_active' => true,
                'order' => 7,
            ],
            [
                'name' => 'Sleepy',
                'code' => 'sleepy',
                'image_url' => 'stickers/emotions/sleepy.png',
                'category' => 'emotion',
                'is_active' => true,
                'order' => 8,
            ],

            // Meme stickers
            [
                'name' => 'Stonks',
                'code' => 'stonks',
                'image_url' => 'stickers/memes/stonks.png',
                'category' => 'meme',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'Doge',
                'code' => 'doge',
                'image_url' => 'stickers/memes/doge.png',
                'category' => 'meme',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'Drake No',
                'code' => 'drake_no',
                'image_url' => 'stickers/memes/drake_no.png',
                'category' => 'meme',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'name' => 'Drake Yes',
                'code' => 'drake_yes',
                'image_url' => 'stickers/memes/drake_yes.png',
                'category' => 'meme',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'name' => 'This Is Fine',
                'code' => 'this_is_fine',
                'image_url' => 'stickers/memes/this_is_fine.png',
                'category' => 'meme',
                'is_active' => true,
                'order' => 5,
            ],
            [
                'name' => 'Big Brain',
                'code' => 'big_brain',
                'image_url' => 'stickers/memes/big_brain.png',
                'category' => 'meme',
                'is_active' => true,
                'order' => 6,
            ],
            [
                'name' => 'Surprised Pikachu',
                'code' => 'surprised_pikachu',
                'image_url' => 'stickers/memes/surprised_pikachu.png',
                'category' => 'meme',
                'is_active' => true,
                'order' => 7,
            ],
            [
                'name' => 'Success Kid',
                'code' => 'success_kid',
                'image_url' => 'stickers/memes/success_kid.png',
                'category' => 'meme',
                'is_active' => true,
                'order' => 8,
            ],
            [
                'name' => 'Galaxy Brain',
                'code' => 'galaxy_brain',
                'image_url' => 'stickers/memes/galaxy_brain.png',
                'category' => 'meme',
                'is_active' => true,
                'order' => 9,
            ],
            [
                'name' => 'Press F',
                'code' => 'press_f',
                'image_url' => 'stickers/memes/press_f.png',
                'category' => 'meme',
                'is_active' => true,
                'order' => 10,
            ],
        ];

        foreach ($stickers as $sticker) {
            Sticker::create($sticker);
        }
    }
}
