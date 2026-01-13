@extends('layouts.app')

@section('title', 'Taking Quiz: ' . $quiz->title)

@section('content')
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Quiz Header - Fixed -->
    <div class="fixed top-14 left-0 right-0 bg-gradient-to-r from-purple-600 to-purple-700 text-white px-4 py-3 shadow-lg z-30">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-bold text-lg">{{ $quiz->title }}</h1>
                <p class="text-xs text-purple-200">Attempt #{{ $attempt->attempt_number }}</p>
            </div>
            @if($quiz->hasTimeLimit())
            <div class="bg-white/20 backdrop-blur-sm px-3 py-2 rounded-lg">
                <p class="text-xs font-medium">Time Remaining</p>
                <p class="text-lg font-bold" id="timer">{{ $quiz->time_limit_minutes }}:00</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Content with top padding for fixed header -->
    <div class="pt-24 px-4 pb-6">
        <form action="{{ route('quizzes.submit', $attempt->id) }}" method="POST" id="quizForm">
            @csrf
            
            <!-- Progress Bar -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progress</span>
                    <span class="text-sm font-bold text-purple-600" id="progress-text">0 of {{ $questions->count() }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" id="progress-bar" style="width: 0%"></div>
                </div>
            </div>

            <!-- Questions -->
            <div class="space-y-4">
                @foreach($questions as $index => $question)
                <div class="bg-white rounded-xl shadow-sm p-6 question-card" data-question-index="{{ $index }}">
                    <!-- Question Number & Type Badge -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-bold text-purple-600">Question {{ $index + 1 }}</span>
                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $question->type === 'multiple_choice' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $question->type === 'true_false' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $question->type === 'short_answer' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $question->type === 'essay' ? 'bg-purple-100 text-purple-700' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                        </span>
                    </div>

                    <!-- Question Text -->
                    <div class="mb-4">
                        <p class="text-gray-900 font-medium leading-relaxed">{{ $question->question }}</p>
                        @if($question->points > 1)
                            <p class="text-xs text-gray-500 mt-1">({{ $question->points }} points)</p>
                        @endif
                    </div>

                    <!-- Question Image -->
                    @if($question->image_url)
                    <div class="mb-4">
                        <img src="{{ $question->image_url }}" alt="Question image" class="w-full rounded-lg">
                    </div>
                    @endif

                    <!-- Answer Options -->
                    @if($question->type === 'multiple_choice')
                        <div class="space-y-3">
                            @php
                                $options = $question->shuffled_options ?? $question->options;
                                $optionLabels = ['A', 'B', 'C', 'D', 'E', 'F'];
                            @endphp
                            @foreach($options as $optionIndex => $option)
                            <label class="flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 hover:bg-purple-50 transition answer-option">
                                <input type="radio" 
                                       name="answers[{{ $question->id }}]" 
                                       value="{{ $optionLabels[$optionIndex] }}" 
                                       class="mt-1 mr-3 text-purple-600 focus:ring-purple-500"
                                       onchange="updateProgress()">
                                <div class="flex-1">
                                    <span class="font-medium text-gray-900">{{ $optionLabels[$optionIndex] }}.</span>
                                    <span class="text-gray-700 ml-2">{{ $option }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>

                    @elseif($question->type === 'true_false')
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-300 hover:bg-green-50 transition answer-option">
                                <input type="radio" 
                                       name="answers[{{ $question->id }}]" 
                                       value="true" 
                                       class="mr-3 text-green-600 focus:ring-green-500"
                                       onchange="updateProgress()">
                                <span class="font-medium text-gray-900">✓ True</span>
                            </label>
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-300 hover:bg-red-50 transition answer-option">
                                <input type="radio" 
                                       name="answers[{{ $question->id }}]" 
                                       value="false" 
                                       class="mr-3 text-red-600 focus:ring-red-500"
                                       onchange="updateProgress()">
                                <span class="font-medium text-gray-900">✗ False</span>
                            </label>
                        </div>

                    @elseif($question->type === 'short_answer')
                        <input type="text" 
                               name="answers[{{ $question->id }}]" 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-purple-500 focus:ring focus:ring-purple-200"
                               placeholder="Type your answer..."
                               onchange="updateProgress()">

                    @elseif($question->type === 'essay')
                        <textarea name="answers[{{ $question->id }}]" 
                                  rows="6" 
                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-purple-500 focus:ring focus:ring-purple-200 resize-none"
                                  placeholder="Write your detailed answer here..."
                                  onchange="updateProgress()"></textarea>
                        <p class="text-xs text-gray-500 mt-2">💡 This question will be graded manually by your instructor</p>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Submit Button - Fixed at bottom -->
            <div class="fixed bottom-16 left-0 right-0 px-4 pb-4 bg-gradient-to-t from-gray-50 pt-4">
                <button type="button" 
                        onclick="confirmSubmit()" 
                        class="w-full py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl font-bold text-lg hover:from-purple-700 hover:to-purple-800 active:scale-95 transition shadow-lg">
                    📝 Submit Quiz
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Update progress
function updateProgress() {
    const total = {{ $questions->count() }};
    const answered = document.querySelectorAll('input[type="radio"]:checked, input[type="text"]:not(:placeholder-shown), textarea:not(:placeholder-shown)').length;
    const percentage = (answered / total) * 100;
    
    document.getElementById('progress-bar').style.width = percentage + '%';
    document.getElementById('progress-text').textContent = answered + ' of ' + total;
}

// Timer (if time limit exists)
@if($quiz->hasTimeLimit())
let timeLeft = {{ $quiz->time_limit_minutes * 60 }};
const timerElement = document.getElementById('timer');

function updateTimer() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    timerElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
    
    if (timeLeft <= 60) {
        timerElement.classList.add('text-red-400');
        timerElement.parentElement.classList.add('bg-red-500/30');
    }
    
    if (timeLeft <= 0) {
        alert('⏰ Time\'s up! The quiz will be submitted automatically.');
        document.getElementById('quizForm').submit();
    }
    
    timeLeft--;
}

setInterval(updateTimer, 1000);
@endif

// Confirm submission
function confirmSubmit() {
    const total = {{ $questions->count() }};
    const answered = document.querySelectorAll('input[type="radio"]:checked, input[type="text"]:not(:placeholder-shown), textarea:not(:placeholder-shown)').length;
    
    let message = 'Are you sure you want to submit?\n\n';
    message += 'Answered: ' + answered + ' of ' + total + ' questions';
    
    if (answered < total) {
        message += '\n\n⚠️ You have ' + (total - answered) + ' unanswered question(s).';
    }
    
    if (confirm(message)) {
        document.getElementById('quizForm').submit();
    }
}

// Prevent accidental page leave
window.addEventListener('beforeunload', function (e) {
    e.preventDefault();
    e.returnValue = '';
});

// Initial progress update
updateProgress();
</script>
@endsection
