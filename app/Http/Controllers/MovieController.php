<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovieController extends Controller
{
    /**
     * Helper function to attach genres to movies
     */
    private function attachGenresToMovies($movies)
    {   
        // Get all movie IDs
        $movieIds = collect($movies)->pluck('id')->toArray();
        
        // Get all genres for these movies in one query
        $movieGenres = DB::table('movie_genres')
            ->join('genres', 'movie_genres.genre_id', '=', 'genres.id')
        ->whereIn('movie_genres.movie_id', $movieIds) //SQL where movie_id IN (...)
            ->select('movie_genres.movie_id', 'genres.name as genre_name')
            ->get()
            ->groupBy('movie_id');
        
        // Attach genres to each movie
        foreach ($movies as $movie) {
            $movie->genres = $movieGenres->get($movie->id, collect())->pluck('genre_name')->toArray();//collect() to avoid null error - default empty collection
        }
        
        return $movies;
    }
    //Homepage function - show movies on homepage
    public function homepage()
    {
        $movies = DB::table('movies')->limit(6)->get(); // only fetch 6 movies for homepage
        
        // Attach genres using helper function
        $movies = $this->attachGenresToMovies($movies);
        
        return view('homepage', compact('movies'));
    }
    
    //1. movie function to fetch all movies from the database and return to index view
    public function index()
    {
        $movies = DB::table('movies')->get();
        
        // Attach genres using helper function
        $movies = $this->attachGenresToMovies($movies);
        
        return view('index', compact('movies'));
    }
    public function show($id)
    {
        $movie = DB::table('movies')->where('id', $id)->first();
        
        // Get genres for this movie
        if ($movie) {
            $movie->genres = DB::table('movie_genres')
                ->join('genres', 'movie_genres.genre_id', '=', 'genres.id')
                ->where('movie_genres.movie_id', $movie->id)
                ->pluck('genres.name')
                ->toArray();
        }
        
        return view('movie_details', compact('movie'));
    }
    //2. upcomingMovies function to fetch upcoming movies from the database and return to upcoming_movies view
    public function upcomingMovies()
    {
        $movies = DB::table('movies')->where('status', 'coming_soon')->get();
        
        // Attach genres using helper function
        $movies = $this->attachGenresToMovies($movies);
        
        return view('movie.upcoming_movies', compact('movies'));
    }
    //3. nowShowing function to fetch now showing movies from the database and return to now_showing view
    public function nowShowing()
    {
        $movies = DB::table('movies')->where('status', 'now_showing')->get();
        
        // Attach genres using helper function
        $movies = $this->attachGenresToMovies($movies);
        
        return view('movie.now_showing', compact('movies'));
    }
    
}