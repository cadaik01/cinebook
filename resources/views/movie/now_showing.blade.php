@extends('layouts.main')

@section('title', 'Now Showing - TCA Cine')

@push('styles')
@vite('resources/css/now_showing.css')
@endpush

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div class="header-content">
        <h1>Now Showing</h1>
        <p>Discover what's currently playing in theaters</p>
    </div>
</div>

<!-- Movies Grid -->
<div class="movies-container">
    @if(isset($movies) && count($movies) > 0)
    <div class="movies-grid">
        @foreach($movies as $movie)
        <div class="movie-card">
            <div class="movie-poster-wrapper">
                @if(isset($movie->poster_url) && $movie->poster_url)
                <img src="{{ strpos($movie->poster_url, 'http') === 0 ? $movie->poster_url : asset('images/' . $movie->poster_url) }}"
                    alt="{{ $movie->title }}" class="movie-poster">
                @else
                <div class="movie-poster-placeholder">
                    <span>No Poster</span>
                </div>
                @endif
                <div class="movie-badge now-showing">Now Showing</div>
            </div>

            <div class="movie-info">
                <h3 class="movie-title">{{ $movie->title }}</h3>
                <div class="movie-meta">
                    <span class="genre">{{ $movie->genre ?? 'Drama' }}</span>
                    <span class="duration">⏱️ {{ $movie->duration ?? '120' }} min</span>
                </div>

                @if($movie->rating_avg > 0)
                <div class="movie-rating">
                    <span class="rating-value">⭐ {{ $movie->rating_avg }}/5</span>
                </div>
                @endif

                <p class="movie-description">
                    {{ Str::limit($movie->description ?? 'Experience this amazing film in theaters now.', 100) }}</p>

                <div class="movie-actions">
                    <a href="/movies/{{ $movie->id }}" class="movie-btn movie-btn-primary">View Details</a>
                    <a href="{{ route('movies.showtimes', ['id' => $movie->id]) }}"
                        class="movie-btn movie-btn-secondary">Book Now</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <div class="empty-icon">🎬</div>
        <h3>No movies currently showing</h3>
        <p>Check back later for new releases!</p>
        <a href="{{ route('homepage') }}" class="movie-btn movie-btn-primary">Back to Homepage</a>
    </div>
    @endif
</div>

<!-- Navigation Links -->
<div class="page-navigation">
    <a href="{{ route('homepage') }}" class="nav-link">← Back to Homepage</a>
    <a href="{{ route('upcoming_movies') }}" class="nav-link">Upcoming Movies →</a>
</div>
@endsection