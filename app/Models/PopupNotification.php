<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PopupNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'is_enabled',
        'type',
        'allow_dismiss',
        'show_once',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'allow_dismiss' => 'boolean',
        'show_once' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * The users who have dismissed this popup notification.
     */
    public function dismissedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'popup_notification_users')
            ->withPivot('dismissed_at')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active popup notifications.
     */
    public function scopeActive($query)
    {
        return $query->where('is_enabled', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Check if a user has dismissed this notification.
     */
    public function isDismissedByUser($userId)
    {
        return $this->dismissedByUsers()->where('user_id', $userId)->exists();
    }
}