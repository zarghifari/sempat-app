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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->integer('attempt_number')->default(1);
            $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
            
            // Timing
            $table->dateTime('started_at');
            $table->dateTime('completed_at')->nullable();
            $table->integer('time_spent_seconds')->default(0);
            
            // Scoring
            $table->json('answers'); // {"question_id": "answer", ...}
            $table->integer('correct_answers')->default(0);
            $table->integer('total_questions')->default(0);
            $table->decimal('score_percentage', 5, 2)->default(0);
            $table->integer('points_earned')->default(0);
            $table->integer('total_points')->default(0);
            $table->boolean('passed')->default(false);
            
            // Feedback
            $table->text('teacher_feedback')->nullable();
            $table->dateTime('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users');
            
            $table->timestamps();
            
            // Indexes
            $table->index('quiz_id');
            $table->index('user_id');
            $table->index(['quiz_id', 'user_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
