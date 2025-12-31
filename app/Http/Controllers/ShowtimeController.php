<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ShowtimeController extends Controller
{
    //1. showtimes function to fetch showtimes for a specific movie
    public function showtimes($id)
    {
        $movie = DB::table('movies')->where('id', $id)->first();
        $showtimes = DB::table('showtimes')
        ->join('rooms', 'showtimes.room_id', '=', 'rooms.id')
        ->where('showtimes.movie_id', $id)
        ->select(
            'showtimes.id',
            'showtimes.show_date',
            'showtimes.show_time',
            'rooms.id as room_id',
        )
        ->orderBy('show_date', 'asc')
        ->orderBy('show_time', 'asc')
        ->get();
        return view('movie.showtimes', compact('movie', 'showtimes'));
    }
    //2. seatMap function to display seat map for a specific showtime
    public function seatMap($showtime_id)
    {
        //table showtime
        $showtime = DB::table('showtimes')->where('id', $showtime_id)->first();
        //table room
        $room = DB::table('rooms')->where('id', $showtime->room_id)->first();
        // rooms's seats
        $seats = DB::table('seats')
            ->where('room_id', $room->id)
            ->orderBy('seat_row', 'asc')
            ->orderBy('seat_number', 'asc')
            ->get();
        // booked seats for the showtime
        $bookedSeats = DB::table('showtime_seats')
            ->where('showtime_id', $showtime_id)
            ->where('status', 'booked')
            ->pluck('seat_id')
            ->toArray();
        return view('booking.seat_map', compact('showtime', 'room', 'seats', 'bookedSeats'));
    }
}
