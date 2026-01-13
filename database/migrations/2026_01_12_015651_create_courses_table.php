<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * FSDL (Facilitated Self-Directed Learning) - Courses Table
     * Core table untuk structured learning dengan guidance dari teacher
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('objectives')->nullable(); // Learning objectives
            $table->text('prerequisites')->nullable();
            
            // Content & Media
            $table->string('thumbnail')->nullable();
            $table->text('intro_video_url')->nullable();
            
            // Metadata
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->string('language', 10)->default('id'); // id, en
            $table->integer('estimated_hours')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_free')->default(true);
            $table->boolean('is_featured')->default(false);
            
            // Enrollment
            $table->integer('max_students')->nullable();
            $table->integer('enrolled_count')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            // Analytics
            $table->integer('views_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('completion_count')->default(0);
            
            // Ownership & Tracking
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('level');
            $table->index('created_by');
            $table->index('is_featured');
            $table->index('published_at');
            $table->fullText(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
