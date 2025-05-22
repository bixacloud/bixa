<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CloudflareConfig extends Model
{
    protected $fillable = [
        'email',
        'api_key',
        'proxy_domain',
        'is_active'
    ];

    protected $hidden = ['api_key'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}