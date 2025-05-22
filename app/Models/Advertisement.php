<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'html_content',
        'is_active',
        'slot_position',
        'clicks',
        'impressions'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'clicks' => 'integer',
        'impressions' => 'integer',
    ];

    /**
     * Get the slot associated with this advertisement
     */
    public function slot()
    {
        return $this->belongsTo(AdSlot::class, 'slot_position', 'code');
    }
    
    /**
     * Record an impression
     */
    public function recordImpression()
    {
        $this->increment('impressions');
        return $this;
    }
    
    /**
     * Record a click
     */
    public function recordClick()
    {
        $this->increment('clicks');
        return $this;
    }
}