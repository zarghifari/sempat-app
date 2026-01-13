<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        
        // Dummy profile data
        $profile = [
            'bio' => 'Passionate learner exploring web development and modern technologies. Always eager to learn new skills and improve my craft.',
            'phone' => '+62 812-3456-7890',
            'date_of_birth' => '1995-06-15',
            'gender' => 'male',
            'address' => 'Jakarta, Indonesia'
        ];
        
        // Dummy stats
        $stats = [
            'courses_enrolled' => 4,
            'courses_completed' => 2,
            'total_study_hours' => 47,
            'current_streak' => 12
        ];
        
        return view('profile.show', compact('user', 'profile', 'stats'));
    }
    
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
