{{--
/**
 * Booking Confirmation Email Template
 * 
 * Email template for booking confirmations including:
 * - Booking details and movie information
 * - QR code for entry
 * - Theater and seat details
 * - Important instructions
 * - Contact information
 */
--}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - TCA Cine</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            background-color: #f4f4f4;
            padding: 20px;
        }
        .email-wrapper { 
            background-color: #f4f4f4; 
            padding: 20px; 
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background: #ffffff;
            border-radius: 12px; 
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .header { 
            background: linear-gradient(135deg, #008080 0%, #006666 100%);
            color: white; 
            padding: 30px 20px; 
            text-align: center; 
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .header .logo {
            font-size: 36px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px 25px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 15px;
            color: #008080;
        }
        .intro-text {
            margin-bottom: 25px;
            color: #555;
        }
        .details-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .details-section h3 {
            color: #008080;
            margin-bottom: 15px;
            font-size: 20px;
            border-bottom: 2px solid #f7c873;
            padding-bottom: 8px;
        }
        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
            min-width: 140px;
        }
        .detail-value {
            color: #333;
            flex: 1;
        }
        .total-price {
            background: linear-gradient(135deg, #f7c873 0%, #e6a040 100%);
            color: #1a2233;
            font-size: 24px;
            font-weight: 700;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-top: 10px;
        }
        .qr-section {
            margin: 30px 0;
            text-align: center;
        }
        .qr-section h3 {
            color: #008080;
            margin-bottom: 10px;
            font-size: 22px;
        }
        .qr-instructions {
            color: #555;
            margin-bottom: 20px;
            font-size: 15px;
            background: #fff3cd;
            padding: 12px;
            border-radius: 6px;
            border-left: 4px solid #f7c873;
        }
        .qr-code-container {
            background: #ffffff;
            border: 2px dashed #008080;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 2px 8px rgba(0, 128, 128, 0.1);
        }
        .seat-info {
            background: #008080;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 16px;
        }
        .qr-code-container img {
            max-width: 220px;
            height: auto;
            margin: 10px auto;
            display: block;
        }
        .qr-text {
            font-size: 12px;
            color: #777;
            margin-top: 10px;
            font-family: 'Courier New', monospace;
            word-wrap: break-word;
        }
        .important-note {
            background: #e7f3f3;
            border-left: 4px solid #008080;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
        }
        .important-note strong {
            color: #008080;
            display: block;
            margin-bottom: 8px;
        }
        .footer { 
            background: #f8f9fa;
            padding: 20px; 
            text-align: center;
            border-top: 3px solid #008080;
        }
        .footer p {
            font-size: 13px;
            color: #666;
            margin: 5px 0;
        }
        .footer .contact {
            color: #008080;
            font-weight: 600;
            text-decoration: none;
        }
        .social-links {
            margin-top: 15px;
        }
        .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #008080;
            text-decoration: none;
            font-size: 14px;
        }
        @media only screen and (max-width: 600px) {
            .container {
                border-radius: 0;
            }
            .content {
                padding: 20px 15px;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                margin-bottom: 5px;
            }
        }
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
