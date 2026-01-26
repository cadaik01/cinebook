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
            <h2>Booking Confirmation Successful</h2>
        </div>

        <p>Hello <strong>{{ $booking->user->name }}</strong>,</p>
        <p>Thank you for booking at TCA Cine. Below is your ticket information:</p>
        
        <div class="details">
            <p><strong>Order Code:</strong> #{{ $booking->id }}</p>
            <p><strong>Movie:</strong> {{ $booking->showtime->movie->title }}</p>
            <p><strong>Theater:</strong> {{ $booking->showtime->room->name }}</p>
            <p><strong>Show Date:</strong> {{ \Carbon\Carbon::parse($booking->showtime->show_date)->format('d/m/Y') }}</p>
            <p><strong>Show Time:</strong> {{ $booking->showtime->show_time }}</p>
            <p><strong>Total:</strong> {{ number_format($booking->total_price, 0, ',', '.') }} VND</p>
        </div>

        <div class="qr-section">
            <h3>Your Ticket (QR Code)</h3>
            <p>Please present the QR code below at the ticket counter or entrance.</p>
            
            @php
                // Group seats by QR code (for couple seats sharing one QR)
                $groupedSeats = $booking->bookingSeats->groupBy('qr_code');
            @endphp

            @foreach($groupedSeats as $qrCode => $seats)
                <div class="qr-code">
                    <div class="seat-info">
                        Seat: {{ $seats->map(fn($s) => $s->seat->seat_code)->join(', ') }}
                    </div>
                    {{-- Generate QR Code Image --}}
                    <img src="data:image/png;base64,{{ base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(200)->generate($qrCode)) }}" alt="QR Code">
                    <p><small>{{ $qrCode }}</small></p>
                </div>
            @endforeach
        </div>

        <div class="footer">
            <p>If you have any questions, please contact us via email support@tcacine.com</p>
            <p>&copy; {{ date('Y') }} TCA Cine. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
