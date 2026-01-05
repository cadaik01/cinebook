<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\ScreenType;
use App\Models\SeatType;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('screenType')->get();
        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        $screenTypes = ScreenType::all();
        $seatTypes = SeatType::all();
        return view('admin.rooms.create', compact('screenTypes', 'seatTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'total_rows' => 'required|integer|min:1|max:20',
            'seats_per_row' => 'required|integer|min:1|max:30',
            'screen_type_id' => 'required|exists:screen_types,id',
        ]);

        DB::beginTransaction();
        try {
            $room = Room::create($validated);

            // Get default seat type (standard)
            $defaultSeatType = SeatType::where('name', 'Standard')->first();
            if (!$defaultSeatType) {
                $defaultSeatType = SeatType::first();
            }

            // Generate seats automatically
            $rowLabels = range('A', 'Z');
            for ($row = 0; $row < $validated['total_rows']; $row++) {
                for ($seat = 1; $seat <= $validated['seats_per_row']; $seat++) {
                    Seat::create([
                        'room_id' => $room->id,
                        'seat_row' => $rowLabels[$row],
                        'seat_number' => $seat,
                        'seat_code' => $rowLabels[$row] . $seat,
                        'seat_type_id' => $defaultSeatType->id,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room created successfully with ' . ($validated['total_rows'] * $validated['seats_per_row']) . ' seats!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create room: ' . $e->getMessage());
        }
    }

    public function edit(Room $room)
    {
        $room->load('seats.seatType', 'screenType');
        $screenTypes = ScreenType::all();
        $seatTypes = SeatType::all();

        // Group seats by row
        $seatsByRow = $room->seats->groupBy('seat_row');

        return view('admin.rooms.edit', compact('room', 'screenTypes', 'seatTypes', 'seatsByRow'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'screen_type_id' => 'required|exists:screen_types,id',
        ]);

        $room->update($validated);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room updated successfully!');
    }

    public function updateSeats(Request $request, Room $room)
    {
        $validated = $request->validate([
            'seats' => 'required|array',
            'seats.*.seat_id' => 'required|exists:seats,id',
            'seats.*.seat_type_id' => 'required|exists:seat_types,id',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['seats'] as $seatData) {
                Seat::where('id', $seatData['seat_id'])
                    ->update(['seat_type_id' => $seatData['seat_type_id']]);
            }

            DB::commit();
            return back()->with('success', 'Seat types updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update seats: ' . $e->getMessage());
        }
    }

    public function destroy(Room $room)
    {
        try {
            $room->delete();
            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete room: ' . $e->getMessage());
        }
    }
}