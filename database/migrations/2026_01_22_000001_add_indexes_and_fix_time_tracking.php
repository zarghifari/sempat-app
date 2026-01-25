<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Optimize time tracking and add necessary indexes for performance
     */
    public function up(): void
    {
        // Add last_time_sync field to track when we last synced to enrollment
        if (!Schema::hasColumn('lesson_completions', 'last_time_sync')) {
            Schema::table('lesson_completions', function (Blueprint $table) {
                $table->timestamp('last_time_sync')->nullable()->after('last_accessed_at');
            });
        }

        // Add study session tracking to learning_goals
        if (!Schema::hasColumn('learning_goals', 'total_study_seconds')) {
            Schema::table('learning_goals', function (Blueprint $table) {
                $table->integer('total_study_seconds')->default(0)->after('days_completed');
            });
        }
        
        if (!Schema::hasColumn('learning_goals', 'last_study_at')) {
            Schema::table('learning_goals', function (Blueprint $table) {
                $table->timestamp('last_study_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('lesson_completions', 'last_time_sync')) {
            Schema::table('lesson_completions', function (Blueprint $table) {
                $table->dropColumn('last_time_sync');
            });
        }

        if (Schema::hasColumn('learning_goals', 'last_study_at')) {
            Schema::table('learning_goals', function (Blueprint $table) {
                $table->dropColumn('last_study_at');
            });
        }
        
        if (Schema::hasColumn('learning_goals', 'total_study_seconds')) {
            Schema::table('learning_goals', function (Blueprint $table) {
                $table->dropColumn('total_study_seconds');
            });
        }
    }
};
