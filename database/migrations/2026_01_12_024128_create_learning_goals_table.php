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
        Schema::create('learning_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Goal Details
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', ['skill', 'knowledge', 'career', 'personal', 'academic'])->default('skill');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['active', 'completed', 'abandoned'])->default('active');
            
            // Timeline
            $table->date('target_date')->nullable();
            $table->date('completed_at')->nullable();
            
            // Progress
            $table->integer('progress_percentage')->default(0);
            $table->text('progress_notes')->nullable();
            
            // Related Articles
            $table->json('related_article_ids')->nullable();
            
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_goals');
    }
};
