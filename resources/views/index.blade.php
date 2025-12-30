@extends('layouts.main')

@section('title', '{{ $movie->title }} - Movie Details')

@section('content')
<body>
    <h1>Movie List</h1>
    @foreach($movies as $movie)
        <div>
            <h3>{{ $movie->title }}</h3>
            <a href="/movies/{{ $movie->id }}">more details</a>
            <p>Genre: {{ $movie->genre }}</p>
            <p>Language: {{ $movie->language }}</p>
            <p>Duration: {{ $movie->duration }}</p>
            <p>Release Date: {{ $movie->release_date }}</p>
            <p>Status: {{ $movie->status }}</p>
            
            @if($movie->poster_url)
                <div>
                    <strong>Poster:</strong><br>
                    <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }} Poster" style="max-width: 300px; height: auto;">
                </div>
            @endif
        </div>
    @endforeach
@endsection
