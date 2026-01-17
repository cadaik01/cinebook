<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Movie;
use App\Models\Genre;

class MovieController extends Controller
{
    /**
     * Helper function to attach genres to movies (using Eloquent relationships)
     */
    private function attachGenresToMovies($movies)
    {   
        // Eager load genres for all movies
        $movieIds = collect($movies)->pluck('id')->toArray();
        
        $moviesWithGenres = Movie::with('genres')
            ->whereIn('id', $movieIds)
            ->get()
            ->keyBy('id');
        
        // Attach genres to each movie
        foreach ($movies as $movie) {
            $movieModel = $moviesWithGenres->get($movie->id);
            $movie->genres = $movieModel ? $movieModel->genres->pluck('name')->toArray() : [];
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
        // Use Eloquent Model instead of DB::table to enable relationships
        $movie = Movie::with('genres','reviews.user')->findOrFail($id);

        // Get genres for this movie using relationships
        if ($movie) {
            $movie->genres = $movie->genres->pluck('name')->toArray();
        }

        // Check if user can review this movie
        $canReview = false;
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->role === 'admin') {
                $canReview = true;
            } else {
                $userId = $user->id;
                // Check if user has watched this movie (showtime must be in the past)
                $hasWatched = \DB::table('booking_seats')
                    ->join('showtimes', 'booking_seats.showtime_id', '=', 'showtimes.id')
                    ->join('bookings', 'booking_seats.booking_id', '=', 'bookings.id')
                    ->where('bookings.user_id', $userId)
                    ->where('showtimes.movie_id', $id)
                    ->where('bookings.payment_status', 'paid')
                    ->where(function($query) {
                        $query->where('showtimes.show_date', '<', now()->toDateString())
                            ->orWhere(function($q) {
                                $q->where('showtimes.show_date', '=', now()->toDateString())
                                  ->where('showtimes.show_time', '<', now()->toTimeString());
                            });
                    })
                    ->exists();

                // Check if user has already reviewed
                $hasReviewed = $movie->reviews->where('user_id', $userId)->isNotEmpty();

                $canReview = $hasWatched && !$hasReviewed;
            }
        }

        return view('movie_details', compact('movie', 'canReview'));
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