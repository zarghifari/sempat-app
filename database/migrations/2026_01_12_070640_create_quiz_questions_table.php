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
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'essay']);
            $table->text('question');
            $table->json('options')->nullable(); // For multiple choice: ["Option A", "Option B", ...]
            $table->text('correct_answer'); // For multiple choice: "A", for true_false: "true"/"false"
            $table->text('explanation')->nullable(); // Explanation shown after answering
            
            $table->integer('points')->default(1);
            $table->integer('order')->default(0);
            
            // Media support
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('quiz_id');
            $table->index(['quiz_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
