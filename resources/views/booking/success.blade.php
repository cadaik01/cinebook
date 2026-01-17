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
    <!-- Seat details with QR Codes -->
    <div style="margin-top: 30px; text-align: left;">
        <h2 style="font-size: 24px; margin-bottom: 15px;">Seat Details & QR Codes:</h2>
        <p style="color: #666; margin-bottom: 20px;">Mỗi ghế hoặc cặp ghế couple có 1 mã QR riêng. Vui lòng xuất trình tại rạp để check-in.</p>
        
        @php
            $displayedQRs = []; // Track displayed QR codes to avoid duplicates
        @endphp
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            @foreach ($seats as $bookingSeat)
                @if (!in_array($bookingSeat->qr_code, $displayedQRs))
                    @php
                        // Get all seats with same QR code (couple seats)
                        $seatsWithSameQR = $seats->where('qr_code', $bookingSeat->qr_code);
                        $displayedQRs[] = $bookingSeat->qr_code;
                    @endphp
                    
                    <div style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; text-align: center; background: #f9f9f9;">
                        <!-- QR Code -->
                        <div style="background: white; padding: 10px; margin-bottom: 10px;">
                            {!! QrCode::size(150)->generate($bookingSeat->qr_code) !!}
                        </div>
                        
                        <!-- Seat Info -->
                        <div style="text-align: left; font-size: 14px;">
                            <p><strong>Ghế:</strong> 
                                {{ $seatsWithSameQR->pluck('seat.seat_code')->join(', ') }}
                            </p>
                            <p><strong>Loại:</strong> {{ $bookingSeat->seat->seatType->name }}</p>
                            <p><strong>Giá:</strong> 
                                {{ number_format($seatsWithSameQR->sum('price')) }} VND
                            </p>
                            <p style="color: #4CAF50; font-weight: bold;">
                                <span style="display: inline-block; width: 10px; height: 10px; background: #4CAF50; border-radius: 50%; margin-right: 5px;"></span>
                                {{ $bookingSeat->qr_status === 'active' ? 'Chưa check-in' : 'Đã check-in' }}
                            </p>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Action buttons -->
    <div style="margin-top: 30px;">
        <a href="{{ route('homepage') }}">Back to Homepage</a>
</div>

<script>
    // Clear booking countdown from localStorage
    localStorage.removeItem('booking_expiry_time');
</script>
@endsection