<?php

// NOTE: This controller is for the Admin side.
// NOTE: Main job: manage bookings (list, view detail, cancel + release seats).

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ShowtimeSeat; // NOTE: Imported but not used directly in this file (we update with DB::table instead).
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminBookingController extends Controller
{
    // ============================================================
    // INDEX() - Booking List Page (with filters + search + stats)
    // ============================================================
    public function index(Request $request)
    {
        // NOTE: Start a query for bookings.
        // NOTE: with(...) = eager loading to avoid N+1 queries (faster and cleaner).
        $query = Booking::with(['user', 'showtime.movie', 'showtime.room', 'bookingSeats.seat']);

        // ------------------------------------------------------------
        // FILTER 1: Filter by booking status (confirmed/pending/etc.)
        // Example URL: /admin/bookings?status=confirmed
        // ------------------------------------------------------------
        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // ------------------------------------------------------------
        // FILTER 2: Filter by payment status (paid/unpaid/etc.)
        // Example URL: /admin/bookings?payment_status=paid
        // ------------------------------------------------------------
        // Filter by payment status
        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        // ------------------------------------------------------------
        // FILTER 3: Filter by booking date (only date part, not time)
        // Example URL: /admin/bookings?date=2026-01-24
        // ------------------------------------------------------------
        // Filter by date
        if ($request->date) {
            $query->whereDate('booking_date', $request->date);
        }

        // ------------------------------------------------------------
        // SEARCH: Search booking by ID OR by user email/name
        // Example URL: /admin/bookings?search=15
        // Example URL: /admin/bookings?search=gmail
        // ------------------------------------------------------------
        // Search by user email or booking ID
        if ($request->search) {
            $search = $request->search;

            // NOTE: where(function...) is used so the OR logic stays grouped correctly.
            $query->where(function($q) use ($search) {
                $q->where('id', $search)
                  ->orWhereHas('user', function($q2) use ($search) {
                      // NOTE: orWhereHas('user') means: check the related user table.
                      $q2->where('email', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        // NOTE: Show newest bookings first.
        // NOTE: paginate(20) = 20 bookings per page (good for admin tables).
        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        // ------------------------------------------------------------
        // STATISTICS: Quick numbers for admin dashboard view
        // ------------------------------------------------------------
        // Statistics
        $stats = [
            // NOTE: Total number of bookings in database
            'total' => Booking::count(),

            // NOTE: Count bookings by status
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
            'expired' => Booking::where('status', 'expired')->count(),

            // NOTE: Revenue only counts PAID bookings (sum of total_price)
            'total_revenue' => Booking::where('payment_status', 'paid')->sum('total_price'),

            // NOTE: Bookings created today (based on created_at, not booking_date)
            'today_bookings' => Booking::whereDate('created_at', Carbon::today())->count(),
        ];

        // NOTE: Return admin bookings list view + pass bookings and stats data.
        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    // ============================================================
    // SHOW() - Booking Detail Page
    // ============================================================
    public function show(Booking $booking)
    {
        // NOTE: Route Model Binding:
        // Laravel automatically finds the Booking by ID from the route.
        // Example: /admin/bookings/5 -> Booking with id=5

        // NOTE: Load deeper relationships for the detail page.
        // - screenType and seatType are extra details for better display.
        $booking->load([
            'user',
            'showtime.movie',
            'showtime.room.screenType',
            'bookingSeats.seat.seatType'
        ]);

        // NOTE: Return booking detail page.
        return view('admin.bookings.show', compact('booking'));
    }

    // ============================================================
    // CANCEL() - Cancel Booking + Release Seats
    // ============================================================
    public function cancel(Booking $booking)
    {
        // NOTE: Prevent cancelling twice (basic safety check).
        if ($booking->status === 'cancelled' || $booking->status === 'expired') {
            return back()->with('error', 'Booking is already cancelled or expired!');
        }

        // NOTE: Transaction is used because we update booking + seats together.
        // If something fails, rollback will keep database consistent.
        DB::beginTransaction();
        try {
            // Update booking status
            // NOTE: Mark booking as cancelled.
            $booking->update(['status' => 'cancelled']);

            // Release seats
            // NOTE: Loop through seats in this booking and set them back to "available"
            // in the showtime_seats table.
            foreach ($booking->bookingSeats as $bookingSeat) {
                DB::table('showtime_seats')
                    ->where('showtime_id', $booking->showtime_id)
                    ->where('seat_id', $bookingSeat->seat_id)
                    ->update(['status' => 'available']);
            }

            // NOTE: If everything is okay, save changes.
            DB::commit();

            // NOTE: Redirect back with success message.
            return back()->with('success', 'Booking cancelled successfully!');

        } catch (\Exception $e) {
            // NOTE: If any error happens, undo all changes.
            DB::rollBack();

            // NOTE: Return error message to admin (helpful for debugging).
            return back()->with('error', 'Failed to cancel booking: ' . $e->getMessage());
        }
    }
}