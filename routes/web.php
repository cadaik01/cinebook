<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ShowtimeController;
use Illuminate\Auth\Events\Login;
use App\Http\Controllers\AdminController;

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
Route::get('/movies/{id}/showtimes', [ShowtimeController::class, 'showtimes'])->name('movies.showtimes');
//Seat Map Route
Route::get('/showtimes/{showtime_id}/seats', [ShowtimeController::class, 'seatMap'])->name('movies.seatmap');

//Admin Routes - Nhóm tất cả các route admin với prefix 'admin'
Route::prefix('admin')->group(function () {
    //Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

     // Quản lý phim
    Route::get('/movies', [AdminController::class, 'moviesList'])->name('admin.movies.list');
    Route::get('/movies/create', [AdminController::class, 'movieCreate'])->name('admin.movies.create');
    Route::post('/movies', [AdminController::class, 'movieStore'])->name('admin.movies.store');
    Route::get('/movies/{id}/edit', [AdminController::class, 'movieEdit'])->name('admin.movies.edit');
    Route::put('/movies/{id}', [AdminController::class, 'movieUpdate'])->name('admin.movies.update');
    Route::delete('/movies/{id}', [AdminController::class, 'movieDelete'])->name('admin.movies.delete');
    
    // Quản lý người dùng
    Route::get('/users', [AdminController::class, 'usersList'])->name('admin.users.list');
    
    // Quản lý đặt vé
    Route::get('/bookings', [AdminController::class, 'bookingsList'])->name('admin.bookings.list');
});
?>