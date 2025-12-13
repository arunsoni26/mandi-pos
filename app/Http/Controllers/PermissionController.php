<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role.superadmin']);
    }

    public function index()
    {
        $permissions = Permission::all();
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'module' => 'required|unique:permissions,module',
        ]);

        Permission::create([
            'module' => $request->module,
        ]);

        return redirect()->route('permissions.index')->with('success', 'Module added successfully');
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'module' => 'required|unique:permissions,module,' . $permission->id,
        ]);

        $permission->update([
            'module' => $request->module,
        ]);

        return redirect()->route('permissions.index')->with('success', 'Module updated successfully');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Module deleted successfully');
    }
}
