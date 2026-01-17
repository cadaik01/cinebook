@extends('layouts.main')

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
        <p><strong>Movie:</strong> {{ $movie->title ?? 'N/A' }}</p>
        <p><strong>Showday:</strong> {{ $showtime->show_date ? $showtime->show_date->format('F j, Y') : 'N/A' }}</p>
        <p><strong>Showtime:</strong> {{ $showtime->show_time ?? 'N/A' }}</p>
        <p><strong>Room:</strong> {{ $room->name ?? 'N/A' }}</p>
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
    <form method="POST" action="{{ route('booking.process', ['showtime_id' => $showtime_id]) }}" style="display: flex; flex-direction: column; align-items: center;">
        @csrf
        <input type="hidden" name="showtime_id" value="{{ $showtime_id }}">
        <input type="hidden" name="seats" value="{{ json_encode(array_column($seatDetails, 'id')) }}">
        <input type="hidden" name="total_price" value="{{ $totalPrice }}">
        <h3 style="text-align: center;">Payment method</h3>
        <select name="payment_method" required style="width: 300px; margin-bottom: 16px; text-align: center;">
            <option value="">Select Payment Method</option>
            <option value="vnpay">VNPay</option>
            <option value="momo">Momo</option>
        </select>
        <div style="display: flex; gap: 16px; justify-content: center;">
            <button type="submit" style="padding: 10px 24px; background: #4b6e57; color: white; border: none; border-radius: 6px; font-size: 16px;">Confirm and Pay</button>
            <a href="{{ route('booking.seatmap', ['showtime_id' => $showtime_id]) }}" style="padding: 10px 24px; background: #e0e0e0; color: #333; border-radius: 6px; text-decoration: none; font-size: 16px;">Back to Seat Selection</a>
        </div>
    </form>
    <script src="{{ asset('js/booking-countdown.js') }}"></script>
@endsection