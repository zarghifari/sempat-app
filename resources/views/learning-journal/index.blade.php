@extends('layouts.app')

@section('page-title', 'Learning Journal')

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
        <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-2xl p-5 text-white shadow-lg">
            <!-- Title and Button -->
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-xl font-bold">Learning Journal 📓</h1>
                    <p class="text-green-100 text-sm mt-1">Reflect on your learning journey</p>
                </div>
                <button onclick="openCreateJournalModal()" 
                        class="bg-white text-green-600 px-4 py-2 rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition-all active:scale-95 flex-shrink-0">
                    <span class="text-lg mr-1">+</span> New Entry
                </button>
            </div>

            <!-- Stats in One Row -->
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-xl px-3 py-2">
                    <span class="text-xl font-bold">{{ $stats['total'] }}</span>
                    <span class="text-xs text-green-100">Total</span>
                </div>
                <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-xl px-3 py-2">
                    <span class="text-xl font-bold">{{ $stats['this_week'] }}</span>
                    <span class="text-xs text-green-100">This Week</span>
                </div>
                {{-- <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-xl px-3 py-2">
                    <span class="text-xl font-bold">{{ $stats['this_month'] }}</span>
                    <span class="text-xs text-green-100">This Month</span>
                </div> --}}
                <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-xl px-3 py-2">
                    <span class="text-xl font-bold">{{ floor($stats['total_study_minutes'] / 60) }}</span>
                    <span class="text-xs text-green-100">Hours</span>
                </div>
            </div>
        </div>

        <!-- Journal Entries Timeline -->
        @if($entries->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm">
                <div class="text-6xl mb-4">📔</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Start Your Learning Journal</h3>
                <p class="text-gray-600 text-sm mb-4">
                    Document your learning experiences, reflections, and insights
                </p>
                <button onclick="openCreateJournalModal()" 
                        class="bg-green-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-green-700 transition-colors active:scale-95">
                    Create First Entry
                </button>
            </div>
        @else
            <!-- Timeline -->
            <div class="space-y-4">
                @foreach($entries as $entry)
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                        <!-- Entry Header -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-bold text-gray-900">{{ $entry->title }}</h3>
                                    @if($entry->mood)
                                        <span class="text-lg">
                                            @switch($entry->mood)
                                                @case('excited') 🤩 @break
                                                @case('confident') 😊 @break
                                                @case('neutral') 😐 @break
                                                @case('challenged') 🤔 @break
                                                @case('frustrated') 😓 @break
                                            @endswitch
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <span>📅 {{ \Carbon\Carbon::parse($entry->entry_date)->format('d M Y') }}</span>
                                    @if($entry->study_duration_minutes)
                                        <span>•</span>
                                        <span>⏱️ {{ $entry->study_duration_minutes }} min</span>
                                    @endif
                                </div>
                            </div>
                            <button onclick="openEditJournalModal({{ $entry->id }})" 
                                    class="text-gray-400 hover:text-gray-600 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>
                        </div>

                        <!-- Entry Content -->
                        <div class="text-sm text-gray-700 mb-3 line-clamp-3">
                            {{ Str::limit($entry->content, 200) }}
                        </div>

                        <!-- Related Content -->
                        @if($entry->course || $entry->article || $entry->learningGoal)
                            <div class="flex flex-wrap gap-2 mb-3">
                                @if($entry->course)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-lg">
                                        📚 {{ Str::limit($entry->course->title, 20) }}
                                    </span>
                                @endif
                                @if($entry->article)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 text-purple-700 text-xs rounded-lg">
                                        📰 {{ Str::limit($entry->article->title, 20) }}
                                    </span>
                                @endif
                                @if($entry->learningGoal)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-orange-50 text-orange-700 text-xs rounded-lg">
                                        🎯 {{ Str::limit($entry->learningGoal->title, 20) }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        <!-- Tags -->
                        @if($entry->tags && count($entry->tags) > 0)
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach($entry->tags as $tag)
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">
                                        #{{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <!-- Reflection Preview -->
                        @if($entry->what_learned || $entry->challenges_faced || $entry->next_steps)
                            <div class="pt-3 border-t border-gray-100">
                                <button onclick="toggleReflection({{ $entry->id }})" 
                                        class="text-xs text-green-600 font-semibold hover:text-green-700 flex items-center gap-1">
                                    <span>View Reflection</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div id="reflection-{{ $entry->id }}" class="hidden mt-3 space-y-2">
                                    @if($entry->what_learned)
                                        <div>
                                            <div class="text-xs font-semibold text-gray-700 mb-1">💡 What I Learned</div>
                                            <div class="text-xs text-gray-600 bg-green-50 rounded-lg p-2">{{ $entry->what_learned }}</div>
                                        </div>
                                    @endif
                                    @if($entry->challenges_faced)
                                        <div>
                                            <div class="text-xs font-semibold text-gray-700 mb-1">🚧 Challenges Faced</div>
                                            <div class="text-xs text-gray-600 bg-orange-50 rounded-lg p-2">{{ $entry->challenges_faced }}</div>
                                        </div>
                                    @endif
                                    @if($entry->next_steps)
                                        <div>
                                            <div class="text-xs font-semibold text-gray-700 mb-1">➡️ Next Steps</div>
                                            <div class="text-xs text-gray-600 bg-blue-50 rounded-lg p-2">{{ $entry->next_steps }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($entries->hasPages())
                <div class="mt-6">
                    {{ $entries->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Create/Edit Journal Modal -->
<div id="journalModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
    <div class="flex items-end justify-center min-h-screen">
        <div class="bg-white rounded-t-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-4 py-4 flex items-center justify-between">
                <h2 id="journalModalTitle" class="text-lg font-bold text-gray-900">New Journal Entry</h2>
                <button onclick="closeJournalModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form id="journalForm" method="POST" action="{{ route('learning-journal.store') }}" class="p-4 space-y-4">
                @csrf
                <input type="hidden" id="journalMethod" name="_method" value="POST">
                <input type="hidden" id="journalId" name="journal_id">
                
                <!-- Title -->
                <div>
                    <x-input-label for="title" value="Title *" />
                    <x-input id="title" type="text" name="title" placeholder="Today's Learning Session..." required />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <!-- Entry Date -->
                <div>
                    <x-input-label for="entry_date" value="Date *" />
                    <x-input id="entry_date" type="date" name="entry_date" :value="date('Y-m-d')" required />
                    <x-input-error :messages="$errors->get('entry_date')" class="mt-1" />
                </div>

                <!-- Content -->
                <div>
                    <x-input-label for="content" value="Content *" />
                    <textarea id="content" name="content" rows="4" 
                              class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm"
                              placeholder="Write about what you learned today..." required></textarea>
                    <x-input-error :messages="$errors->get('content')" class="mt-1" />
                </div>

                <!-- What Learned -->
                <div>
                    <x-input-label for="what_learned" value="💡 What I Learned" />
                    <textarea id="what_learned" name="what_learned" rows="2" 
                              class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm"
                              placeholder="Key takeaways and new knowledge gained..."></textarea>
                </div>

                <!-- Challenges Faced -->
                <div>
                    <x-input-label for="challenges_faced" value="🚧 Challenges Faced" />
                    <textarea id="challenges_faced" name="challenges_faced" rows="2" 
                              class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm"
                              placeholder="Difficulties and obstacles encountered..."></textarea>
                </div>

                <!-- Next Steps -->
                <div>
                    <x-input-label for="next_steps" value="➡️ Next Steps" />
                    <textarea id="next_steps" name="next_steps" rows="2" 
                              class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm"
                              placeholder="What you plan to do next..."></textarea>
                </div>

                <!-- Mood and Duration -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="mood" value="Mood" />
                        <select id="mood" name="mood" 
                                class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">
                            <option value="">Select mood...</option>
                            <option value="excited">🤩 Excited</option>
                            <option value="confident">😊 Confident</option>
                            <option value="neutral">😐 Neutral</option>
                            <option value="challenged">🤔 Challenged</option>
                            <option value="frustrated">😓 Frustrated</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="study_duration_minutes" value="Duration (min)" />
                        <x-input id="study_duration_minutes" type="number" name="study_duration_minutes" 
                                 placeholder="60" min="1" />
                    </div>
                </div>

                <!-- Tags -->
                <div>
                    <x-input-label for="tags_input" value="Tags (comma separated)" />
                    <x-input id="tags_input" type="text" name="tags_input" 
                             placeholder="programming, python, webdev" />
                    <p class="text-xs text-gray-500 mt-1">Separate tags with commas</p>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeJournalModal()" 
                            class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700 transition-colors">
                        Save Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Create Journal Modal
    function openCreateJournalModal() {
        document.getElementById('journalModalTitle').textContent = 'New Journal Entry';
        document.getElementById('journalForm').action = '{{ route("learning-journal.store") }}';
        document.getElementById('journalMethod').value = 'POST';
        document.getElementById('journalForm').reset();
        document.getElementById('entry_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('journalModal').classList.remove('hidden');
    }

    // Edit Journal Modal
    function openEditJournalModal(journalId) {
        // In a real implementation, fetch journal data via AJAX
        document.getElementById('journalModalTitle').textContent = 'Edit Journal Entry';
        document.getElementById('journalForm').action = `/learning-journal/${journalId}`;
        document.getElementById('journalMethod').value = 'PUT';
        document.getElementById('journalModal').classList.remove('hidden');
    }

    // Close Modal
    function closeJournalModal() {
        document.getElementById('journalModal').classList.add('hidden');
    }

    // Toggle Reflection
    function toggleReflection(entryId) {
        const reflection = document.getElementById(`reflection-${entryId}`);
        reflection.classList.toggle('hidden');
    }

    // Close modal on outside click
    document.getElementById('journalModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeJournalModal();
        }
    });
</script>
@endsection
