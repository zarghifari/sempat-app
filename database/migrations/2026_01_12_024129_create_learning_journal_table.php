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
        Schema::create('learning_journal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Entry Details
            $table->string('title');
            $table->longText('content');
            $table->date('entry_date');
            
            // Reflection
            $table->text('what_learned')->nullable();
            $table->text('challenges_faced')->nullable();
            $table->text('next_steps')->nullable();
            
            // Related Content
            $table->foreignId('article_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('learning_goal_id')->nullable()->constrained()->onDelete('set null');
            
            // Mood & Time
            $table->enum('mood', ['excited', 'confident', 'neutral', 'challenged', 'frustrated'])->nullable();
            $table->integer('study_duration_minutes')->nullable();
            
            // Tags
            $table->json('tags')->nullable();
            
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('entry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_journal');
    }
};
