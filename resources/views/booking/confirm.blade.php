extends('layouts.main')

@section('title', 'Confirm Booking')

@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <h2>Confirm Booking</h2>
    <!-- Countdown Timer -->
<div style="background: #ff6b6b; color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
    <h3 style="margin: 0;">⏰ Expiration Time</h3>
    <div id="countdown" style="font-size: 32px; font-weight: bold; margin-top: 10px;">10:00</div>
    <p style="margin: 5px 0 0 0; font-size: 14px;">Booking will be automatically canceled if not paid within this time</p>
</div>
    <!-- Display movie and showtime details -->
    <div>
        <h3>Booking Details</h3>
        <p><strong>Movie:</strong> {{ $movie->title }}</p>
        <p><strong>Showday:</strong> {{ date('F j, Y', strtotime($showtime->start_time)) }}</p>
        <p><strong>Showtime:</strong> {{ date('g:i A', strtotime($showtime->start_time)) }}</p>
        <p><strong>Room:</strong> {{ $room->name }}</p>
    </div>
    <!-- Display selected seats -->
    <div>
        <h3>Selected Seats</h3>
        <table>
            <thead>
                <tr>
                    <th>Seat Code</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($seatDetails as $seat)
                    <tr>
                        <td>{{ $seat['seat_code'] }}</td>
                        <td>{{ number_format($seat['price']) }} VND</td>
                    </tr>
                @endforeach
                <tr>
                    <td><strong>Total Price</strong></td>
                    <td><strong>{{ number_format($totalPrice) }} VND</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
    <!-- Payment form could go here -->
    <form method="POST" action="{{ route('booking.process', ['showtime_id' => $showtime_id]) }}">
        @csrf
        <input type="hidden" name="showtime_id" value="{{ $showtime_id }}">
        <input type="hidden" name="seats" value='@json_encode(array_column($seatDetails, "id"))'>
        <input type="hidden" name="total_price" value="{{ $totalPrice }}">
        
        <h3>Payment method</h3>
        <select name="payment_method" required>
            <option value="">Select Payment Method</option>
            <option value="vnpay">VNPay</option>
            <option value="momo">Momo</option>
        </select>

        <div>
            <button type="submit">Confirm and Pay</button>
            <a href="{{ route('booking.seatmap', ['showtime_id' => $showtime->id]) }}">Back to Seat Selection</a>
        </div>
    </form>
    <script src="{{ asset('js/booking-countdown.js') }}"></script>
@endsection