@extends('layouts.main')

@section('title', 'Upcoming Movies - TCA Cine')

@section('content')
<h1>Upcoming Movies</h1>
<p>Get ready for these exciting releases coming soon</p>
@if(isset($movies) && count($movies) > 0)
    <div>
        @foreach($movies as $movie)
            <div>
                @if(isset($movie->poster_url) && $movie->poster_url)
                    <img src="{{ asset('images/' . $movie->poster_url) }}" alt="{{ $movie->title }}">
                @else
                    <div>
                        <span>No Poster</span>
                    </div>
                @endif
                
                <h3>{{ $movie->title }}</h3>
                <p>{{ $movie->genre ?? 'Drama' }}</p>
                <p>{{ $movie->duration ?? '120' }} min</p>
                <p>Coming Soon</p>
                
                <a href="/movies/{{ $movie->id }}">View Details</a>
            </div>
        @endforeach
    </div>
@else
    <div>
        <h3>No upcoming movies</h3>
        <p>Check back later for new releases!</p>
        <a href="{{ route('homepage') }}">Back to Homepage</a>
    </div>
@endif

<div>
    <a href="{{ route('homepage') }}">Back to Homepage</a>
    <a href="{{ route('upcoming_movies') }}">Upcoming Movies</a>
</div>
@endsection

