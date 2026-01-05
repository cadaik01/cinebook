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
</div>

<!-- Featured Movies -->
<div class="featured-movies">
    <h2 class="section-title">🎬 Featured Movies</h2>

    @if(isset($movies) && count($movies) > 0)
    <div class="movies-grid">
        @foreach($movies as $movie)
        <div class="movie-card">
            @if(isset($movie->poster_url) && $movie->poster_url)
            <img src="{{ strpos($movie->poster_url, 'http') === 0 ? $movie->poster_url : asset('images/' . $movie->poster_url) }}"
                alt="{{ $movie->title }}" class="movie-poster">
            @else
            <div class="movie-poster"
                style="display: flex; align-items: center; justify-content: center; background-color: #e9ecef; color: #999;">
                <span>No Image</span>
            </div>
            @endif

            <div class="movie-info">
                <h3 class="movie-title">{{ $movie->title }}</h3>
                <p class="movie-genre">{{ $movie->genre ?? 'Unknown' }}</p>
                <p class="movie-duration">⏱️ {{ $movie->duration ?? '120' }} min</p>

                <a href="/movies/{{ $movie->id }}" class="btn-detail">
                    View Details
                </a>
            </div>
        </div>
        @endforeach
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