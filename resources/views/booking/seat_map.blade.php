@extends('layouts.main')

@section('title', 'Seat Map - {{ $showtime->movie_title }}')

@section('content')
<h2>Select Seats</h2>

<p>
    <span style="color:#2ecc71">■</span> Standard
    <span style="color:#f1c40f">■</span> VIP
    <span style="color:#e84393">■</span> Couple
    <span style="color:#999">■</span> Booked
</p>
@php
    $grouped = $seats->groupBy('seat_row');
@endphp
@foreach($grouped as $row => $seatsInRow)
    <div style="margin-bottom: 15px;">
        <strong>Row {{ $row }}:</strong>
        @foreach($seatsInRow as $seat)
            @php
                $isBooked = in_array($seat->id, $bookedSeats);
                if ($isBooked) {
                    $seatColor = '#999'; // Booked
                } elseif ($seat->seat_type_id == 2) {
                    $seatColor = '#f1c40f'; // VIP (id = 2)
                } elseif ($seat->seat_type_id == 3) {
                    $seatColor = '#e84393'; // Couple (id = 3)
                } else {
                    $seatColor = '#2ecc71'; // Standard (id = 1)
                }
            @endphp
            <button
                style="
                width: 40px; 
                height: 40px; 
                margin: 5px; 
                background: {{ $seatColor }};
                    color: white;
                    font-weight: bold;
                    cursor: {{ $isBooked ? 'not-allowed' : 'pointer' }};
                "
                {{ $isBooked ? 'disabled' : '' }}>
                {{ $seat->seat_number }}
            </button>
        @endforeach
    </div>
@endforeach

@endsection