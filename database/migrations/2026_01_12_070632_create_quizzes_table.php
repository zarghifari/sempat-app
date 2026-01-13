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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            
            // Quiz Settings
            $table->integer('time_limit_minutes')->nullable(); // null = no time limit
            $table->integer('passing_score')->default(70); // percentage
            $table->integer('max_attempts')->default(3); // 0 = unlimited
            $table->boolean('show_correct_answers')->default(true);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_options')->default(false);
            
            // Quiz Status
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->dateTime('published_at')->nullable();
            
            // Statistics
            $table->integer('total_questions')->default(0);
            $table->integer('total_attempts')->default(0);
            $table->decimal('average_score', 5, 2)->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index('lesson_id');
            $table->index('created_by');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
