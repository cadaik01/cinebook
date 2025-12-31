@extends('layouts.main')

@section('title', '{{ $movie->title }} - Movie Details')

@section('content')
<h1>{{ $movie->title }}</h1>
<p><strong>Genre:</strong> {{ $movie->genre }}</p>
<p><strong>Language:</strong> {{ $movie->language }}</p>
<p><strong>Duration:</strong> {{ $movie->duration }}</p>
<p><strong>Release Date:</strong> {{ $movie->release_date }}</p>
<p><strong>Age Rating:</strong> {{ $movie->age_rating }}</p>
<p><strong>Director:</strong> {{ $movie->director }}</p>
<p><strong>Cast:</strong> {{ $movie->cast }}</p>
<p><strong>Status:</strong> 
    @if($movie->status == 'now_showing')
        Now Showing
    @elseif($movie->status == 'coming_soon')
        Coming Soon
    @else
        {{ $movie->status }}
    @endif
</p>

@if($movie->poster_url)
    <div>
        <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }} Poster" width="300">
    </div>
@endif

@if($movie->trailer_url)
    <p><strong>Trailer:</strong> <a href="{{ $movie->trailer_url }}" target="_blank">Watch Trailer</a></p>
@endif

<p><strong>Description:</strong> {{ $movie->description }}</p>

<div>
    <a href="">Book Now</a>
    <a href="/index">Back to List</a>
</div>
@endsection

