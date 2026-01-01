@extends('layouts.main')

@section('title', '{{ $movie->title }} - Movie Details')

@push('styles')
@vite('resources/css/movie_details.css')
@endpush

@section('content')
<div class="movie-details-container">
    <div class="movie-header">
        <h1>{{ $movie->title }}</h1>
    </div>

    <div class="movie-content">
        <div class="movie-poster">
            @if($movie->poster_url)
            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }} Poster" class="poster-img">
            @endif

            <div class="poster-buttons">
                @if($movie->status == 'now_showing')
                <a href="{{ route('movies.showtimes', ['id' => $movie->id]) }}"
                    class="detail-btn detail-btn-primary">Book Now</a>
                @else
                <button class="detail-btn detail-btn-disabled" disabled>Coming Soon</button>
                @endif

                @if($movie->trailer_url)
                <button class="detail-btn detail-btn-trailer" onclick="openTrailerModal()">Watch Trailer</button>
                @endif
            </div>
        </div>

        <div class="movie-info">
            <div class="info-section">
                <h3>Movie Information</h3>
                <p><strong>Genre:</strong> {{ $movie->genre }}</p>
                <p><strong>Language:</strong> {{ $movie->language }}</p>
                <p><strong>Duration:</strong> {{ $movie->duration }} minutes</p>
                <p><strong>Director:</strong> {{ $movie->director }}</p>
                <p><strong>Cast:</strong> {{ $movie->cast }}</p>
                <p><strong>Release Date:</strong> {{ date('d/m/Y', strtotime($movie->release_date)) }}</p>
                <p><strong>Age Rating:</strong> <span class="age-rating">{{ $movie->age_rating }}</span></p>
                <p><strong>Status:</strong>
                    @if($movie->status == 'now_showing')
                    <span class="status-badge status-showing">Now Showing</span>
                    @elseif($movie->status == 'coming_soon')
                    <span class="status-badge status-soon">Coming Soon</span>
                    @else
                    <span class="status-badge status-ended">{{ $movie->status }}</span>
                    @endif
                </p>
                @if($movie->rating_avg > 0)
                <p><strong>Rating:</strong> <span class="rating">{{ $movie->rating_avg }}/5</span></p>
                @endif
            </div>

            <div class="info-section">
                <h3>Synopsis</h3>
                <p>{{ $movie->description }}</p>
            </div>
        </div>
    </div>

    <div class="back-button">
        @if($movie->status == 'now_showing')
        <a href="{{ route('now_showing') }}" class="detail-btn detail-btn-secondary">Back to Now Showing</a>
        @else
        <a href="{{ route('upcoming_movies') }}" class="detail-btn detail-btn-secondary">Back to Upcoming</a>
        @endif
    </div>

    <!-- Trailer Modal -->
    @if($movie->trailer_url)
    <div id="trailerModal" class="modal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeTrailerModal()">&times;</button>
            <div class="modal-body">
                @php
                // Extract YouTube video ID from URL
                preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $movie->trailer_url, $matches);
                $videoId = $matches[1] ?? '';
                @endphp
                @if($videoId)
                <iframe width="100%" height="600" src="https://www.youtube.com/embed/{{ $videoId }}"
                    title="{{ $movie->title }} Trailer" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen>
                </iframe>
                @else
                <p><a href="{{ $movie->trailer_url }}" target="_blank">Watch Trailer</a></p>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function openTrailerModal() {
    document.getElementById('trailerModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeTrailerModal() {
    document.getElementById('trailerModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('trailerModal');
    if (modal && event.target == modal) {
        closeTrailerModal();
    }
}

// Close modal when pressing Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('trailerModal');
        if (modal && modal.style.display === 'flex') {
            closeTrailerModal();
        }
    }
});
</script>
@endsection