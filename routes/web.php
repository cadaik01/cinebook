<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/movies', function () {
    // Tạm thời tạo dữ liệu giả để test
    $movies = [
        (object) [
            'title' => 'Avengers: Endgame',
            'genre' => 'Action, Adventure',
            'language' => 'English'
        ],
        (object) [
            'title' => 'Spider-Man: No Way Home', 
            'genre' => 'Action, Adventure',
            'language' => 'English'
        ],
        (object) [
            'title' => 'Fast & Furious 10',
            'genre' => 'Action, Thriller',
            'language' => 'English'
        ]
    ];
    
    return view('index', compact('movies'));
});

