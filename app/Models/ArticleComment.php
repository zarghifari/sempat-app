<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleComment extends Model
{
    protected $fillable = [
        'article_id',
        'user_id',
        'parent_id',
        'content',
        'sticker_id',
        'is_edited',
        'edited_at',
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
    ];

    /**
     * Get the article that owns the comment.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the user who created the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sticker if this is a sticker comment.
     */
    public function sticker(): BelongsTo
    {
        return $this->belongsTo(Sticker::class);
    }

    /**
     * Get the parent comment if this is a reply.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ArticleComment::class, 'parent_id');
    }

    /**
     * Get all replies to this comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ArticleComment::class, 'parent_id')->with('user', 'sticker');
    }

    /**
     * Check if the comment is owned by the given user.
     */
    public function isOwnedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        
        return $this->user_id === $user->id;
    }

    /**
     * Check if the comment can be deleted by the given user.
     */
    public function canDelete(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        
        // Students can only delete their own comments, admins can delete all
        return $this->isOwnedBy($user) || $user->hasRole('admin');
    }

    /**
     * Check if this comment is a text comment.
     */
    public function isTextComment(): bool
    {
        return !empty($this->content);
    }

    /**
     * Check if this comment is a sticker comment.
     */
    public function isStickerComment(): bool
    {
        return $this->sticker_id !== null;
    }
}
