<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Redirect Admin/Pustakawan ke dashboard mereka jika akses /profile
        // Mereka harusnya akses /admin/profile atau /pustakawan/profile
        if (in_array($user->role, ['Admin', 'Pustakawan'])) {
            // Check if current route is the prefixed one
            $currentRoute = request()->route()->getName();
            
            // If accessing /profile (not /admin/profile or /pustakawan/profile)
            if ($currentRoute === 'profile.index') {
                return redirect(dashboard_route());
            }
        }
        
        return view('profile', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'numeric'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return back()->with('success', 'Password berhasil diperbarui!');
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $user = Auth::user();

        // Delete old photo if exists
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        // Store new photo
        $photoPath = $request->file('photo')->store('profile-photos', 'public');

        $user->update([
            'photo' => $photoPath
        ]);

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Delete the user's profile photo.
     */
    public function deletePhoto()
    {
        $user = Auth::user();

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
            $user->update(['photo' => null]);
        }

        return back()->with('success', 'Foto profil berhasil dihapus!');
    }
}