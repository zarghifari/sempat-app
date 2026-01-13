# Blade Components Documentation

## Form Components

### Input Field
```blade
<x-input 
    id="email" 
    type="email" 
    name="email" 
    :value="old('email')" 
    placeholder="your@email.com"
    required
/>
```

### Input Label
```blade
<x-input-label for="email" value="Email Address" />
<!-- OR -->
<x-input-label for="email">Email Address</x-input-label>
```

### Input Error
```blade
<x-input-error :messages="$errors->get('email')" class="mt-2" />
```

### Primary Button
```blade
<x-primary-button>Save Changes</x-primary-button>
<x-primary-button type="button">Cancel</x-primary-button>
```

### Secondary Button
```blade
<x-secondary-button>Cancel</x-secondary-button>
```

## UI Components

### Alert
```blade
<x-alert variant="success">Your changes have been saved!</x-alert>
<x-alert variant="error">An error occurred!</x-alert>
<x-alert variant="warning">Warning message</x-alert>
<x-alert variant="info" dismissible>Info message with close button</x-alert>
```

### Stat Card
```blade
<x-stat-card 
    color="blue" 
    value="12" 
    label="Total Courses"
>
    <x-slot name="subtitle">of 20 available</x-slot>
</x-stat-card>

<!-- Available colors: blue, green, purple, orange -->
```

### Course Card
```blade
<x-course-card 
    href="{{ route('courses.show', 1) }}"
    title="Introduction to Physics"
    description="Chapter 3: Motion"
    :progress="65"
>
    <x-slot name="thumbnail">
        <div class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
            <svg class="w-10 h-10 text-white">...</svg>
        </div>
    </x-slot>
</x-course-card>
```

## Navigation Components

### Nav Link
```blade
<x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
    Dashboard
</x-nav-link>
```

### Dropdown
```blade
<x-dropdown align="right" width="48">
    <x-slot name="trigger">
        <button>Settings</button>
    </x-slot>

    <x-slot name="content">
        <x-dropdown-link :href="route('profile')">Profile</x-dropdown-link>
        <x-dropdown-link :href="route('settings')">Settings</x-dropdown-link>
    </x-slot>
</x-dropdown>
```

### Dropdown Link
```blade
<x-dropdown-link :href="route('profile')" :active="request()->routeIs('profile')">
    Profile
</x-dropdown-link>
```

## Modal
```blade
<x-modal name="confirm-delete" :show="false" maxWidth="md">
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
            Are you sure?
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            This action cannot be undone.
        </p>
        <div class="mt-6 flex justify-end gap-3">
            <x-secondary-button x-on:click="$dispatch('close')">
                Cancel
            </x-secondary-button>
            <x-primary-button>
                Confirm
            </x-primary-button>
        </div>
    </div>
</x-modal>
```

## Complete Form Example

```blade
<form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
    @csrf
    @method('PUT')

    <!-- Name Field -->
    <div>
        <x-input-label for="name" value="Full Name" />
        <x-input 
            id="name" 
            name="name" 
            type="text" 
            :value="old('name', $user->name)" 
            required 
            class="mt-1 block w-full"
        />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <!-- Email Field -->
    <div>
        <x-input-label for="email" value="Email" />
        <x-input 
            id="email" 
            name="email" 
            type="email" 
            :value="old('email', $user->email)" 
            required 
            class="mt-1 block w-full"
        />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <!-- Submit Button -->
    <div class="flex items-center gap-4">
        <x-primary-button>Save</x-primary-button>
        <x-secondary-button type="button">Cancel</x-secondary-button>
    </div>
</form>
```

## Success Messages

```blade
@if (session('success'))
    <x-alert variant="success">
        {{ session('success') }}
    </x-alert>
@endif

@if (session('error'))
    <x-alert variant="error">
        {{ session('error') }}
    </x-alert>
@endif
```

## Styling Guidelines

All components use Tailwind CSS and follow these conventions:
- **Rounded corners**: `rounded-xl` for cards, `rounded-lg` for buttons
- **Shadows**: `shadow-sm` for subtle, `shadow-lg` for prominent
- **Colors**: Primary blue (`blue-600`), success green (`green-500`), error red (`red-500`)
- **Transitions**: All interactive elements have `transition` classes
- **Mobile-first**: All components are responsive by default
