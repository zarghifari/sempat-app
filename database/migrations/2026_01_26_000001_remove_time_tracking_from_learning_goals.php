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
        // Remove time tracking fields from learning_goals
        // Learning goals hanya untuk set target, bukan tempat belajar
        Schema::table('learning_goals', function (Blueprint $table) {
            if (Schema::hasColumn('learning_goals', 'total_study_seconds')) {
                $table->dropColumn('total_study_seconds');
            }
            if (Schema::hasColumn('learning_goals', 'last_study_at')) {
                $table->dropColumn('last_study_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_goals', function (Blueprint $table) {
            $table->integer('total_study_seconds')->default(0)->after('days_completed');
            $table->timestamp('last_study_at')->nullable()->after('total_study_seconds');
        });
    }
};
