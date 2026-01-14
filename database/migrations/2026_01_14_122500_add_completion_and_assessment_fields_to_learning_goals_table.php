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
            // Completion type: 'final_project' or 'final_assessment'
            $table->enum('completion_type', ['final_project', 'final_assessment'])->nullable()->after('final_project_submitted_at');
            
            // Final Assessment fields (SPSDL reflection)
            $table->text('assessment_what_learned')->nullable()->after('completion_type');
            $table->text('assessment_how_applied')->nullable()->after('assessment_what_learned');
            $table->text('assessment_challenges')->nullable()->after('assessment_how_applied');
            $table->text('assessment_next_steps')->nullable()->after('assessment_challenges');
            $table->timestamp('assessment_submitted_at')->nullable()->after('assessment_next_steps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_goals', function (Blueprint $table) {
            $table->dropColumn([
                'completion_type',
                'assessment_what_learned',
                'assessment_how_applied',
                'assessment_challenges',
                'assessment_next_steps',
                'assessment_submitted_at'
            ]);
        });
    }
};
