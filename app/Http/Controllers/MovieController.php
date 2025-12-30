<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovieController extends Controller
{
    //1. movie function to fetch all movies from the database and return to index view
    public function index()
    {
        $movies = DB::table('movies')->get();
        return view('index', compact('movies'));
    }
}