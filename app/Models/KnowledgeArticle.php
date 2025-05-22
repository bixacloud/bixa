<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgeArticle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'is_featured',
        'is_published',
        'view_count',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the category this article belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeCategory::class, 'category_id');
    }

    /**
     * Get the user (author) who created this article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all ratings for this article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(KnowledgeRating::class, 'article_id');
    }

    /**
     * Get the count of likes for this article.
     *
     * @return int
     */
    public function getLikesCountAttribute(): int
    {
        return $this->ratings()->where('is_helpful', true)->count();
    }

    /**
     * Get the count of dislikes for this article.
     *
     * @return int
     */
    public function getDislikesCountAttribute(): int
    {
        return $this->ratings()->where('is_helpful', false)->count();
    }

    /**
     * Increment the view count for this article.
     *
     * @return void
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Check if user has rated this article.
     *
     * @param int $userId
     * @return \App\Models\KnowledgeRating|null
     */
    public function getUserRating(int $userId)
    {
        return $this->ratings()->where('user_id', $userId)->first();
    }
}