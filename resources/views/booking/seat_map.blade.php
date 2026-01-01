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
<!-- Calculate prices for different seat types -->
@php
    $screenPrice = $room->screen_price ?? 0;
    $standardPrice = $screenPrice;
    $vipPrice = $screenPrice + 10000;
    $couplePrice = $screenPrice + 20000;
@endphp

<!-- Legend for colors with numbers format for Vn price -->
<div style="margin-bottom: 20px;">
    <h4>Legend:</h4>
    <p>
        <span style="color:#2ecc71">■</span> Standard ({{ number_format($standardPrice) }} VND)
        <span style="color:#f1c40f">■</span> VIP ({{ number_format($vipPrice) }} VND)
        <span style="color:#e84393">■</span> Couple ({{ number_format($couplePrice) }} VND)
        <span style="color:#999">■</span> Booked
        <span style="color:#3498db">■</span> Selected
    </p>
</div>

<!-- Form submit selected seats -->
<form method="POST" action="{{ route('movies.selectseats', ['showtime_id' => $showtime->id]) }}" id="seatForm">
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
                        // Real price calculation with screen price + seat base price
                        $seatPrice = ($room->screen_price ?? 0) + ($seat->base_price ?? 0);
                        // Determine seat color
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
                        data-seat-price="{{ $seatPrice }}"
                        <!-- display row and number for couple seat logic -->
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

<!-- JavaScript to handle seat selection -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    //Take all available seat buttons
    const seatButtons = document.querySelectorAll('.seat-btn.available');
    const selectedSeatIds = document.getElementById('selectedSeatIds');
    const seatList = document.getElementById('seatList');
    const totalPrice = document.getElementById('totalPrice');
    const bookBtn = document.getElementById('bookBtn');
    // Array to hold selected seats
    let selectedSeats = [];
    // Add click event to each seat button
    seatButtons.forEach(button => {
        button.addEventListener('click', function() {
            const seatId = this.getAttribute('data-seat-id');
            const seatCode = this.getAttribute('data-seat-code');
            const seatType = parseInt(this.getAttribute('data-seat-type'));
            const seatPrice = parseFloat(this.getAttribute('data-seat-price'));
            
            // Toggle seat selection
            if (selectedSeats.find(seat => seat.id === seatId)) {
                // Deselect seat
                selectedSeats = selectedSeats.filter(seat => seat.id !== seatId);
                this.style.backgroundColor = this.getAttribute('data-original-color') || 
                    (seatType === 2 ? '#f1c40f' : seatType === 3 ? '#e84393' : '#2ecc71');
                this.style.border = '2px solid transparent';
            } else {
                // Select seat  
                if (!this.getAttribute('data-original-color')) {
                    this.setAttribute('data-original-color', this.style.backgroundColor);
                }
                selectedSeats.push({
                    id: seatId,
                    code: seatCode,
                    type: seatType,
                    price: seatPrice
                });
                this.style.backgroundColor = '#3498db'; // Selected color
                this.style.border = '2px solid #2980b9';
            }
            
            // Update UI
            updateSelectedSeatsDisplay();
        });
    });
    // Function to update selected seats display and total price
    function updateSelectedSeatsDisplay() {
        // If no seats selected -> disable button
        if (selectedSeats.length === 0) {
            seatList.textContent = 'None';
            totalPrice.textContent = '0';
            selectedSeatIds.value = '';
            bookBtn.disabled = true;
            bookBtn.style.backgroundColor = '#6c757d';
        } else {
            // Show selected seats
            const seatCodes = selectedSeats.map(seat => seat.code).join(', ');
            seatList.textContent = seatCodes;
            
            // Calculate total price
            const total = selectedSeats.reduce((sum, seat) => sum + seat.price, 0);
            // Format number for Vn locale
            totalPrice.textContent = new Intl.NumberFormat('vi-VN').format(total);
            
            // Update hidden input
            const seatIds = selectedSeats.map(seat => seat.id);
            selectedSeatIds.value = JSON.stringify(seatIds);
            
            // Enable button of selected seats
            bookBtn.disabled = false;
            bookBtn.style.backgroundColor = '#28a745';
        }
    }
});
</script>

@endsection