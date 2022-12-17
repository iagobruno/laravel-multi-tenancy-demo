<?php

namespace App\Models;

use Filament\Models\Contracts\{FilamentUser, HasAvatar, HasName};
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\{HasMany};
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasName
{
    use CentralConnection;
    use HasFactory;
    use HasApiTokens;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class);
    }

    public function password(): Attribute
    {
        return Attribute::set(
            fn ($value) => Hash::make($value)
        );
    }

    public function ownsCurrentTenant(): bool
    {
        return tenant()->checkBelongsTo($this);
    }


    /**
     * Filament methods
     */
    public function canAccessFilament(): bool
    {
        return $this->ownsCurrentTenant();
    }

    public function getFilamentName(): string
    {
        return $this->email;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }
}
