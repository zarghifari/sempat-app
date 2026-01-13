<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleLike;
use App\Models\Sticker;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::whereHas('roles', function($query) {
            $query->where('slug', 'student');
        })->get();
        $articles = Article::all();
        $stickers = Sticker::all();

        if ($students->isEmpty() || $articles->isEmpty()) {
            $this->command->warn('No students or articles found. Skipping seeder.');
            return;
        }

        // Text comments with varied content
        $commentTexts = [
            'Artikel ini sangat membantu! Terima kasih atas penjelasannya 🙏',
            'Saya baru paham sekarang, penjelasannya mudah dipahami',
            'Bisa dijelaskan lebih detail tentang bagian ini?',
            'Keren banget penjelasannya, langsung praktek!',
            'Ada referensi tambahan untuk belajar lebih lanjut?',
            'Wah ini yang saya cari-cari, makasih banyak!',
            'Penjelasannya detail dan mudah diikuti',
            'Boleh tanya dong, kalau untuk kasus X gimana ya?',
            'Artikel yang sangat bermanfaat untuk pemula seperti saya',
            'Simpel tapi jelas, langsung ngerti!',
            'Ini cocok banget buat yang baru mulai belajar',
            'Materinya padat tapi gak bikin pusing',
            'Contohnya relate banget sama kehidupan sehari-hari',
            'Bisa diterapin langsung nih ilmunya',
            'Thanks for sharing! Very helpful',
            'Step by stepnya jelas banget',
            'Butuh baca beberapa kali biar paham sepenuhnya',
            'Ilustrasinya membantu banget untuk memahami konsepnya',
            'Bagus! Tapi ada typo di paragraf kedua',
            'Penjelasan yang comprehensive dan mudah diikuti',
        ];

        $replyTexts = [
            'Setuju banget!',
            'Sama, saya juga baru paham sekarang',
            'Coba cek di bagian referensi, ada link tambahan',
            'Untuk kasus itu bisa pakai cara yang sama kok',
            'Terima kasih atas feedbacknya!',
            'Betul, saya juga langsung praktek dan berhasil',
            'Good question! Saya juga penasaran',
            'Sama-sama! Senang bisa membantu',
            'Thanks for the tips!',
            'Noted, akan saya perbaiki',
        ];

        foreach ($articles as $article) {
            // Random number of comments per article (3-8)
            $commentCount = rand(3, 8);
            
            for ($i = 0; $i < $commentCount; $i++) {
                $student = $students->random();
                
                // 70% text comments, 30% sticker comments
                $isTextComment = rand(1, 100) <= 70;
                
                if ($isTextComment) {
                    $comment = ArticleComment::create([
                        'article_id' => $article->id,
                        'user_id' => $student->id,
                        'content' => $commentTexts[array_rand($commentTexts)],
                        'created_at' => now()->subDays(rand(0, 10))->subHours(rand(0, 23)),
                    ]);
                } else {
                    $sticker = $stickers->random();
                    $comment = ArticleComment::create([
                        'article_id' => $article->id,
                        'user_id' => $student->id,
                        'sticker_id' => $sticker->id,
                        'created_at' => now()->subDays(rand(0, 10))->subHours(rand(0, 23)),
                    ]);
                    
                    // Increment sticker usage
                    $sticker->incrementUsage();
                }

                // 40% chance of having a reply
                if (rand(1, 100) <= 40 && $students->count() > 1) {
                    $otherStudents = $students->where('id', '!=', $student->id);
                    if ($otherStudents->isNotEmpty()) {
                        $replier = $otherStudents->random();
                    } else {
                        continue;
                    }
                    
                    // 80% text reply, 20% sticker reply
                    $isTextReply = rand(1, 100) <= 80;
                    
                    if ($isTextReply) {
                        ArticleComment::create([
                            'article_id' => $article->id,
                            'user_id' => $replier->id,
                            'parent_id' => $comment->id,
                            'content' => $replyTexts[array_rand($replyTexts)],
                            'created_at' => now()->subDays(rand(0, 9))->subHours(rand(0, 23)),
                        ]);
                    } else {
                        $replySticker = $stickers->random();
                        ArticleComment::create([
                            'article_id' => $article->id,
                            'user_id' => $replier->id,
                            'parent_id' => $comment->id,
                            'sticker_id' => $replySticker->id,
                            'created_at' => now()->subDays(rand(0, 9))->subHours(rand(0, 23)),
                        ]);
                        
                        $replySticker->incrementUsage();
                    }
                }
            }

            // Update article comments count
            $article->update(['comments_count' => $article->allComments()->count()]);

            // Add random likes to article (30-70% of students)
            $likeCount = rand((int)($students->count() * 0.3), (int)($students->count() * 0.7));
            if ($likeCount > 0) {
                $likers = $students->random($likeCount);
            } else {
                $likers = collect();
            }
            
            foreach ($likers as $liker) {
                ArticleLike::create([
                    'article_id' => $article->id,
                    'user_id' => $liker->id,
                    'created_at' => now()->subDays(rand(0, 10))->subHours(rand(0, 23)),
                ]);
            }

            // Update article likes count
            $article->update(['likes_count' => $article->likes()->count()]);
        }

        $this->command->info('Created comments and likes for ' . $articles->count() . ' articles');
    }
}
