<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Booking;
use App\Models\User;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * AdminDashboardController
 * 
 * Handles admin dashboard operations including:
 * - Statistics overview and metrics
 * - Revenue and booking analytics
 * - User activity tracking
 * - System performance monitoring
 */
class AdminDashboardController extends Controller
{
    public function index()
    {
        // Basic stats
        $totalUsers = User::where('role', 'user')->count();
        $totalMovies = Movie::count();

        // Today's stats - count tickets from booking_seats
        $today = Carbon::today();
        $ticketsSoldToday = DB::table('booking_seats')
            ->join('bookings', 'booking_seats.booking_id', '=', 'bookings.id')
            ->whereDate('bookings.created_at', $today)
            ->where('bookings.payment_status', 'paid')
            ->count();

        $revenueToday = Booking::whereDate('created_at', $today)
            ->where('payment_status', 'paid')
            ->sum('total_price');

        // This month's revenue
        $revenueThisMonth = Booking::whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->where('payment_status', 'paid')
            ->sum('total_price');

        // Active showtimes (only ongoing and upcoming)
        $activeShowtimes = Showtime::with('movie')->get()->filter(function($showtime) {
            return in_array($showtime->status, ['ongoing', 'upcoming']);
        })->count();

        // Highest and lowest revenue movies (show all movies, even if revenue is 0)
        $movieRevenue = Movie::leftJoin('showtimes', 'movies.id', '=', 'showtimes.movie_id')
            ->leftJoin('bookings', function ($join) {
                $join->on('showtimes.id', '=', 'bookings.showtime_id')
                    ->where('bookings.payment_status', 'paid');
            })
            ->select('movies.id', 'movies.title', DB::raw('COALESCE(SUM(bookings.total_price), 0) as revenue'))
            ->groupBy('movies.id', 'movies.title')
            ->orderBy('revenue', 'desc')
            ->get();

        $highestRevenueMovie = $movieRevenue->first();
        $lowestRevenueMovie = $movieRevenue->last();

        // Recent bookings
        $recentBookings = Booking::with(['user', 'showtime.movie', 'bookingSeats'])
            ->latest()
            ->take(10)
            ->get();

        // Additional stats
        $nowShowingMovies = Movie::where('status', 'now_showing')->count();
        $comingSoonMovies = Movie::where('status', 'coming_soon')->count();
        $totalRevenue = Booking::where('payment_status', 'paid')->sum('total_price');

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalMovies',
            'ticketsSoldToday',
            'revenueToday',
            'revenueThisMonth',
            'activeShowtimes',
            'highestRevenueMovie',
            'lowestRevenueMovie',
            'recentBookings',
            'nowShowingMovies',
            'comingSoonMovies',
            'totalRevenue'
        ));
    }
}
