<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Showtime;
use App\Models\Room;
use App\Models\Seat;
use App\Models\Booking;

class BookingController extends Controller
{
    //Display seat map for a specific showtime
    public function seatMap($showtime_id)
    {
        // Get showtime details using Eloquent
        $showtime = Showtime::with('movie')->find($showtime_id);
        
        if (!$showtime) {
            return redirect()->route('homepage')->with('error', 'Showtime not found.');
        }
        
        // Get room with screen type and pricing using relationships
        $room = Room::with('screenType')->find($showtime->room_id);
        if (!$room) {
            return redirect()->route('homepage')->with('error', 'Room not found.');
        }
        
        // Get all seats in the room with their types using relationships
        $seats = $room->seats()->with('seatType')
            ->orderBy('seat_row', 'asc')
            ->orderBy('seat_number', 'asc')
            ->get();
        
        // Get booked seats for this showtime
        $bookedSeats = DB::table('showtime_seats')
            ->where('showtime_id', $showtime_id)
            ->where('status', 'booked')//only get booked seats
            ->pluck('seat_id') //pluck seat_id for booked seats
            ->toArray();//convert to array
            
        return view('booking.seat_map', compact('showtime', 'room', 'seats', 'bookedSeats'));//return seat map view with data- compact is used to pass multiple variables to the view
    }
    
    /**
     * Process seat booking with proper validation and pricing
    */
    public function bookSeats(Request $request, $showtime_id)
    {
        //1. Check if user is logged in
        $user_id = Session::get('user_id');
        if (!$user_id) {
            return redirect('/login')->with('error', 'Please log in to book seats.');
        }
        
        //2. Get selected seats from request
        $selectedSeatsJson = $request->input('seats', '[]');//decode JSON array of selected seats
        $selectedSeats = json_decode($selectedSeatsJson, true);//decode to PHP array
        
        //3. Validate input
        if (empty($selectedSeats) || !is_array($selectedSeats))//check if seats are selected - with seat array is empty or not an array
        {
            return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                           ->with('error', 'No seats selected.');
        }
        
        //4. Get showtime and room information for pricing
        $showtime = Showtime::find($showtime_id);
        if (!$showtime) {
            return redirect()->route('homepage')->with('error', 'Invalid showtime.');
        }
        
        $room = $showtime->room()->with('screenType')->first();
        if (!$room) {
            return redirect()->route('homepage')->with('error', 'Room not found.');
        }
        
        //5. Collect seat information for confirmation and pricing
        $seatDetails = [];
        $totalPrice = 0;
        $validatedCouplePairs = []; // Track validated couple pairs to avoid duplicate validation
        foreach ($selectedSeats as $seat_id) {
            //Get seat info with pricing using relationships
            $seat = Seat::with('seatType')->find($seat_id);
            //check if seat exists
            if (!$seat) {
            return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                           ->with('error', 'Invalid seat selected.');
            }
             //check if seat is already booked
            $existingBooking = DB::table('showtime_seats')
                ->where('showtime_id', $showtime_id)
                ->where('seat_id', $seat_id)
                ->first();
            //if booked, redirect back with error
            if ($existingBooking) {
                return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                               ->with('error', 'Seat ' . $seat->seat_code . ' is already booked.');
            }
            
            // If seat is couple type, validate pair seat
            if ($seat->seat_type_id === '3') {
                $pairKey = $this->getCouplePairKey($seat->seat_code);
                // Only validate if this couple pair hasn't been validated yet
                if (!in_array($pairKey, $validatedCouplePairs)) {
                    $validation = $this->validateCoupleSeat($seat, $selectedSeats, $showtime_id);
                    if (!$validation['valid']) {
                        return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                                       ->with('error', $validation['message']);
                    }
                    $validatedCouplePairs[] = $pairKey; // Mark this pair as validated
                }
            }
            
        //6. Calculate real price from database
            $seatPrice = ($room->screenType->price ?? 0) + ($seat->seatType->base_price ?? 0);
            $totalPrice += $seatPrice;

        //7. Store seat details for confirmation
            $seatDetails[] = [
                'id' => $seat->id,
                'seat_code' => $seat->seat_code,
                'seat_type' => $seat->seatType->name ?? 'Unknown',
                'price' => $seatPrice,
            ];
        }
        //8. Get Movie info using relationship
        $movie = $showtime->movie;
        //9. Redirect to confirmation page with booking details
        return view('booking.confirm', compact('movie', 'showtime', 'seatDetails', 'totalPrice', 'showtime_id'));
    }
    
    /**
     * Validate couple seat logic to call into bookSeats method
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
     * Generate a unique key for couple seat pairs
     */
    private function getCouplePairKey($seatCode)
    {
        $rowLetter = substr($seatCode, 0, 1);
        $seatNumber = (int)substr($seatCode, 1);
        $lowerNumber = ($seatNumber % 2 === 1) ? $seatNumber : $seatNumber - 1;
        return $rowLetter . $lowerNumber . '-' . ($lowerNumber + 1);
    }
    /**
     * Display booking confirmation page
     */
    public function confirmBooking($booking_id)
    {
        //check logged in
        $user_id = Session::get('user_id');
        if (!$user_id) {
            return redirect('/login')->with('error', 'Please log in to view booking details.');
        }
        //get booking details using relationships
        $booking = Booking::with(['showtime.movie', 'showtime.room'])
            ->where('id', $booking_id)
            ->where('user_id', $user_id)
            ->first();
        //check if booking exists
        if (!$booking) {
            return redirect()->route('homepage')->with('error', 'Booking not found.');
        }
        //get booked seats details using relationships
        $seats = $booking->bookingSeats()->with(['seat.seatType'])->get();
        //return confirmation view with data
        return view('booking.confirmation_details', compact('booking', 'seats'));
    }

    //Process booking confirmation and payment
    public function processBooking(Request $request)
    {
        //1.Get data from form
        $showtime_id = $request->input('showtime_id');
        $total_price = $request->input('total_price');  
        $user_id = Session::get('user_id');
        if (!$user_id) {
            return redirect('/login')->with('error', 'Please log in to complete booking.');
        }
        $payment_method = $request->input('payment_method');
        //2.Get seat ids from form (already an array)
        $selectedSeats = $request->input('seats', []);
        //3.Validate input
        if (empty($selectedSeats) || !is_array($selectedSeats)) {
            return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                           ->with('error', 'No seats selected for booking.');
        }
        //4.Start transaction
        DB::beginTransaction();
        try {
            //a. re-check seat availability
            foreach ($selectedSeats as $seat_id) {
                $existingBooking = DB::table('showtime_seats')
                    ->where('showtime_id', $showtime_id)
                    ->where('seat_id', $seat_id)
                    ->first();
                if ($existingBooking) {
                    DB::rollback();
                    return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                                   ->with('error', 'Seat already booked during your booking process. Please select different seats.');
                }
            }
            
            //b. Book seats in showtime_seats table
            foreach ($selectedSeats as $seat_id) {
                DB::table('showtime_seats')->insert([
                    'showtime_id' => $showtime_id,
                    'seat_id' => $seat_id,
                    'status' => 'booked',
                    'user_id' => $user_id,
                ]);
            }
            
            //c. Create booking record
            //calculate expiration time (10 minutes from now)
            $expirationTime = now()->addMinutes(10);
            //Booking record
            $bookingId = DB::table('bookings')->insertGetId([
                'user_id' => $user_id,
                'showtime_id' => $showtime_id,
                'total_price' => $total_price,
                'status' => 'pending',
                'payment_method' => $payment_method,
                'payment_status' => 'pending',
                'expires_at' => $expirationTime,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            //d. Insert into booking_seats table with pricing for each seat
            foreach ($selectedSeats as $seat_id) {
                $seat = DB::table('seats')
                    ->join('seat_types', 'seats.seat_type_id', '=', 'seat_types.id')
                    ->join('showtimes', 'showtimes.id', '=', DB::raw($showtime_id))
                    ->join('rooms', 'showtimes.room_id', '=', 'rooms.id')
                    ->join('screen_types', 'rooms.screen_type_id', '=', 'screen_types.id')
                    ->where('seats.id', $seat_id)
                    ->select('seats.*', 'seat_types.base_price', 'screen_types.price as screen_price')
                    ->first();
                    
                $seatPrice = ($seat->base_price ?? 0) + ($seat->screen_price ?? 0);
                
                DB::table('booking_seats')->insert([
                    'booking_id' => $bookingId,
                    'seat_id' => $seat_id,
                    'price' => $seatPrice,
                ]);
            }

            //5.Commit transaction
            DB::commit();

            //** Mock payment processing (Momo/VNPay only) */
            // All payments go through mock payment gateway
            return redirect()->route('payment.mock', ['booking_id' => $bookingId]);
        } catch (\Exception $e) {
            //7.Rollback on error
            DB::rollback();
            return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                           ->with('error', 'An error occurred during booking: ' . $e->getMessage());
        }
    }
    //**Display booking success page */
    public function bookingSuccess($booking_id){
        //Check logged in
        $user_id = Session::get('user_id');
        if (!$user_id) {
            return redirect('/login')->with('error', 'Please log in to view booking details.');
        }

        //Get booking details using relationships
        $booking = Booking::with(['showtime.movie', 'showtime.room'])
            ->where('id', $booking_id)
            ->where('user_id', $user_id)
            ->first();

        //check if booking exists
        if (!$booking) {
            return redirect()->route('homepage')->with('error', 'Booking not found.');
        }

        //Get booked seats details using relationships
        $seats = $booking->bookingSeats()->with(['seat.seatType'])->get();
 
        //Generate QR code data (booking info)
        $qrData = "Booking ID: " . $booking->id . "\n"
                . "Movie: " . $booking->showtime->movie->title . "\n"
                . "Showtime: " . $booking->showtime->show_date->format('Y-m-d') . " " . $booking->showtime->show_time . "\n"
                . "Seats: " . implode(', ', $seats->pluck('seat.seat_code')->toArray()) . "\n"
                . "Total Price: " . number_format($booking->total_price, 0) . " VND";
        //Return success view with data
        return view('booking.success', compact('booking', 'seats', 'qrData'));
    }
}