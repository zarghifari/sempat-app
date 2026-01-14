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
        Schema::create('study_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('learning_goal_id')->constrained()->onDelete('cascade');
            $table->json('session_logs'); // {"2026-01-14": 45, "2026-01-13": 60}
            $table->timestamps();
            
            // Index untuk query cepat
            $table->index(['user_id', 'learning_goal_id']);
            
            // Unique constraint: satu user hanya punya 1 record per goal
            $table->unique(['user_id', 'learning_goal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_sessions');
    }
};
