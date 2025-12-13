<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() {
        return view('admin.users.index');
    }

    public function list(Request $request) {
        $query = User::with('role') // Eager load group relation
            ->select('id', 'name', 'email', 'status', 'role_id')
            ->where('role_id', 2);

        // Filters
        if ($request->role_id) {
            $query->where('group_id', $request->group_id);
        }
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }
        // if ($request->code) {
        //     $query->where('code', 'LIKE', "%{$request->code}%");
        // }

        $users = $query->get();

        // Format Data
        $data = $users->map(function ($row) {
            return [
                'name' => $row->name,
                'email' => $row->email,
                'role' => $row->role->name ?? '-',
                
                'status_toggle' => '
                    <div class="form-check form-switch">
                        <input 
                            type="checkbox"
                            class="form-check-input toggle-status"
                            data-id="'.$row->id.'" '.($row->status ? 'checked' : '').'>
                    </div>
                ',

                'actions' => view('admin.users.partials.actions', compact('row'))->render()
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function form(Request $request) {
        $user = $request->userId ? User::findOrFail($request->userId) : null;
        return view('admin.users.add-edit-form', compact('user'));
    }

    public function save(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . ($request->id ?? 'NULL'),
            'password' => $request->id ? 'nullable|min:6' : 'required|min:6',
        ]);

        $user = $request->id ? User::findOrFail($request->id) : new User();

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->role_id = $request->role_id ?? 2;
        $user->status = $request->status ?? 1;
        $user->save();

        return response()->json(['success' => true]);
    }

    public function toggleStatus($id) {
        $user = User::findOrFail($id);
        $user->status = !$user->status;
        $user->save();
        return response()->json(['success' => true]);
    }

    public function destroy($id) {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['success' => true]);
    }
}
