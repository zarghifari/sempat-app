@extends('layouts.app')

@section('title', 'User Management')

@section('page-title', 'User Management')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gray-50 pb-24 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Total Users</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Students</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['students'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Teachers</p>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['teachers'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Admins</p>
                <p class="text-3xl font-bold text-orange-600">{{ $stats['admins'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Search -->
                <form action="{{ route('admin.users') }}" method="GET" class="flex-1 flex gap-4">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search users by name or email..." 
                           class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                    
                    <!-- Role Filter -->
                    <select name="role" 
                            class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                        <option value="">All Roles</option>
                        <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    
                    <button type="submit" class="px-6 py-3 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors font-medium">
                        Search
                    </button>
                </form>
                
                <!-- Add User Button -->
                <button onclick="alert('Add new user - will be implemented')"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium whitespace-nowrap">
                    + Add User
                </button>
            </div>
        </div>

        <!-- Users Table -->
        @if($users->count() > 0)
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <!-- User Info -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            @if($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}" 
                                                     alt="{{ $user->name }}" 
                                                     class="w-10 h-10 rounded-full">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-400 to-gray-500 flex items-center justify-center text-white font-semibold">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Email -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                    </td>
                                    
                                    <!-- Role -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $roleColors = [
                                                'admin' => 'orange',
                                                'teacher' => 'purple',
                                                'student' => 'blue',
                                            ];
                                            $role = $user->roles->first()?->name ?? 'student';
                                            $color = $roleColors[$role] ?? 'gray';
                                        @endphp
                                        <span class="px-3 py-1 bg-{{ $color }}-100 text-{{ $color }}-800 rounded-full text-xs font-medium">
                                            {{ ucfirst($role) }}
                                        </span>
                                    </td>
                                    
                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->email_verified_at)
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                                Verified
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                                Unverified
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <!-- Joined Date -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </td>
                                    
                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.users.show', $user->id) }}" 
                                               class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors">
                                                View
                                            </a>
                                            <button onclick="alert('Edit user {{ $user->name }} - will be implemented')"
                                                    class="px-3 py-1 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition-colors">
                                                Edit
                                            </button>
                                            @if($user->id !== Auth::id())
                                                <button onclick="if(confirm('Delete user {{ $user->name }}?')) alert('Delete user - will be implemented')"
                                                        class="px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors">
                                                    Delete
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $users->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <div class="text-6xl mb-4">👥</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Users Found</h3>
                <p class="text-gray-600 mb-6">
                    @if(request('search'))
                        No users match your search criteria.
                    @else
                        Start by adding your first user.
                    @endif
                </p>
                <button onclick="alert('Add new user - will be implemented')"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Add New User
                </button>
            </div>
        @endif
    </div>
</div>
@endsection
