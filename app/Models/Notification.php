<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type',
        'title_key',
        'content_key',
        'title_params',
        'content_params',
        'icon_class',
        'color_class',
        'image',
        'action_text_key',
        'action_url',
        'meta_data',
        'is_read',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'title_params' => 'array',
        'content_params' => 'array',
        'meta_data' => 'array',
    ];

    /**
     * Get the user that the notification belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark the notification as read.
     *
     * @return bool
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
     *
     * @return string
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get localized title.
     * This method translates the title key into the current application language.
     *
     * @return string
     */
    public function getLocalizedTitleAttribute()
    {
        // Make sure the parameters are always an array
        $params = is_array($this->title_params) ? $this->title_params : [];
        
        // Use the __() helper to translate the key with parameters
        return __($this->title_key, $params);
    }

    /**
     * Get localized content.
     * This method translates the content key into the current application language.
     *
     * @return string
     */
    public function getLocalizedContentAttribute()
    {
        // Make sure the parameters are always an array
        $params = is_array($this->content_params) ? $this->content_params : [];
        
        // Use the __() helper to translate the key with parameters
        return __($this->content_key, $params);
    }

    /**
     * Get localized action text.
     * This method translates the action text key into the current application language.
     *
     * @return string|null
     */
    public function getLocalizedActionTextAttribute()
    {
        return $this->action_text_key ? __($this->action_text_key) : null;
    }

    /**
     * Get the appropriate icon class based on notification type.
     *
     * @param string|null $value
     * @return string
     */
    public function getIconClassAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return match($this->type) {
            'login' => 'bx bx-log-in',
            'hosting' => 'bx bx-server',
            'ticket' => 'bx bx-support',
            'ssl' => 'bx bx-lock-alt',
            'account' => 'bx bx-user',
            default => 'bx bx-bell',
        };
    }

    /**
     * Get color class based on notification type.
     *
     * @param string|null $value
     * @return string
     */
    public function getColorClassAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return match($this->type) {
            'login' => 'bg-primary',
            'hosting' => 'bg-success',
            'ticket' => 'bg-info',
            'ssl' => 'bg-warning',
            'account' => 'bg-secondary',
            default => 'bg-primary',
        };
    }

    /**
     * Format the notification for API responses.
     *
     * @param bool $withTranslations Include translated strings
     * @return array
     */
    public function formatForApi($withTranslations = true)
    {
        $formatted = [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title_key,
            'content' => $this->content_key,
            'icon_class' => $this->icon_class,
            'color_class' => $this->color_class,
            'image' => $this->image,
            'action_text' => $this->action_text_key,
            'action_url' => $this->action_url,
            'is_read' => (bool)$this->is_read,
            'time_ago' => $this->time_ago,
            'created_at' => $this->created_at->toISOString(),
        ];

        // Add translated versions if requested
        if ($withTranslations) {
            $formatted['localized_title'] = $this->localized_title;
            $formatted['localized_content'] = $this->localized_content;
            $formatted['localized_action_text'] = $this->localized_action_text;
        }

        return $formatted;
    }
    
    /**
     * Scope a query to only include notifications older than given days.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOlderThan($query, $days = 30)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }
    
    /**
     * Delete old notifications.
     * This is designed to run during normal application traffic, not via cron.
     *
     * @param int $days Days to keep notifications
     * @param int $batchSize Batch size for chunking
     * @return int Number of deleted notifications
     */
    public static function deleteOldNotifications($days = 30, $batchSize = 100)
    {
        try {
            // We use chunking to avoid memory issues with large datasets
            $count = 0;
            static::olderThan($days)->chunkById($batchSize, function ($notifications) use (&$count) {
                foreach ($notifications as $notification) {
                    $notification->delete();
                    $count++;
                }
            });
            
            return $count;
        } catch (\Exception $e) {
            \Log::error('Failed to delete old notifications: ' . $e->getMessage(), [
                'days' => $days,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 0;
        }
    }
}