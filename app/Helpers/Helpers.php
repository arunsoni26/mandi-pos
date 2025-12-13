<?php

use App\Models\Module;
use App\Models\RolePermission;
use App\Models\UserPermission;
use Illuminate\Support\Str;

if (!function_exists('canDo')) {
    function canDo(string $moduleSlug, string $ability, $user = null): bool
    {
        $user = $user ?: auth()->user();
        if (!$user) return false;

        $abilities = ['can_view_nav','can_access','can_add','can_view','can_edit'];
        if (!in_array($ability, $abilities, true)) {
            return false;
        }

        // Superadmin full access
        if (isset($user->role->slug) && Str::lower($user->role->slug) === 'superadmin') {
            return true;
        }

        $module = Module::where('slug', $moduleSlug)->first();
        if (!$module) return false;

        // 1. User-specific permissions
        $uPerm = UserPermission::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->first();
        if ($uPerm && !is_null($uPerm->{$ability})) {
            return (bool) $uPerm->{$ability};
        }

        // 2. Role-based permissions
        $roleId = $user->role_id ?? null;
        if (!$roleId) return false;

        $rPerm = RolePermission::where('role_id', $roleId)
            ->where('module_id', $module->id)
            ->first();

        return $rPerm ? (bool) $rPerm->{$ability} : false;
    }
}
