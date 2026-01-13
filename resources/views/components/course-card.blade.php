@props(['href' => '#', 'title', 'description', 'progress' => null, 'thumbnail'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'block bg-white rounded-xl shadow-sm overflow-hidden active:scale-[0.98] transition-transform']) }}>
    <div class="flex">
        <!-- Thumbnail -->
        <div class="w-24 h-24 flex-shrink-0">
            {{ $thumbnail }}
        </div>
        
        <!-- Content -->
        <div class="flex-1 p-4">
            <h4 class="font-semibold text-gray-900 mb-1 line-clamp-1">{{ $title }}</h4>
            <p class="text-xs text-gray-600 mb-2">{{ $description }}</p>
            
            @if($progress !== null)
                <!-- Progress Bar -->
                <div class="flex items-center space-x-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                    </div>
                    <span class="text-xs font-medium text-gray-600">{{ $progress }}%</span>
                </div>
            @endif
        </div>
    </div>
</a>
