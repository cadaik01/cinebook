@extends('layouts.main')

@section('title', 'TCA Cine - Homepage')

@section('content')
<h1>Welcome to TCA Cine</h1>
<p>Experience the magic of cinema</p>

@if(session('user_id'))
    <!-- Login success -->
    <div style="background: #f0f8ff; padding: 10px; border-radius: 5px; margin: 20px 0;">
        <p><strong>Hi, {{ session('user_name') }}!</strong></p>
        <p>You have successfully logged in.</p>
        <a href="{{ route('logout') }}" style="color: red;">Logout</a>
    </div>
@else
    <!-- Login not success -->
    <div style="background: #fff8dc; padding: 10px; border-radius: 5px; margin: 20px 0;">
        <p>You are not logged in.</p>
        <a href="/login">Login now</a>
    </div>
@endif

<!-- Movies Section -->
<div style="margin: 30px 0;">
    <h2>Featured Movies</h2>
    
    @if(isset($movies) && count($movies) > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
            @foreach($movies as $movie)
                <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center;">
                    @if(isset($movie->poster_url) && $movie->poster_url)
                        <img src="{{ asset('images/' . $movie->poster_url) }}" alt="{{ $movie->title }}" 
                             style="width: 100%; height: 200px; object-fit: cover; border-radius: 5px;">
                    @else
                        <div style="width: 100%; height: 200px; background: #f0f0f0; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                            <span>No Image</span>
                        </div>
                    @endif
                    
                    <h3 style="margin: 10px 0 5px 0;">{{ $movie->title }}</h3>
                    <p style="color: #666; font-size: 14px;">{{ $movie->genre ?? 'Drama' }}</p>
                    <p style="color: #888; font-size: 12px;">{{ $movie->duration ?? '120' }} min</p>
                    
                    <a href="/movies/{{ $movie->id }}" style="display: inline-block; background: #007bff; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; margin-top: 10px;">
                        View Details
                    </a>
                </div>
            @endforeach
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('movies.index') }}" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                View All Movies
            </a>
        </div>
    @else
        <p style="color: #888; text-align: center; padding: 20px;">No movies available at the moment.</p>
    @endif
</div>

<h2>Ready for Cinema?</h2>
<p>Join thousands of movie lovers at TCA Cine</p>
<div>
    <a href="">Join Now</a>
    <a href="">Contact Us</a>
</div>
@endsection