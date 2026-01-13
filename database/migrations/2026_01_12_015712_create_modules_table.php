<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Modules - Organizational units within courses
     */
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0); // Sequence in course
            
            // Content
            $table->integer('estimated_minutes')->default(0);
            $table->integer('lessons_count')->default(0);
            
            // Status
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('is_locked')->default(false); // Requires previous module completion
            
            // Tracking
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('course_id');
            $table->index(['course_id', 'order']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
