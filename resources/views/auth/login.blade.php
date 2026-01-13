<x-guest-layout>
    @section('title', 'Login')

    <!-- Header -->
    <div class="px-6 pt-6 pb-4">
        <h2 class="text-2xl font-bold text-gray-900">Welcome Back! 👋</h2>
        <p class="mt-2 text-sm text-gray-600">
            Sign in to continue learning
        </p>
    </div>

    <!-- Form Container -->
    <div class="px-6 pb-6">
        <!-- Success/Status Message -->
        @if (session('status'))
            <div class="mb-4">
                <x-alert variant="success">{{ session('status') }}</x-alert>
            </div>
        @endif

        <!-- Error Message -->
        @if ($errors->any())
            <div class="mb-4">
                <x-alert variant="error">{{ $errors->first() }}</x-alert>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" value="Email Address" />
                <x-input 
                    id="email" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    autocomplete="username"
                    placeholder="your@email.com"
                    class="mt-1 block w-full px-4 py-3 rounded-xl"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" value="Password" />
                <x-input 
                    id="password" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="mt-1 block w-full px-4 py-3 rounded-xl"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        name="remember" 
                        {{ old('remember') ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    >
                    <span class="ml-2 text-sm text-gray-700">Remember me</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        Forgot?
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <x-primary-button class="w-full justify-center py-3 rounded-xl">
                Sign In
            </x-primary-button>
        </form>

        <!-- Register Link -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                    Register now
                </a>
            </p>
        </div>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-white text-gray-500">Demo Accounts</span>
            </div>
        </div>

        <!-- Demo Accounts -->
        <div class="space-y-2 text-xs">
            <button type="button" onclick="fillLogin('admin@sempat.test', 'password')" class="w-full text-left px-3 py-2 bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg active:scale-95 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-purple-900">Admin Account</p>
                        <p class="text-purple-600">admin@sempat.test</p>
                    </div>
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </button>

            <button type="button" onclick="fillLogin('teacher@sempat.test', 'password')" class="w-full text-left px-3 py-2 bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg active:scale-95 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-green-900">Teacher Account</p>
                        <p class="text-green-600">teacher@sempat.test</p>
                    </div>
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </button>

            <button type="button" onclick="fillLogin('student@sempat.test', 'password')" class="w-full text-left px-3 py-2 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg active:scale-95 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-blue-900">Student Account</p>
                        <p class="text-blue-600">student@sempat.test</p>
                    </div>
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </button>
        </div>
    </div>
</x-guest-layout>
