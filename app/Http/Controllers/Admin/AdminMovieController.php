<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMovieController extends Controller
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

    public function index()
    {
        $movies = Movie::latest()->paginate(20);
        
        // Convert to array for helper function
        $moviesArray = $movies->items();
        $moviesArray = $this->attachGenresToMovies($moviesArray);
        
        return view('admin.movies.index', compact('movies'));
    }

    public function create()
    {
        $genres = Genre::orderBy('name')->get();
        return view('admin.movies.create', compact('genres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'director' => 'nullable|string|max:255',
            'cast' => 'nullable|string',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
            'duration' => 'required|integer|min:1',
            'release_date' => 'required|date',
            'language' => 'nullable|string|max:100',
            'rating' => 'nullable|numeric|min:0|max:10',
            'poster_url' => 'nullable|url',
            'trailer_url' => 'nullable|url',
            'status' => 'required|in:now_showing,coming_soon,ended',
            'description' => 'nullable|string',
        ]);

        // Remove genres from validated data for mass assignment
        $genres = $validated['genres'] ?? [];
        unset($validated['genres']);

        $movie = Movie::create($validated);

        // Sync genres
        if (!empty($genres)) {
            $movie->genres()->sync($genres);
        }

        return redirect()->route('admin.movies.index')
            ->with('success', 'Movie created successfully!');
    }

    public function edit(Movie $movie)
    {
        $genres = Genre::orderBy('name')->get();
        $movie->load('genres'); // Eager load genres
        return view('admin.movies.edit', compact('movie', 'genres'));
    }

    public function update(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'director' => 'nullable|string|max:255',
            'cast' => 'nullable|string',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
            'duration' => 'required|integer|min:1',
            'release_date' => 'required|date',
            'language' => 'nullable|string|max:100',
            'rating' => 'nullable|numeric|min:0|max:10',
            'poster_url' => 'nullable|url',
            'trailer_url' => 'nullable|url',
            'status' => 'required|in:now_showing,coming_soon,ended',
            'description' => 'nullable|string',
        ]);

        // Remove genres from validated data for mass assignment
        $genres = $validated['genres'] ?? [];
        unset($validated['genres']);

        $movie->update($validated);

        // Sync genres
        $movie->genres()->sync($genres);

        return redirect()->route('admin.movies.index')
            ->with('success', 'Movie updated successfully!');
    }

    public function destroy(Movie $movie)
    {
        // Detach all genres first
        $movie->genres()->detach();
        
        $movie->delete();
        return redirect()->route('admin.movies.index')
            ->with('success', 'Movie deleted successfully!');
    }
}