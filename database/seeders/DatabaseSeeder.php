<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            CourseCategorySeeder::class,
            CourseSeeder::class,
            ModuleSeeder::class,
            LessonSeeder::class,
            HtmlDocumentImportSeeder::class,
            QuizSeeder::class,
            EnrollmentSeeder::class,
            ArticleCategorySeeder::class,
            TagSeeder::class,
            ArticleSeeder::class,
            StickerSeeder::class,
            ArticleCommentSeeder::class,
            LearningGoalSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('==================================');
        $this->command->info('✓ Database seeding completed!');
        $this->command->info('==================================');
        $this->command->info('Demo accounts:');
        $this->command->info('  Admin:       admin@sempat.test / password');
        $this->command->info('  Teacher 1:   teacher@sempat.test / password');
        $this->command->info('  Teacher 2:   teacher2@sempat.test / password');
        $this->command->info('  Student:     student@sempat.test / password');
        $this->command->info('==================================');
        $this->command->info('FSDL Data:');
        $this->command->info('  6 Course Categories');
        $this->command->info('  3 Courses with modules & lessons');
        $this->command->info('  3 Quizzes (HTML, CSS, JavaScript)');
        $this->command->info('  Student enrolled in 2 courses');
        $this->command->info('==================================');
        $this->command->info('SPSDL Data:');
        $this->command->info('  6 Article Categories');
        $this->command->info('  8 Tags');
        $this->command->info('  3 Published Articles');
        $this->command->info('  26 Stickers (reactions, emotions, memes)');
        $this->command->info('  Comments & Likes on 3 Articles');
        $this->command->info('  3 Learning Goals (active, completed)');
        $this->command->info('==================================');
    }
}
