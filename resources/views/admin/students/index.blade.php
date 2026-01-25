@extends('layouts.app')

@section('title', 'Manage Students')

@section('page-title', 'Manage All Students')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gray-50 pb-24 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Total Students</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Active Students</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['active'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Total Enrollments</p>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['total_enrollments'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Avg Completion</p>
                <p class="text-3xl font-bold text-orange-600">{{ number_format($stats['avg_progress'] ?? 0, 1) }}%</p>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <form action="{{ route('admin.students') }}" method="GET" class="flex gap-4">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search students by name or email..." 
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                <button type="submit" class="px-6 py-3 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors font-medium">
                    Search
                </button>
            </form>
        </div>

        <!-- Students Grid -->
        @if($students->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($students as $student)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                        <div class="p-6">
                            <!-- Student Header -->
                            <div class="flex items-center gap-4 mb-4">
                                @if($student->avatar)
                                    <img src="{{ asset('storage/' . $student->avatar) }}" 
                                         alt="{{ $student->name }}" 
                                         class="w-16 h-16 rounded-full border-2 border-slate-200">
                                @else
                                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-slate-400 to-gray-500 flex items-center justify-center text-2xl font-bold text-white">
                                        {{ substr($student->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-bold text-gray-900 truncate">{{ $student->name }}</h3>
                                    <p class="text-sm text-gray-600 truncate">{{ $student->email }}</p>
                                </div>
                            </div>

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-3 gap-2 mb-4 pt-4 border-t border-gray-100">
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 mb-1">Courses</p>
                                    <p class="text-xl font-bold text-blue-600">{{ $student->enrollments_count ?? 0 }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 mb-1">Progress</p>
                                    <p class="text-xl font-bold text-green-600">{{ number_format($student->avg_progress ?? 0, 1) }}%</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 mb-1">Study Time</p>
                                    <p class="text-xl font-bold text-purple-600">{{ gmdate('H:i', ($student->total_study_minutes ?? 0) * 60) }}</p>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                    <span>Overall Progress</span>
                                    <span class="font-semibold">{{ number_format($student->avg_progress ?? 0, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 h-2 rounded-full transition-all" 
                                         style="width: {{ $student->avg_progress ?? 0 }}%"></div>
                                </div>
                            </div>

                            <!-- Joined Date -->
                            <div class="flex items-center gap-2 text-xs text-gray-500 mb-4">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Joined {{ $student->created_at->diffForHumans() }}</span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <a href="{{ route('admin.students.show', $student->id) }}" 
                                   class="flex-1 px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors text-center text-sm font-medium">
                                    View Details
                                </a>
                                <button onclick="alert('Manage roles - will be implemented')"
                                        class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $students->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <div class="text-6xl mb-4">👨‍🎓</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Students Found</h3>
                <p class="text-gray-600 mb-6">
                    @if(request('search'))
                        No students match your search criteria.
                    @else
                        There are no students registered yet.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
