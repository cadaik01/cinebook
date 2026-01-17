<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Genre;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');
        $movies = Movie::with('genres')
            ->where('title', 'like', "%$query%")
            ->orWhere('director', 'like', "%$query%")
            ->orWhere('language', 'like', "%$query%")
            ->orWhereHas('genres', function($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('description', 'like', "%$query%") ;
            })
            ->get();
        return view('search_results', compact('movies', 'query'));
    }
}