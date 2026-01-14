<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\User;
use App\Services\DocumentTransformService;
use Illuminate\Support\Facades\DB;

class HtmlDocumentImportSeeder extends Seeder
{
    protected DocumentTransformService $documentService;

    public function __construct()
    {
        $this->documentService = new DocumentTransformService();
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting HTML Document Import...');

        // Get teacher user
        $teacher = User::where('email', 'teacher@sempat.test')->first();
        if (!$teacher) {
            $this->command->error('Teacher user not found!');
            return;
        }

        // Create or get Computer Graphics course
        $course = $this->createComputerGraphicsCourse($teacher);
        $this->command->info("✅ Course: {$course->title}");

        // Create module
        $module = $this->createModule($course, $teacher);
        $this->command->info("✅ Module: {$module->title}");

        // Import HTML document as lesson
        $this->importHtmlDocument($module, $teacher);

        $this->command->info('✨ HTML Document Import Complete!');
    }

    /**
     * Create Computer Graphics course
     */
    protected function createComputerGraphicsCourse(User $teacher): Course
    {
        return Course::firstOrCreate(
            [
                'title' => 'Komputer Grafis & Desain Visual',
                'created_by' => $teacher->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'slug' => 'komputer-grafis-desain-visual',
                'description' => 'Pelajari konsep fundamental komputer grafis, dari raster & vektor hingga transformasi 3D. Cocok untuk pemula yang ingin memahami dasar-dasar pemrograman grafis.',
                'objectives' => json_encode([
                    'Memahami perbedaan grafis raster dan vektor',
                    'Menguasai sistem koordinat dan transformasi 2D/3D',
                    'Memahami konsep rendering dan pipeline grafis',
                    'Dapat mengaplikasikan konsep dalam project nyata',
                ]),
                'prerequisites' => json_encode([
                    'Pemahaman dasar matematika (aljabar, geometri)',
                    'Dasar pemrograman (opsional tapi direkomendasikan)',
                ]),
                'level' => 'beginner',
                'language' => 'id',
                'estimated_hours' => 8,
                'is_free' => true,
                'status' => 'published',
                'published_at' => now(),
                'thumbnail' => 'courses/thumbnails/computer-graphics.jpg',
            ]
        );
    }

    /**
     * Create module
     */
    protected function createModule(Course $course, User $teacher): Module
    {
        return Module::firstOrCreate(
            [
                'course_id' => $course->id,
                'title' => 'Pengantar Komputer Grafis',
                'order' => 1,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'description' => 'Modul pengenalan komputer grafis mencakup definisi, tipe data gambar (raster vs vektor), sistem koordinat, dan konsep dasar rendering.',
                'order' => 1,
                'estimated_minutes' => 120,
                'created_by' => $teacher->id,
                'status' => 'published',
            ]
        );
    }

    /**
     * Import HTML document as lesson
     */
    protected function importHtmlDocument(Module $module, User $teacher): void
    {
        $this->command->info('📄 Importing HTML document...');

        // Path to HTML file
        $htmlPath = database_path('seeders/1_computer_grafis/1_computer_grafis.htm');
        
        if (!file_exists($htmlPath)) {
            $this->command->error("HTML file not found: {$htmlPath}");
            return;
        }

        try {
            // Transform HTML to lesson content
            $transformed = $this->documentService->transformHtmlToLesson($htmlPath);
            
            $this->command->info("📝 Title: {$transformed['title']}");
            $this->command->info("⏱️  Reading time: {$transformed['reading_time']} minutes");
            $this->command->info("🖼️  Images found: " . count($transformed['images']));
            $this->command->info("🔗 External links: " . count($transformed['external_links']));

            // Copy attachments
            $attachmentsPath = database_path('seeders/1_computer_grafis/1_computer_grafis_files');
            $attachments = [];
            if (is_dir($attachmentsPath)) {
                $attachments = $this->documentService->copyAttachments($attachmentsPath);
                $this->command->info("📎 Attachments copied: " . count($attachments));
            }

            // Create lesson
            $lesson = Lesson::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'module_id' => $module->id,
                'title' => $transformed['title'],
                'type' => 'text',
                'content' => $transformed['content'],
                'order' => 1,
                'duration_minutes' => $transformed['reading_time'],
                'is_preview' => true, // Allow preview without enrollment
                'attachments' => $attachments,
                'external_links' => $transformed['external_links'],
                'created_by' => $teacher->id,
                'status' => 'published',
            ]);

            $this->command->info("✅ Lesson created: {$lesson->title}");
            $this->command->info("🔗 Lesson ID: {$lesson->id}");

            // Update module estimated time
            $module->update([
                'estimated_minutes' => $module->lessons()->sum('duration_minutes'),
            ]);

        } catch (\Exception $e) {
            $this->command->error("Error importing HTML: {$e->getMessage()}");
            $this->command->error($e->getTraceAsString());
        }
    }
}
