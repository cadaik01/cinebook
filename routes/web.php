<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ShowtimeController;
use Illuminate\Auth\Events\Login;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminMovieController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminRoomController;
use App\Http\Controllers\Admin\AdminShowtimeController;
use App\Http\Controllers\Admin\AdminBookingController;

//homepage Route
Route::get('/index', [MovieController::class, 'index'])->name('movies.index');
Route::get('/movies/{id}', [MovieController::class, 'show']);
Route::get('/', [MovieController::class, 'homepage'])->name('homepage');
// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm']);
Route::post('/login',[LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/logout', [LoginController::class, 'logout']);
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
//Select Seats Route
Route::post('/showtimes/{showtime_id}/seats/select', [ShowtimeController::class, 'selectSeats'])->name('movies.selectseats');

//Admin Routes - Nhóm tất cả các route admin với prefix 'admin'
Route::prefix('admin')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Movies Management
    Route::resource('movies', AdminMovieController::class, ['as' => 'admin']);

    // Users Management
    Route::get('users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('users/{user}/toggle-role', [AdminUserController::class, 'toggleRole'])->name('admin.users.toggle-role');

    // Rooms Management
    Route::resource('rooms', AdminRoomController::class, ['as' => 'admin']);
    Route::post('rooms/{room}/update-seats', [AdminRoomController::class, 'updateSeats'])->name('admin.rooms.update-seats');

    // Showtimes Management
    Route::resource('showtimes', AdminShowtimeController::class, ['as' => 'admin']);

    // Bookings Management
    Route::get('bookings', [AdminBookingController::class, 'index'])->name('admin.bookings.index');
    Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('admin.bookings.show');
    Route::post('bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('admin.bookings.cancel');
});