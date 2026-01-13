<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username', 'alpha_dash'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        // Create user
        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        // Assign default role (Student)
        $studentRole = Role::where('slug', 'student')->first();
        if ($studentRole) {
            $user->roles()->attach($studentRole->id, [
                'assigned_at' => now(),
            ]);
        }

        // Create user profile
        $user->profile()->create([
            'bio' => null,
            'school_name' => null,
            'grade_level' => null,
        ]);

        // Check if it's an API request
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Registration successful',
                'user' => [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'username' => $user->username,
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                    'roles' => $user->roles->pluck('name'),
                ],
            ], 201);
        }

        // Auto-login after registration
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Registration successful! Welcome to LMS SEMPAT.');
    }
}
