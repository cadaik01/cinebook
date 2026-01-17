@extends('layouts.main')

@section('title', 'Search Results')

@section('content')
<div class="container" style="margin-top:40px;">
    <h2>Search Results for: <span style="color:#3498db">{{ $query }}</span></h2>
    @if($movies->count() > 0)
        <div class="movies-grid">
            @foreach($movies as $movie)
                <div class="movie-card">
                    <div class="movie-poster-wrapper">
                        @if($movie->poster_url)
                            <img src="{{ strpos($movie->poster_url, 'http') === 0 ? $movie->poster_url : asset('images/' . $movie->poster_url) }}" alt="{{ $movie->title }}" class="movie-poster">
                        @else
                            <div class="movie-poster-placeholder"><span>No Poster</span></div>
                        @endif
                    </div>
                    <div class="movie-info">
                        <h3 class="movie-title">{{ $movie->title }}</h3>
                        <div class="movie-meta">
                            <span class="genre">
                                @if($movie->genres && count($movie->genres) > 0)
                                    {{ $movie->genres->pluck('name')->join(', ') }}
                                @else
                                    Unknown
                                @endif
                            </span>
                            <span class="duration">⏱️ {{ $movie->duration ?? '120' }} min</span>
                        </div>
                        <p class="movie-description">{{ Str::limit($movie->description, 100) }}</p>
                        <div class="movie-actions">
                            <a href="/movies/{{ $movie->id }}" class="movie-btn movie-btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <h4>No movies found matching your search.</h4>
        </div>
    @endif
</div>
@endsection
