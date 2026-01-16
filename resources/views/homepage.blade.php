@extends('layouts.main')

@section('title', 'TCA Cine - Home')

@push('styles')
@vite(['resources/css/homepage.css'])
@endpush

@section('content')
<!-- Hero Banner -->
<div class="hero-section">
    <h1>Welcome to TCA Cine</h1>
    <p>Experience the Wonderful World of Cinema</p>
    <form action="{{ route('search') }}" method="get" class="search-form">
        <input type="text" name="q" class="search-input" placeholder="Search by title, genre, director, language..."
            required>
        <button type="submit" class="search-btn">Search</button>
    </form>
    <a href="{{ route('now_showing') }}" class="btn-cta btn-cta-primary btn-lg">Book Tickets Now</a>
</div>

<!-- Featured Movies -->
<div class="featured-movies">
    <h2 class="section-title">🎬 Featured Movies</h2>

    @if(isset($movies) && count($movies) > 0)
    <div class="movies-container">
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

                    <div
                        class="movie-badge {{ ($movie->status ?? '') === 'coming_soon' ? 'coming-soon' : 'now-showing' }}">
                        {{ ($movie->status ?? '') === 'coming_soon' ? 'Coming Soon' : 'Now Showing' }}
                    </div>
                </div>

                <div class="movie-info">
                    <h3 class="movie-title">{{ $movie->title }}</h3>

                    <div class="movie-meta">
                        <span class="genre">
                            @if(isset($movie->genres) && count($movie->genres) > 0)
                            {{ implode(', ', $movie->genres) }}
                            @else
                            Unknown
                            @endif
                        </span>
                        <span class="duration">⏱️ {{ $movie->duration ?? '120' }} min</span>
                    </div>

                    @if(isset($movie->rating_avg) && $movie->rating_avg > 0)
                    <div class="movie-rating">
                        <span class="rating-value">⭐ {{ $movie->rating_avg }}/5</span>
                    </div>
                    @endif

                    <p class="movie-description">
                        {{ Str::limit($movie->description ?? 'Experience this amazing film in theaters now.', 110) }}
                    </p>

                    <div class="movie-actions">
                        <a href="/movies/{{ $movie->id }}" class="btn btn-detail">View Details</a>
                        <a href="{{ route('movies.showtimes', ['id' => $movie->id]) }}" class="btn btn-secondary">Book
                            Now</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="btn-view-all-container">
        <a href="{{ route('movies.index') }}" class="btn-view-all">
            View All Movies →
        </a>
    </div>
    @else
    <div class="empty-state">
        <p>No movies available at the moment. Please check back later!</p>
    </div>
    @endif
</div>

<!-- Call to Action Section -->
<div class="cta-section">
    <h2>Ready to Watch?</h2>
    <p>Join TCA Cine's movie-loving community today</p>
    <div class="cta-buttons">
        <a href="{{ route('register') }}" class="btn-cta btn-cta-primary">
            Sign Up Now
        </a>
        <a href="#contact" class="btn-cta btn-cta-secondary">
            Contact Us
        </a>
    </div>
</div>
@endsection