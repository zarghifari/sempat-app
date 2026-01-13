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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('category_id')->constrained('article_categories')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            // Content
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('thumbnail')->nullable();
            $table->json('attachments')->nullable();
            $table->json('external_links')->nullable();
            
            // Metadata
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->integer('reading_time_minutes')->nullable();
            $table->string('language', 5)->default('id');
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_comments')->default(true);
            
            // Analytics
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('bookmarks_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->decimal('rating_average', 3, 2)->nullable();
            $table->integer('rating_count')->default(0);
            
            // Publishing
            $table->timestamp('published_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('category_id');
            $table->index('created_by');
            $table->index('status');
            $table->index('is_featured');
            $table->index('published_at');
            $table->fullText(['title', 'excerpt', 'content']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
