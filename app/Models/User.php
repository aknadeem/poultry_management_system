<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * Never add 'is_admin', 'user_role_id', or similar privilege columns here
     * unless that operation is explicitly intended.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_role_id',
        'picture',
        'cnic_no',
        'contact_no',
        'address',
        'is_active',
        'addedby',
        'updatedby',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userRole()
    {
        return $this->belongsTo(\App\Models\UserRole::class, 'user_role_id', 'id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->userRole?->slug === 'super-admin';
    }

    public function isAdmin(): bool
    {
        return $this->userRole?->slug === 'admin';
    }

    public function isHod(): bool
    {
        return $this->userRole?->slug === 'hod';
    }

    /**
     * @param string|list<string> $roles
     */
    public function hasRole(string|array $roles): bool
    {
        $roleSlug = $this->userRole?->slug;
        if (! $roleSlug) {
            return false;
        }

        if (is_array($roles)) {
            return in_array($roleSlug, $roles, true);
        }

        return $roleSlug === $roles;
    }
}
