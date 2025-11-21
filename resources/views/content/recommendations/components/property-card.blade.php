<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
    <div class="relative">
       @if(!empty($property->images) && $property->images->isNotEmpty())
            <img src="{{ asset('storage/' . $property->images->first()->image_path) }}" 
                alt="{{ $property->title }}" 
                class="w-full h-48 object-cover">
        @else
            <div class="w-full h-48 bg-gray-300 flex items-center justify-center">
                <span class="text-gray-500">No Image</span>
            </div>
        @endif

        
        @if(isset($property->final_score))
            <div class="absolute top-2 right-2 bg-blue-500 text-white px-3 py-1 rounded-full text-sm">
                {{ round($property->final_score * 100) }}% Match
            </div>
        @endif
    </div>
    
    <div class="p-4">
        <h3 class="text-xl font-semibold mb-2">{{ $property->title }}</h3>
        
        <div class="flex items-center text-gray-600 mb-2">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM9 9a1 1 0 012 0v4a1 1 0 11-2 0V9zm1-5a1 1 0 100 2 1 1 0 000-2z"/>
            </svg>
            <span class="text-sm">{{ $property->distance_from_campus }} km from campus</span>
        </div>
        
        <div class="flex items-center justify-between mb-4">
            <span class="text-2xl font-bold text-blue-600">₱{{ number_format($property->price, 2) }}</span>
            @if($property->ratings_avg_rating)
                <div class="flex items-center">
                    <span class="text-yellow-500 mr-1">⭐</span>
                    <span class="font-semibold">{{ number_format($property->ratings_avg_rating, 1) }}</span>
                    <span class="text-gray-500 text-sm ml-1">({{ $property->ratings_count }})</span>
                </div>
            @endif
        </div>
        
        <div class="flex gap-2">
            <a href="" 
               class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center py-2 rounded transition"
               onclick="trackClick({{ $property->id }})">
                View Details
            </a>
            
            <button onclick="openRatingModal({{ $property->id }})" 
                    class="px-4 py-2 border border-blue-500 text-blue-500 hover:bg-blue-50 rounded transition">
                Rate
            </button>
        </div>
    </div>
</div>

<script>
function trackClick(propertyId) {
    fetch(`/recommendations/track-click/${propertyId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
}
</script>