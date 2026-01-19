<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use App\Models\ShowtimePrice;
use App\Models\ShowtimeSeat;
use App\Models\Movie;
use App\Models\Room;
use App\Models\SeatType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminShowtimeController extends Controller
{
    public function index(Request $request)
    {
        $query = Showtime::with(['movie', 'room.screenType', 'showtimeSeats']);

        // Filter by movie
        if ($request->movie_id) {
            $query->where('movie_id', $request->movie_id);
        }

        // Filter by room
        if ($request->room_id) {
            $query->where('room_id', $request->room_id);
        }

        // Filter by date
        if ($request->date) {
            $query->whereDate('show_date', $request->date);
        }

        $showtimes = $query->orderBy('show_date', 'desc')
            ->orderBy('show_time', 'desc')
            ->paginate(20);

        $movies = Movie::where('status', '!=', 'ended')->orderBy('title')->get();
        $rooms = Room::with('screenType')->orderBy('name')->get();

        return view('admin.showtimes.index', compact('showtimes', 'movies', 'rooms'));
    }

    public function create()
    {
        $movies = Movie::where('status', 'now_showing')->orderBy('title')->get();
        $rooms = Room::with('screenType')->orderBy('name')->get();
        $seatTypes = SeatType::all();

        return view('admin.showtimes.create', compact('movies', 'rooms', 'seatTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'room_id' => 'required|exists:rooms,id',
            'show_date' => 'required|date',
            'show_time' => 'required',
            'seat_type_prices' => 'required|array',
            'seat_type_prices.*' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            // Check if showtime already exists
            $exists = Showtime::where('room_id', $request->room_id)
                ->where('show_date', $request->show_date)
                ->where('show_time', $request->show_time)
                ->exists();

            if ($exists) {
                return back()->with('error', 'This showtime slot is already taken!');
            }

            // Create showtime
            $showtime = Showtime::create([
                'movie_id' => $request->movie_id,
                'room_id' => $request->room_id,
                'show_date' => $request->show_date,
                'show_time' => $request->show_time,
            ]);

            // Create showtime prices for each seat type
            foreach ($request->seat_type_prices as $seatTypeId => $price) {
                ShowtimePrice::create([
                    'showtime_id' => $showtime->id,
                    'seat_type_id' => $seatTypeId,
                    'price' => $price,
                ]);
            }

            // Get all seats in the room
            $room = Room::with('seats')->find($request->room_id);
            
            // Create showtime_seats for each seat
            foreach ($room->seats as $seat) {
                ShowtimeSeat::create([
                    'showtime_id' => $showtime->id,
                    'seat_id' => $seat->id,
                    'status' => 'available',
                ]);
            }

            DB::commit();
            return redirect()->route('admin.showtimes.index')
                ->with('success', 'Showtime created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create showtime: ' . $e->getMessage());
        }
    }

    public function edit(Showtime $showtime)
    {
        $showtime->load(['movie', 'room.screenType', 'showtimePrices.seatType', 'showtimeSeats']);
        $movies = Movie::where('status', 'now_showing')->orderBy('title')->get();
        $rooms = Room::with('screenType')->orderBy('name')->get();
        $seatTypes = SeatType::all();

        return view('admin.showtimes.edit', compact('showtime', 'movies', 'rooms', 'seatTypes'));
    }

    public function update(Request $request, Showtime $showtime)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'room_id' => 'required|exists:rooms,id',
            'show_date' => 'required|date',
            'show_time' => 'required',
            'seat_type_prices' => 'required|array',
            'seat_type_prices.*' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            // Check if showtime slot is taken (excluding current showtime)
            $exists = Showtime::where('room_id', $request->room_id)
                ->where('show_date', $request->show_date)
                ->where('show_time', $request->show_time)
                ->where('id', '!=', $showtime->id)
                ->exists();

            if ($exists) {
                return back()->with('error', 'This showtime slot is already taken!');
            }

            // Update showtime
            $showtime->update([
                'movie_id' => $request->movie_id,
                'room_id' => $request->room_id,
                'show_date' => $request->show_date,
                'show_time' => $request->show_time,
            ]);

            // Update showtime prices
            foreach ($request->seat_type_prices as $seatTypeId => $price) {
                ShowtimePrice::updateOrCreate(
                    [
                        'showtime_id' => $showtime->id,
                        'seat_type_id' => $seatTypeId,
                    ],
                    ['price' => $price]
                );
            }

            DB::commit();
            return redirect()->route('admin.showtimes.index')
                ->with('success', 'Showtime updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update showtime: ' . $e->getMessage());
        }
    }

    public function destroy(Showtime $showtime)
    {
        DB::beginTransaction();
        try {
            // Check if there are any bookings
            if ($showtime->bookings()->exists()) {
                return back()->with('error', 'Cannot delete showtime with existing bookings!');
            }

            // Delete showtime seats
            $showtime->showtimeSeats()->delete();

            // Delete showtime prices
            $showtime->showtimePrices()->delete();

            // Delete showtime
            $showtime->delete();

            DB::commit();
            return back()->with('success', 'Showtime deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete showtime: ' . $e->getMessage());
        }
    }
}
