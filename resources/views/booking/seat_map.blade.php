@extends('layouts.main')

@section('title', 'Seat Map - {{ $showtime->movie_title }}')

@section('content')
<h2>Select Seats</h2>

<!-- success/error messages -->
@if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0;">
        {{ session('error') }}
    </div>
@endif

<!-- Legend for seat types -->
<div style="margin-bottom: 20px;">
    <h4>Legend:</h4>
    <p>
        <span style="color:#2ecc71">■</span> Standard
        <span style="color:#f1c40f">■</span> VIP 
        <span style="color:#e84393">■</span> Couple
        <span style="color:#999">■</span> Booked
        <span style="color:#3498db">■</span> Selected
    </p>
    <small style="color: #666;">*Giá cuối cùng sẽ được tính toán bởi hệ thống</small>
</div>

<!-- Form submit selected seats -->
<form method="POST" action="{{ route('booking.book', ['showtime_id' => $showtime->id]) }}" id="seatForm">
    @csrf
    <div id="seatMap" style="margin-bottom: 20px;">
        @php
            $grouped = $seats->groupBy('seat_row');
        @endphp
        @foreach($grouped as $row => $seatsInRow)
            <div style="margin-bottom: 15px;">
                <strong>Row {{ $row }}:</strong>
                @foreach($seatsInRow as $seat)
                    @php
                        $isBooked = in_array($seat->id, $bookedSeats);
                        // Determine seat color based on type and booking status
                        if ($isBooked) {
                            $seatColor = '#999'; // Booked
                        } elseif ($seat->seat_type_id == 2) {
                            $seatColor = '#f1c40f'; // VIP
                        } elseif ($seat->seat_type_id == 3) {
                            $seatColor = '#e84393'; // Couple
                        } else {
                            $seatColor = '#2ecc71'; // Standard
                        }
                    @endphp
                    <button
                        type="button"
                        class="seat-btn {{ $isBooked ? 'booked' : 'available' }}" 
                        data-seat-id="{{ $seat->id }}"
                        data-seat-code="{{ $seat->seat_code }}"
                        data-seat-type="{{ $seat->seat_type_id }}"
                        style="
                            width: 40px; 
                            height: 40px; 
                            margin: 5px; 
                            background: {{ $seatColor }};
                            color: white;
                            font-weight: bold;
                            border: 2px solid transparent;
                            border-radius: 5px;
                            cursor: {{ $isBooked ? 'not-allowed' : 'pointer' }};
                        "
                        {{ $isBooked ? 'disabled' : '' }}>
                        {{ $seat->seat_number }}
                    </button>
                @endforeach
            </div>
        @endforeach
    </div>

    <!-- Show selected seats and total price -->
    <div id="selectedSeats" style="margin-bottom: 20px;">
        <h4>Selected Seats: <span id="seatList">None</span></h4>
        <p>Total Price: <span id="totalPrice">0</span> VND</p>
    </div>

    <!-- Hidden input to store IDs of selected seats -->
    <input type="hidden" name="seats" id="selectedSeatIds" value="">

    <!-- Submit button -->
    <div>
        <button type="submit" id="bookBtn" 
                style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;"
                disabled>
            Book Selected Seats
        </button>
        <a href="{{ route('movies.showtimes', ['id' => $showtime->movie_id]) }}" 
           style="background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;">
            Back to Showtimes
        </a>
    </div>
</form>

<!-- Include seat map JavaScript -->
<script src="{{ asset('js/seat_map.js') }}"></script>

@endsection