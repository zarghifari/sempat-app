 <?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DocumentImport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'original_filename',
        'file_path',
        'file_type',
        'file_size',
        'status',
        'started_at',
        'completed_at',
        'processing_time_seconds',
        'html_content',
        'extracted_images',
        'metadata',
        'word_count',
        'image_count',
        'page_count',
        'error_message',
        'error_details',
        'lesson_id',
    ];

    protected $casts = [
        'extracted_images' => 'array',
        'metadata' => 'array',
        'error_details' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'file_size' => 'integer',
        'word_count' => 'integer',
        'image_count' => 'integer',
        'page_count' => 'integer',
        'processing_time_seconds' => 'integer',
    ];

    /**
     * Boot function to auto-generate UUID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Helper Methods
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function markAsProcessing(): void
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);
    }

    public function markAsCompleted(array $data = []): void
    {
        $processingTime = $this->started_at 
            ? now()->diffInSeconds($this->started_at) 
            : null;

        $this->update(array_merge([
            'status' => 'completed',
            'completed_at' => now(),
            'processing_time_seconds' => $processingTime,
        ], $data));
    }

    public function markAsFailed(string $message, array $details = []): void
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'error_message' => $message,
            'error_details' => $details,
        ]);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get processing duration in human readable format
     */
    public function getProcessingDurationAttribute(): ?string
    {
        if (!$this->processing_time_seconds) {
            return null;
        }

        $seconds = $this->processing_time_seconds;
        
        if ($seconds < 60) {
            return $seconds . ' seconds';
        }
        
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        
        return $minutes . 'm ' . $remainingSeconds . 's';
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'gray',
            'processing' => 'blue',
            'completed' => 'green',
            'failed' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status icon
     */
    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'pending' => '⏳',
            'processing' => '⚙️',
            'completed' => '✅',
            'failed' => '❌',
            default => '📄',
        };
    }
}
