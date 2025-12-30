<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovieController extends Controller
{
    //1. ham hien thi trang movies
    public function index()
    {
        $movies = DB::table('movies')->get();
        return view('index', compact('movies'));
    }
}