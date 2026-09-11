<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        // Menggunakan Auth::user() atau auth()->user()
        $user = Auth::user() ?? User::first();

        return view('pages.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user() ?? User::first();

        if ($user->role === 'admin') {
            return redirect()
                ->route('profile.index')
                ->withErrors(['profile' => 'Admin hanya dapat melihat profil dan tidak dapat mengubahnya.']);
        }

        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'  => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Upload Avatar/Foto Profil
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }
}
