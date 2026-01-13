<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Enrollments - Track student enrollment in courses (FSDL)
     */
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            
            // Enrollment Details
            $table->enum('status', ['active', 'completed', 'dropped', 'suspended'])->default('active');
            $table->timestamp('enrolled_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            // Progress Tracking
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->integer('completed_lessons')->default(0);
            $table->integer('total_lessons')->default(0);
            $table->integer('completed_modules')->default(0);
            $table->integer('total_modules')->default(0);
            
            // Time Tracking
            $table->integer('total_study_minutes')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->integer('access_count')->default(0);
            
            // Performance
            $table->decimal('quiz_average_score', 5, 2)->nullable();
            $table->integer('quizzes_taken')->default(0);
            $table->integer('quizzes_passed')->default(0);
            
            // Certificate
            $table->string('certificate_number')->nullable()->unique();
            $table->timestamp('certificate_issued_at')->nullable();
            
            // Tracking
            $table->foreignId('enrolled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->unique(['user_id', 'course_id']);
            $table->index('status');
            $table->index('enrolled_at');
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
