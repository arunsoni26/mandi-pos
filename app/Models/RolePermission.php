<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $fillable = ['role_id', 'module_id', 'can_view', 'can_add', 'can_edit', 'can_delete'];

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function module() {
        return $this->belongsTo(Module::class);
    }
}
