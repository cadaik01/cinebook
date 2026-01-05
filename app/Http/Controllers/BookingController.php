<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BookingController extends Controller
{
    /**
     * Display seat map for a specific showtime
     */
    public function seatMap($showtime_id)
    {
        // Get showtime details
        $showtime = DB::table('showtimes')->where('id', $showtime_id)->first();
        
        if (!$showtime) {
            return redirect()->route('homepage')->with('error', 'Showtime not found.');
        }
        
        // Get room with screen type and pricing
        $room = DB::table('rooms')
            ->join('screen_types', 'rooms.screen_type_id', '=', 'screen_types.id')
            ->where('rooms.id', $showtime->room_id)
            ->select('rooms.*', 'screen_types.price as screen_price')
            ->first();
        
        // Get all seats in the room with their types
        $seats = DB::table('seats')
            ->join('seat_types', 'seats.seat_type_id', '=', 'seat_types.id')
            ->where('room_id', $room->id)
            ->select('seats.*', 'seat_types.name as seat_type_name', 'seat_types.base_price')
            ->orderBy('seat_row', 'asc')
            ->orderBy('seat_number', 'asc')
            ->get();
        
        // Get booked seats for this showtime
        $bookedSeats = DB::table('showtime_seats')
            ->where('showtime_id', $showtime_id)
            ->where('status', 'booked')
            ->pluck('seat_id')
            ->toArray();
            
        return view('booking.seat_map', compact('showtime', 'room', 'seats', 'bookedSeats'));
    }
    
    /**
     * Process seat booking with proper validation and pricing
     */
    public function bookSeats(Request $request, $showtime_id)
    {
        // Check if user is logged in
        $user_id = Session::get('user_id');
        if (!$user_id) {
            return redirect('/login')->with('error', 'Please log in to book seats.');
        }
        
        // Get selected seats from request
        $selectedSeatsJson = $request->input('seats', '[]');
        $selectedSeats = json_decode($selectedSeatsJson, true);
        
        // Validate input
        if (empty($selectedSeats) || !is_array($selectedSeats)) {
            return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                           ->with('error', 'No seats selected.');
        }
        
        // Get showtime and room information for pricing
        $showtime = DB::table('showtimes')->where('id', $showtime_id)->first();
        if (!$showtime) {
            return redirect()->route('homepage')->with('error', 'Invalid showtime.');
        }
        
        $room = DB::table('rooms')
            ->join('screen_types', 'rooms.screen_type_id', '=', 'screen_types.id')
            ->where('rooms.id', $showtime->room_id)
            ->select('rooms.*', 'screen_types.price as screen_price')
            ->first();
        
        // Begin transaction for data integrity
        DB::beginTransaction();
        
        try {
            $bookedSeats = [];
            $alreadyBookedSeats = [];
            $totalPrice = 0;
            $couplePairs = [];
            
            // Validate and process each selected seat
            foreach ($selectedSeats as $seat_id) {
                // Validate seat exists
                $seat = DB::table('seats')
                    ->join('seat_types', 'seats.seat_type_id', '=', 'seat_types.id')
                    ->where('seats.id', $seat_id)
                    ->select('seats.*', 'seat_types.base_price', 'seat_types.name as seat_type_name')
                    ->first();
                    
                if (!$seat) {
                    DB::rollback();
                    return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                                   ->with('error', 'Invalid seat selected.');
                }
                
                // Check if seat is already booked
                $existingBooking = DB::table('showtime_seats')
                    ->where('showtime_id', $showtime_id)
                    ->where('seat_id', $seat_id)
                    ->first();
                    
                if ($existingBooking) {
                    $alreadyBookedSeats[] = $seat->seat_code;
                    continue;
                }
                
                // Calculate real price from database
                $seatPrice = ($room->screen_price ?? 0) + ($seat->base_price ?? 0);
                
                // Handle couple seat validation
                if ($seat->seat_type_id == 3) { // Couple seat
                    $pairValidation = $this->validateCoupleSeat($seat, $selectedSeats, $showtime_id);
                    if (!$pairValidation['valid']) {
                        DB::rollback();
                        return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                                       ->with('error', $pairValidation['message']);
                    }
                    
                    // Track couple pairs to avoid double processing
                    $pairKey = $this->getCouplePairKey($seat->seat_code);
                    if (!in_array($pairKey, $couplePairs)) {
                        $couplePairs[] = $pairKey;
                    }
                }
                
                // Book the seat
                DB::table('showtime_seats')->insert([
                    'showtime_id' => $showtime_id,
                    'seat_id' => $seat_id,
                    'status' => 'booked',
                    'user_id' => $user_id,
                ]);
                
                $bookedSeats[] = $seat->seat_code;
                $totalPrice += $seatPrice;
            }
            
            // If we have successfully booked seats, create booking record
            if (!empty($bookedSeats)) {
                $booking_id = DB::table('bookings')->insertGetId([
                    'user_id' => $user_id,
                    'showtime_id' => $showtime_id,
                    'total_price' => $totalPrice,
                    'status' => 'confirmed',
                    'payment_status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Create booking_seats records
                foreach ($selectedSeats as $seat_id) {
                    if (!DB::table('showtime_seats')
                           ->where('showtime_id', $showtime_id)
                           ->where('seat_id', $seat_id)
                           ->where('status', 'booked')
                           ->exists()) {
                        continue; // Skip seats that weren't actually booked
                    }
                    
                    $seat = DB::table('seats')
                        ->join('seat_types', 'seats.seat_type_id', '=', 'seat_types.id')
                        ->where('seats.id', $seat_id)
                        ->select('seats.*', 'seat_types.base_price')
                        ->first();
                        
                    $seatPrice = ($room->screen_price ?? 0) + ($seat->base_price ?? 0);
                    
                    DB::table('booking_seats')->insert([
                        'booking_id' => $booking_id,
                        'seat_id' => $seat_id,
                        'price' => $seatPrice,
                    ]);
                }
            }
            
            DB::commit();
            
            // Prepare success message
            $message = '';
            if (!empty($bookedSeats)) {
                $message .= 'Successfully booked seats: ' . implode(', ', $bookedSeats) . '. ';
                $message .= 'Total: ' . number_format($totalPrice) . ' VND. ';
            }
            if (!empty($alreadyBookedSeats)) {
                $message .= 'Some seats were already booked: ' . implode(', ', $alreadyBookedSeats) . '.';
            }
            
            return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                           ->with('success', $message ?: 'Booking completed.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                           ->with('error', 'An error occurred while booking seats. Please try again.');
        }
    }
    
    /**
     * Validate couple seat logic
     */
    private function validateCoupleSeat($seat, $selectedSeats, $showtime_id)
    {
        // Extract row and seat number
        $rowLetter = substr($seat->seat_code, 0, 1);
        $seatNumber = (int)substr($seat->seat_code, 1);
        
        // Determine pair seat number
        $pairSeatNumber = ($seatNumber % 2 === 1) ? $seatNumber + 1 : $seatNumber - 1;
        $pairSeatCode = $rowLetter . $pairSeatNumber;
        
        // Find pair seat
        $pairSeat = DB::table('seats')
            ->where('seat_code', $pairSeatCode)
            ->where('room_id', $seat->room_id)
            ->first();
            
        if (!$pairSeat) {
            return ['valid' => false, 'message' => 'Couple seat pair not found.'];
        }
        
        // Check if pair seat is also selected
        if (!in_array($pairSeat->id, $selectedSeats)) {
            return ['valid' => false, 'message' => 'Both seats in couple pair must be selected.'];
        }
        
        // Check if pair seat is already booked
        $pairBooked = DB::table('showtime_seats')
            ->where('showtime_id', $showtime_id)
            ->where('seat_id', $pairSeat->id)
            ->exists();
            
        if ($pairBooked) {
            return ['valid' => false, 'message' => 'Couple seat pair is not available.'];
        }
        
        return ['valid' => true, 'message' => ''];
    }
    
    /**
     * Get couple pair key for tracking
     */
    private function getCouplePairKey($seatCode)
    {
        $rowLetter = substr($seatCode, 0, 1);
        $seatNumber = (int)substr($seatCode, 1);
        $lowerNumber = ($seatNumber % 2 === 1) ? $seatNumber : $seatNumber - 1;
        return $rowLetter . $lowerNumber . '-' . ($lowerNumber + 1);
    }
}