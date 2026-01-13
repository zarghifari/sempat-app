<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Sticker extends Model
{
    protected $fillable = [
        'name',
        'code',
        'image_url',
        'category',
        'usage_count',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'usage_count' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get all comments using this sticker.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ArticleComment::class);
    }

    /**
     * Scope to get only active stickers.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get stickers by category.
     */
    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to order by custom order field.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /**
     * Increment usage count when sticker is used.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Get the full URL for the sticker image.
     */
    public function getImageUrlAttribute($value): string
    {
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        
        return asset('storage/' . $value);
    }
}
