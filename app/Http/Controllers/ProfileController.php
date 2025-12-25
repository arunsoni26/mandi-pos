<?php

namespace App\Http\Controllers;

use App\Models\bannerImage;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        $userInfo = Auth::user();
        return view('admin.users.profile', compact('userInfo'));
    }

    // update profile
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->name  = $request->name;
        $user->save();

        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }

    // update password
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('profile')
                ->withErrors($validator)
                ->withInput()
                ->with('active_tab', 'password');
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('profile')
                ->withErrors(['current_password' => 'Current password does not match'])
                ->with(['active_tab' => 'password']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('profile')
            ->with('success', 'Password updated successfully!')
            ->with('active_tab', 'password');
    }
}
