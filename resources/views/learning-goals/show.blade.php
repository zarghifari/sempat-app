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
            <h2 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <span class="text-xl">⏱️</span>
                Daily Study Target
            </h2>
            <div class="grid grid-cols-3 gap-3">
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $learningGoal->daily_target_minutes }}</div>
                    <div class="text-xs text-gray-600">Minutes/Day</div>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $learningGoal->days_completed }}/{{ $learningGoal->target_days }}</div>
                    <div class="text-xs text-gray-600">Days Completed</div>
                </div>
                <div class="text-center p-3 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">
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

        <!-- Final Project Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-xl">📁</span>
                Final Project
            </h2>

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
                    <p class="text-gray-500 text-sm mb-4">No final project yet</p>
                    <button onclick="openFinalProjectModal()" 
                            class="bg-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                        Add Final Project
                    </button>
                </div>
            @endif
        </div>

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
<div id="finalProjectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-end sm:items-center justify-center">
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

<!-- Evidence Modal -->
<div id="evidenceModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-end sm:items-center justify-center">
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
</script>
@endsection
