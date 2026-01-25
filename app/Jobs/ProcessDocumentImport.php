<?php

namespace App\Jobs;

use App\Models\DocumentImport;
use App\Services\DocumentImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDocumentImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300; // 5 minutes

    /**
     * The document import instance.
     *
     * @var \App\Models\DocumentImport
     */
    protected $documentImport;

    /**
     * Create a new job instance.
     */
    public function __construct(DocumentImport $documentImport)
    {
        $this->documentImport = $documentImport;
    }

    /**
     * Execute the job.
     */
    public function handle(DocumentImportService $service): void
    {
        Log::info('Processing document import', [
            'id' => $this->documentImport->id,
            'filename' => $this->documentImport->original_filename,
        ]);

        try {
            $service->processImport($this->documentImport);
            
            Log::info('Document import completed successfully', [
                'id' => $this->documentImport->id,
                'word_count' => $this->documentImport->word_count,
                'image_count' => $this->documentImport->image_count,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Document import failed', [
                'id' => $this->documentImport->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e; // Re-throw to trigger job retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Document import job failed permanently', [
            'id' => $this->documentImport->id,
            'error' => $exception->getMessage(),
        ]);

        // Mark the import as failed if not already
        if (!$this->documentImport->isFailed()) {
            $this->documentImport->markAsFailed(
                'Job failed after ' . $this->tries . ' attempts: ' . $exception->getMessage(),
                [
                    'exception' => get_class($exception),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ]
            );
        }
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [30, 60, 120]; // Retry after 30s, 60s, then 120s
    }
}
