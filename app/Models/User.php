<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function userPermissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    /**
     * Check if user has permission for a module
     */
    public function hasPermission($moduleSlug, $action, $recordOwnerId = null)
    {
        // SUPERADMIN: all permissions
        if ($this->role->slug === 'superadmin') {
            return true;
        }

        // CUSTOMER: can only view/edit their own records
        if ($this->role->slug === 'customer') {
            if ($recordOwnerId && $this->id !== $recordOwnerId) {
                return false; // trying to access someone else's record
            }

            // Customer permissions are usually fixed, but if you want DB-based:
            $perm = $this->userPermissions()->whereHas('module', function($q) use ($moduleSlug) {
                $q->where('slug', $moduleSlug);
            })->first();

            return $perm && $perm->{$action};
        }

        // ADMIN: check user-specific permissions first
        $userPerm = $this->userPermissions()->whereHas('module', function($q) use ($moduleSlug) {
            $q->where('slug', $moduleSlug);
        })->first();

        if ($userPerm) {
            return $userPerm->{$action};
        }

        // fallback: role permissions
        $rolePerm = RolePermission::where('role_id', $this->role_id)
            ->whereHas('module', function($q) use ($moduleSlug) {
                $q->where('slug', $moduleSlug);
            })
            ->first();

        return $rolePerm && $rolePerm->{$action};
    }

    public function canDo($moduleSlug, $action, $recordOwnerId = null)
    {
        return $this->hasPermission($moduleSlug, $action, $recordOwnerId);
    }
}
