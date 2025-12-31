<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\LoginController;
use Illuminate\Auth\Events\Login;
//homepage Route
Route::get('/index', [MovieController::class, 'index'])->name('movies.index');
Route::get('/movies/{id}', [MovieController::class, 'show']);
Route::get('/', [MovieController::class, 'homepage'])->name('homepage');
// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm']);
Route::post('/login',[LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
// Register Routes
Route::get('register',[LoginController::class, 'showRegisterForm']);
Route::post('register',[LoginController::class, 'register'])->name('register');
// Now Showing Route
Route::get('/now-showing', [MovieController::class, 'nowShowing'])->name('now_showing');
// Upcoming Movies Route
Route::get('/upcoming-movies', [MovieController::class, 'upcomingMovies'])->name('upcoming_movies');
//showtime Route
Route::get('/movies/{id}/showtimes', [MovieController::class, 'showtimes'])->name('movies.showtimes');
?>
