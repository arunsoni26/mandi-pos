<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\RolePermission;
use DB;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role.superadmin']);
    }

    public function index()
    {
        // Exclude super admin from editing
        $roles = Role::where('name', '!=', 'super admin')->get();

        return view('admin.role-permissions.index', compact('roles'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array'
        ]);

        $roleId = $request->input('role_id');
        $permissions = $request->input('permissions', []);

        try {
            DB::transaction(function () use ($roleId, $permissions) {
                // remove old permissions
                RolePermission::where('role_id', $roleId)->delete();

                $now = now();
                $inserts = [];

                foreach ($permissions as $moduleId => $actions) {
                    $inserts[] = [
                        'role_id'      => $roleId,
                        'module_id'    => (int)$moduleId,
                        'can_view_nav' => isset($actions['can_view_nav']) ? 1 : 0,
                        'can_access'   => isset($actions['can_access']) ? 1 : 0,
                        'can_add'      => isset($actions['can_add']) ? 1 : 0,
                        'can_view'     => isset($actions['can_view']) ? 1 : 0,
                        'can_edit'     => isset($actions['can_edit']) ? 1 : 0,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ];
                }

                if (!empty($inserts)) {
                    DB::table('role_permissions')->insert($inserts);
                }
            });

            return response()->json(['code' => 200, 'msg' => 'Permissions updated successfully.']);
        } catch (\Throwable $e) {
            // log($e->getMessage());
            dd($e->getMessage());
            return response()->json(['code' => 500, 'msg' => 'Failed to update permissions.'], 500);
        }
    }

    public function addModule(Request $request)
    {
        $request->validate([
            'module' => 'required|unique:permissions,module',
        ]);

        Permission::create([
            'module' => $request->module,
        ]);

        return redirect()->route('admin.role-permissions')
                         ->with('success', 'Module added successfully');
    }

    public function rolePermissionForm(Request $request)
    {
        $role = Role::findOrFail($request->roleId);
        $modules = Module::all();
        $rolePermissions = RolePermission::where('role_id', $role->id)->get()->keyBy('module_id');

        $html = view('admin.role-permissions.permissions', [
            'role' => $role,
            'modules' => $modules,
            'rolePermissions' => $rolePermissions,
        ])->render();

        return $html;
    }
    
    public function getPermissions($roleId)
    {
        $role = Role::findOrFail($roleId);
        $modules = Permission::all();

        return response()->json([
            'role' => $role,
            'modules' => $modules,
            'rolePermissions' => $role->permissions,
        ]);
    }
}
