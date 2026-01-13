<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application (Web).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Update last login
            Auth::user()->updateLastLogin($request->ip());

            // Check if it's an API request
            if ($request->expectsJson()) {
                $user = Auth::user();

                return response()->json([
                    'message' => 'Login successful',
                    'user' => [
                        'id' => $user->id,
                        'uuid' => $user->uuid,
                        'username' => $user->username,
                        'email' => $user->email,
                        'full_name' => $user->full_name,
                        'avatar' => $user->avatar,
                        'roles' => $user->roles->pluck('name'),
                    ],
                    'session_token' => $request->session()->getId(),
                ], 200);
            }

            return redirect()->intended('dashboard');
        }

        if ($request->expectsJson()) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Get the authenticated user (API).
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'username' => $user->username,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->full_name,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'is_active' => $user->is_active,
                'email_verified_at' => $user->email_verified_at,
                'last_login' => $user->last_login,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->roles->flatMap->permissions->pluck('slug')->unique()->values(),
            ],
        ], 200);
    }

    /**
     * Update user profile (API).
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'avatar' => ['sometimes', 'nullable', 'image', 'max:2048'], // 2MB max
        ]);

        if ($request->hasFile('avatar')) {
            // Handle avatar upload
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'full_name' => $user->full_name,
                'avatar' => $user->avatar,
            ],
        ], 200);
    }
}
