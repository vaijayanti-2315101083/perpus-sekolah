<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number_type' => ['required', Rule::in(User::NUMBER_TYPES)],
            'number' => ['required', 'numeric', 'unique:' . User::class],
            'address' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'numeric'],
            'gender' => ['required', Rule::in(['Man', 'Woman'])],
            'password' => ['required', 'string', 'confirmed', 'max:255'],
        ]);

        $credentials['role'] = 'Member';
        $credentials['password'] = Hash::make($credentials['password']);

        $user = User::create($credentials);

        Auth::login($user);

        // Log registration
        ActivityLog::log('create', "User baru mendaftar: {$user->name} ({$user->number_type}: {$user->number})", $user);

        return redirect()->route('home');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'number' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Log login
            $user = Auth::user();
            ActivityLog::log('login', "User login: {$user->name} ({$user->role})", $user);

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'number' => 'The provided credentials do not match our records.',
        ])->onlyInput('number');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log logout before actually logging out
        ActivityLog::log('logout', "User logout: {$user->name} ({$user->role})", $user);

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
