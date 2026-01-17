@extends('layouts.main')

@section('title', 'Seat Map - {{ $showtime->movie->title }}')

@vite('resources/css/seat_map.css')

@section('content')
<div class="seat-map-container">
    <div class="seat-map-header">
        <h2>Select Seats</h2>
        <p class="showtime-info">{{ $showtime->movie->title }} - {{ $showtime->show_date->format('d/m/Y') }} at {{ $showtime->show_time->format('H:i') }}</p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <!-- Legend for Seat Types -->
    <div class="seat-legend">
        <h4>Legend:</h4>
        <div class="legend-items">
            <div class="legend-item">
                <span class="legend-color standard"></span>
                <span>Standard</span>
            </div>
            <div class="legend-item">
                <span class="legend-color vip"></span>
                <span>VIP</span>
            </div>
            <div class="legend-item">
                <span class="legend-color couple"></span>
                <span>Couple</span>
            </div>
            <div class="legend-item">
                <span class="legend-color booked"></span>
                <span>Booked</span>
            </div>
            <div class="legend-item">
                <span class="legend-color selected"></span>
                <span>Selected</span>
            </div>
        </div>
    </div>

    <!-- Cinema Screen -->
    <div class="cinema-screen">
        <div class="screen-label">Screen</div>
    </div>

    <!-- Seat Map -->
    <div class="seat-map-wrapper">
        <form method="POST" action="{{ route('booking.book', ['showtime_id' => $showtime->id]) }}" id="seatForm">
            @csrf
            <div id="seatMap">
                @php
                    $grouped = $seats->groupBy('seat_row');
                @endphp
                @foreach($grouped as $row => $seatsInRow)
                    <div class="seat-row">
                        <div class="seat-row-label">Row {{ $row }}:</div>
                        <div class="seats-container">
                            @foreach($seatsInRow as $seat)
                                @php
                                    $isBooked = in_array($seat->id, $bookedSeats);
                                @endphp
                                <button
                                    type="button"
                                    class="seat-btn {{ $isBooked ? 'booked' : 'available' }}"
                                    data-seat-id="{{ $seat->id }}"
                                    data-seat-code="{{ $seat->seat_code }}"
                                    data-seat-type="{{ $seat->seat_type_id }}"
                                    {{ $isBooked ? 'disabled' : '' }}>
                                    {{ $seat->seat_number }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Show Selected Seats -->
    <div class="selected-seats-display">
        <h4>Selected Seats: <span id="seatList">None</span></h4>
    </div>

    <!-- Hidden input to store IDs of selected seats -->
    <input type="hidden" name="seats" id="selectedSeatIds" value="" form="seatForm">

    <!-- Action Buttons -->
    <div class="seatmap-actions">
        <button type="submit" id="bookBtn" class="seatmap-book-btn" form="seatForm" disabled>
            Book Selected Seats
        </button>
        <a href="{{ route('movies.showtimes', ['id' => $showtime->movie_id]) }}" class="seatmap-back-btn">
            Back to Showtimes
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/seat_map.js') }}"></script>
@endpush