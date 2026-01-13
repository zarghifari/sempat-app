<x-guest-layout>
    @section('title', 'Register')

    <!-- Header -->
    <div class="px-6 pt-6 pb-4">
        <h2 class="text-2xl font-bold text-gray-900">Join SEMPAT! 🎓</h2>
        <p class="mt-2 text-sm text-gray-600">
            Create your account to start learning
        </p>
    </div>

    <!-- Form Container -->
    <div class="px-6 pb-6">
        <!-- Error Message -->
        @if ($errors->any())
            <div class="mb-4">
                <x-alert variant="error">
                    <p class="font-medium mb-1">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Username -->
            <div>
                <x-input-label for="username">
                    Username <span class="text-red-500">*</span>
                </x-input-label>
                <x-input 
                    id="username" 
                    type="text" 
                    name="username" 
                    :value="old('username')" 
                    required 
                    autofocus 
                    placeholder="johndoe123"
                    class="mt-1 block w-full px-4 py-3 rounded-xl"
                />
                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                @if(!$errors->has('username'))
                    <p class="mt-1 text-xs text-gray-500">Letters, numbers, dashes, and underscores only</p>
                @endif
            </div>

            <!-- Email -->
            <div>
                <x-input-label for="email">
                    Email Address <span class="text-red-500">*</span>
                </x-input-label>
                <x-input 
                    id="email" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autocomplete="username"
                    placeholder="john@example.com"
                    class="mt-1 block w-full px-4 py-3 rounded-xl"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Name Row (Two Columns) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- First Name -->
                <div>
                    <x-input-label for="first_name">
                        First Name <span class="text-red-500">*</span>
                    </x-input-label>
                    <x-input 
                        id="first_name" 
                        type="text" 
                        name="first_name" 
                        :value="old('first_name')" 
                        required 
                        placeholder="John"
                        class="mt-1 block w-full px-4 py-3 rounded-xl"
                    />
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>

                <!-- Last Name -->
                <div>
                    <x-input-label for="last_name">
                        Last Name <span class="text-red-500">*</span>
                    </x-input-label>
                    <x-input 
                        id="last_name" 
                        type="text" 
                        name="last_name" 
                        :value="old('last_name')" 
                        required 
                        placeholder="Doe"
                        class="mt-1 block w-full px-4 py-3 rounded-xl"
                    />
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>
            </div>

            <!-- Phone (Optional) -->
            <div>
                <x-input-label for="phone">
                    Phone Number <span class="text-gray-400 text-xs">(optional)</span>
                </x-input-label>
                <x-input 
                    id="phone" 
                    type="text" 
                    name="phone" 
                    :value="old('phone')" 
                    placeholder="08123456789"
                    class="mt-1 block w-full px-4 py-3 rounded-xl"
                />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password">
                    Password <span class="text-red-500">*</span>
                </x-input-label>
                <x-input 
                    id="password" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="new-password"
                    placeholder="Minimum 8 characters"
                    class="mt-1 block w-full px-4 py-3 rounded-xl"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                @if(!$errors->has('password'))
                    <p class="mt-1 text-xs text-gray-500">Must be at least 8 characters long</p>
                @endif
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation">
                    Confirm Password <span class="text-red-500">*</span>
                </x-input-label>
                <x-input 
                    id="password_confirmation" 
                    type="password" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    placeholder="Re-enter your password"
                    class="mt-1 block w-full px-4 py-3 rounded-xl"
                />
            </div>

            <!-- Submit Button -->
            <x-primary-button class="w-full justify-center py-3.5 rounded-xl">
                Create Account
            </x-primary-button>
        </form>

        <!-- Already have account -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Already have an account?
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium active:text-blue-800">
                    Sign in here
                </a>
            </p>
        </div>

        <!-- Terms -->
        <div class="mt-6 p-3 bg-blue-50 rounded-xl">
            <p class="text-xs text-gray-600 text-center">
                By registering, you agree to our <span class="font-medium">Terms of Service</span> and <span class="font-medium">Privacy Policy</span>. New accounts will be assigned the Student role by default.
            </p>
        </div>
    </div>
</x-guest-layout>
