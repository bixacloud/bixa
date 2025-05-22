<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpSetting extends Model
{
    protected $fillable = [
        'type',
        'hostname', 
        'username',
        'password',
        'from_email',
        'from_name',
        'port',
        'encryption',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];
}