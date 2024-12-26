<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Core\UserStatus;
use App\Settings\CoreSettings;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'country_code',
        'phone',
        'email',
        'username',
        'password',
        'avatar',
        'status_id',
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

    protected $appends = ['full_name'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Who can access the dashboard
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Get filament user's avatar URL
     *
     * @return string|null
     */
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    /**
     * Get user's avatar URL
     *
     * @return string|null
     */
    public function getAvatarUrlAttribute(): ?string
    {
        // User uploaded avatar
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }

        // Default avatar
        $settingsDefaultAvatarPath = app(CoreSettings::class)->default_avatar;
        if ($settingsDefaultAvatarPath && Storage::disk('public')->exists($settingsDefaultAvatarPath)) {
            return asset('storage/' . $settingsDefaultAvatarPath);
        } else {
            return asset('assets/images/default-avatar.png');
        }
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get full name attribute
     * 
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get display phone attribute
     * 
     * @return string
     */
    public function getDisplayPhoneAttribute(): string
    {
        return trim($this->country_code . '-' . $this->phone, '-');
    }

    /**
     * Scope user with respect to role name
     *
     * @param Builder $query
     * @param string $role
     * @return void
     */
    public function scopeWhereHasRole(Builder $query, string $role)
    {
        $query->whereHas('roles', fn($query) => $query->where('name', $role));
    }

    /**
     * Get status
     *
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(UserStatus::class, 'status_id',);
    }

    /**
     * Scope active users
     *
     * @param Builder $query
     * @return void
     */
    public function scopeWhereActive(Builder $query)
    {
        $query->whereHas('status', fn($query) => $query->where('key', 'active'));
    }

    /**
     * Scope inactive users
     *
     * @param Builder $query
     * @return void
     */
    public function scopeWhereInactive(Builder $query)
    {
        $query->whereHas('status', fn($query) => $query->where('key', 'inactive'));
    }
}
