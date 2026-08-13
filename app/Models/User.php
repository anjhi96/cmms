<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * Role constants
     */
    public const ROLE_ADMIN = 'ADMIN';

    public const ROLE_KOORDINATOR_WWD = 'KOORDINATOR WWD';
    public const ROLE_KOORDINATOR_BUL = 'KOORDINATOR BUL';

    public const ROLE_PIC_WWD = 'PIC WWD';
    public const ROLE_PIC_BUL = 'PIC BUL';

    public const ROLE_GUEST = 'GUEST';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Role Checking Methods
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isKoordinator(): bool
    {
        return in_array($this->role, [
            self::ROLE_KOORDINATOR_WWD,
            self::ROLE_KOORDINATOR_BUL,
        ], true);
    }

    public function isPic(): bool
    {
        return in_array($this->role, [
            self::ROLE_PIC_WWD,
            self::ROLE_PIC_BUL,
        ], true);
    }

    public function isKoordinatorWwd(): bool
    {
        return $this->role === self::ROLE_KOORDINATOR_WWD;
    }

    public function isKoordinatorBul(): bool
    {
        return $this->role === self::ROLE_KOORDINATOR_BUL;
    }

    public function isPicWwd(): bool
    {
        return $this->role === self::ROLE_PIC_WWD;
    }

    public function isPicBul(): bool
    {
        return $this->role === self::ROLE_PIC_BUL;
    }

    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    public function hasRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    protected function nameFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => \Illuminate\Support\Str::title(strtolower($this->name))
        );
    }
}
