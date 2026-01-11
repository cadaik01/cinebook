@extends('layouts.main')

@section('title', 'Booking Success')

@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 20px; text-align: center;">
    <!-- Success header -->
    <h1 style="color: #4CAF50; font-size: 36px; margin-bottom: 20px;">Booking Successful!</h1>
    <p style="font-size: 18px; margin-bottom: 30px;">Thank you for your booking!</p>
    <a href="{{ url('/') }}" >Return to Home</a>
    <!-- Booking details -->
    <div style="margin-top: 40px; text-align: left;">
        <h2 style="font-size: 24px; margin-bottom: 15px;">Booking Details:</h2>
        <ul style="list-style-type: none; padding: 0; font-size: 18px;">
            <li><strong>Booking ID:</strong> {{ $booking->id }}</li>
            <li><strong>Movie:</strong> {{ $booking->showtime->movie->title }}</li>
            <li><strong>Date:</strong> {{ $booking->showtime->show_date->format('F j, Y') }}</li>
            <li><strong>Time:</strong> {{ $booking->showtime->show_time }}</li>
            <li><strong>Room:</strong> {{ $booking->showtime->room->name }}</li>
            <li><strong>Payment Method:</strong> {{ $booking->payment_method }}</li>
            <li><strong>Status:</strong> {{ $booking->status }}</li>
            @if ($booking->payment_status =='paid')
                <span style="color: #28a745;">✅ All Paid</span>
            @else
                <span style="color: #ffc107;">⏳ Pending Payment</span>
            @endif
            <li><strong>Total:</strong> {{ $booking->total_price }}</li>
        </ul>
</div>
    <!-- Seat details -->
    <div style="margin-top: 30px; text-align: left;">
        <h2 style="font-size: 24px; margin-bottom: 15px;">Seat Details:</h2>
        <div style="list-style-type: none; padding: 0; font-size: 18px;">
            @foreach ($seats as $bookingSeat)
                <p>Seat: {{ $bookingSeat->seat->seat_code }}</p>
                <p>Type: {{ $bookingSeat->seat->seatType->name }}</p>
                <p>Price: {{ number_format($bookingSeat->price) }} VND</p>
                <hr style="border: 1px solid #ddd; margin: 10px 0;">
            @endforeach
        </div>
    </div>

    <!-- QR Code section -->
    <div>
        <h3>QR Code:</h3>
        <div>{!! QrCode::size(200)->generate($booking->qr_code) !!}</div>
        <p>Show this QR code at the entrance for verification.</p>
    </div>

    <!-- Action buttons -->
    <div style="margin-top: 30px;">
        <a href="{{ route('homepage') }}">Back to Homepage</a>
</div>
@endsection