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
        Schema::create('document_imports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            
            // User who uploaded the document
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Original file information
            $table->string('original_filename');
            $table->string('file_path'); // Path to uploaded file
            $table->string('file_type'); // .docx, .doc
            $table->unsignedBigInteger('file_size'); // in bytes
            
            // Import status
            $table->enum('status', [
                'pending',      // Uploaded, waiting to be processed
                'processing',   // Currently being processed
                'completed',    // Successfully imported
                'failed'        // Import failed
            ])->default('pending');
            
            // Processing information
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('processing_time_seconds')->nullable();
            
            // Transformation results
            $table->longText('html_content')->nullable(); // Converted HTML
            $table->json('extracted_images')->nullable(); // Array of image paths
            $table->json('metadata')->nullable(); // Document metadata (author, title, etc.)
            
            // Statistics
            $table->integer('word_count')->nullable();
            $table->integer('image_count')->default(0);
            $table->integer('page_count')->nullable();
            
            // Error handling
            $table->text('error_message')->nullable();
            $table->json('error_details')->nullable();
            
            // Optional: Link to created lesson if imported as lesson
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_imports');
    }
};
