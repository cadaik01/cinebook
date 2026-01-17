@extends('layouts.main')

@section('title', 'Upcoming Movies - TCA Cine')

@push('styles')
{{-- @vite('resources/css/upcoming_movies.css') --}}
@endpush

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div class="header-content">
        <h1>Upcoming Movies</h1>
        <p>Exciting films coming soon to theaters</p>
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
                <div class="movie-badge coming-soon">Coming Soon</div>
            </div>

            <div class="movie-info">
                <h3 class="movie-title">{{ $movie->title }}</h3>
                <div class="movie-meta">
                    <span class="genre">
                        @if(isset($movie->genres) && is_array($movie->genres) && count($movie->genres) > 0)
                        {{ implode(', ', $movie->genres) }}
                        @elseif(method_exists($movie, 'getGenresStringAttribute') && $movie->genres &&
                        count($movie->genres) > 0)
                        {{ $movie->genres_string }}
                        @else
                        Unknown
                        @endif
                    </span>
                    @if($movie->release_date)
                    <span class="release-date">📅
                        {{ \Carbon\Carbon::parse($movie->release_date)->format('M d, Y') }}</span>
                    @endif
                </div>

                @if($movie->duration)
                <div class="movie-duration">
                    <span>⏱️ {{ $movie->duration }} min</span>
                </div>
                @endif

                <p class="movie-description">
                    {{ Str::limit($movie->description ?? 'Get ready for this exciting upcoming film!', 100) }}</p>

                <div class="movie-actions">
                    <a href="/movies/{{ $movie->id }}" class="movie-btn movie-btn-primary">View Details</a>
                    <a href="#" class="movie-btn movie-btn-notify">Notify Me</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <div class="empty-icon">🍿</div>
        <h3>No upcoming movies</h3>
        <p>Check our now showing movies or check back later for announcements!</p>
        <a href="{{ route('now_showing') }}" class="movie-btn movie-btn-primary">Now Showing</a>
    </div>
    @endif
</div>

<!-- Navigation Links -->
<div class="page-navigation">
    <a href="{{ route('now_showing') }}" class="nav-link">← Now Showing</a>
    <a href="{{ route('homepage') }}" class="nav-link">Back to Homepage →</a>
</div>
@endsection