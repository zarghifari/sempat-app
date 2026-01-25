<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_study_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('study_date'); // YYYY-MM-DD
            
            // Aggregated totals (in seconds)
            $table->integer('total_articles_time')->default(0);
            $table->integer('total_lessons_time')->default(0);
            $table->integer('total_goals_time')->default(0);
            $table->integer('total_study_time')->default(0); // Sum of all
            
            // Detailed breakdown (JSON)
            // Articles: {"article_3": 120, "article_5": 300, ...}
            $table->json('articles_breakdown')->nullable();
            
            // Lessons per course: {"course_1": 450, "course_2": 600, ...}
            $table->json('courses_breakdown')->nullable();
            
            // Goals: {"goal_18": 1800, ...}
            $table->json('goals_breakdown')->nullable();
            
            // Session metadata
            $table->integer('sessions_count')->default(0); // How many study sessions
            $table->timestamp('first_activity_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for fast queries
            $table->unique(['user_id', 'study_date']); // One record per user per day
            $table->index(['study_date']); // For date-based queries
            $table->index(['user_id', 'study_date']); // Composite for user queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_study_sessions');
    }
};
