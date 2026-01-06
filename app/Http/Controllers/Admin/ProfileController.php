<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display user profile
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('admin.profile.index')->with('user', $user);
    }

    /**
     * Update user profile data
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number_type' => ['required', Rule::in(User::NUMBER_TYPES)],
            'number' => ['required', 'numeric', Rule::unique(User::class)->ignore($user->id)],
            'address' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'numeric'],
            'gender' => ['required', Rule::in(User::GENDERS)],
        ]);

        $user->update($validated);

        return redirect()
            ->route('admin.profile.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ]);

        // Check if current password is correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.'
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()
            ->route('admin.profile.index')
            ->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Update user photo
     */
    public function updatePhoto(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // Delete old photo if exists
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        // Store new photo
        $photoPath = $request->file('photo')->store('photos', 'public');

        $user->update([
            'photo' => $photoPath
        ]);

        return redirect()
            ->route('admin.profile.index')
            ->with('success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Delete user photo
     */
    public function deletePhoto()
    {
        $user = Auth::user();

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
            
            $user->update([
                'photo' => null
            ]);

            return redirect()
                ->route('admin.profile.index')
                ->with('success', 'Foto profil berhasil dihapus.');
        }

        return redirect()
            ->route('admin.profile.index')
            ->with('error', 'Tidak ada foto profil untuk dihapus.');
    }
}