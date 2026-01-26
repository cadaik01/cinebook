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
    <div class="email-wrapper">
        <div class="container">
            <div class="header">
                <div class="logo">🎬</div>
                <h1>ĐẶT VÉ THÀNH CÔNG!</h1>
                <p>TCA Cine - Your Cinema Experience</p>
            </div>
            
            <div class="content">
                <p class="greeting">Xin chào <strong>{{ $booking->user->name }}</strong>,</p>
                <p class="intro-text">
                    Cảm ơn bạn đã tin tưởng và đặt vé tại <strong>TCA Cine</strong>. 
                    Chúng tôi rất vui được phục vụ bạn! Dưới đây là thông tin chi tiết về đơn đặt vé của bạn:
                </p>
                
                <div class="details-section">
                    <h3>📋 Thông Tin Đặt Vé</h3>
                    
                    <div class="detail-row">
                        <span class="detail-label">Mã đơn hàng:</span>
                        <span class="detail-value"><strong>#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</strong></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Phim:</span>
                        <span class="detail-value"><strong>{{ $booking->showtime->movie->title }}</strong></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Phòng chiếu:</span>
                        <span class="detail-value">{{ $booking->showtime->room->name }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Ngày chiếu:</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($booking->showtime->show_date)->locale('vi')->isoFormat('dddd, DD/MM/YYYY') }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Giờ chiếu:</span>
                        <span class="detail-value">{{ $booking->showtime->show_time }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Ghế đã đặt:</span>
                        <span class="detail-value">
                            <strong>{{ $booking->bookingSeats->map(fn($s) => $s->seat->seat_code)->join(', ') }}</strong>
                            ({{ $booking->bookingSeats->count() }} ghế)
                        </span>
                    </div>
                    
                    <div class="total-price">
                        Tổng tiền: {{ number_format($booking->total_price, 0, ',', '.') }} VNĐ
                    </div>
                </div>

                <div class="qr-section">
                    <h3>🎫 Vé Điện Tử Của Bạn</h3>
                    <div class="qr-instructions">
                        <strong>📱 Hướng dẫn sử dụng:</strong>
                        Vui lòng xuất trình mã QR bên dưới tại quầy vé hoặc cửa soát vé khi đến rạp.
                    </div>
                    
                    @php
                        // Group seats by QR code (for couple seats sharing one QR)
                        $groupedSeats = $booking->bookingSeats->groupBy('qr_code');
                    @endphp

                    @foreach($groupedSeats as $qrCode => $seats)
                        <div class="qr-code-container">
                            <div class="seat-info">
                                🪑 Ghế: {{ $seats->map(fn($s) => $s->seat->seat_code)->join(', ') }}
                            </div>
                            
                            {{-- Generate QR Code Image --}}
                            @php
                                try {
                                    $qrImage = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(220)->margin(1)->generate($qrCode));
                                } catch (\Exception $e) {
                                    $qrImage = '';
                                }
                            @endphp
                            
                            @if($qrImage)
                                <img src="data:image/png;base64,{{ $qrImage }}" alt="QR Code - {{ $seats->map(fn($s) => $s->seat->seat_code)->join(', ') }}">
                            @else
                                <p style="color: #dc3545;">Không thể tạo mã QR. Vui lòng liên hệ bộ phận hỗ trợ.</p>
                            @endif
                            
                            <p class="qr-text">{{ $qrCode }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="important-note">
                    <strong>⚠️ Lưu ý quan trọng:</strong>
                    <ul style="margin-left: 20px; color: #555;">
                        <li>Vui lòng đến rạp trước giờ chiếu ít nhất 15 phút</li>
                        <li>Xuất trình mã QR hoặc mã đơn hàng tại quầy vé</li>
                        <li>Mã QR chỉ sử dụng được một lần duy nhất</li>
                        <li>Không chia sẻ mã QR cho người khác</li>
                    </ul>
                </div>
            </div>

            <div class="footer">
                <p>Nếu bạn có thắc mắc, vui lòng liên hệ với chúng tôi:</p>
                <p>
                    📧 Email: <a href="mailto:support@tcacine.com" class="contact">support@tcacine.com</a> | 
                    📞 Hotline: <strong>1900-xxxx</strong>
                </p>
                <div class="social-links">
                    <a href="#">Facebook</a> | 
                    <a href="#">Instagram</a> | 
                    <a href="#">Twitter</a>
                </div>
                <p style="margin-top: 15px; color: #999;">
                    &copy; {{ date('Y') }} TCA Cine. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
