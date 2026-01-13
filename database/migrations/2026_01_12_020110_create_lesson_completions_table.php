<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Lesson Completions - Detailed tracking of lesson progress
     */
    public function up(): void
    {
        Schema::create('lesson_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            
            // Completion Status
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->decimal('progress_percentage', 5, 2)->default(0);
            
            // Time Tracking
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_spent_seconds')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            
            // Engagement Metrics
            $table->integer('view_count')->default(0);
            $table->integer('replay_count')->default(0);
            $table->json('video_progress')->nullable(); // For video lessons
            
            // Quiz/Assessment (if lesson has quiz)
            $table->decimal('quiz_score', 5, 2)->nullable();
            $table->integer('quiz_attempts')->default(0);
            $table->timestamp('quiz_passed_at')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            $table->json('bookmarks')->nullable(); // Timestamp bookmarks for video
            
            $table->timestamps();
            
            // Indexes
            $table->unique(['user_id', 'lesson_id']);
            $table->index('enrollment_id');
            $table->index('status');
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_completions');
    }
};
