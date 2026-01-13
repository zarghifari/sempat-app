<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleLike extends Model
{
    protected $fillable = [
        'article_id',
        'user_id',
    ];

    /**
     * Get the article that was liked.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the user who liked the article.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
