<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Services\DocumentTransformService;
use Illuminate\Support\Str;

class BatchHtmlImportSeeder extends Seeder
{
    protected $transformService;
    
    public function __construct(DocumentTransformService $transformService)
    {
        $this->transformService = $transformService;
    }
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🚀 Starting Batch HTML Document Import...\n\n";
        
        // Get teacher user
        $teacher = User::where('email', 'teacher@sempat.test')->first();
        if (!$teacher) {
            echo "❌ Error: Teacher user not found. Run UserSeeder first.\n";
            return;
        }
        
        // Configuration
        $config = [
            'course' => [
                'title' => 'Komputer Grafis & Desain Visual',
                'slug' => 'komputer-grafis-desain-visual',
                'description' => 'Pelajari konsep dasar hingga lanjutan tentang komputer grafis dan desain visual.',
            ],
            'module' => [
                'title' => 'Pengantar Komputer Grafis',
                'description' => 'Modul pengenalan konsep dasar komputer grafis dan desain visual.',
            ],
            'documents_path' => database_path('seeders/1_computer_grafis'), // Directory containing HTML files
        ];
        
        // Create or get course
        $course = $this->createOrGetCourse($config['course'], $teacher);
        echo "✅ Course: {$course->title}\n";
        
        // Create or get module
        $module = $this->createOrGetModule($config['module'], $course, $teacher);
        echo "✅ Module: {$module->title}\n\n";
        
        // Find all HTML files in directory
        $htmlFiles = $this->findHtmlFiles($config['documents_path']);
        
        if (empty($htmlFiles)) {
            echo "⚠️  No HTML files found in {$config['documents_path']}\n";
            echo "   Please place .htm or .html files in this directory.\n";
            return;
        }
        
        echo "📂 Found " . count($htmlFiles) . " HTML file(s) to import:\n";
        foreach ($htmlFiles as $index => $file) {
            echo "   " . ($index + 1) . ". " . basename($file) . "\n";
        }
        echo "\n";
        
        // Import each HTML file
        $imported = 0;
        $failed = 0;
        
        foreach ($htmlFiles as $index => $htmlPath) {
            echo "📄 Importing [" . ($index + 1) . "/" . count($htmlFiles) . "]: " . basename($htmlPath) . "\n";
            
            try {
                $result = $this->importHtmlDocument($htmlPath, $module, $teacher, $index + 1);
                
                if ($result) {
                    echo "   ✅ Success: {$result['title']}\n";
                    echo "   ⏱️  Reading time: {$result['reading_time']} minutes\n";
                    echo "   🖼️  Images: {$result['images_count']}\n";
                    echo "   📎 Attachments: {$result['attachments_count']}\n";
                    echo "   🔗 Lesson ID: {$result['lesson_id']}\n\n";
                    $imported++;
                } else {
                    echo "   ❌ Failed to import\n\n";
                    $failed++;
                }
            } catch (\Exception $e) {
                echo "   ❌ Error: {$e->getMessage()}\n\n";
                $failed++;
            }
        }
        
        echo "✨ Batch Import Complete!\n";
        echo "   ✅ Imported: {$imported}\n";
        if ($failed > 0) {
            echo "   ❌ Failed: {$failed}\n";
        }
    }
    
    /**
     * Find all HTML files in directory
     */
    protected function findHtmlFiles(string $path): array
    {
        if (!is_dir($path)) {
            return [];
        }
        
        $files = [];
        $items = scandir($path);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $fullPath = $path . DIRECTORY_SEPARATOR . $item;
            
            // Only get .htm and .html files (not directories)
            if (is_file($fullPath) && preg_match('/\.(htm|html)$/i', $item)) {
                $files[] = $fullPath;
            }
        }
        
        // Sort alphabetically
        sort($files);
        
        return $files;
    }
    
    /**
     * Create or get existing course
     */
    protected function createOrGetCourse(array $config, User $teacher): Course
    {
        $course = Course::where('slug', $config['slug'])->first();
        
        if ($course) {
            return $course;
        }
        
        return Course::create([
            'uuid' => Str::uuid(),
            'title' => $config['title'],
            'slug' => $config['slug'],
            'description' => $config['description'],
            'objectives' => 'Memahami konsep dan aplikasi komputer grafis',
            'requirements' => 'Kemampuan dasar penggunaan komputer',
            'target_audience' => 'Siswa SMA/SMK yang tertarik dengan desain grafis',
            'level' => 'beginner',
            'price' => 0,
            'is_free' => true,
            'language' => 'id',
            'duration_hours' => 8,
            'status' => 'published',
            'created_by' => $teacher->id,
        ]);
    }
    
    /**
     * Create or get existing module
     */
    protected function createOrGetModule(array $config, Course $course, User $teacher): Module
    {
        $module = Module::where('course_id', $course->id)
            ->where('title', $config['title'])
            ->first();
        
        if ($module) {
            return $module;
        }
        
        $order = Module::where('course_id', $course->id)->max('order') ?? 0;
        
        return Module::create([
            'course_id' => $course->id,
            'title' => $config['title'],
            'description' => $config['description'],
            'order' => $order + 1,
            'estimated_minutes' => 120,
            'is_locked' => false,
            'status' => 'published',
            'created_by' => $teacher->id,
        ]);
    }
    
    /**
     * Import single HTML document as lesson
     */
    protected function importHtmlDocument(string $htmlPath, Module $module, User $teacher, int $order): ?array
    {
        if (!file_exists($htmlPath)) {
            return null;
        }
        
        // Transform HTML to lesson data
        $lessonData = $this->transformService->transformHtmlToLesson($htmlPath);
        
        // Create lesson
        $lesson = Lesson::create([
            'module_id' => $module->id,
            'title' => $lessonData['title'],
            'type' => 'text',
            'content' => $lessonData['content'],
            'order' => $order,
            'duration_minutes' => $lessonData['reading_time'],
            'is_preview' => false,
            'status' => 'published',
            'attachments' => json_encode($lessonData['attachments'] ?? []),
            'external_links' => json_encode($lessonData['external_links']),
            'created_by' => $teacher->id,
        ]);
        
        return [
            'lesson_id' => $lesson->id,
            'title' => $lesson->title,
            'reading_time' => $lesson->duration_minutes,
            'images_count' => count($lessonData['images']),
            'attachments_count' => count($lessonData['attachments'] ?? []),
        ];
    }
}
