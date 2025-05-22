<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use App\Helpers\GravatarHelper;
use App\Models\StaffRating;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, AuthenticationLoggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'social_id',
        'social_type',
        'role',
        'signature',
        'locale',
        'locale_auto_detected',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'locale_auto_detected' => 'boolean',
    ];

    /**
     * Set user's locale preference
     *
     * @param string $locale
     * @param bool $autoDetected Whether the locale was automatically detected
     * @return void
     */
    public function setLocale(string $locale, bool $autoDetected = false): void
    {
        $this->locale = $locale;
        $this->locale_auto_detected = $autoDetected;
        $this->save();
    }

    /**
     * Get user's preferred locale or app default
     * 
     * @return string
     */
    public function getPreferredLocale(): string
    {
        return $this->locale ?? config('app.locale');
    }

    /**
     * Check if user's locale was automatically detected
     * 
     * @return bool
     */
    public function isLocaleAutoDetected(): bool
    {
        return (bool) $this->locale_auto_detected;
    }

    /**
     * Check if user is admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has specific role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
    
    /**
     * Get user's Gravatar URL
     *
     * @param int $size
     * @return string
     */
    public function getGravatarUrl($size = 80)
    {
        return GravatarHelper::url($this->email, $size);
    }

    /**
     * Check if user is support staff
     *
     * @return bool
     */
    public function isSupport(): bool
    {
        return $this->role === 'support';
    }

    /**
     * Check if user has admin access (either admin or support)
     *
     * @return bool
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['admin', 'support']);
    }

    /**
     * Check if user can manage tickets
     *
     * @return bool
     */
    public function canManageTickets(): bool
    {
        return in_array($this->role, ['admin', 'support']);
    }

    /**
     * Check if user can manage hosting
     *
     * @return bool
     */
    public function canManageHosting(): bool
    {
        return in_array($this->role, ['admin', 'support']);
    }

    /**
     * Get ratings given by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function givenRatings()
    {
        return $this->hasMany(StaffRating::class, 'user_id');
    }

    /**
     * Get ratings received by this admin.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedRatings()
    {
        return $this->hasMany(StaffRating::class, 'admin_id');
    }
}