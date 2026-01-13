<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'category_id',
        'created_by',
        'title',
        'slug',
        'excerpt',
        'content',
        'thumbnail',
        'attachments',
        'external_links',
        'status',
        'difficulty_level',
        'reading_time_minutes',
        'language',
        'is_featured',
        'allow_comments',
        'views_count',
        'likes_count',
        'bookmarks_count',
        'comments_count',
        'rating_average',
        'rating_count',
        'published_at',
        'updated_by',
    ];

    protected $casts = [
        'attachments' => 'array',
        'external_links' => 'array',
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
        'rating_average' => 'decimal:2',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($article) {
            if (empty($article->uuid)) {
                $article->uuid = Str::uuid();
            }
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(ArticleBookmark::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(LearningJournal::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ArticleComment::class)
            ->whereNull('parent_id')
            ->with('user', 'sticker', 'replies')
            ->latest();
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(ArticleComment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ArticleLike::class);
    }

    // Helper Methods
    public function isLikedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function toggleLike(User $user): bool
    {
        $like = $this->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $this->decrement('likes_count');
            return false;
        }

        $this->likes()->create(['user_id' => $user->id]);
        $this->increment('likes_count');
        return true;
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeLevel($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }
}
