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
    const seatButtons = document.querySelectorAll('.seat-btn.available');
    const selectedSeatIds = document.getElementById('selectedSeatIds');
    const seatList = document.getElementById('seatList');
    const totalPrice = document.getElementById('totalPrice');
    const bookBtn = document.getElementById('bookBtn');
    
    let selectedSeats = [];
    
    seatButtons.forEach(button => {
        button.addEventListener('click', function() {
            const seatId = this.getAttribute('data-seat-id');
            const seatCode = this.getAttribute('data-seat-code');
            const seatType = parseInt(this.getAttribute('data-seat-type'));
            const seatPrice = parseFloat(this.getAttribute('data-seat-price'));
            
            const rowLetter = seatCode.match(/^[A-Z]+/)[0];
            
            if (rowLetter === 'H') {
                // Handle couple seat logic (H1-2, H3-4, H5-6, ...)
                const seatNumber = parseInt(seatCode.match(/\d+$/)[0]);
                
                let pairSeatNumber;
                if (seatNumber % 2 === 1) {
                    pairSeatNumber = seatNumber + 1;
                } else {
                    pairSeatNumber = seatNumber - 1;
                }
                const pairSeatCode = rowLetter + pairSeatNumber;
                const pairSeatButton = document.querySelector(`[data-seat-code="${pairSeatCode}"]`);
                
                if (selectedSeats.find(seat => seat.id === seatId)) {
                    // Deselect both seats in the pair
                    selectedSeats = selectedSeats.filter(seat => seat.id !== seatId);
                    this.style.backgroundColor = this.getAttribute('data-original-color') || '#e84393';
                    this.style.border = '2px solid transparent';
                    
                    if (pairSeatButton) {
                        const pairSeatId = pairSeatButton.getAttribute('data-seat-id');
                        selectedSeats = selectedSeats.filter(seat => seat.id !== pairSeatId);
                        pairSeatButton.style.backgroundColor = pairSeatButton.getAttribute('data-original-color') || '#e84393';
                        pairSeatButton.style.border = '2px solid transparent';
                    }
                } else {
                    // Select both seats in the pair
                    if (pairSeatButton && pairSeatButton.classList.contains('available')) {
                        const pairSeatId = pairSeatButton.getAttribute('data-seat-id');
                        const pairSeatType = parseInt(pairSeatButton.getAttribute('data-seat-type'));
                        const pairSeatPrice = parseFloat(pairSeatButton.getAttribute('data-seat-price'));
                        
                        if (!this.getAttribute('data-original-color')) {
                            this.setAttribute('data-original-color', this.style.backgroundColor);
                        }
                        if (!pairSeatButton.getAttribute('data-original-color')) {
                            pairSeatButton.setAttribute('data-original-color', pairSeatButton.style.backgroundColor);
                        }
                        
                        selectedSeats.push({
                            id: seatId,
                            code: seatCode,
                            type: seatType,
                            price: seatPrice
                        });
                        selectedSeats.push({
                            id: pairSeatId,
                            code: pairSeatCode,
                            type: pairSeatType,
                            price: pairSeatPrice
                        });
                        
                        this.style.backgroundColor = '#3498db';
                        this.style.border = '2px solid #2980b9';
                        pairSeatButton.style.backgroundColor = '#3498db';
                        pairSeatButton.style.border = '2px solid #2980b9';
                    } else {
                        alert('Ghế đôi không khả dụng. Vui lòng chọn cặp ghế khác.');
                        return;
                    }
                }
            } else {
                // Handle regular seat logic (non-couple seats)
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
                    this.style.backgroundColor = '#3498db';
                    this.style.border = '2px solid #2980b9';
                }
            }
            
            updateSelectedSeatsDisplay();
        });
    });
    
    function updateSelectedSeatsDisplay() {
        if (selectedSeats.length === 0) {
            seatList.textContent = 'None';
            totalPrice.textContent = '0';
            selectedSeatIds.value = '';
            bookBtn.disabled = true;
            bookBtn.style.backgroundColor = '#6c757d';
        } else {
            const seatCodes = selectedSeats.map(seat => seat.code).join(', ');
            seatList.textContent = seatCodes;
            
            const total = selectedSeats.reduce((sum, seat) => sum + seat.price, 0);
            totalPrice.textContent = new Intl.NumberFormat('vi-VN').format(total);
            
            const seatIds = selectedSeats.map(seat => seat.id);
            selectedSeatIds.value = JSON.stringify(seatIds);
            
            bookBtn.disabled = false;
            bookBtn.style.backgroundColor = '#28a745';
        }
    }
});
</script>

@endsection