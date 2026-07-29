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
    use HasFactory, Notifiable;

    /**
     * Role constants
     */
    public const ROLE_ADMIN = 'ADMIN';
    public const ROLE_KOORDINATOR = 'KOORDINATOR';
    public const ROLE_PIC = 'PIC';

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
        return $this->role === self::ROLE_KOORDINATOR;
    }

    public function isPic(): bool
    {
        return $this->role === self::ROLE_PIC;
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