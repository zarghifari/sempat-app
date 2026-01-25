@extends('layouts.app')

@section('title', 'My Students')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 pb-20">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">👥 My Students</h1>
                    <p class="text-blue-100">Real-time student learning analytics</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-blue-100">Last updated</div>
                    <div class="text-lg font-semibold" id="lastUpdated">Loading...</div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Class Summary Cards -->
        <div id="classSummary" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Students -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Students</p>
                        <p id="totalStudents" class="text-3xl font-bold text-blue-600">-</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Today -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Active Today</p>
                        <p class="text-3xl font-bold text-green-600">
                            <span id="activeToday">-</span>
                            <span id="activePercentage" class="text-base text-gray-500">-</span>
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Study Time -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Study Time</p>
                        <p id="totalTime" class="text-3xl font-bold text-purple-600">-</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Average per Student -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Average per Student</p>
                        <p id="avgTime" class="text-3xl font-bold text-orange-600">-</p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">🏆 Top Performers Today</h2>
            <div id="topPerformers" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <p class="col-span-full text-center text-gray-400">Loading...</p>
            </div>
        </div>

        <!-- Students List -->
        <div class="bg-white rounded-xl shadow-md">
            <!-- Search & Filter Bar -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="flex-1 w-full md:w-auto">
                        <div class="relative">
                            <input 
                                type="text" 
                                id="studentSearch"
                                placeholder="🔍 Search by name or email..."
                                class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <select id="perPage" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="10">10 per page</option>
                            <option value="20" selected>20 per page</option>
                            <option value="50">50 per page</option>
                        </select>
                        <button 
                            id="refreshBtn"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>

            <!-- Students Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Time Today</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Breakdown</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Sessions</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Last Activity</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody" class="divide-y divide-gray-200">
                        <!-- Loading State -->
                        <tr class="animate-pulse">
                            <td colspan="6" class="px-6 py-8 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-gray-600">Loading student data...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="pagination" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <span id="paginationInfo">Loading...</span>
                </div>
                <div id="paginationButtons" class="flex gap-2">
                    <!-- Will be populated by JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Detail Modal -->
<div id="studentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <img id="modalStudentAvatar" src="" alt="" class="w-16 h-16 rounded-full border-4 border-white">
                    <div>
                        <h2 id="modalStudentName" class="text-2xl font-bold">Loading...</h2>
                        <p id="modalStudentEmail" class="text-blue-100">Loading...</p>
                    </div>
                </div>
                <button onclick="closeStudentModal()" class="text-white hover:bg-white/20 rounded-full p-2 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Content -->
        <div id="studentModalBody" class="p-6">
            <div class="flex items-center justify-center py-12">
                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
class StudentProgressMonitor {
    constructor() {
        this.currentPage = 1;
        this.perPage = 20;
        this.searchQuery = '';
        this.autoRefreshInterval = null;
        this.lastUpdated = new Date();
    }

    async init() {
        this.setupEventListeners();
        await this.loadClassSummary();
        await this.loadStudents();
        this.startAutoRefresh();
    }

    setupEventListeners() {
        // Search
        const searchInput = document.getElementById('studentSearch');
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.searchQuery = e.target.value;
                this.currentPage = 1;
                this.loadStudents();
            }, 500);
        });

        // Refresh button
        document.getElementById('refreshBtn').addEventListener('click', () => this.refresh());

        // Per page select
        document.getElementById('perPage').addEventListener('change', (e) => {
            this.perPage = parseInt(e.target.value);
            this.currentPage = 1;
            this.loadStudents();
        });
    }

    async loadClassSummary() {
        try {
            const response = await fetch('/teacher/api/class/summary', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed to load summary');
            const data = await response.json();
            this.renderClassSummary(data);
        } catch (error) {
            console.error('Error loading class summary:', error);
        }
    }

    async loadStudents() {
        const tableBody = document.getElementById('studentsTableBody');
        tableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div><p class="mt-2 text-gray-500">Loading student data...</p></td></tr>';

        try {
            const url = new URL('/teacher/api/students/today-progress', window.location.origin);
            url.searchParams.append('page', this.currentPage);
            url.searchParams.append('per_page', this.perPage);
            if (this.searchQuery) url.searchParams.append('search', this.searchQuery);

            const response = await fetch(url, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed to load students');
            const data = await response.json();
            this.renderStudents(data.students);
            this.renderPagination(data.pagination);
            this.renderClassSummary(data.summary);
            this.updateLastUpdated();
        } catch (error) {
            console.error('Error loading students:', error);
            tableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-red-600">Failed to load student data. Please try again.</td></tr>';
        }
    }

    renderClassSummary(data) {
        document.getElementById('totalStudents').textContent = data.total_students;
        document.getElementById('activeToday').textContent = data.active_today;
        const percentage = data.total_students > 0 ? Math.round((data.active_today / data.total_students) * 100) : 0;
        document.getElementById('activePercentage').textContent = `(${percentage}%)`;
        document.getElementById('totalTime').textContent = this.formatSeconds(data.total_study_time);
        document.getElementById('avgTime').textContent = this.formatSeconds(data.average_per_student);
        
        // Update top performers from students list
        this.updateTopPerformers();
    }

    updateTopPerformers() {
        // Will be populated from students data after rendering
        const container = document.getElementById('topPerformers');
        container.innerHTML = '<p class="text-gray-500 text-center py-4 text-sm">Top performers will appear here</p>';
    }

    renderStudents(students) {
        const tableBody = document.getElementById('studentsTableBody');
        
        if (!students.length) {
            tableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No students found</td></tr>';
            return;
        }

        tableBody.innerHTML = students.map(student => {
            const progress = student.today_progress;
            return `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-indigo-600 font-medium text-sm">${student.name.charAt(0).toUpperCase()}</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${student.name}</div>
                            <div class="text-sm text-gray-500">${student.email}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-semibold text-gray-900">${progress.total_time_formatted}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-xs text-gray-600">
                        <div>📚 Goals: <span class="font-medium">${this.formatSeconds(progress.goals_time)}</span></div>
                        <div>📖 Lessons: <span class="font-medium">${this.formatSeconds(progress.lessons_time)}</span></div>
                        <div>📄 Articles: <span class="font-medium">${this.formatSeconds(progress.articles_time)}</span></div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${progress.sessions_count} sessions
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${progress.last_activity 
                        ? `<span class="text-sm text-gray-500">${this.formatRelativeTime(progress.last_activity)}</span>`
                        : '<span class="text-sm text-gray-400">Never</span>'
                    }
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button onclick="studentMonitor.viewStudentDetail(${student.id})" 
                            class="text-indigo-600 hover:text-indigo-900 transition-colors">
                        View Detail
                    </button>
                </td>
            </tr>
        `;
        }).join('');
    }

    formatSeconds(seconds) {
        if (!seconds || seconds === 0) return '0 min';
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        if (hours > 0) {
            return `${hours} jam ${minutes} min`;
        }
        return `${minutes} min`;
    }

    renderPagination(data) {
        const container = document.getElementById('pagination');
        const { current_page, last_page, from, to, total } = data;
        
        if (last_page <= 1) {
            container.innerHTML = '';
            return;
        }

        let pages = [];
        for (let i = Math.max(1, current_page - 2); i <= Math.min(last_page, current_page + 2); i++) {
            pages.push(i);
        }

        container.innerHTML = `
            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 rounded-lg">
                <div class="flex flex-1 justify-between sm:hidden">
                    <button onclick="studentMonitor.goToPage(${current_page - 1})" 
                            ${current_page === 1 ? 'disabled' : ''}
                            class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50">
                        Previous
                    </button>
                    <button onclick="studentMonitor.goToPage(${current_page + 1})" 
                            ${current_page === last_page ? 'disabled' : ''}
                            class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50">
                        Next
                    </button>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">${from}</span> to <span class="font-medium">${to}</span> of{' '}
                            <span class="font-medium">${total}</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm">
                            <button onclick="studentMonitor.goToPage(${current_page - 1})" 
                                    ${current_page === 1 ? 'disabled' : ''}
                                    class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 disabled:opacity-50">
                                <span class="sr-only">Previous</span>
                                ←
                            </button>
                            ${pages.map(page => `
                                <button onclick="studentMonitor.goToPage(${page})"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ${
                                            page === current_page 
                                                ? 'z-10 bg-indigo-600 text-white focus:z-20' 
                                                : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50'
                                        }">
                                    ${page}
                                </button>
                            `).join('')}
                            <button onclick="studentMonitor.goToPage(${current_page + 1})" 
                                    ${current_page === last_page ? 'disabled' : ''}
                                    class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 disabled:opacity-50">
                                <span class="sr-only">Next</span>
                                →
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        `;
    }

    goToPage(page) {
        this.currentPage = page;
        this.loadStudents();
    }

    async viewStudentDetail(userId) {
        const modal = document.getElementById('studentModal');
        const modalBody = document.getElementById('studentModalBody');
        
        modal.classList.remove('hidden');
        modalBody.innerHTML = '<div class="flex justify-center items-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div></div>';

        try {
            const response = await fetch(`/teacher/api/students/${userId}/progress`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed to load student detail');
            const data = await response.json();
            this.renderStudentDetail(data);
        } catch (error) {
            console.error('Error loading student detail:', error);
            modalBody.innerHTML = '<p class="text-center text-red-600 py-12">Failed to load student details</p>';
        }
    }

    renderStudentDetail(data) {
        const modalBody = document.getElementById('studentModalBody');
        
        modalBody.innerHTML = `
            <div class="space-y-6">
                <!-- Student Header -->
                <div class="flex items-center space-x-4 border-b pb-4">
                    <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-indigo-600 font-bold text-xl">${data.student.name.charAt(0).toUpperCase()}</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">${data.student.name}</h3>
                        <p class="text-sm text-gray-500">${data.student.email}</p>
                    </div>
                </div>

                <!-- Today's Progress -->
                <div>
                    <h4 class="text-lg font-semibold mb-3">Today's Progress</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-blue-600 font-medium">Total Time</p>
                            <p class="text-2xl font-bold text-blue-900">${data.today.total_time_formatted}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm text-green-600 font-medium">Goals</p>
                            <p class="text-2xl font-bold text-green-900">${this.formatSeconds(data.today.breakdown.goals)}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="text-sm text-purple-600 font-medium">Lessons</p>
                            <p class="text-2xl font-bold text-purple-900">${this.formatSeconds(data.today.breakdown.lessons)}</p>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg">
                            <p class="text-sm text-orange-600 font-medium">Sessions</p>
                            <p class="text-2xl font-bold text-orange-900">${data.today.sessions_count}</p>
                        </div>
                    </div>
                </div>

                <!-- Week Chart -->
                <div>
                    <h4 class="text-lg font-semibold mb-3">This Week</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-end justify-between space-x-2" style="height: 200px;">
                            ${data.week.daily_data.map((day, index) => {
                                const maxTime = Math.max(...data.week.daily_data.map(d => d.total_time));
                                const percentage = maxTime > 0 ? (day.total_time / maxTime * 100) : 0;
                                const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                const date = new Date(day.date);
                                const dayLabel = dayNames[date.getDay()];
                                
                                return `
                                    <div class="flex-1 flex flex-col items-center justify-end">
                                        <div class="w-full bg-indigo-600 rounded-t hover:bg-indigo-700 transition-colors relative group" 
                                             style="height: ${percentage}%; min-height: ${day.total_time > 0 ? '10px' : '2px'}">
                                            <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">
                                                ${day.formatted_time}
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-2">${dayLabel}</p>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                </div>

                <!-- Active Goals -->
                ${data.active_goals && data.active_goals.length > 0 ? `
                    <div>
                        <h4 class="text-lg font-semibold mb-3">Active Learning Goals</h4>
                        <div class="space-y-3">
                            ${data.active_goals.map(goal => `
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h5 class="font-medium text-gray-900">${goal.title}</h5>
                                        <span class="text-sm font-semibold ${
                                            goal.progress_percentage >= 100 ? 'text-green-600' : 'text-indigo-600'
                                        }">${goal.progress_percentage}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                        <div class="h-2 rounded-full ${
                                            goal.progress_percentage >= 100 ? 'bg-green-600' : 'bg-indigo-600'
                                        }" style="width: ${Math.min(goal.progress_percentage, 100)}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-500">
                                        <span>Today: ${goal.today_time_formatted}</span>
                                        <span>${goal.days_completed}/${goal.target_days} days</span>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : '<p class="text-gray-500 text-center py-4">No active learning goals</p>'}
            </div>
        `;
    }

    formatRelativeTime(isoString) {
        const date = new Date(isoString);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000); // seconds

        if (diff < 60) return 'just now';
        if (diff < 3600) return `${Math.floor(diff / 60)} mins ago`;
        if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
        return `${Math.floor(diff / 86400)} days ago`;
    }

    updateLastUpdated() {
        this.lastUpdated = new Date();
        const timeStr = this.lastUpdated.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        document.getElementById('lastUpdated').textContent = timeStr;
    }

    startAutoRefresh() {
        this.autoRefreshInterval = setInterval(() => this.refresh(), 30000); // 30 seconds
    }

    stopAutoRefresh() {
        if (this.autoRefreshInterval) {
            clearInterval(this.autoRefreshInterval);
            this.autoRefreshInterval = null;
        }
    }

    async refresh() {
        const btn = document.getElementById('refreshBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        
        await Promise.all([
            this.loadClassSummary(),
            this.loadStudents()
        ]);
        
        btn.disabled = false;
        btn.innerHTML = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
    }
}

// Initialize on page load
let studentMonitor;
document.addEventListener('DOMContentLoaded', function() {
    studentMonitor = new StudentProgressMonitor();
    studentMonitor.init();
});

// Close modal function (global)
function closeStudentModal() {
    document.getElementById('studentModal').classList.add('hidden');
}
</script>
@endpush
@endsection
