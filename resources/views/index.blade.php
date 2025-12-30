
<!DOCTYPE html>
<html>
<head>
    <title>Movie List</title>
</head>
<body>
    <h1>Movie List</h1>
    @foreach($movies as $movie)
        <div>
            <h3>{{ $movie->title }}</h3>
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
            
            @if($movie->trailer_url)
                <p><strong>Trailer:</strong> <a href="{{ $movie->trailer_url }}" target="_blank">Xem trailer</a></p>
            @endif
            <p><strong>Description:</strong> {{ $movie->description }}</p>
            <hr>
        </div>
    @endforeach
</body>
</html>
