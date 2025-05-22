<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'is_enabled',
        'display_order',
        'type',
        'icon',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Scope a query to only include active announcements.
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
            })
            ->orderBy('display_order', 'asc');
    }

    /**
     * Get icon class based on type
     */
    public function getIconClassAttribute()
    {
        if ($this->icon) {
            return $this->icon;
        }

        return match($this->type) {
            'info' => 'bx bx-info-circle',
            'success' => 'bx bx-check-circle',
            'warning' => 'bx bx-error',
            'danger' => 'bx bx-x-circle',
            default => 'bx bx-bell',
        };
    }
}