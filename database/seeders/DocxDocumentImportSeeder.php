<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Module;
use App\Models\User;
use App\Services\DocumentImportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class DocxDocumentImportSeeder extends Seeder
{
    protected DocumentImportService $importService;

    public function __construct()
    {
        $this->importService = new DocumentImportService();
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting DOCX Document Import Test...');

        // Get teacher user
        $teacher = User::where('email', 'teacher@sempat.test')->first();
        if (!$teacher) {
            $this->command->error('Teacher user not found!');
            return;
        }

        // Get Computer Graphics course
        $course = Course::where('slug', 'komputer-grafis-desain-visual')->first();
        if (!$course) {
            $this->command->error('Course not found! Run HtmlDocumentImportSeeder first.');
            return;
        }
        $this->command->info("✅ Course: {$course->title}");

        // Get or create module
        $module = Module::where('course_id', $course->id)
            ->where('title', 'Pengantar Komputer Grafis')
            ->first();
        
        if (!$module) {
            $this->command->error('Module not found!');
            return;
        }
        $this->command->info("✅ Module: {$module->title}");

        // Skip if DOCX lesson already exists
        $existing = \App\Models\Lesson::where('module_id', $module->id)
            ->where('type', 'document')
            ->first();
        if ($existing) {
            $this->command->info("⏭️  DOCX lesson already exists (ID: {$existing->id}), skipping.");
            $this->command->info('✨ DOCX Document Import Test Complete!');
            return;
        }

        // Test DOCX import
        $this->importDocxDocument($module, $teacher);

        $this->command->info('✨ DOCX Document Import Test Complete!');
    }

    /**
     * Import DOCX document
     */
    protected function importDocxDocument(Module $module, User $teacher): void
    {
        $this->command->info('📄 Testing DOCX document conversion...');

        // Path to DOCX file
        $docxPath = database_path('seeders/1_computer_grafis.docx');
        
        if (!file_exists($docxPath)) {
            $this->command->error("DOCX file not found at: {$docxPath}");
            return;
        }

        // Generate unique filename
        $filename = 'computer-grafis-' . time() . '.docx';
        
        // Store file in public disk
        $path = 'document-imports/uploads/' . $filename;
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, file_get_contents($docxPath));

        try {
            // Create import record
            $import = \App\Models\DocumentImport::create([
                'user_id' => $teacher->id,
                'original_filename' => '1_computer_grafis.docx',
                'file_path' => $path,
                'file_type' => 'docx',
                'file_size' => filesize($docxPath),
                'status' => 'pending',
            ]);

            $this->command->info("📝 Import record created: ID {$import->id}");

            // Process immediately (not dispatching job, for testing)
            $this->importService->processImport($import);

            // Reload to get updated data
            $import->refresh();

            $this->command->info("📝 Status: {$import->status}");
            
            if ($import->status === 'completed') {
                $this->command->info("⏱️  Processing time: " . ($import->processing_time ?? 'N/A') . "s");
                $this->command->info("🖼️  Images found: " . ($import->image_count ?? 0));
                $this->command->info("📎 Attachments: " . ($import->extracted_attachments ? count($import->extracted_attachments) : 0));
                $this->command->info("📝 Word count: " . ($import->word_count ?? 0));
                
                // Create lesson from import
                $this->command->info("🎓 Creating lesson from import...");
                
                $lesson = $this->importService->createLessonFromImport(
                    $import,
                    $module->id,
                    [
                        'title' => 'Komputer Grafis & Desain Visual (Dari DOCX)',
                        'description' => 'Lesson yang diimport dari dokumen DOCX',
                        'order' => 2,
                    ]
                );
                
                $import->refresh();
                
                if ($lesson) {
                    $this->command->info("✅ Lesson created: {$lesson->title}");
                    $this->command->info("🔗 Lesson ID: {$lesson->id}");
                    $this->command->info("📖 Duration: {$lesson->duration_minutes} minutes");
                    
                    // Show sample content (first 800 chars)
                    $content = substr($lesson->content, 0, 800);
                    $this->command->line("\n📖 Content Preview:");
                    $this->command->line(str_repeat('-', 70));
                    $this->command->line($content . '...');
                    $this->command->line(str_repeat('-', 70));
                    
                    // Check for Word artifacts
                    $hasConditionals = preg_match('/<!\[(?:if|endif)\]?/i', $lesson->content);
                    $hasMsoClasses = preg_match('/class="Mso/i', $lesson->content);
                    $hasOfficeTags = preg_match('/<[ovw]:/i', $lesson->content);
                    
                    if ($hasConditionals || $hasMsoClasses || $hasOfficeTags) {
                        $this->command->error('⚠️  Warning: Word artifacts detected in content!');
                        if ($hasConditionals) $this->command->error('   - Conditional comments found');
                        if ($hasMsoClasses) $this->command->error('   - MSO classes found');
                        if ($hasOfficeTags) $this->command->error('   - Office XML tags found');
                    } else {
                        $this->command->info('✅ Content is clean - no Word artifacts detected');
                    }
                } else {
                    $this->command->error('❌ No lesson created');
                }
            } else {
                $this->command->error("❌ Import failed: {$import->error_message}");
            }

        } catch (\Exception $e) {
            $this->command->error('❌ Import failed: ' . $e->getMessage());
            $this->command->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
