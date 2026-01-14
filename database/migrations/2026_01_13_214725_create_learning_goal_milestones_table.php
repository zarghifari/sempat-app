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
        Schema::create('learning_goal_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_goal_id')->constrained()->onDelete('cascade');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by_journal_id')->nullable()->constrained('learning_journal')->onDelete('set null');
            
            $table->timestamps();
            
            $table->index('learning_goal_id');
            $table->index('is_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_goal_milestones');
    }
};
