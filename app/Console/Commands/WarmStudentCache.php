<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\StudentProgressCacheService;
use Illuminate\Console\Command;

class WarmStudentCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:warm-students 
                            {--all : Warm cache for all students}
                            {--active : Only active students (default)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pre-warm Redis cache for student progress data';

    protected StudentProgressCacheService $cacheService;

    public function __construct(StudentProgressCacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔥 Starting cache warming for student progress...');
        
        // Get students based on option
        $query = User::whereHas('roles', fn($q) => $q->where('slug', 'student'));
        
        if ($this->option('all')) {
            $this->info('📊 Mode: All students');
        } else {
            $this->info('📊 Mode: Active students only');
            $query->where('is_active', true);
        }
        
        $students = $query->pluck('id');
        $total = $students->count();
        
        if ($total === 0) {
            $this->warn('⚠️  No students found.');
            return 0;
        }
        
        $this->info("👥 Found {$total} students to cache");
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        $successful = 0;
        $failed = 0;
        
        // Batch processing (50 students at a time)
        $chunks = $students->chunk(50);
        
        foreach ($chunks as $chunk) {
            try {
                // Batch cache today's progress
                $this->cacheService->getBatchTodayProgress($chunk->toArray());
                
                // Cache week summary for each student
                foreach ($chunk as $userId) {
                    try {
                        $this->cacheService->getWeekSummary($userId);
                        $successful++;
                        $bar->advance();
                    } catch (\Exception $e) {
                        $failed++;
                        $bar->advance();
                        $this->newLine();
                        $this->error("Failed for user {$userId}: " . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                $failed += $chunk->count();
                $bar->advance($chunk->count());
                $this->newLine();
                $this->error('Batch failed: ' . $e->getMessage());
            }
        }
        
        $bar->finish();
        $this->newLine(2);
        
        // Summary
        $this->info('✅ Cache warming completed!');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Students', $total],
                ['Successfully Cached', $successful],
                ['Failed', $failed],
                ['Success Rate', round(($successful / $total) * 100, 1) . '%'],
            ]
        );
        
        $this->info('💡 Tip: Schedule this command to run every morning at 6 AM');
        $this->line('   Add to app/Console/Kernel.php:');
        $this->line('   $schedule->command(\'cache:warm-students --active\')->dailyAt(\'06:00\');');
        
        return 0;
    }
}
