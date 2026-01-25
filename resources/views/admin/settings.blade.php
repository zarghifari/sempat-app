@extends('layouts.app')

@section('title', 'System Settings')

@section('page-title', 'System Settings')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gray-50 pb-24 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Settings Tabs -->
        <div class="bg-white rounded-xl shadow-md mb-6">
            <div class="border-b border-gray-200 px-6">
                <nav class="flex gap-6 overflow-x-auto no-scrollbar" aria-label="Tabs">
                    <button onclick="showTab('general')" id="tab-general" class="py-4 px-1 border-b-2 border-slate-600 text-slate-700 font-medium whitespace-nowrap">
                        General Settings
                    </button>
                    <button onclick="showTab('users')" id="tab-users" class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium whitespace-nowrap">
                        User Management
                    </button>
                    <button onclick="showTab('system')" id="tab-system" class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium whitespace-nowrap">
                        System Health
                    </button>
                    <button onclick="showTab('backup')" id="tab-backup" class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium whitespace-nowrap">
                        Backup & Restore
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content: General Settings -->
        <div id="content-general" class="tab-content">
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Platform Configuration</h3>
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Platform Name</label>
                        <input type="text" 
                               value="SEMPAT - Self-Managed Platform for Adaptive Teaching" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Platform Description</label>
                        <textarea rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">Platform pembelajaran yang mendukung self-regulated learning dengan fitur-fitur adaptif.</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Default Language</label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                                <option value="id">Bahasa Indonesia</option>
                                <option value="en">English</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                                <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                                <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pt-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" checked class="w-5 h-5 text-slate-600 border-gray-300 rounded focus:ring-slate-500">
                            <span class="text-sm font-medium text-gray-700">Allow Student Registration</span>
                        </label>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" checked class="w-5 h-5 text-slate-600 border-gray-300 rounded focus:ring-slate-500">
                            <span class="text-sm font-medium text-gray-700">Require Email Verification</span>
                        </label>
                    </div>
                    <div class="pt-4">
                        <button type="button" 
                                onclick="alert('Save settings - will be implemented')"
                                class="px-6 py-3 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors font-medium">
                            Save General Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Email Settings -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Email Configuration</h3>
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                        <input type="text" 
                               placeholder="smtp.example.com" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                            <input type="text" 
                                   value="587" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Encryption</label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Email</label>
                        <input type="email" 
                               placeholder="noreply@sempat.com" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent">
                    </div>
                    <div class="pt-4">
                        <button type="button" 
                                onclick="alert('Test email configuration - will be implemented')"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium mr-3">
                            Test Connection
                        </button>
                        <button type="button" 
                                onclick="alert('Save email settings - will be implemented')"
                                class="px-6 py-3 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors font-medium">
                            Save Email Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab Content: User Management -->
        <div id="content-users" class="tab-content hidden">
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Role Management</h3>
                <div class="space-y-4">
                    <!-- Admin Role -->
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h4 class="text-md font-bold text-gray-900">Administrator</h4>
                                <p class="text-sm text-gray-600">Full system access and control</p>
                            </div>
                            <button onclick="alert('Edit role permissions - will be implemented')"
                                    class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition-colors text-sm font-medium">
                                Edit Permissions
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Manage Users</span>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Manage Courses</span>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">View Analytics</span>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">System Settings</span>
                        </div>
                    </div>

                    <!-- Teacher Role -->
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h4 class="text-md font-bold text-gray-900">Teacher</h4>
                                <p class="text-sm text-gray-600">Create and manage own courses</p>
                            </div>
                            <button onclick="alert('Edit role permissions - will be implemented')"
                                    class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition-colors text-sm font-medium">
                                Edit Permissions
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Create Courses</span>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Grade Quizzes</span>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">View Students</span>
                        </div>
                    </div>

                    <!-- Student Role -->
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h4 class="text-md font-bold text-gray-900">Student</h4>
                                <p class="text-sm text-gray-600">Enroll and learn from courses</p>
                            </div>
                            <button onclick="alert('Edit role permissions - will be implemented')"
                                    class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition-colors text-sm font-medium">
                                Edit Permissions
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">Enroll Courses</span>
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">Take Quizzes</span>
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">Learning Goals</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Bulk User Actions</h3>
                <div class="space-y-3">
                    <button onclick="alert('Import users from CSV - will be implemented')"
                            class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-left flex items-center justify-between">
                        <span>Import Users from CSV</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </button>
                    <button onclick="alert('Export users to CSV - will be implemented')"
                            class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-left flex items-center justify-between">
                        <span>Export Users to CSV</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab Content: System Health -->
        <div id="content-system" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- System Status -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">System Status</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 bg-green-600 rounded-full animate-pulse"></div>
                                <span class="font-medium text-gray-700">Database</span>
                            </div>
                            <span class="text-sm font-semibold text-green-600">Connected</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 bg-green-600 rounded-full animate-pulse"></div>
                                <span class="font-medium text-gray-700">Storage</span>
                            </div>
                            <span class="text-sm font-semibold text-green-600">Available</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 bg-yellow-600 rounded-full animate-pulse"></div>
                                <span class="font-medium text-gray-700">Cache</span>
                            </div>
                            <span class="text-sm font-semibold text-yellow-600">77% Full</span>
                        </div>
                    </div>
                </div>

                <!-- Resource Usage -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Resource Usage</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">Storage</span>
                                <span class="text-gray-600">2.3 GB / 10 GB</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-blue-600 h-3 rounded-full" style="width: 23%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">Database Size</span>
                                <span class="text-gray-600">156 MB</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-green-600 h-3 rounded-full" style="width: 15.6%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">Cache Size</span>
                                <span class="text-gray-600">45 MB</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-purple-600 h-3 rounded-full" style="width: 77%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Actions -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">System Maintenance</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button onclick="if(confirm('Clear all cache?')) alert('Clear cache - will be implemented')"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Clear Cache
                    </button>
                    <button onclick="if(confirm('Optimize database?')) alert('Optimize database - will be implemented')"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                        Optimize Database
                    </button>
                    <button onclick="alert('View system logs - will be implemented')"
                            class="px-6 py-3 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors font-medium">
                        View System Logs
                    </button>
                    <button onclick="if(confirm('Run system health check?')) alert('Health check - will be implemented')"
                            class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium">
                        Run Health Check
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab Content: Backup & Restore -->
        <div id="content-backup" class="tab-content hidden">
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Database Backup</h3>
                <div class="space-y-4">
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <strong>Automatic Backup:</strong> Daily at 2:00 AM (Last backup: 2024-01-15 02:00:00)
                        </p>
                    </div>
                    <button onclick="if(confirm('Create manual backup now?')) alert('Create backup - will be implemented')"
                            class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Create Manual Backup Now
                    </button>
                </div>
            </div>

            <!-- Backup History -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Backup History</h3>
                <div class="space-y-3">
                    @for($i = 0; $i < 5; $i++)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">backup_{{ date('Y-m-d', strtotime("-$i days")) }}.sql</p>
                                    <p class="text-sm text-gray-600">{{ date('M d, Y H:i', strtotime("-$i days")) }} • 12.5 MB</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="alert('Download backup - will be implemented')"
                                        class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                                    Download
                                </button>
                                <button onclick="if(confirm('Restore this backup?')) alert('Restore backup - will be implemented')"
                                        class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors text-sm font-medium">
                                    Restore
                                </button>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function showTab(tab) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('[id^="tab-"]').forEach(el => {
        el.classList.remove('border-slate-600', 'text-slate-700');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    document.getElementById('content-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById('tab-' + tab).classList.add('border-slate-600', 'text-slate-700');
}
</script>
@endsection
