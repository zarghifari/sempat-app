<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Lessons - Individual learning units within modules
     */
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0); // Sequence in module
            
            // Content
            $table->enum('type', ['text', 'video', 'audio', 'document', 'interactive', 'quiz'])->default('text');
            $table->longText('content')->nullable(); // HTML content for text type
            $table->string('video_url')->nullable();
            $table->string('audio_url')->nullable();
            $table->integer('duration_minutes')->default(0);
            
            // Resources
            $table->json('attachments')->nullable(); // Array of file paths
            $table->json('external_links')->nullable();
            
            // Status & Access
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('is_preview')->default(false); // Allow preview without enrollment
            $table->boolean('is_mandatory')->default(true);
            
            // Completion Requirements
            $table->integer('min_completion_time')->default(0); // Seconds
            $table->boolean('requires_quiz')->default(false);
            $table->decimal('min_quiz_score', 5, 2)->nullable();
            
            // Analytics
            $table->integer('views_count')->default(0);
            $table->integer('completion_count')->default(0);
            $table->decimal('avg_completion_time', 8, 2)->default(0); // Minutes
            
            // Tracking
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('module_id');
            $table->index(['module_id', 'order']);
            $table->index('type');
            $table->index('status');
            $table->fullText(['title', 'content']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
