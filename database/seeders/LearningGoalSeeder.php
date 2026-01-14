<?php

namespace Database\Seeders;

use App\Models\LearningGoal;
use App\Models\LearningGoalMilestone;
use App\Models\User;
use App\Models\Course;
use App\Models\Article;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LearningGoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = User::whereHas('roles', function ($query) {
            $query->where('slug', 'student');
        })->first();

        if (!$student) {
            $this->command->warn('No student found. Skipping LearningGoalSeeder.');
            return;
        }
        
        // Clear existing learning goals for student to avoid duplicates
        LearningGoal::where('user_id', $student->id)->delete();

        $courses = Course::where('status', 'published')->take(3)->get();
        $articles = Article::where('status', 'published')->take(2)->get();

        if ($courses->isEmpty()) {
            $this->command->warn('No courses found. Please run CourseSeeder first.');
            return;
        }

        $this->command->info('Creating learning goals...');

        // Goal 1: Active with milestones and final project
        $goal1 = LearningGoal::create([
            'user_id' => $student->id,
            'title' => 'Menguasai Dasar HTML & CSS',
            'description' => 'Mempelajari fundamental HTML dan CSS untuk membuat website sederhana. Target: bisa membuat landing page responsif.',
            'category' => 'skill',
            'priority' => 'high',
            'status' => 'active',
            'target_date' => Carbon::now()->addDays(25),
            'completed_at' => null,
            'progress_notes' => 'Sudah selesai belajar HTML dasar, sedang fokus ke CSS.',
            'related_article_ids' => $articles->isNotEmpty() ? json_encode([$articles->first()->id]) : null,
            'final_project_title' => 'Portfolio Website',
            'final_project_description' => 'Website portfolio personal dengan 5 section: Home, About, Skills, Projects, Contact',
            'final_project_url' => null,
            'final_project_file' => null,
            'final_project_submitted_at' => null,
            'completion_type' => 'final_project',
        ]);

        // Milestones untuk Goal 1
        LearningGoalMilestone::create([
            'learning_goal_id' => $goal1->id,
            'title' => 'HTML Basics',
            'description' => 'Memahami struktur HTML, tags, elements, dan attributes',
            'order' => 1,
            'is_completed' => true,
            'completed_at' => Carbon::now()->subDays(15),
        ]);

        LearningGoalMilestone::create([
            'learning_goal_id' => $goal1->id,
            'title' => 'CSS Fundamentals',
            'description' => 'Belajar CSS selectors, properties, colors, dan fonts',
            'order' => 2,
            'is_completed' => true,
            'completed_at' => Carbon::now()->subDays(10),
        ]);

        LearningGoalMilestone::create([
            'learning_goal_id' => $goal1->id,
            'title' => 'CSS Layout (Flexbox)',
            'description' => 'Menguasai Flexbox untuk membuat layout responsive',
            'order' => 3,
            'is_completed' => true,
            'completed_at' => Carbon::now()->subDays(5),
        ]);

        LearningGoalMilestone::create([
            'learning_goal_id' => $goal1->id,
            'title' => 'Responsive Design',
            'description' => 'Membuat website yang responsive di berbagai ukuran layar',
            'order' => 4,
            'is_completed' => false,
        ]);

        LearningGoalMilestone::create([
            'learning_goal_id' => $goal1->id,
            'title' => 'Final Project: Portfolio',
            'description' => 'Membuat dan deploy portfolio website lengkap',
            'order' => 5,
            'is_completed' => false,
        ]);
        
        // Recalculate progress based on completed milestones
        $goal1->recalculateProgress();

        // Goal 2: Habit goal with daily target and assessment (prerequisites met - can submit)
        $goal2 = LearningGoal::create([
            'user_id' => $student->id,
            'title' => 'Belajar 30 Menit Setiap Hari',
            'description' => 'Konsisten belajar minimal 30 menit setiap hari selama 30 hari untuk membangun kebiasaan belajar yang baik.',
            'category' => 'personal',
            'priority' => 'high',
            'status' => 'active',
            'target_date' => Carbon::now()->addDays(10),
            'completed_at' => null,
            'progress_notes' => 'Sudah mencapai target 30 hari!',
            'related_article_ids' => null,
            'daily_target_minutes' => 30,
            'target_days' => 30,
            'days_completed' => 30, // Target tercapai!
            'completion_type' => 'final_assessment',
        ]);
        
        // Add milestones for Goal 2
        LearningGoalMilestone::create([
            'learning_goal_id' => $goal2->id,
            'title' => 'Week 1: Establish Routine',
            'description' => 'Belajar konsisten 7 hari pertama',
            'order' => 1,
            'is_completed' => true,
            'completed_at' => Carbon::now()->subDays(23),
        ]);

        LearningGoalMilestone::create([
            'learning_goal_id' => $goal2->id,
            'title' => 'Week 2-3: Build Momentum',
            'description' => 'Terus konsisten hingga 21 hari',
            'order' => 2,
            'is_completed' => true,
            'completed_at' => Carbon::now()->subDays(9),
        ]);

        LearningGoalMilestone::create([
            'learning_goal_id' => $goal2->id,
            'title' => 'Week 4: Complete Challenge',
            'description' => 'Selesaikan 30 hari penuh',
            'order' => 3,
            'is_completed' => true,
            'completed_at' => Carbon::now()->subDays(1),
        ]);
        
        // Recalculate progress based on daily target completion
        $goal2->recalculateProgress();

        // Goal 3: Completed with assessment
        $goal3 = LearningGoal::create([
            'user_id' => $student->id,
            'title' => 'Menyelesaikan Course Matematika Dasar',
            'description' => 'Menyelesaikan semua module dan quiz di course Matematika Dasar dengan nilai minimal 80%.',
            'category' => 'academic',
            'priority' => 'high',
            'status' => 'completed',
            'target_date' => Carbon::now()->subDays(5),
            'completed_at' => Carbon::now()->subDays(3),
            'progress_notes' => 'Selesai dengan nilai rata-rata 85%. Sangat membantu!',
            'related_article_ids' => null,
            'completion_type' => 'final_project',
            'final_project_title' => 'Kumpulan Soal Matematika',
            'final_project_description' => 'Rangkuman materi dan 20 soal latihan matematika dasar dengan pembahasan',
            'final_project_url' => 'https://drive.google.com/file/d/example123',
            'final_project_file' => 'matematika-rangkuman.pdf',
            'final_project_submitted_at' => Carbon::now()->subDays(3),
        ]);

        // Milestones untuk Goal 3 (all completed)
        LearningGoalMilestone::create([
            'learning_goal_id' => $goal3->id,
            'title' => 'Selesaikan Semua Module',
            'description' => 'Menyelesaikan 5 module pembelajaran',
            'order' => 1,
            'is_completed' => true,
            'completed_at' => Carbon::now()->subDays(10),
        ]);

        LearningGoalMilestone::create([
            'learning_goal_id' => $goal3->id,
            'title' => 'Lulus Semua Quiz',
            'description' => 'Mendapat nilai minimal 80% di semua quiz',
            'order' => 2,
            'is_completed' => true,
            'completed_at' => Carbon::now()->subDays(5),
        ]);

        LearningGoalMilestone::create([
            'learning_goal_id' => $goal3->id,
            'title' => 'Submit Final Project',
            'description' => 'Membuat dan submit rangkuman materi',
            'order' => 3,
            'is_completed' => true,
            'completed_at' => Carbon::now()->subDays(3),
        ]);
        
        // Recalculate progress - should be 100% since all milestones and final project completed
        $goal3->recalculateProgress();

        $this->command->info('✓ Created 3 learning goals with milestones for student');
    }
}
