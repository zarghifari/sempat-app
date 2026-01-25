<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleReading extends Model
{
    protected $fillable = [
        'user_id',
        'article_id',
        'time_spent_seconds',
        'last_time_sync',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'last_time_sync' => 'datetime',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
