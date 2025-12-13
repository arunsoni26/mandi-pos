<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['name', 'slug'];

    public function rolePermissions() {
        return $this->hasMany(RolePermission::class);
    }

    public function userPermissions() {
        return $this->hasMany(UserPermission::class);
    }
}
