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
        Schema::table('learning_goals', function (Blueprint $table) {
            // Daily habit tracking
            $table->integer('daily_target_minutes')->nullable()->after('progress_percentage');
            $table->integer('target_days')->nullable()->after('daily_target_minutes');
            $table->integer('days_completed')->default(0)->after('target_days');
            
            // Final project
            $table->string('final_project_title')->nullable()->after('progress_notes');
            $table->text('final_project_description')->nullable()->after('final_project_title');
            $table->string('final_project_url')->nullable()->after('final_project_description');
            $table->string('final_project_file')->nullable()->after('final_project_url');
            $table->timestamp('final_project_submitted_at')->nullable()->after('final_project_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_goals', function (Blueprint $table) {
            $table->dropColumn([
                'daily_target_minutes',
                'target_days',
                'days_completed',
                'final_project_title',
                'final_project_description',
                'final_project_url',
                'final_project_file',
                'final_project_submitted_at',
            ]);
        });
    }
};
