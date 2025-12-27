<!DOCTYPE html>
<html>
<head>
    <title>Movie List</title>
</head>
<body>
    <h1>Danh sách phim</h1>

    @foreach($movies as $movie)
        <div>
            <h3>{{ $movie->title }}</h3>
            <p>Thể loại: {{ $movie->genre }}</p>
            <p>Ngôn ngữ: {{ $movie->language }}</p>
            <hr>
        </div>
    @endforeach
</body>
</html>
