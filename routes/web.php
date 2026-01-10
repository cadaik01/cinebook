<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\BookingController;
use Illuminate\Auth\Events\Login;

//***Movie Controller */
//movie list Route
Route::get('/index', [MovieController::class, 'index'])->name('movies.index');
//movie detail Route
Route::get('/movies/{id}', [MovieController::class, 'show']);
// Homepage Route
Route::get('/', [MovieController::class, 'homepage'])->name('homepage');
// Now Showing Route
Route::get('/now-showing', [MovieController::class, 'nowShowing'])->name('now_showing');
// Upcoming Movies Route
Route::get('/upcoming-movies', [MovieController::class, 'upcomingMovies'])->name('upcoming_movies');

//**Login Controller */
// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm']);
Route::post('/login',[LoginController::class, 'login'])->name('login');
// Logout Route
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
// Register Routes
Route::get('register',[LoginController::class, 'showRegisterForm']);
Route::post('register',[LoginController::class, 'register'])->name('register');

/**Showtime Controller */
//showtime Route
Route::get('/movies/{id}/showtimes', [ShowtimeController::class, 'showtimes'])->name('movies.showtimes');

//**Booking Controller */
// Seatmap Page
Route::get('/showtimes/{showtime_id}/seats', [BookingController::class, 'seatMap'])->name('booking.seatmap');
// Book Seats Page
Route::post('/showtimes/{showtime_id}/book', [BookingController::class, 'bookSeats'])->name('booking.book');
// Select Seats Page
Route::post('/showtimes/{showtime_id}/seats/select', [ShowtimeController::class, 'selectSeats'])->name('movies.selectseats');
// Process Booking
Route::post('/booking/process', [BookingController::class, 'processBooking'])->name('booking.process');
// Booking Success
Route::get('/booking/success/{booking_id}', [BookingController::class, 'bookingSuccess'])->name('booking.success');
// Mock Payment
Route::get('payment/mock/{booking_id}', [BookingController::class, 'mockPayment'])->name('payment.mock');
Route::post('payment/mock/{booking_id}', [BookingController::class, 'confirmPayment'])->name('payment.confirm');
?>
