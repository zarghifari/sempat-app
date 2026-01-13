<x-guest-layout>
    <!-- Page Header -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Forgot Password? 🔑</h1>
        <p class="text-sm text-gray-600">
            No problem! Enter your email and we'll send you a password reset link.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email Address" />
            <x-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            Send Reset Link
        </x-primary-button>

        <div class="text-center pt-4">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                ← Back to Login
            </a>
        </div>
    </form>
</x-guest-layout>
