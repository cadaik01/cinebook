<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
Route::get('/index', [MovieController::class, 'index'])->name('movies.index');
Route::get('/movies/{id}', [MovieController::class, 'show']);
Route::get('/', function () {
    return view('homepage');
});
?>
