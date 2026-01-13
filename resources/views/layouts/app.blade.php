<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <title>{{ config('app.name', 'LMS SEMPAT') }} - @yield('title', 'Learning')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            overscroll-behavior-y: contain;
        }
        .safe-top {
            padding-top: env(safe-area-inset-top);
        }
        .safe-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="flex flex-col h-screen overflow-hidden">
        <!-- App Bar (Top Navigation) -->
        <header class="bg-white shadow-md safe-top fixed top-0 left-0 right-0 z-50 border-b border-gray-200">
            <div class="flex items-center justify-between h-14 px-4">
                <!-- Left: Menu/Back -->
                <div class="flex items-center space-x-3">
                    @if(isset($showBack) && $showBack)
                        <button onclick="history.back()" class="p-2 -ml-2 rounded-lg active:bg-gray-100 text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                    @endif
                    <h1 class="text-lg font-bold text-gray-900">@yield('page-title', 'LMS SEMPAT')</h1>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center space-x-2">
                    @auth
                        <!-- Notifications -->
                        <button class="p-2 rounded-lg active:bg-gray-100 relative text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        <!-- Profile Menu -->
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center space-x-2 p-1 rounded-lg active:bg-gray-100">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-8 h-8 rounded-full border-2 border-blue-500">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 border-2 border-blue-100 flex items-center justify-center text-sm font-bold text-white shadow-sm">
                                            {{ substr(Auth::user()->first_name, 0, 1) }}
                                        </div>
                                    @endif
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.show')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto no-scrollbar mt-14 mb-16 py-4">
            @yield('content')
        </main>

        <!-- Bottom Navigation -->
        @auth
        <nav class="bg-white border-t border-gray-200 shadow-lg safe-bottom fixed bottom-0 left-0 right-0 z-50">
            <div class="flex justify-around items-center h-16">
                <!-- Home -->
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-600' }} active:bg-gray-50">
                    <svg class="w-6 h-6 mb-1" fill="{{ request()->routeIs('dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-xs font-medium">Home</span>
                </a>

                <!-- Courses -->
                <a href="{{ route('courses.index') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('courses.*') ? 'text-blue-600' : 'text-gray-600' }} active:bg-gray-50">
                    <svg class="w-6 h-6 mb-1" fill="{{ request()->routeIs('courses.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="text-xs font-medium">Courses</span>
                </a>

                <!-- Articles -->
                <a href="{{ route('articles.index') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('articles.*') ? 'text-blue-600' : 'text-gray-600' }} active:bg-gray-50">
                    <svg class="w-6 h-6 mb-1" fill="{{ request()->routeIs('articles.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    <span class="text-xs font-medium">Articles</span>
                </a>

                <!-- Profile -->
                <a href="{{ route('profile.show') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('profile.*') ? 'text-blue-600' : 'text-gray-600' }} active:bg-gray-50">
                    <svg class="w-6 h-6 mb-1" fill="{{ request()->routeIs('profile.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-xs font-medium">Profile</span>
                </a>
            </div>
        </nav>
        @endauth
    </div>
</body>
</html>
