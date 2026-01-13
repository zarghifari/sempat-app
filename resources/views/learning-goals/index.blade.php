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
                    @if($goal->progress_percentage > 0)
                        <div class="mb-3">
                            <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                <span>Progress</span>
                                <span class="font-semibold">{{ $goal->progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all" 
                                     style="width: {{ $goal->progress_percentage }}%"></div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Actions -->
                    <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                        <!-- Update Progress -->
                        @if($goal->status !== 'completed' && $goal->status !== 'abandoned')
                            <button onclick="openProgressModal({{ $goal->id }}, {{ $goal->progress_percentage }})"  
                                    class="flex-1 text-center py-2 bg-purple-50 text-gray-600 rounded-lg text-sm font-semibold hover:bg-purple-100 transition-colors active:scale-95">
                                Update Progress
                            </button>
                        @endif
                        
                        <!-- Mark Complete -->
                        @if($goal->status !== 'completed')
                            <form action="{{ route('learning-goals.update-status', $goal) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" 
                                        class="w-full py-2 bg-green-50 text-green-600 rounded-lg text-sm font-semibold hover:bg-green-100 transition-colors active:scale-95">
                                    Mark Complete
                                </button>
                            </form>
                        @endif
                        
                        <!-- Edit -->
                        <button onclick="openEditGoalModal({{ json_encode($goal) }})" 
                                class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg text-sm transition-colors active:scale-95">
                            Edit
                        </button>
                        
                        <!-- Delete -->
                        <form action="{{ route('learning-goals.destroy', $goal) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this goal?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg text-sm transition-colors active:scale-95">
                                Delete
                            </button>
                        </form>
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
                        
                        <!-- Progress (only for edit) -->
                        <div id="progressField" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Progress (%)</label>
                            <input type="number" name="progress_percentage" id="goalProgress" min="0" max="100"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
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

    <!-- Progress Update Modal -->
    <div id="progressModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full">
            <form id="progressForm" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Update Progress</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Progress (%)</label>
                            <input type="range" name="progress_percentage" id="progressSlider" min="0" max="100" value="0"
                                   class="w-full"
                                   oninput="document.getElementById('progressValue').textContent = this.value + '%'">
                            <div class="text-center mt-2">
                                <span id="progressValue" class="text-2xl font-bold text-gray-600">0%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 mt-6">
                        <button type="button" onclick="closeProgressModal()"
                                class="flex-1 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors active:scale-95">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-black rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all active:scale-95">
                            Update
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
            document.getElementById('progressField').classList.add('hidden');
            
            // Reset form
            document.getElementById('goalForm').reset();
            
            document.getElementById('goalModal').classList.remove('hidden');
        }
        
        function openEditGoalModal(goal) {
            document.getElementById('modalTitle').textContent = 'Edit Goal';
            document.getElementById('goalForm').action = `/learning-goals/${goal.id}`;
            document.getElementById('methodField').innerHTML = '@method("PUT")';
            document.getElementById('statusField').classList.remove('hidden');
            document.getElementById('progressField').classList.remove('hidden');
            
            // Populate form
            document.getElementById('goalTitle').value = goal.title;
            document.getElementById('goalDescription').value = goal.description || '';
            document.getElementById('goalCategory').value = goal.category;
            document.getElementById('goalPriority').value = goal.priority;
            document.getElementById('goalTargetDate').value = goal.target_date || '';
            document.getElementById('goalStatus').value = goal.status;
            document.getElementById('goalProgress').value = goal.progress_percentage || 0;
            
            document.getElementById('goalModal').classList.remove('hidden');
        }
        
        function closeGoalModal() {
            document.getElementById('goalModal').classList.add('hidden');
        }
        
        function openProgressModal(goalId, currentProgress) {
            document.getElementById('progressForm').action = `/learning-goals/${goalId}/progress`;
            document.getElementById('progressSlider').value = currentProgress;
            document.getElementById('progressValue').textContent = currentProgress + '%';
            document.getElementById('progressModal').classList.remove('hidden');
        }
        
        function closeProgressModal() {
            document.getElementById('progressModal').classList.add('hidden');
        }
        
        // Close modals on outside click
        document.getElementById('goalModal').addEventListener('click', function(e) {
            if (e.target === this) closeGoalModal();
        });
        
        document.getElementById('progressModal').addEventListener('click', function(e) {
            if (e.target === this) closeProgressModal();
        });
    </script>
</div>
@endsection
