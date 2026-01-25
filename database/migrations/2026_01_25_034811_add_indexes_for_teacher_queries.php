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
        Schema::table('daily_study_sessions', function (Blueprint $table) {
            // Index untuk date range queries (teacher melihat range tanggal)
            $table->index(['study_date', 'user_id'], 'idx_date_user');
            
            // Index untuk sorting by activity
            $table->index(['last_activity_at'], 'idx_last_activity');
            
            // Index untuk filtering active sessions
            $table->index(['sessions_count'], 'idx_sessions_count');
        });

        Schema::table('learning_goals', function (Blueprint $table) {
            // Index untuk guru melihat goals siswa yang active
            $table->index(['user_id', 'status'], 'idx_user_status');
            
            // Index untuk sorting by last study
            $table->index(['last_study_at'], 'idx_last_study');
        });

        Schema::table('users', function (Blueprint $table) {
            // Index untuk search by name (guru search siswa)
            $table->index(['first_name', 'last_name'], 'idx_full_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_study_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_date_user');
            $table->dropIndex('idx_last_activity');
            $table->dropIndex('idx_sessions_count');
        });

        Schema::table('learning_goals', function (Blueprint $table) {
            $table->dropIndex('idx_user_status');
            $table->dropIndex('idx_last_study');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_full_name');
        });
    }
};
