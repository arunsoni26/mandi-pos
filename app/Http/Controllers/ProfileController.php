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
        $news = News::withTrashed()->where('user_id', Auth::id())->orderBy('id', 'desc')->get();
        $gallery = bannerImage::where('user_id', $userInfo->id)->latest()->get();
        $trash = bannerImage::onlyTrashed()->where('user_id', $userInfo->id)->latest()->get();
        return view('admin.users.profile', compact('userInfo', 'news', 'gallery', 'trash'));
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

    // News add
    public function addNews(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description.*' => 'nullable|string',
            'color.*' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->route('profile')
                ->withErrors($validator)
                ->withInput()
                ->with('active_tab', 'news'); // ✅ Keep news tab on error
        }

        $user = Auth::user();

        foreach ($request->description as $index => $desc) {
            if (!empty(trim($desc))) {
                News::create([
                    'description' => $desc,
                    'color' => $request->color[$index] ?? '#000000',
                    'user_id' => $user->id,
                    'updated_by' => $user->id
                ]);
            }
        }

        return redirect()->route('admin.profile')
            ->with('success', 'News created successfully!')
            ->with('active_tab', 'news');
    }

    // News delete
    public function destroy($id)
    {
        $newsItem = News::where('user_id', Auth::id())->findOrFail($id);
        $newsItem->delete();

        return back()->with('success', 'News deleted successfully')->with('active_tab', 'news');
    }

    // News restore
    public function restore($id)
    {
        News::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id)->restore();
        return back()->with('success', 'News restored successfully')->with('active_tab', 'news');
    }

    // News update
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'description.*' => 'nullable|string',
            'color.*' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->route('profile')
                ->withErrors($validator)
                ->withInput()
                ->with('active_tab', 'news');
        }

        $newsItem = News::where('user_id', Auth::id())->findOrFail($id);
        $newsItem->update([
            'description' => $request->description,
            'color' => $request->color,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'News updated successfully')->with('active_tab', 'news');
    }

    //Banner add
    public function addBanner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'bannerdescription' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->route('profile')
                ->withErrors($validator)
                ->withInput()
                ->with('active_tab', 'gallery');
        }

        $imagePath = $request->file('image')->store('banners', 'public');

        bannerImage::create([
            'user_id'     => Auth::id(),
            'updated_by'  => Auth::id(),
            'image'       => $imagePath,
            'description' => $request->bannerdescription,
        ]);

        return back()->with('success', 'Banner added successfully')->with('active_tab', 'gallery');
    }

    //Banner delete
    public function deleteBanner($id)
    {
        bannerImage::findOrFail($id)->delete();
        return back()->with(['success' => 'Image moved to trash', 'active_tab' => 'gallery']);
    }

    //Banner restore
    public function restoreBanner($id)
    {
        bannerImage::withTrashed()->findOrFail($id)->restore();
        return back()->with(['success' => 'Image restored successfully', 'active_tab' => 'gallery']);
    }
}
