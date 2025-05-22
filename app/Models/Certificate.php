<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'domain',
        'type',
        'status',
        'order_id',
        'dns_validation',
        'private_key',
		'csr',
        'certificate',
        'ca_certificate',
        'valid_until',
        'revoked_at',
    ];

    protected $casts = [
        'dns_validation' => 'array',
        'valid_until' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpiringSoon()
    {
        if (!$this->valid_until) {
            return false;
        }

        // Consider "soon" as within 14 days
        return $this->valid_until->diffInDays(now()) <= 14;
    }
}