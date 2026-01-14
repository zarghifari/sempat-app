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
        Schema::table('learning_goal_milestones', function (Blueprint $table) {
            $table->boolean('requires_evidence')->default(false)->after('description');
            $table->text('evidence_text')->nullable()->after('completed_at');
            $table->string('evidence_file')->nullable()->after('evidence_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_goal_milestones', function (Blueprint $table) {
            $table->dropColumn(['requires_evidence', 'evidence_text', 'evidence_file']);
        });
    }
};
