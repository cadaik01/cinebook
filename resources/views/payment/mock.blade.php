@extends('layouts.main')

@section('title', 'Payment - {{ $booking->payment_method }}')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2>💳 Online Payment</h2>
    
    <!-- Payment Method Info -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <h3 style="margin-top: 0;">
            @if($booking->payment_method == 'vnpay')
                🏦 VNPay
            @elseif($booking->payment_method == 'momo')
                💰 MoMo
            @else
                💳 {{ $booking->payment_method }}
            @endif
        </h3>
        <p><strong>Movie:</strong> {{ $booking->movie_title }}</p>
        <p><strong>Showtime:</strong> {{ date('d/m/Y H:i', strtotime($booking->start_time)) }}</p>
        <p><strong>Total Price:</strong> {{ number_format($booking->total_price) }} VND</p>
        <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>
    </div>
    
    <!-- Fake Payment Interface -->
    @if($booking->payment_method == 'vnpay')
        <!-- VNPay Mock -->
        <div style="background: #0066cc; color: white; padding: 25px; border-radius: 10px; text-align: center; margin-bottom: 20px;">
            <h3 style="margin: 0 0 15px 0;">🏦 VNPay Gateway</h3>
            <!-- Fake QR Code -->
            <div style="background: white; padding: 20px; border-radius: 10px; margin: 15px auto; max-width: 200px;">
                <div style="width: 150px; height: 150px; background: #000; margin: 0 auto; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; text-align: center;">
                    QR CODE<br>
                    TOTAL<br>
                    {{ number_format($booking->total_price) }} VND
                </div>
        </div>
        
    @elseif($booking->payment_method == 'momo')
        <!-- MoMo Mock -->
        <div style="background: #d82d8b; color: white; padding: 25px; border-radius: 10px; text-align: center; margin-bottom: 20px;">
            <h3 style="margin: 0 0 15px 0;">💰MoMo</h3>
            
            <!-- Fake QR Code -->
            <div style="background: white; padding: 20px; border-radius: 10px; margin: 15px auto; max-width: 200px;">
                <div style="width: 150px; height: 150px; background: #000; margin: 0 auto; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; text-align: center;">
                    QR CODE<br>
                    TOTAL<br>
                    {{ number_format($booking->total_price) }} VND
                </div>
            </div>
            
            <p style="margin: 15px 0;">📱 Scan the QR code using the MoMo app</p>
        </div>
    @endif
    
    <!-- Countdown Timer -->
    <div style="background: #ff6b6b; color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
        <h4 style="margin: 0;">⏰ Thời gian còn lại</h4>
        <div id="countdown" style="font-size: 24px; font-weight: bold; margin-top: 10px;">10:00</div>
        <p style="margin: 5px 0 0 0; font-size: 12px;">Giao dịch sẽ tự động hủy nếu không hoàn tất</p>
    </div>
    
    <!-- Action Buttons -->
    <div style="display: flex; gap: 15px; flex-direction: column;">
        <!-- Success Button (Mock) -->
        <form method="POST" action="{{ route('payment.confirm', ['booking_id' => $booking->id]) }}">
            @csrf
            <button type="submit" 
                    style="width: 100%; background: #28a745; color: white; padding: 15px; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer;">
                ✅ THANH TOÁN THÀNH CÔNG
            </button>
        </form>
        
        <!-- Cancel Button -->
        <a href="{{ route('booking.seatmap', ['showtime_id' => $booking->showtime_id]) }}" 
           style="width: 100%; background: #6c757d; color: white; padding: 15px; border: none; border-radius: 8px; text-align: center; text-decoration: none; display: block; font-size: 16px; font-weight: bold;">
            ❌ Hủy Thanh Toán
        </a>
    </div>
    
    <!-- Warning Notice -->
    <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin-top: 20px; font-size: 14px;">
        <p style="margin: 0;"><strong>⚠️ Lưu ý:</strong> Đây là giao diện thanh toán giả lập cho mục đích demo. Trong thực tế, bạn sẽ được chuyển đến cổng thanh toán thật của VNPay/MoMo.</p>
    </div>
</div>

<!-- Countdown Script -->
<script src="{{ asset('js/booking-countdown.js') }}"></script>
@endsection