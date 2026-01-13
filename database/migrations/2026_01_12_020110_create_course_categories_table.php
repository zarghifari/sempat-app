<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Course Categories - Organize courses by subject/topic
     */
    public function up(): void
    {
        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color', 20)->default('blue');
            $table->foreignId('parent_id')->nullable()->constrained('course_categories')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('parent_id');
            $table->index('is_active');
        });
        
        // Pivot table for course-category relationship
        Schema::create('course_category', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_category_id')->constrained()->onDelete('cascade');
            $table->primary(['course_id', 'course_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_category');
        Schema::dropIfExists('course_categories');
    }
};
