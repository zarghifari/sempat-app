@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <!-- Profile Header -->
    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-6 text-white mb-6 -mx-4 mx-0">
        <div class="flex items-center gap-4 mb-4">
            <!-- Avatar -->
            @if($user->profile?->avatar)
                <img src="{{ $user->profile->avatar }}" 
                     alt="{{ $user->first_name }}" 
                     class="w-20 h-20 rounded-full border-4 border-white/20 object-cover">
            @else
                <div class="w-20 h-20 rounded-full border-4 border-white/20 bg-white/20 flex items-center justify-center text-3xl font-bold">
                    {{ substr($user->first_name, 0, 1) }}
                </div>
            @endif

            <!-- User Info -->
            <div class="flex-1">
                <h1 class="text-2xl font-bold">{{ $user->first_name }} {{ $user->last_name }}</h1>
                <p class="text-blue-100">{{ '@' . $user->username }}</p>
                <p class="text-sm text-blue-100 mt-1">{{ $user->email }}</p>
            </div>
        </div>

        <!-- Edit Profile Button -->
        <a href="{{ route('profile.edit') }}" 
           class="block w-full bg-white/10 hover:bg-white/20 text-white font-medium py-3 px-4 rounded-xl text-center active:scale-95 transition-all">
            Edit Profile
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 gap-3 mb-6 px-4">
        <div class="bg-white rounded-xl p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['courses_enrolled'] }}</div>
            <div class="text-xs text-gray-600 mt-1">Courses Enrolled</div>
        </div>
        <div class="bg-white rounded-xl p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-green-600">{{ $stats['courses_completed'] }}</div>
            <div class="text-xs text-gray-600 mt-1">Completed</div>
        </div>
        <div class="bg-white rounded-xl p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['total_study_hours'] }}h</div>
            <div class="text-xs text-gray-600 mt-1">Study Hours</div>
        </div>
        <div class="bg-white rounded-xl p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-orange-600">{{ $stats['current_streak'] }}</div>
            <div class="text-xs text-gray-600 mt-1">Day Streak 🔥</div>
        </div>
    </div>

    <!-- About Section -->
    <div class="bg-white rounded-xl p-5 mb-4 shadow-sm mx-4">
        <h2 class="font-bold text-gray-900 mb-3">About</h2>
        <p class="text-sm text-gray-600 leading-relaxed">{{ $profile['bio'] }}</p>
    </div>

    <!-- Account Information -->
    <details class="bg-white rounded-xl overflow-hidden mb-4 shadow-sm mx-4">
        <summary class="px-5 py-4 cursor-pointer font-medium text-gray-800 hover:bg-gray-50 active:bg-gray-100 flex items-center justify-between">
            <span>Account Information</span>
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </summary>
        <div class="px-5 py-4 border-t border-gray-200 space-y-3">
            <div>
                <label class="text-xs font-medium text-gray-500">Username</label>
                <p class="text-sm text-gray-900">{{ $user->username }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500">Email</label>
                <p class="text-sm text-gray-900">{{ $user->email }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500">Phone</label>
                <p class="text-sm text-gray-900">{{ $profile['phone'] }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500">Date of Birth</label>
                <p class="text-sm text-gray-900">{{ $profile['date_of_birth'] ? date('d M Y', strtotime($profile['date_of_birth'])) : '-' }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500">Gender</label>
                <p class="text-sm text-gray-900">{{ ucfirst($profile['gender']) }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500">Address</label>
                <p class="text-sm text-gray-900">{{ $profile['address'] }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500">Member Since</label>
                <p class="text-sm text-gray-900">{{ $user->created_at->format('d M Y') }}</p>
            </div>
        </div>
    </details>

    <!-- Role Information -->
    <div class="bg-white rounded-xl p-5 mb-4 shadow-sm mx-4">
        <h2 class="font-bold text-gray-900 mb-3">Roles & Permissions</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($user->roles as $role)
                <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                    {{ ucfirst($role->name) }}
                </span>
            @endforeach
        </div>
    </div>

    <!-- Settings Menu -->
    <div class="space-y-2 mb-20 px-4">
        <a href="#" class="block bg-white rounded-xl p-4 shadow-sm active:scale-95 transition-transform">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-medium text-gray-900">Change Password</h3>
                    <p class="text-xs text-gray-600">Update your password</p>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>

        <a href="#" class="block bg-white rounded-xl p-4 shadow-sm active:scale-95 transition-transform">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-medium text-gray-900">Notifications</h3>
                    <p class="text-xs text-gray-600">Manage notification preferences</p>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>

        <a href="#" class="block bg-white rounded-xl p-4 shadow-sm active:scale-95 transition-transform">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-medium text-gray-900">Help & Support</h3>
                    <p class="text-xs text-gray-600">Get help and contact support</p>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>

        <form method="POST" action="{{ route('logout') }}" class="block">
            @csrf
            <button type="submit" class="w-full bg-white rounded-xl p-4 shadow-sm active:scale-95 transition-transform text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-medium text-red-600">Logout</h3>
                        <p class="text-xs text-gray-600">Sign out from your account</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </button>
        </form>
    </div>
@endsection
