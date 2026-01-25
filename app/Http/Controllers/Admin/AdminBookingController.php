<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ShowtimeSeat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * AdminBookingController
 * 
 * Handles admin booking management operations including:
 * - Booking listing with filtering and search
 * - Booking detail view with relationships
 * - Booking cancellation and seat release
 * - Booking statistics and analytics
 */
class AdminBookingController extends Controller
{
    // Booking List Page
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'showtime.movie', 'showtime.room', 'bookingSeats.seat']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date
        if ($request->date) {
            $query->whereDate('booking_date', $request->date);
        }

        // Search by user email or booking ID
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', $search)
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('email', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => Booking::count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
            'expired' => Booking::where('status', 'expired')->count(),
            'total_revenue' => Booking::where('payment_status', 'paid')->sum('total_price'),
            'today_bookings' => Booking::whereDate('created_at', Carbon::today())->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    // Booking Detail Page
    public function show(Booking $booking)
    {
        $booking->load([
            'user',
            'showtime.movie',
            'showtime.room.screenType',
            'bookingSeats.seat.seatType'
        ]);

        return view('admin.bookings.show', compact('booking'));
    }

    // Cancel Booking + Release Seats
    public function cancel(Booking $booking)
    {
        if ($booking->status === 'cancelled' || $booking->status === 'expired') {
            return back()->with('error', 'Booking is already cancelled or expired!');
        }

        DB::beginTransaction();
        try {
            // Update booking status
            $booking->update(['status' => 'cancelled']);

            // Release seats
            foreach ($booking->bookingSeats as $bookingSeat) {
                DB::table('showtime_seats')
                    ->where('showtime_id', $booking->showtime_id)
                    ->where('seat_id', $bookingSeat->seat_id)
                    ->update(['status' => 'available']);
            }

            DB::commit();
            return back()->with('success', 'Booking cancelled successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel booking: ' . $e->getMessage());
        }
    }
}