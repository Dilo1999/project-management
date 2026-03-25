<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'role',
        'approved_at',
    ];

    public const ROLE_NORMAL = 'normal';
    public const ROLE_DEVELOPER = 'developer';
    public const ROLE_DESIGNER = 'designer';
    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLES = [
        self::ROLE_NORMAL,
        self::ROLE_DEVELOPER,
        self::ROLE_DESIGNER,
        self::ROLE_SUPER_ADMIN,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'approved_at' => 'datetime',
    ];

    /**
     * Check if the user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isNormal(): bool
    {
        return $this->role === self::ROLE_NORMAL;
    }

    public static function isAssignableRole(string $role): bool
    {
        return in_array($role, [self::ROLE_NORMAL, self::ROLE_DEVELOPER, self::ROLE_DESIGNER], true);
    }

    /**
     * Check if the user's account has been approved by a super admin.
     */
    public function isApproved(): bool
    {
        return $this->approved_at !== null;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
