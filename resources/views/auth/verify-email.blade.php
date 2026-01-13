<x-guest-layout>
    <!-- Page Header -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Verify Email 📧</h1>
        <p class="text-sm text-gray-600">
            Thanks for signing up! Please verify your email address by clicking the link we sent you.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <x-alert type="success" class="mb-6">
            A new verification link has been sent to your email address!
        </x-alert>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
        @csrf
        <x-primary-button class="w-full">
            Resend Verification Email
        </x-primary-button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <x-secondary-button class="w-full">
            Log Out
        </x-secondary-button>
    </form>
</x-guest-layout>
