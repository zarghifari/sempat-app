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
        // Drop study_sessions table as it's now redundant
        // All time tracking data is consolidated in daily_study_sessions
        Schema::dropIfExists('study_sessions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate table structure for rollback
        Schema::create('study_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('learning_goal_id')->constrained()->onDelete('cascade');
            $table->json('session_logs')->default('{}');
            $table->timestamps();

            $table->unique(['user_id', 'learning_goal_id']);
        });
    }
};
