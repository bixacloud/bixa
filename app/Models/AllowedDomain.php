<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowedDomain extends Model
{
    protected $fillable = ['domain_name'];

    public function setDomainNameAttribute($value)
    {
        // Ensure domain starts with dot and is lowercase
        if (strpos($value, '.') !== 0) {
            $value = '.' . $value;
        }
        $this->attributes['domain_name'] = strtolower($value);
    }
}