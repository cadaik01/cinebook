<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovieController extends Controller
{
    //Homepage function - show movies on homepage
    public function homepage()
    {
        $movies = DB::table('movies')->limit(6)->get(); // only fetch 6 movies for homepage
        return view('homepage', compact('movies'));
    }
    
    //1. movie function to fetch all movies from the database and return to index view
    public function index()
    {
        $movies = DB::table('movies')->get();
        return view('index', compact('movies'));
    }
    public function show($id)
    {
        $movie = DB::table('movies')->where('id', $id)->first();
        return view('movie_details', compact('movie'));
    }
    //2. upcomingMovies function to fetch upcoming movies from the database and return to upcoming_movies view
    public function upcomingMovies()
    {
        $movies = DB::table('movies')->where('status', 'coming_soon')->get();
        return view('movie.upcoming_movies', compact('movies'));
    }
    //3. nowShowing function to fetch now showing movies from the database and return to now_showing view
    public function nowShowing()
    {
        $movies = DB::table('movies')->where('status', 'now_showing')->get();
        return view('movie.now_showing', compact('movies'));
    }
    //4. showtimes function to fetch showtimes for a specific movie
    public function showtimes($id)
    {
        $movie = DB::table('movies')->where('id', $id)->first();
        $showtimes = DB::table('showtimes')
        ->join('rooms', 'showtimes.room_id', '=', 'rooms.id')
        ->where('showtimes.movie_id', $id)
        ->select(
            'showtimes.id',
            'showtimes.show_date',
            'showtimes.show_time',
            'rooms.id as room_id',
        )
        ->orderBy('show_date', 'asc')
        ->orderBy('show_time', 'asc')
        ->get();
        return view('movie.showtimes', compact('movie', 'showtimes'));
    }
}