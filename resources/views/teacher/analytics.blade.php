@extends('layouts.app')

@section('title', 'Analytics')

@section('page-title', 'Teaching Analytics')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 pb-24 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Overview Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Total Students</p>
                <p class="text-3xl font-bold text-blue-600">{{ $overview['total_students'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">Across all courses</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Active Courses</p>
                <p class="text-3xl font-bold text-green-600">{{ $overview['active_courses'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">Published courses</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Completion Rate</p>
                <p class="text-3xl font-bold text-purple-600">{{ number_format($overview['completion_rate'] ?? 0, 1) }}%</p>
                <p class="text-xs text-gray-500 mt-1">Average completion</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
                <p class="text-gray-600 text-sm font-medium mb-1">Total Quizzes</p>
                <p class="text-3xl font-bold text-orange-600">{{ $overview['total_quizzes'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">Quiz attempts</p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Course Performance Chart -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Course Performance</h3>
                <div class="space-y-3">
                    @forelse($coursePerformance ?? [] as $course)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700 truncate flex-1 mr-2">{{ $course['title'] }}</span>
                                <span class="text-gray-600 font-semibold">{{ $course['avg_progress'] }}%</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-purple-600 to-blue-600 h-3 rounded-full transition-all" 
                                         style="width: {{ $course['avg_progress'] }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 w-12 text-right">{{ $course['students'] }} students</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-2">📊</div>
                            <p>No course data available</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Student Progress Distribution -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Student Progress Distribution</h3>
                <div class="space-y-3">
                    @php
                        $distribution = $progressDistribution ?? [
                            ['label' => '0-25%', 'count' => 0],
                            ['label' => '26-50%', 'count' => 0],
                            ['label' => '51-75%', 'count' => 0],
                            ['label' => '76-100%', 'count' => 0],
                        ];
                        $maxCount = max(array_column($distribution, 'count')) ?: 1;
                    @endphp
                    @foreach($distribution as $range)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $range['label'] }}</span>
                                <span class="text-gray-600 font-semibold">{{ $range['count'] }} students</span>
                            </div>
                            <div class="bg-gray-200 rounded-full h-3">
                                <div class="bg-blue-600 h-3 rounded-full transition-all" 
                                     style="width: {{ ($range['count'] / $maxCount) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Quiz Performance -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Quiz Performance</h3>
            @if(isset($recentQuizzes) && count($recentQuizzes) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quiz</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attempts</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pass Rate</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentQuizzes as $quiz)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $quiz['title'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $quiz['course_title'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $quiz['attempts'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold {{ $quiz['avg_score'] >= 70 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($quiz['avg_score'], 1) }}%
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-gray-200 rounded-full h-2 max-w-[100px]">
                                                <div class="bg-green-600 h-2 rounded-full" 
                                                     style="width: {{ $quiz['pass_rate'] }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-700">{{ number_format($quiz['pass_rate'], 0) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <div class="text-4xl mb-2">✍️</div>
                    <p>No quiz data available</p>
                </div>
            @endif
        </div>

        <!-- Monthly Activity -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Monthly Activity</h3>
            <div class="grid grid-cols-7 gap-2 mb-4">
                @php
                    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    $currentMonth = date('n');
                    $monthlyData = $monthlyActivity ?? array_fill(0, 12, 0);
                    $maxActivity = max($monthlyData) ?: 1;
                @endphp
                @foreach($months as $index => $month)
                    <div class="text-center">
                        <div class="text-xs text-gray-500 mb-2">{{ $month }}</div>
                        <div class="bg-gray-200 rounded h-24 flex items-end justify-center">
                            <div class="bg-purple-600 rounded w-full transition-all" 
                                 style="height: {{ ($monthlyData[$index] / $maxActivity) * 100 }}%"
                                 title="{{ $monthlyData[$index] }} enrollments"></div>
                        </div>
                        <div class="text-xs font-medium text-gray-700 mt-1">{{ $monthlyData[$index] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Performing Students -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Top Performing Students</h3>
            @if(isset($topStudents) && count($topStudents) > 0)
                <div class="space-y-3">
                    @foreach($topStudents as $index => $student)
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-sm">
                                #{{ $index + 1 }}
                            </div>
                            @if($student['avatar'])
                                <img src="{{ asset('storage/' . $student['avatar']) }}" 
                                     alt="{{ $student['name'] }}" 
                                     class="w-10 h-10 rounded-full">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center text-white font-semibold">
                                    {{ substr($student['name'], 0, 1) }}
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ $student['name'] }}</p>
                                <p class="text-sm text-gray-600">{{ $student['email'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-green-600">{{ number_format($student['avg_progress'], 1) }}%</p>
                                <p class="text-xs text-gray-500">{{ $student['completed_courses'] }} completed</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <div class="text-4xl mb-2">🏆</div>
                    <p>No student data available</p>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
