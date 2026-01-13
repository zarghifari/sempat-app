<x-guest-layout>
    <!-- Page Header -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Confirm Password 🔐</h1>
        <p class="text-sm text-gray-600">
            This is a secure area. Please confirm your password to continue.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Password" />
            <x-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            Confirm
        </x-primary-button>
    </form>
</x-guest-layout>
