@extends('layouts.app')

@section('page-title', $learningGoal->title)

@section('content')
<div class="px-4">
    <div class="space-y-4">
        <!-- Success Message -->
        @if (session('success'))
            <x-alert variant="success">
                {{ session('success') }}
            </x-alert>
        @endif

        <!-- Back Button -->
        <a href="{{ route('learning-goals.index') }}" 
           class="inline-flex items-center text-gray-600 hover:text-gray-800 text-sm font-medium mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Goals
        </a>

        <!-- Goal Header -->
        <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-2xl p-5 text-white shadow-lg">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <h1 class="text-xl font-bold mb-1">{{ $learningGoal->title }}</h1>
                    @if($learningGoal->description)
                        <p class="text-purple-100 text-sm">{{ $learningGoal->description }}</p>
                    @endif
                </div>
                
                <!-- Status Badge -->
                @if($learningGoal->status === 'completed')
                    <span class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">✓ Completed</span>
                @elseif($learningGoal->status === 'active')
                    <span class="px-3 py-1 bg-blue-500 text-white text-xs font-semibold rounded-full">Active</span>
                @else
                    <span class="px-3 py-1 bg-gray-500 text-white text-xs font-semibold rounded-full">Abandoned</span>
                @endif
            </div>

            <!-- Progress Bar -->
            <div class="mb-4">
                <div class="flex items-center justify-between text-sm mb-2">
                    <span>Overall Progress</span>
                    <span class="font-bold">{{ $learningGoal->progress_percentage }}%</span>
                </div>
                <div class="w-full bg-white/30 rounded-full h-3">
                    <div class="bg-white h-3 rounded-full transition-all shadow-sm" 
                         style="width: {{ $learningGoal->progress_percentage }}%"></div>
                </div>
            </div>

            <!-- Goal Info -->
            <div class="grid grid-cols-3 gap-2">
                <div class="bg-white/20 backdrop-blur-sm rounded-lg p-2 text-center">
                    <div class="text-xs text-purple-100 mb-1">Category</div>
                    <div class="font-semibold text-sm capitalize">
                        @if($learningGoal->category === 'knowledge')
                            📚 Knowledge
                        @elseif($learningGoal->category === 'skill')
                            🛠️ Skill
                        @elseif($learningGoal->category === 'career')
                            💼 Career
                        @elseif($learningGoal->category === 'personal')
                            🌱 Personal
                        @else
                            🎓 Academic
                        @endif
                    </div>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-lg p-2 text-center">
                    <div class="text-xs text-purple-100 mb-1">Priority</div>
                    <div class="font-semibold text-sm capitalize">
                        @if($learningGoal->priority === 'high')
                            🔥 High
                        @elseif($learningGoal->priority === 'medium')
                            ⚡ Medium
                        @else
                            📌 Low
                        @endif
                    </div>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-lg p-2 text-center">
                    <div class="text-xs text-purple-100 mb-1">Target Date</div>
                    <div class="font-semibold text-sm">
                        @if($learningGoal->target_date)
                            {{ \Carbon\Carbon::parse($learningGoal->target_date)->format('d M Y') }}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Target (if exists) -->
        @if($learningGoal->daily_target_minutes)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                    <span class="text-xl">⏱️</span>
                    Daily Study Target
                </h2>
                <button id="viewLogsBtn" 
                        class="text-sm text-purple-600 hover:text-purple-700 font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    View History
                </button>
            </div>

            <!-- Timer Widget -->
            <div class="mb-4 p-4 bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl border border-purple-100">
                <div class="text-center mb-3">
                    <div id="timerDisplay" class="text-4xl font-bold text-purple-700 mb-1">00:00</div>
                    <div id="timerStatus" class="text-sm text-gray-600">Ready to start</div>
                </div>
                
                <div class="flex items-center gap-2">
                    <button id="startTimerBtn" 
                            class="flex-1 py-2.5 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Start
                    </button>
                    <button id="pauseTimerBtn" 
                            class="hidden flex-1 py-2.5 bg-yellow-600 text-white rounded-lg font-semibold hover:bg-yellow-700 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pause
                    </button>
                    <button id="stopTimerBtn" 
                            class="hidden px-4 py-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-all active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                        </svg>
                    </button>
                </div>

                <!-- Progress Bar -->
                <div class="mt-3">
                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                        <span id="todayMinutes">Today: 0 min</span>
                        <span id="remainingMinutes">Remaining: {{ $learningGoal->daily_target_minutes }} min</span>
                    </div>
                    <div class="w-full bg-white rounded-full h-2">
                        <div id="todayProgress" 
                             class="bg-gradient-to-r from-purple-500 to-blue-500 h-2 rounded-full transition-all" 
                             style="width: 0%"></div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-3 gap-3">
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $learningGoal->daily_target_minutes }}</div>
                    <div class="text-xs text-gray-600">Minutes/Day</div>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <div id="daysCompletedDisplay" class="text-2xl font-bold text-green-600">{{ $learningGoal->days_completed }}/{{ $learningGoal->target_days }}</div>
                    <div class="text-xs text-gray-600">Days Completed</div>
                </div>
                <div class="text-center p-3 bg-purple-50 rounded-lg">
                    <div id="overallProgressDisplay" class="text-2xl font-bold text-purple-600">
                        {{ $learningGoal->target_days ? round(($learningGoal->days_completed / $learningGoal->target_days) * 100) : 0 }}%
                    </div>
                    <div class="text-xs text-gray-600">Progress</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Milestones Section -->
        @if($learningGoal->milestones->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-xl">🎯</span>
                Milestones
                <span class="text-xs text-gray-500 font-normal">
                    ({{ $learningGoal->milestones->where('is_completed', true)->count() }}/{{ $learningGoal->milestones->count() }} completed)
                </span>
            </h2>
            
            <div class="space-y-3">
                @foreach($learningGoal->milestones as $milestone)
                <div class="p-3 rounded-lg {{ $milestone->is_completed ? 'bg-green-50' : 'bg-gray-50' }} transition-all">
                    <div class="flex items-start gap-3 mb-2">
                        <!-- Checkbox/Action -->
                        @if($milestone->requires_evidence && !$milestone->is_completed)
                            <button onclick="openEvidenceModal({{ $milestone->id }}, '{{ $milestone->title }}')"
                                    class="flex-shrink-0 w-6 h-6 rounded-full border-2 bg-white border-gray-300 hover:border-purple-500 flex items-center justify-center transition-all active:scale-90">
                            </button>
                        @else
                            <form action="{{ route('milestones.toggle', $milestone) }}" method="POST" class="flex-shrink-0">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all active:scale-90
                                               {{ $milestone->is_completed ? 'bg-green-500 border-green-500' : 'bg-white border-gray-300 hover:border-purple-500' }}">
                                    @if($milestone->is_completed)
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </button>
                            </form>
                        @endif
                        
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-gray-800 {{ $milestone->is_completed ? 'line-through text-gray-500' : '' }}">
                                    {{ $milestone->title }}
                                </h3>
                                @if($milestone->requires_evidence)
                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-600 text-xs font-semibold rounded-full">📎 Evidence</span>
                                @endif
                            </div>
                            @if($milestone->description)
                                <p class="text-sm text-gray-600 mt-1 {{ $milestone->is_completed ? 'line-through' : '' }}">
                                    {{ $milestone->description }}
                                </p>
                            @endif
                            
                            @if($milestone->is_completed && $milestone->completed_at)
                                <p class="text-xs text-green-600 mt-1">
                                    ✓ Completed {{ \Carbon\Carbon::parse($milestone->completed_at)->diffForHumans() }}
                                </p>
                            @endif

                            <!-- Evidence Display -->
                            @if($milestone->evidence_text || $milestone->evidence_file)
                                <div class="mt-2 p-2 bg-white rounded border border-purple-200">
                                    <p class="text-xs font-semibold text-purple-600 mb-1">Bukti Capaian:</p>
                                    @if($milestone->evidence_text)
                                        <p class="text-sm text-gray-700 mb-1">{{ $milestone->evidence_text }}</p>
                                    @endif
                                    @if($milestone->evidence_file)
                                        <a href="{{ asset('storage/milestone-evidence/' . $milestone->evidence_file) }}" 
                                           target="_blank"
                                           class="text-xs text-blue-600 hover:text-blue-700 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            {{ $milestone->evidence_file }}
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        <!-- Milestone Number -->
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-xs font-bold">
                            {{ $milestone->order }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Final Completion Section -->
        @if($learningGoal->completion_type)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                @if($learningGoal->completion_type === 'final_project')
                    <span class="text-xl">📁</span>
                    Final Project
                @else
                    <span class="text-xl">📝</span>
                    Final Assessment
                @endif
            </h2>

            @if($learningGoal->completion_type === 'final_project')
                <!-- Final Project -->
                @if($learningGoal->hasFinalProject())
                    <!-- Project Details -->
                    <div class="bg-purple-50 rounded-lg p-4 mb-4">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="font-semibold text-gray-800">{{ $learningGoal->final_project_title }}</h3>
                            @if($learningGoal->isFinalProjectSubmitted())
                                <span class="px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">✓ Submitted</span>
                            @else
                                <span class="px-2 py-1 bg-orange-500 text-white text-xs font-semibold rounded-full">Draft</span>
                            @endif
                        </div>
                        
                        @if($learningGoal->final_project_description)
                            <p class="text-sm text-gray-600 mb-3">{{ $learningGoal->final_project_description }}</p>
                        @endif
                        
                        <div class="flex items-center gap-3">
                            @if($learningGoal->final_project_url)
                                <a href="{{ $learningGoal->final_project_url }}" target="_blank" 
                                   class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    View Project URL
                                </a>
                            @endif
                            
                            @if($learningGoal->final_project_file)
                                <a href="{{ asset('storage/final-projects/' . $learningGoal->final_project_file) }}" target="_blank" 
                                   class="text-sm text-purple-600 hover:text-purple-700 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download File
                                </a>
                            @endif
                        </div>
                        
                        @if($learningGoal->isFinalProjectSubmitted())
                            <p class="text-xs text-gray-500 mt-3">
                                Submitted {{ \Carbon\Carbon::parse($learningGoal->final_project_submitted_at)->diffForHumans() }}
                            </p>
                        @endif
                    </div>

                    <!-- Edit Project Button -->
                    <button onclick="openFinalProjectModal()" 
                            class="w-full py-2 bg-purple-50 text-purple-600 rounded-lg text-sm font-semibold hover:bg-purple-100 transition-colors">
                        Edit Final Project
                    </button>
                @else
                    <!-- No Project Yet -->
                    <div class="text-center py-6">
                        <div class="text-4xl mb-2">📝</div>
                        
                        @if($learningGoal->canSubmitFinalCompletion())
                            <p class="text-gray-500 text-sm mb-4">No final project yet</p>
                            <button onclick="openFinalProjectModal()" 
                                    class="bg-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                                Add Final Project
                            </button>
                        @else
                            <p class="text-orange-600 text-sm mb-2 font-medium">⚠️ Prerequisites Required</p>
                            <p class="text-gray-500 text-xs mb-4 max-w-xs mx-auto">
                                Complete all milestones and daily target before submitting final project
                            </p>
                            <button disabled 
                                    class="bg-gray-300 text-gray-500 px-6 py-2 rounded-lg font-semibold cursor-not-allowed opacity-60">
                                Add Final Project
                            </button>
                            
                            <!-- Progress Checklist -->
                            <div class="mt-4 text-left max-w-xs mx-auto bg-orange-50 rounded-lg p-3">
                                <p class="text-xs font-semibold text-gray-700 mb-2">Requirements:</p>
                                <ul class="space-y-1 text-xs">
                                    @if($learningGoal->milestones()->count() > 0)
                                        <li class="flex items-center gap-2 {{ $learningGoal->milestones()->where('is_completed', true)->count() === $learningGoal->milestones()->count() ? 'text-green-600' : 'text-orange-600' }}">
                                            @if($learningGoal->milestones()->where('is_completed', true)->count() === $learningGoal->milestones()->count())
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                            <span>Milestones: {{ $learningGoal->milestones()->where('is_completed', true)->count() }}/{{ $learningGoal->milestones()->count() }}</span>
                                        </li>
                                    @endif
                                    
                                    @if($learningGoal->daily_target_minutes && $learningGoal->target_days)
                                        <li class="flex items-center gap-2 {{ $learningGoal->days_completed >= $learningGoal->target_days ? 'text-green-600' : 'text-orange-600' }}">
                                            @if($learningGoal->days_completed >= $learningGoal->target_days)
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                            <span>Daily Target: {{ $learningGoal->days_completed }}/{{ $learningGoal->target_days }} days</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            @else
                <!-- Final Assessment -->
                @if($learningGoal->isAssessmentSubmitted())
                    <!-- Assessment Results -->
                    <div class="bg-blue-50 rounded-lg p-4 mb-4 space-y-4">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="font-semibold text-gray-800">Assessment Completed</h3>
                            <span class="px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">✓ Submitted</span>
                        </div>
                        
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-600 mb-1">Apa yang telah Anda pelajari?</p>
                                <p class="text-sm text-gray-700">{{ $learningGoal->assessment_what_learned }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-600 mb-1">Bagaimana Anda akan mengaplikasikannya?</p>
                                <p class="text-sm text-gray-700">{{ $learningGoal->assessment_how_applied }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-600 mb-1">Tantangan yang dihadapi dan solusinya</p>
                                <p class="text-sm text-gray-700">{{ $learningGoal->assessment_challenges }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-600 mb-1">Langkah selanjutnya dalam pembelajaran</p>
                                <p class="text-sm text-gray-700">{{ $learningGoal->assessment_next_steps }}</p>
                            </div>
                        </div>
                        
                        <p class="text-xs text-gray-500 mt-3 pt-3 border-t border-blue-200">
                            Submitted {{ \Carbon\Carbon::parse($learningGoal->assessment_submitted_at)->diffForHumans() }}
                        </p>
                    </div>
                @else
                    <!-- No Assessment Yet -->
                    <div class="text-center py-6">
                        <div class="text-4xl mb-2">📋</div>
                        
                        @if($learningGoal->canSubmitFinalCompletion())
                            <p class="text-gray-500 text-sm mb-4">Complete your learning reflection</p>
                            <button onclick="openAssessmentModal()" 
                                    class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                Start Assessment
                            </button>
                        @else
                            <p class="text-orange-600 text-sm mb-2 font-medium">⚠️ Prerequisites Required</p>
                            <p class="text-gray-500 text-xs mb-4 max-w-xs mx-auto">
                                Complete all milestones and daily target before starting assessment
                            </p>
                            <button disabled 
                                    class="bg-gray-300 text-gray-500 px-6 py-2 rounded-lg font-semibold cursor-not-allowed opacity-60">
                                Start Assessment
                            </button>
                            
                            <!-- Progress Checklist -->
                            <div class="mt-4 text-left max-w-xs mx-auto bg-orange-50 rounded-lg p-3">
                                <p class="text-xs font-semibold text-gray-700 mb-2">Requirements:</p>
                                <ul class="space-y-1 text-xs">
                                    @if($learningGoal->milestones()->count() > 0)
                                        <li class="flex items-center gap-2 {{ $learningGoal->milestones()->where('is_completed', true)->count() === $learningGoal->milestones()->count() ? 'text-green-600' : 'text-orange-600' }}">
                                            @if($learningGoal->milestones()->where('is_completed', true)->count() === $learningGoal->milestones()->count())
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                            <span>Milestones: {{ $learningGoal->milestones()->where('is_completed', true)->count() }}/{{ $learningGoal->milestones()->count() }}</span>
                                        </li>
                                    @endif
                                    
                                    @if($learningGoal->daily_target_minutes && $learningGoal->target_days)
                                        <li class="flex items-center gap-2 {{ $learningGoal->days_completed >= $learningGoal->target_days ? 'text-green-600' : 'text-orange-600' }}">
                                            @if($learningGoal->days_completed >= $learningGoal->target_days)
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                            <span>Daily Target: {{ $learningGoal->days_completed }}/{{ $learningGoal->target_days }} days</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            @endif
        </div>
        @endif

        <!-- Related Journal Entries -->
        @if($learningGoal->journalEntries->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-xl">📔</span>
                Related Journal Entries
                <span class="text-xs text-gray-500 font-normal">({{ $learningGoal->journalEntries->count() }})</span>
            </h2>
            
            <div class="space-y-2">
                @foreach($learningGoal->journalEntries->take(5) as $entry)
                <a href="{{ route('learning-journal.index') }}" 
                   class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-800 text-sm">{{ $entry->title }}</h3>
                            @if($entry->study_duration_minutes)
                                <p class="text-xs text-gray-500 mt-1">⏱️ {{ $entry->study_duration_minutes }} minutes</p>
                            @endif
                        </div>
                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($entry->entry_date)->format('d M') }}</span>
                    </div>
                </a>
                @endforeach
            </div>
            
            @if($learningGoal->journalEntries->count() > 5)
                <a href="{{ route('learning-journal.index') }}" 
                   class="block text-center text-sm text-purple-600 font-medium mt-3 hover:text-purple-700">
                    View All Entries →
                </a>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- Final Project Modal -->
<div id="finalProjectModal" class="hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end sm:items-center justify-center">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl w-full sm:max-w-lg max-h-[90vh] overflow-y-auto">
            <form action="{{ route('learning-goals.final-project', $learningGoal) }}" method="POST" enctype="multipart/form-data">
                @csrf
            
            <div class="sticky top-0 bg-white border-b border-gray-100 px-5 py-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800">Final Project</h2>
                <button type="button" onclick="closeFinalProjectModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="p-5 space-y-4">
                <!-- Project Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project Title *</label>
                    <input type="text" name="final_project_title" 
                           value="{{ $learningGoal->final_project_title }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                           required>
                </div>
                
                <!-- Project Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="final_project_description" rows="3"
                              class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ $learningGoal->final_project_description }}</textarea>
                </div>
                
                <!-- Project URL -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project URL (optional)</label>
                    <input type="url" name="final_project_url" 
                           value="{{ $learningGoal->final_project_url }}"
                           placeholder="https://github.com/yourname/project"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                
                <!-- Project File -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload File (optional, max 10MB)</label>
                    @if($learningGoal->final_project_file)
                        <p class="text-sm text-gray-600 mb-2">
                            Current file: <span class="font-medium">{{ $learningGoal->final_project_file }}</span>
                        </p>
                    @endif
                    <input type="file" name="final_project_file" 
                           accept=".pdf,.doc,.docx,.zip,.rar"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Accepted: PDF, DOC, DOCX, ZIP, RAR</p>
                </div>
            </div>
            
            <div class="sticky bottom-0 bg-gray-50 px-5 py-4 flex gap-3">
                <button type="button" onclick="closeFinalProjectModal()" 
                        class="flex-1 py-2 px-4 bg-white border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 py-2 px-4 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                    Submit Project
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Final Assessment Modal -->
<div id="assessmentModal" class="hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end sm:items-center justify-center">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl w-full sm:max-w-2xl max-h-[90vh] overflow-y-auto">
            <form action="{{ route('learning-goals.submit-assessment', $learningGoal) }}" method="POST" id="assessmentForm">
                @csrf
            
            <div class="sticky top-0 bg-white border-b border-gray-100 px-5 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Final Assessment</h2>
                    <p class="text-xs text-gray-500">Refleksi pembelajaran SPSDL</p>
                </div>
                <button type="button" onclick="closeAssessmentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="p-5 space-y-6">
                <!-- Question 1 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        1. Apa yang telah Anda pelajari? *
                    </label>
                    <p class="text-xs text-gray-500 mb-2">Jelaskan pengetahuan atau skill utama yang Anda peroleh</p>
                    <textarea name="assessment_what_learned" rows="4" required
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Contoh: Saya telah mempelajari konsep React Hooks dan bagaimana menggunakannya untuk state management..."></textarea>
                </div>

                <!-- Question 2 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        2. Bagaimana Anda akan mengaplikasikannya? *
                    </label>
                    <p class="text-xs text-gray-500 mb-2">Jelaskan rencana implementasi dalam project atau pekerjaan</p>
                    <textarea name="assessment_how_applied" rows="4" required
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Contoh: Saya akan menggunakan React Hooks untuk refactor aplikasi dashboard yang sedang saya kembangkan..."></textarea>
                </div>

                <!-- Question 3 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        3. Tantangan yang dihadapi dan solusinya *
                    </label>
                    <p class="text-xs text-gray-500 mb-2">Jelaskan kesulitan yang dialami dan bagaimana mengatasinya</p>
                    <textarea name="assessment_challenges" rows="4" required
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Contoh: Kesulitan memahami useEffect lifecycle. Saya mengatasinya dengan membaca dokumentasi dan membuat diagram flow..."></textarea>
                </div>

                <!-- Question 4 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        4. Langkah selanjutnya dalam pembelajaran *
                    </label>
                    <p class="text-xs text-gray-500 mb-2">Apa yang akan Anda pelajari atau tingkatkan selanjutnya?</p>
                    <textarea name="assessment_next_steps" rows="4" required
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Contoh: Saya akan mempelajari advanced React patterns seperti Context API dan custom hooks..."></textarea>
                </div>
            </div>
            
            <div class="sticky bottom-0 bg-gray-50 px-5 py-4 flex gap-3">
                <button type="button" onclick="closeAssessmentModal()" 
                        class="flex-1 py-2 px-4 bg-white border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 py-2 px-4 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Submit Assessment
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Evidence Modal -->
<div id="evidenceModal" class="hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end sm:items-center justify-center">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl w-full sm:max-w-lg max-h-[90vh] overflow-y-auto">
            <form id="evidenceForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
            
            <div class="sticky top-0 bg-white border-b border-gray-100 px-5 py-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800">Submit Bukti Capaian</h2>
                <button type="button" onclick="closeEvidenceModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="p-5 space-y-4">
                <div class="bg-purple-50 rounded-lg p-3">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold">Milestone:</span> 
                        <span id="evidenceMilestoneTitle"></span>
                    </p>
                </div>

                <!-- Evidence Text -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Capaian <span class="text-red-500">*</span>
                    </label>
                    <textarea name="evidence_text" rows="4" required
                              placeholder="Jelaskan apa yang sudah kamu pelajari/capai untuk milestone ini..."
                              class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Min. 50 karakter</p>
                </div>
                
                <!-- Evidence File -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Upload File (optional, max 5MB)
                    </label>
                    <input type="file" name="evidence_file" 
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Accepted: PDF, DOC, Image, ZIP</p>
                </div>

                <div class="bg-blue-50 rounded-lg p-3">
                    <p class="text-xs text-gray-600">
                        💡 <strong>Tips:</strong> Berikan bukti konkrit seperti screenshot, code snippet, atau penjelasan detail tentang apa yang sudah kamu pelajari.
                    </p>
                </div>
            </div>
            
            <div class="sticky bottom-0 bg-gray-50 px-5 py-4 flex gap-3">
                <button type="button" onclick="closeEvidenceModal()" 
                        class="flex-1 py-2 px-4 bg-white border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 py-2 px-4 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors">
                    ✓ Submit & Complete
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

<script>
function openFinalProjectModal() {
    document.getElementById('finalProjectModal').classList.remove('hidden');
}

function closeFinalProjectModal() {
    document.getElementById('finalProjectModal').classList.add('hidden');
}

function openEvidenceModal(milestoneId, milestoneTitle) {
    document.getElementById('evidenceForm').action = `/milestones/${milestoneId}/toggle`;
    document.getElementById('evidenceMilestoneTitle').textContent = milestoneTitle;
    document.getElementById('evidenceModal').classList.remove('hidden');
}

function closeEvidenceModal() {
    document.getElementById('evidenceModal').classList.add('hidden');
    document.getElementById('evidenceForm').reset();
}

// Close modal on backdrop click
document.getElementById('finalProjectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFinalProjectModal();
    }
});

document.getElementById('evidenceModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEvidenceModal();
    }
});

// Study Timer Functionality
@if($learningGoal->daily_target_minutes)
let studyTimer = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize timer
    studyTimer = new StudyTimer(
        {{ $learningGoal->id }},
        "{{ $learningGoal->title }}",
        {{ $learningGoal->daily_target_minutes }}
    );

    // Get UI elements
    const startBtn = document.getElementById('startTimerBtn');
    const pauseBtn = document.getElementById('pauseTimerBtn');
    const stopBtn = document.getElementById('stopTimerBtn');
    const viewLogsBtn = document.getElementById('viewLogsBtn');
    const timerDisplay = document.getElementById('timerDisplay');
    const timerStatus = document.getElementById('timerStatus');
    const todayMinutes = document.getElementById('todayMinutes');
    const remainingMinutes = document.getElementById('remainingMinutes');
    const todayProgress = document.getElementById('todayProgress');
    const daysCompletedDisplay = document.getElementById('daysCompletedDisplay');
    const overallProgressDisplay = document.getElementById('overallProgressDisplay');

    // Fetch current status on load
    studyTimer.fetchStatus().then(data => {
        updateTodayStats(data);
    }).catch(err => {
        console.error('Failed to load status:', err);
    });

    // Button click handlers
    startBtn.addEventListener('click', function() {
        studyTimer.start();
        startBtn.classList.add('hidden');
        pauseBtn.classList.remove('hidden');
        stopBtn.classList.remove('hidden');
    });

    pauseBtn.addEventListener('click', function() {
        studyTimer.pause();
        pauseBtn.classList.add('hidden');
        startBtn.classList.remove('hidden');
    });

    stopBtn.addEventListener('click', async function() {
        if (confirm('Save and stop the timer? Your progress will be recorded.')) {
            await studyTimer.stop();
            startBtn.classList.remove('hidden');
            pauseBtn.classList.add('hidden');
            stopBtn.classList.add('hidden');
        }
    });

    viewLogsBtn.addEventListener('click', function() {
        showSessionLogs();
    });

    // Listen to timer events
    window.addEventListener('studyTimer:update', function(e) {
        const data = e.detail;
        timerDisplay.textContent = data.formattedTime;
        
        if (data.isRunning) {
            timerStatus.textContent = `${data.elapsedMinutes} of ${studyTimer.dailyTargetMinutes} minutes completed`;
        } else {
            timerStatus.textContent = data.elapsedMinutes > 0 ? 'Paused' : 'Ready to start';
        }

        // Update today's progress (combine saved + current session)
        const totalMinutesToday = (window.savedMinutesToday || 0) + data.elapsedMinutes;
        todayMinutes.textContent = `Today: ${totalMinutesToday} min`;
        remainingMinutes.textContent = `Remaining: ${Math.max(0, studyTimer.dailyTargetMinutes - totalMinutesToday)} min`;
        
        const progressPercent = Math.min(100, (totalMinutesToday / studyTimer.dailyTargetMinutes) * 100);
        todayProgress.style.width = progressPercent + '%';

        // Check if target reached
        if (totalMinutesToday >= studyTimer.dailyTargetMinutes && !window.targetReachedShown) {
            window.targetReachedShown = true;
            showTargetReachedCelebration();
        }
    });

    window.addEventListener('studyTimer:saved', function(e) {
        const data = e.detail;
        window.savedMinutesToday = data.total_minutes_today;
        
        // Update days completed if target reached
        if (data.target_reached) {
            const newDaysCompleted = data.days_completed;
            daysCompletedDisplay.textContent = `${newDaysCompleted}/{{ $learningGoal->target_days }}`;
            
            const newOverallProgress = Math.round((newDaysCompleted / {{ $learningGoal->target_days }}) * 100);
            overallProgressDisplay.textContent = newOverallProgress + '%';
        }

        // Show save notification
        showNotification('Progress saved!', 'success');
    });

    window.addEventListener('studyTimer:error', function(e) {
        showNotification(e.detail.message, 'error');
    });

    window.addEventListener('studyTimer:started', function() {
        window.targetReachedShown = false;
        timerStatus.textContent = 'Timer running...';
    });

    window.addEventListener('studyTimer:stopped', function() {
        timerStatus.textContent = 'Session saved';
        // Reload status
        studyTimer.fetchStatus().then(data => {
            updateTodayStats(data);
        });
    });

    // If timer was running, resume UI and restart interval
    if (studyTimer.isRunning) {
        startBtn.classList.add('hidden');
        pauseBtn.classList.remove('hidden');
        stopBtn.classList.remove('hidden');
        
        // Restart the interval to continue ticking
        studyTimer.timerInterval = setInterval(() => {
            studyTimer.elapsedSeconds++;
            studyTimer.saveToStorage();
            studyTimer.updateUI();
            studyTimer.checkDayChange();
        }, 1000);
        
        // Restart auto-save interval
        studyTimer.autoSaveInterval = setInterval(() => {
            if (studyTimer.elapsedSeconds > 0) {
                studyTimer.saveToBackend(studyTimer.date, Math.floor(studyTimer.elapsedSeconds / 60));
            }
        }, 5 * 60 * 1000);
        
        studyTimer.updateUI();
    }
});

function updateTodayStats(data) {
    window.savedMinutesToday = data.today_minutes || 0;
    document.getElementById('todayMinutes').textContent = `Today: ${data.today_minutes || 0} min`;
    document.getElementById('remainingMinutes').textContent = `Remaining: ${data.remaining_minutes || 0} min`;
    
    const progressPercent = Math.min(100, ((data.today_minutes || 0) / {{ $learningGoal->daily_target_minutes }}) * 100);
    document.getElementById('todayProgress').style.width = progressPercent + '%';
    
    if (data.target_reached) {
        document.getElementById('timerStatus').textContent = '✓ Target reached today!';
    }
}

function showSessionLogs() {
    studyTimer.fetchLogs().then(data => {
        const logs = data.session_logs || {};
        const entries = Object.entries(logs).sort((a, b) => b[0].localeCompare(a[0])); // Sort by date desc
        
        let html = `
            <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="this.remove()">
                <div class="bg-white rounded-xl max-w-2xl w-full max-h-[80vh] overflow-hidden" onclick="event.stopPropagation()">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-800">Study Session History</h3>
                            <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 overflow-y-auto max-h-[60vh]">
                        ${entries.length === 0 ? '<p class="text-center text-gray-500 py-8">No study sessions yet. Start your timer to track progress!</p>' : ''}
                        <div class="space-y-2">
        `;
        
        entries.forEach(([date, minutes]) => {
            const targetReached = minutes >= {{ $learningGoal->daily_target_minutes }};
            html += `
                <div class="flex items-center justify-between p-3 rounded-lg ${targetReached ? 'bg-green-50 border border-green-200' : 'bg-gray-50'}">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full ${targetReached ? 'bg-green-500' : 'bg-gray-400'} flex items-center justify-center text-white font-bold">
                            ${targetReached ? '✓' : minutes}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">${formatDate(date)}</div>
                            <div class="text-sm text-gray-600">${minutes} minutes studied</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold ${targetReached ? 'text-green-600' : 'text-gray-600'}">
                            ${Math.round((minutes / {{ $learningGoal->daily_target_minutes }}) * 100)}%
                        </div>
                        ${targetReached ? '<div class="text-xs text-green-600">Target met!</div>' : ''}
                    </div>
                </div>
            `;
        });
        
        html += `
                        </div>
                    </div>
                    <div class="p-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Days: ${entries.length}</span>
                            <span class="text-gray-600">Days Completed: ${data.days_completed} / ${data.target_days}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', html);
    }).catch(err => {
        showNotification('Failed to load history', 'error');
    });
}

function formatDate(dateStr) {
    const date = new Date(dateStr + 'T00:00:00');
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    
    if (date.toDateString() === today.toDateString()) {
        return 'Today';
    } else if (date.toDateString() === yesterday.toDateString()) {
        return 'Yesterday';
    } else {
        return date.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
    }
}

function showTargetReachedCelebration() {
    const celebration = document.createElement('div');
    celebration.className = 'fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4';
    celebration.innerHTML = `
        <div class="bg-white rounded-2xl p-8 max-w-md text-center animate-bounce">
            <div class="text-6xl mb-4">🎉</div>
            <h3 class="text-2xl font-bold text-green-600 mb-2">Target Reached!</h3>
            <p class="text-gray-600 mb-4">You've completed your daily study target. Great job!</p>
            <button onclick="this.closest('.fixed').remove()" 
                    class="px-6 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700">
                Awesome!
            </button>
        </div>
    `;
    document.body.appendChild(celebration);
    
    setTimeout(() => {
        celebration.remove();
    }, 5000);
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white font-medium animate-slide-in`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Save timer on page unload
window.addEventListener('beforeunload', function() {
    if (studyTimer && studyTimer.isRunning) {
        studyTimer.saveToStorage();
    }
});
@endif

function openAssessmentModal() {
    document.getElementById('assessmentModal').classList.remove('hidden');
}

function closeAssessmentModal() {
    document.getElementById('assessmentModal').classList.add('hidden');
}

// Close assessment modal on backdrop click
document.getElementById('assessmentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAssessmentModal();
    }
});
</script>
@endsection
