@extends('layouts.app')

@section('page-title', 'Learning Goals')

@section('content')
<div class="px-4">
    <div class="space-y-4">
        <!-- Success Message -->
        @if (session('success'))
            <x-alert variant="success">
                {{ session('success') }}
            </x-alert>
        @endif

        <!-- Page Header -->
        <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-2xl p-5 text-black shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-xl font-bold">My Learning Goals 🎯</h1>
                    <p class="text-gray-100 text-sm mt-1">Set and track your learning objectives</p>
                </div>
                <button onclick="openCreateGoalModal()" 
                        class="bg-white text-gray-600 px-4 py-2 rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition-all active:scale-95">
                    <span class="text-lg mr-1">+</span> New Goal
                </button>
            </div>

            <!-- Stats Grid -->
            {{-- <div class="grid grid-cols-4 gap-2">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3 text-center">
                    <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                    <div class="text-xs text-purple-100">Total</div>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3 text-center">
                    <div class="text-2xl font-bold">{{ $stats['active'] }}</div>
                    <div class="text-xs text-purple-100">Active</div>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3 text-center">
                    <div class="text-2xl font-bold">{{ $stats['completed'] }}</div>
                    <div class="text-xs text-purple-100">Completed</div>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3 text-center">
                    <div class="text-2xl font-bold">{{ $stats['abandoned'] }}</div>
                    <div class="text-xs text-purple-100">Abandoned</div>
                </div>
            </div> --}}
        </div>

        <!-- Goals List -->
        @if($goals->isEmpty())
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🎯</div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Learning Goals Yet</h3>
                <p class="text-gray-500 text-sm mb-6">Start by creating your first learning goal!</p>
                <button onclick="openCreateGoalModal()" 
                        class="bg-gradient-to-r from-purple-600 to-purple-700 text-black px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all active:scale-95">
                    Create Your First Goal
                </button>
            </div>
        @else
            <!-- Goals Cards -->
            <div class="space-y-3">
                @foreach($goals as $goal)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-semibold text-gray-800">{{ $goal->title }}</h3>
                                <!-- Priority Badge -->
                                @if($goal->priority === 'high')
                                    <span class="px-2 py-0.5 bg-red-100 text-red-600 text-xs font-semibold rounded-full">High</span>
                                @elseif($goal->priority === 'medium')
                                    <span class="px-2 py-0.5 bg-orange-100 text-orange-600 text-xs font-semibold rounded-full">Medium</span>
                                @else
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Low</span>
                                @endif
                            </div>
                            
                            @if($goal->description)
                                <p class="text-sm text-gray-600 mb-2">{{ $goal->description }}</p>
                            @endif
                            
                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                <!-- Category -->
                                <span class="flex items-center gap-1">
                                    @if($goal->category === 'knowledge')
                                        📚
                                    @elseif($goal->category === 'skill')
                                        🛠️
                                    @elseif($goal->category === 'career')
                                        💼
                                    @elseif($goal->category === 'personal')
                                        🌱
                                    @elseif($goal->category === 'academic')
                                        🎓
                                    @else
                                        📌
                                    @endif
                                    <span class="capitalize">{{ $goal->category }}</span>
                                </span>
                                
                                <!-- Target Date -->
                                @if($goal->target_date)
                                    <span class="flex items-center gap-1">
                                        📅 {{ \Carbon\Carbon::parse($goal->target_date)->format('d M Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Status Badge -->
                        <div>
                            @if($goal->status === 'completed')
                                <span class="px-3 py-1 bg-green-100 text-green-600 text-xs font-semibold rounded-full">✓ Completed</span>
                            @elseif($goal->status === 'active')
                                <span class="px-3 py-1 bg-blue-100 text-blue-600 text-xs font-semibold rounded-full">Active</span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Abandoned</span>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-3">
                        <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                            <span>Progress</span>
                            <span class="font-semibold">{{ $goal->progress_percentage ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all" 
                                 style="width: {{ $goal->progress_percentage ?? 0 }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                        <!-- View Details -->
                        <a href="{{ route('learning-goals.show', $goal) }}" 
                           class="flex-1 text-center py-2 bg-purple-50 text-purple-600 rounded-lg text-sm font-semibold hover:bg-purple-100 transition-colors active:scale-95">
                            View Details & Milestones
                        </a>
                        
                        <!-- Mark Complete -->
                        @if($goal->status !== 'completed')
                            <form action="{{ route('learning-goals.update-status', $goal) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" 
                                        class="px-4 py-2 bg-green-50 text-green-600 rounded-lg text-sm font-semibold hover:bg-green-100 transition-colors active:scale-95">
                                    ✓
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
    <!-- End space-y-4 -->

    <!-- Create/Edit Goal Modal -->
    <div id="goalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <form id="goalForm" method="POST" action="{{ route('learning-goals.store') }}">
                @csrf
                <div id="methodField"></div>
                
                <div class="p-6">
                    <h2 id="modalTitle" class="text-xl font-bold text-gray-800 mb-4">Create New Goal</h2>
                    
                    <div class="space-y-4">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Goal Title *</label>
                            <input type="text" name="title" id="goalTitle" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="e.g., Complete JavaScript Course">
                        </div>
                        
                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="goalDescription" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                      placeholder="What do you want to achieve?"></textarea>
                        </div>
                        
                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                            <select name="category" id="goalCategory" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="skill">🛠️ Skill</option>
                                <option value="knowledge">📚 Knowledge</option>
                                <option value="career">💼 Career</option>
                                <option value="personal">🌱 Personal</option>
                                <option value="academic">🎓 Academic</option>
                            </select>
                        </div>
                        
                        <!-- Priority -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                            <select name="priority" id="goalPriority" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        
                        <!-- Target Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Target Date</label>
                            <input type="date" name="target_date" id="goalTargetDate"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        </div>

                        <!-- Daily Study Target (Optional) -->
                        <div class="border-t border-gray-200 pt-4">
                            <label class="flex items-center gap-2 mb-3">
                                <input type="checkbox" id="enableDailyTarget" class="rounded" onchange="toggleDailyTarget()">
                                <span class="text-sm font-medium text-gray-700">⏱️ Set Daily Study Target</span>
                            </label>
                            <div id="dailyTargetFields" class="hidden space-y-3">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Minutes/Day</label>
                                        <input type="number" name="daily_target_minutes" id="dailyTargetMinutes" min="1"
                                               placeholder="30"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Total Days</label>
                                        <input type="number" name="target_days" id="targetDays" min="1"
                                               placeholder="90"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500">Auto-tracked dari journal entries</p>
                            </div>
                        </div>

                        <!-- Milestones Builder -->
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-sm font-medium text-gray-700">🎯 Milestones (Optional)</label>
                                <button type="button" onclick="addMilestone()" 
                                        class="text-sm text-purple-600 font-medium hover:text-purple-700">
                                    + Add Milestone
                                </button>
                            </div>
                            <div id="milestonesContainer" class="space-y-2">
                                <!-- Milestones will be added here dynamically -->
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Tambahkan checkpoint untuk track progress (4-6 milestone disarankan)</p>
                        </div>

                        <!-- Final Completion (Required) -->
                        <div class="border-t border-gray-200 pt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                🎓 Final Completion (Required - Choose One)
                            </label>
                            <div class="space-y-3">
                                <!-- Option 1: Final Project -->
                                <label class="flex items-start gap-3 p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-400 transition-all has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50">
                                    <input type="radio" name="completion_type" value="final_project" class="mt-1" required onchange="toggleCompletionType('final_project')">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-800 mb-1">📁 Final Project</div>
                                        <p class="text-xs text-gray-600">Create a tangible project (portfolio, app, presentation, etc.)</p>
                                    </div>
                                </label>
                                
                                <!-- Option 2: Final Assessment -->
                                <label class="flex items-start gap-3 p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-400 transition-all has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50">
                                    <input type="radio" name="completion_type" value="final_assessment" class="mt-1" required onchange="toggleCompletionType('final_assessment')">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-800 mb-1">📝 Final Assessment</div>
                                        <p class="text-xs text-gray-600">Reflection questionnaire (SPSDL principle)</p>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- Final Project Fields -->
                            <div id="finalProjectFields" class="hidden mt-4 space-y-3 p-4 bg-purple-50 rounded-lg">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Project Title</label>
                                    <input type="text" name="final_project_title" id="finalProjectTitle"
                                           placeholder="e.g., Portfolio Website"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Project Description</label>
                                    <textarea name="final_project_description" id="finalProjectDescription" rows="2"
                                              placeholder="Describe your final project..."
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"></textarea>
                                </div>
                                <p class="text-xs text-gray-500">File dan URL bisa diupload nanti saat submit project</p>
                            </div>
                            
                            <!-- Final Assessment Info -->
                            <div id="finalAssessmentInfo" class="hidden mt-4 p-4 bg-blue-50 rounded-lg">
                                <p class="text-xs text-gray-600">
                                    Anda akan menjawab 4 pertanyaan refleksi saat menyelesaikan goal:
                                </p>
                                <ul class="text-xs text-gray-600 mt-2 space-y-1 list-disc list-inside">
                                    <li>Apa yang telah Anda pelajari?</li>
                                    <li>Bagaimana Anda akan mengaplikasikannya?</li>
                                    <li>Tantangan yang dihadapi dan solusinya</li>
                                    <li>Langkah selanjutnya dalam pembelajaran</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Status (only for edit) -->
                        <div id="statusField" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="goalStatus"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                                <option value="abandoned">Abandoned</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Modal Actions -->
                    <div class="flex gap-3 mt-6">
                        <button type="button" onclick="closeGoalModal()"
                                class="flex-1 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors active:scale-95">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-black rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all active:scale-95">
                            Save Goal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <script>
        function openCreateGoalModal() {
            document.getElementById('modalTitle').textContent = 'Create New Goal';
            document.getElementById('goalForm').action = '{{ route('learning-goals.store') }}';
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('statusField').classList.add('hidden');
            
            // Reset form
            document.getElementById('goalForm').reset();
            
            document.getElementById('goalModal').classList.remove('hidden');
        }
        
        function openEditGoalModal(goal) {
            document.getElementById('modalTitle').textContent = 'Edit Goal';
            document.getElementById('goalForm').action = `/learning-goals/${goal.id}`;
            document.getElementById('methodField').innerHTML = '@method("PUT")';
            document.getElementById('statusField').classList.remove('hidden');
            
            // Populate form
            document.getElementById('goalTitle').value = goal.title;
            document.getElementById('goalDescription').value = goal.description || '';
            document.getElementById('goalCategory').value = goal.category;
            document.getElementById('goalPriority').value = goal.priority;
            document.getElementById('goalTargetDate').value = goal.target_date || '';
            document.getElementById('goalStatus').value = goal.status;
            
            document.getElementById('goalModal').classList.remove('hidden');
        }
        
        function closeGoalModal() {
            document.getElementById('goalModal').classList.add('hidden');
        }
        
        // Close modals on outside click
        document.getElementById('goalModal').addEventListener('click', function(e) {
            if (e.target === this) closeGoalModal();
        });

        // Daily Target Toggle
        function toggleDailyTarget() {
            const checkbox = document.getElementById('enableDailyTarget');
            const fields = document.getElementById('dailyTargetFields');
            
            if (checkbox.checked) {
                fields.classList.remove('hidden');
                document.getElementById('dailyTargetMinutes').required = true;
                document.getElementById('targetDays').required = true;
            } else {
                fields.classList.add('hidden');
                document.getElementById('dailyTargetMinutes').required = false;
                document.getElementById('targetDays').required = false;
                document.getElementById('dailyTargetMinutes').value = '';
                document.getElementById('targetDays').value = '';
            }
        }

        // Final Project Toggle
        function toggleFinalProject() {
            const checkbox = document.getElementById('enableFinalProject');
            const fields = document.getElementById('finalProjectFields');
            
            if (checkbox.checked) {
                fields.classList.remove('hidden');
            } else {
                fields.classList.add('hidden');
                document.getElementById('finalProjectTitle').value = '';
                document.getElementById('finalProjectDescription').value = '';
            }
        }

        // Completion Type Toggle
        function toggleCompletionType(type) {
            const projectFields = document.getElementById('finalProjectFields');
            const assessmentInfo = document.getElementById('finalAssessmentInfo');
            
            if (type === 'final_project') {
                projectFields.classList.remove('hidden');
                assessmentInfo.classList.add('hidden');
            } else if (type === 'final_assessment') {
                projectFields.classList.add('hidden');
                assessmentInfo.classList.remove('hidden');
            }
        }

        // Milestone Builder
        let milestoneCount = 0;

        function addMilestone() {
            milestoneCount++;
            const container = document.getElementById('milestonesContainer');
            
            const milestoneHTML = `
                <div id="milestone-${milestoneCount}" class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-start gap-2">
                        <div class="flex-shrink-0 w-6 h-6 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-xs font-bold mt-2">
                            ${milestoneCount}
                        </div>
                        <div class="flex-1 space-y-2">
                            <input type="text" 
                                   name="milestones[${milestoneCount - 1}][title]" 
                                   placeholder="Milestone title (required)"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                            <textarea name="milestones[${milestoneCount - 1}][description]" 
                                      rows="2"
                                      placeholder="Description (optional)"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm"></textarea>
                            <label class="flex items-center gap-2 text-xs text-gray-600">
                                <input type="checkbox" 
                                       name="milestones[${milestoneCount - 1}][requires_evidence]" 
                                       value="1"
                                       class="rounded">
                                <span>📎 Membutuhkan bukti capaian (text/file)</span>
                            </label>
                        </div>
                        <button type="button" 
                                onclick="removeMilestone(${milestoneCount})"
                                class="flex-shrink-0 text-red-500 hover:text-red-700 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', milestoneHTML);
        }

        function removeMilestone(id) {
            document.getElementById(`milestone-${id}`).remove();
        }
    </script>
</div>
@endsection
