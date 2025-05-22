<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'icon',
        'image',
        'type',
        'action_text',
        'action_url',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that the notification belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead()
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Get formatted time ago.
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the appropriate icon class based on notification type.
     */
    public function getIconClassAttribute()
    {
        if ($this->icon) {
            return $this->icon;
        }

        return match($this->type) {
            'login' => 'bx bx-log-in',
            'hosting' => 'bx bx-server',
            'ticket' => 'bx bx-support',
            'ssl' => 'bx bx-lock',
            'account' => 'bx bx-user',
            default => 'bx bx-bell',
        };
    }

    /**
     * Get color class based on notification type.
     */
    public function getColorClassAttribute()
    {
        return match($this->type) {
            'login' => 'bg-primary',
            'hosting' => 'bg-success',
            'ticket' => 'bg-info',
            'ssl' => 'bg-warning',
            'account' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}