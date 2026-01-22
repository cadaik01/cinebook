<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background-color: #008080; color: white; padding: 10px; text-align: center; border-radius: 5px 5px 0 0; }
        .details { margin: 20px 0; }
        .qr-code { text-align: center; margin: 20px 0; border: 1px dashed #ccc; padding: 10px; }
        .seat-info { margin-bottom: 10px; font-weight: bold; }
        .footer { font-size: 12px; color: #777; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Xác nhận đặt vé thành công</h2>
        </div>
        
        <p>Xin chào <strong>{{ $booking->user->name }}</strong>,</p>
        <p>Cảm ơn bạn đã đặt vé tại TCA Cine. Dưới đây là thông tin vé của bạn:</p>
        
        <div class="details">
            <p><strong>Mã đơn hàng:</strong> #{{ $booking->id }}</p>
            <p><strong>Phim:</strong> {{ $booking->showtime->movie->title }}</p>
            <p><strong>Rạp:</strong> {{ $booking->showtime->room->name }}</p>
            <p><strong>Ngày chiếu:</strong> {{ \Carbon\Carbon::parse($booking->showtime->show_date)->format('d/m/Y') }}</p>
            <p><strong>Giờ chiếu:</strong> {{ $booking->showtime->show_time }}</p>
            <p><strong>Tổng tiền:</strong> {{ number_format($booking->total_price, 0, ',', '.') }} đ</p>
        </div>

        <div class="qr-section">
            <h3>Vé của bạn (Mã QR)</h3>
            <p>Vui lòng xuất trình mã QR bên dưới tại quầy vé hoặc cửa soát vé.</p>
            
            @php
                // Group seats by QR code (for couple seats sharing one QR)
                $groupedSeats = $booking->bookingSeats->groupBy('qr_code');
            @endphp

            @foreach($groupedSeats as $qrCode => $seats)
                <div class="qr-code">
                    <div class="seat-info">
                        Ghế: {{ $seats->map(fn($s) => $s->seat->seat_code)->join(', ') }}
                    </div>
                    {{-- Generate QR Code Image --}}
                    <img src="data:image/png;base64,{{ base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(200)->generate($qrCode)) }}" alt="QR Code">
                    <p><small>{{ $qrCode }}</small></p>
                </div>
            @endforeach
        </div>

        <div class="footer">
            <p>Nếu bạn có thắc mắc, vui lòng liên hệ với chúng tôi qua email support@tcacine.com</p>
            <p>&copy; {{ date('Y') }} TCA Cine. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
