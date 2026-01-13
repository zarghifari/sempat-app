@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Profile</h1>
        <p class="text-sm text-gray-600 mt-1">Update your account information</p>
    </div>

    <!-- Success Message -->
    @if(session('status'))
        <x-alert type="success" class="mb-4">
            {{ session('status') }}
        </x-alert>
    @endif

    <!-- Profile Information Section -->
    <div class="bg-white rounded-xl p-5 mb-4 shadow-sm">
        @include('profile.partials.update-profile-information-form')
    </div>

    <!-- Update Password Section -->
    <div class="bg-white rounded-xl p-5 mb-4 shadow-sm">
        @include('profile.partials.update-password-form')
    </div>

    <!-- Delete Account Section -->
    <div class="bg-white rounded-xl p-5 mb-20 shadow-sm">
        @include('profile.partials.delete-user-form')
    </div>
@endsection
