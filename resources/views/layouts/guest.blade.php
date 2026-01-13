<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#3B82F6">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <title>{{ config('app.name', 'LMS SEMPAT') }} - @yield('title', 'Welcome')</title>

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
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <div class="min-h-screen flex flex-col">
        <!-- Mobile App Header -->
        <div class="bg-white shadow-sm border-b border-gray-100">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">LMS SEMPAT</h1>
                        <p class="text-xs text-gray-500">Learning Made Easy</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Container -->
        <div class="flex-1 flex items-center justify-center p-4">
            <div class="w-full max-w-md">
                <!-- Card Container -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="py-4 text-center">
            <p class="text-xs text-gray-500">
                © {{ date('Y') }} LMS SEMPAT. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
