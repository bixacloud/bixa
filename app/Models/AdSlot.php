<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'page',
        'type',
        'selector',
        'position',
        'description',
        'image',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get advertisements in this slot
     */
    public function advertisements()
    {
        return $this->hasMany(Advertisement::class, 'slot_position', 'code');
    }
    
    /**
     * Check if this slot is dynamic
     */
    public function isDynamic()
    {
        return $this->type === 'dynamic';
    }
    
    /**
     * Check if this slot is predefined
     */
    public function isPredefined()
    {
        return $this->type === 'predefined';
    }
    
    /**
     * Scope for active slots
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}