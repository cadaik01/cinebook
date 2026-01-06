<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\BookingController;
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
Route::get('/movies/{id}/showtimes', [ShowtimeController::class, 'showtimes'])->name('movies.showtimes');

// Booking Routes (using BookingController)
Route::get('/showtimes/{showtime_id}/seats', [BookingController::class, 'seatMap'])->name('booking.seatmap');
Route::post('/showtimes/{showtime_id}/book', [BookingController::class, 'bookSeats'])->name('booking.book');
Route::post('/showtimes/{showtime_id}/seats/select', [ShowtimeController::class, 'selectSeats'])->name('movies.selectseats');
Route::post('/booking/process', [BookingController::class, 'processBooking'])->name('booking.process');
?>
