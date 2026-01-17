# 09. THANH TOÁN VÀ QR CODE

## 🎯 Mục tiêu bài học

Sau bài học này, bạn sẽ có:
- ✅ Payment processing với Database Transaction
- ✅ QR code generation cho từng vé
- ✅ Mock payment gateway (VNPay, MoMo)
- ✅ Booking confirmation flow
- ✅ Success page với QR codes
- ✅ Email booking confirmation (bonus)

**Thời gian ước tính**: 90 phút

---

## 📚 Kiến thức cần biết

- Laravel Database Transactions
- QR Code generation
- Hash functions (SHA256)
- Session management
- Blade components

---

## 🛠️ BƯỚC 1: CẬP NHẬT PAYMENTCONTROLLER

### 1.1. Process Booking Method

**File**: `app/Http/Controllers/PaymentController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Showtime;
use App\Models\ShowtimeSeat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Process booking and create pending booking.
     */
    public function processBooking(Request $request)
    {
        // Require authentication
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để đặt vé.');
        }

        // Validate seat selection
        $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'seat_ids' => 'required|json',
            'payment_method' => 'required|in:momo,vnpay',
        ]);

        $seatIds = json_decode($request->seat_ids, true);

        if (empty($seatIds)) {
            return redirect()->back()
                ->with('error', 'Vui lòng chọn ít nhất một ghế.');
        }

        $showtime = Showtime::with(['movie', 'room', 'showtimePrices'])
            ->findOrFail($request->showtime_id);

        // Check if showtime is in the past
        if ($showtime->isPast()) {
            return redirect()->back()
                ->with('error', 'Suất chiếu này đã kết thúc.');
        }

        try {
            DB::beginTransaction();

            // Verify all seats are available
            $showtimeSeats = ShowtimeSeat::where('showtime_id', $showtime->id)
                ->whereIn('seat_id', $seatIds)
                ->lockForUpdate() // Lock rows for update
                ->get();

            foreach ($showtimeSeats as $seat) {
                if ($seat->status !== 'available') {
                    throw new \Exception("Ghế {$seat->seat->seat_code} đã được đặt.");
                }
            }

            // Calculate total price
            $totalPrice = 0;
            $seatDetails = [];

            foreach ($showtimeSeats as $showtimeSeat) {
                $seat = $showtimeSeat->seat;
                $seatTypeId = $seat->seat_type_id;

                // Get price from showtime_prices
                $price = $showtime->showtimePrices()
                    ->where('seat_type_id', $seatTypeId)
                    ->first()->price;

                $totalPrice += $price;

                $seatDetails[] = [
                    'seat_id' => $seat->id,
                    'seat_code' => $seat->seat_code,
                    'seat_type_id' => $seatTypeId,
                    'price' => $price,
                ];
            }

            // Create booking record
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'showtime_id' => $showtime->id,
                'booking_date' => now(),
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'expired_at' => Carbon::now()->addMinutes(10), // 10 minutes to pay
            ]);

            // Create booking_seats with QR codes
            foreach ($seatDetails as $detail) {
                // Generate unique QR code
                $qrCode = BookingSeat::generateQRCode(
                    $booking->id,
                    $detail['seat_code']
                );

                BookingSeat::create([
                    'booking_id' => $booking->id,
                    'showtime_id' => $showtime->id,
                    'seat_id' => $detail['seat_id'],
                    'price' => $detail['price'],
                    'qr_code' => $qrCode,
                    'qr_status' => 'active',
                ]);
            }

            // Update showtime_seats status to 'reserved'
            ShowtimeSeat::where('showtime_id', $showtime->id)
                ->whereIn('seat_id', $seatIds)
                ->update(['status' => 'reserved']);

            DB::commit();

            // Clear localStorage selection
            session()->flash('clear_seat_selection', true);

            // Redirect to payment page
            return redirect()->route('payment.mock', $booking->id);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Đặt vé thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Show mock payment page.
     */
    public function showMockPayment($bookingId)
    {
        $booking = Booking::with([
            'showtime.movie',
            'showtime.room',
            'bookingSeats.seat'
        ])->findOrFail($bookingId);

        // Check if booking belongs to current user
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if booking is expired
        if ($booking->isExpired()) {
            return redirect()->route('home')
                ->with('error', 'Đơn đặt vé đã hết hạn.');
        }

        return view('payment.mock', compact('booking'));
    }

    /**
     * Confirm payment (mock).
     */
    public function confirmPayment(Request $request, $bookingId)
    {
        $booking = Booking::with('bookingSeats')->findOrFail($bookingId);

        // Verify ownership
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            // Update booking status
            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
            ]);

            // Update showtime_seats from 'reserved' to 'booked'
            $seatIds = $booking->bookingSeats->pluck('seat_id')->toArray();

            ShowtimeSeat::where('showtime_id', $booking->showtime_id)
                ->whereIn('seat_id', $seatIds)
                ->update(['status' => 'booked']);

            DB::commit();

            // Redirect to success page
            return redirect()->route('booking.success', $booking->id)
                ->with('success', 'Thanh toán thành công!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Xác nhận thanh toán thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Show booking success page with QR codes.
     */
    public function bookingSuccess($bookingId)
    {
        $booking = Booking::with([
            'showtime.movie',
            'showtime.room',
            'bookingSeats.seat.seatType'
        ])->findOrFail($bookingId);

        // Verify ownership
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if booking is confirmed
        if (!$booking->isConfirmed()) {
            return redirect()->route('home')
                ->with('error', 'Đơn đặt vé chưa được xác nhận.');
        }

        // Group couple seats
        $groupedSeats = $this->groupCoupleSeats($booking->bookingSeats);

        return view('booking.success', compact('booking', 'groupedSeats'));
    }

    /**
     * Group couple seats together (share same QR code).
     */
    private function groupCoupleSeats($bookingSeats)
    {
        $grouped = [];

        foreach ($bookingSeats as $bookingSeat) {
            $qrCode = $bookingSeat->qr_code;

            if (!isset($grouped[$qrCode])) {
                $grouped[$qrCode] = [
                    'qr_code' => $qrCode,
                    'seats' => [],
                    'total_price' => 0,
                ];
            }

            $grouped[$qrCode]['seats'][] = $bookingSeat;
            $grouped[$qrCode]['total_price'] += $bookingSeat->price;
        }

        return array_values($grouped);
    }

    /**
     * Helper: Get couple pair key for grouping.
     */
    private function getCouplePairKey($seat)
    {
        if ($seat->seat_type_id !== 3) {
            return null; // Not a couple seat
        }

        $row = $seat->seat_row;
        $number = $seat->seat_number;

        // Calculate pair: odd-even (1-2, 3-4, etc.)
        $pairStart = ($number % 2 === 1) ? $number : $number - 1;

        return "{$row}{$pairStart}";
    }
}
```

---

## 🛠️ BƯỚC 2: TẠO MOCK PAYMENT VIEW

### 2.1. Mock Payment Page

**File**: `resources/views/payment/mock.blade.php`

```blade
@extends('layouts.main')

@section('title', 'Thanh toán')

@section('content')
<div class="payment-container">
    <div class="container">
        <div class="payment-wrapper">
            <!-- Payment Header -->
            <div class="payment-header">
                <h1>Thanh toán đặt vé</h1>
                <p class="payment-note">Đây là trang thanh toán mô phỏng (Mock Payment)</p>
            </div>

            <!-- Booking Summary -->
            <div class="booking-summary-card">
                <h2>Thông tin đặt vé</h2>

                <div class="movie-info">
                    <img src="{{ $booking->showtime->movie->poster_url }}"
                         alt="{{ $booking->showtime->movie->title }}">
                    <div class="movie-details">
                        <h3>{{ $booking->showtime->movie->title }}</h3>
                        <p><strong>Phòng:</strong> {{ $booking->showtime->room->name }}</p>
                        <p><strong>Suất chiếu:</strong>
                            {{ $booking->showtime->show_date->format('d/m/Y') }} -
                            {{ $booking->showtime->show_time->format('H:i') }}
                        </p>
                    </div>
                </div>

                <div class="seats-info">
                    <h4>Ghế đã chọn:</h4>
                    <div class="seats-list">
                        @foreach($booking->bookingSeats as $bookingSeat)
                            <span class="seat-badge">{{ $bookingSeat->seat->seat_code }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="price-breakdown">
                    <div class="price-row">
                        <span>Số lượng vé:</span>
                        <span>{{ $booking->bookingSeats->count() }} vé</span>
                    </div>
                    <div class="price-row total">
                        <span>Tổng tiền:</span>
                        <span class="total-amount">{{ number_format($booking->total_price, 0, ',', '.') }} ₫</span>
                    </div>
                </div>

                <!-- Countdown Timer -->
                <div class="countdown-timer">
                    <p>Thời gian còn lại để thanh toán:</p>
                    <div id="countdownDisplay" class="countdown-display">10:00</div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="payment-methods-card">
                <h2>Phương thức thanh toán</h2>

                <div class="payment-method selected">
                    @if($booking->payment_method === 'momo')
                        <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png"
                             alt="MoMo" class="payment-logo">
                        <span>Ví MoMo</span>
                    @else
                        <img src="https://vnpay.vn/s1/statics.vnpay.vn/2023/6/0oxhzjmxbksr1686814746087.png"
                             alt="VNPay" class="payment-logo">
                        <span>VNPay</span>
                    @endif
                </div>

                <p class="mock-note">
                    ⚠️ Đây là thanh toán mô phỏng. Bạn chỉ cần nhấn "Thanh toán" để hoàn tất.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="payment-actions">
                <a href="{{ route('showtime.seats', $booking->showtime_id) }}"
                   class="btn btn-secondary">
                    Quay lại
                </a>

                <form action="{{ route('payment.confirm', $booking->id) }}"
                      method="POST"
                      id="paymentForm">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg">
                        Thanh toán {{ number_format($booking->total_price, 0, ',', '.') }} ₫
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Countdown timer
    const expiredAt = new Date('{{ $booking->expired_at }}').getTime();
    const countdownDisplay = document.getElementById('countdownDisplay');

    const timer = setInterval(() => {
        const now = new Date().getTime();
        const distance = expiredAt - now;

        if (distance < 0) {
            clearInterval(timer);
            countdownDisplay.textContent = 'Hết hạn';
            countdownDisplay.classList.add('expired');

            // Disable payment button
            document.querySelector('#paymentForm button').disabled = true;

            // Redirect after 3 seconds
            setTimeout(() => {
                window.location.href = '{{ route("home") }}';
            }, 3000);
            return;
        }

        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        countdownDisplay.textContent =
            `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

        // Warning when < 1 minute
        if (distance < 60000) {
            countdownDisplay.classList.add('warning');
        }
    }, 1000);
</script>
@endsection
```

---

## 🛠️ BƯỚC 3: TẠO SUCCESS PAGE VỚI QR CODES

### 3.1. Booking Success View

**File**: `resources/views/booking/success.blade.php`

```blade
@extends('layouts.main')

@section('title', 'Đặt vé thành công')

@section('content')
<div class="success-container">
    <div class="container">
        <div class="success-wrapper">
            <!-- Success Icon -->
            <div class="success-icon">
                <svg width="80" height="80" viewBox="0 0 80 80">
                    <circle cx="40" cy="40" r="38" fill="#46d369" stroke="#fff" stroke-width="2"/>
                    <path d="M25 40 L35 50 L55 30" stroke="#fff" stroke-width="4"
                          stroke-linecap="round" fill="none"/>
                </svg>
            </div>

            <h1 class="success-title">Đặt vé thành công!</h1>
            <p class="success-message">
                Cảm ơn bạn đã đặt vé tại Cinebook.
                Vui lòng mang mã QR đến quầy để check-in.
            </p>

            <!-- Booking Info -->
            <div class="booking-info-card">
                <h2>Thông tin vé</h2>

                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Mã đặt vé:</span>
                        <span class="value">#{{ $booking->id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Phim:</span>
                        <span class="value">{{ $booking->showtime->movie->title }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Phòng:</span>
                        <span class="value">{{ $booking->showtime->room->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Suất chiếu:</span>
                        <span class="value">
                            {{ $booking->showtime->show_date->format('d/m/Y') }} -
                            {{ $booking->showtime->show_time->format('H:i') }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="label">Ghế:</span>
                        <span class="value">
                            @foreach($booking->bookingSeats as $bookingSeat)
                                {{ $bookingSeat->seat->seat_code }}{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="label">Tổng tiền:</span>
                        <span class="value price">
                            {{ number_format($booking->total_price, 0, ',', '.') }} ₫
                        </span>
                    </div>
                </div>
            </div>

            <!-- QR Codes -->
            <div class="qr-codes-section">
                <h2>Mã QR check-in</h2>
                <p class="qr-note">Mỗi vé/cặp ghế có 1 mã QR riêng</p>

                <div class="qr-codes-grid">
                    @foreach($groupedSeats as $group)
                        <div class="qr-code-card">
                            <div class="qr-code-image">
                                {!! QrCode::size(200)->generate($group['qr_code']) !!}
                            </div>

                            <div class="qr-code-info">
                                <p class="seat-codes">
                                    <strong>Ghế:</strong>
                                    @foreach($group['seats'] as $seat)
                                        {{ $seat->seat->seat_code }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </p>
                                <p class="qr-price">
                                    {{ number_format($group['total_price'], 0, ',', '.') }} ₫
                                </p>
                            </div>

                            <button class="btn-download"
                                    onclick="downloadQR('{{ $group['qr_code'] }}', '{{ implode('-', array_column($group['seats']->toArray(), 'seat')['seat_code'] ?? []) }}')">
                                Tải xuống QR
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="success-actions">
                <a href="{{ route('home') }}" class="btn btn-secondary">
                    Về trang chủ
                </a>
                <a href="{{ route('profile.bookings') }}" class="btn btn-primary">
                    Xem vé của tôi
                </a>
            </div>

            <!-- Auto Redirect -->
            <p class="auto-redirect">
                Tự động chuyển về trang chủ sau <span id="countdown">30</span> giây
            </p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/success-countdown.js') }}"></script>
<script>
    // Download QR code
    function downloadQR(qrCode, seatCodes) {
        // Create a canvas from SVG
        const svg = event.target.closest('.qr-code-card').querySelector('svg');
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        canvas.width = 200;
        canvas.height = 200;

        const img = new Image();
        const svgBlob = new Blob([svg.outerHTML], {type: 'image/svg+xml'});
        const url = URL.createObjectURL(svgBlob);

        img.onload = function() {
            ctx.drawImage(img, 0, 0);

            // Download
            const link = document.createElement('a');
            link.download = `QR_${seatCodes}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();

            URL.revokeObjectURL(url);
        };

        img.src = url;
    }
</script>
@endsection
```

---

## 🛠️ BƯỚC 4: THÊM ROUTES

**File**: `routes/web.php`

```php
// Payment routes
Route::middleware('auth')->group(function () {
    Route::post('/booking/process', [PaymentController::class, 'processBooking'])
        ->name('booking.process');

    Route::get('/payment/mock/{id}', [PaymentController::class, 'showMockPayment'])
        ->name('payment.mock');

    Route::post('/payment/confirm/{id}', [PaymentController::class, 'confirmPayment'])
        ->name('payment.confirm');

    Route::get('/booking/success/{id}', [PaymentController::class, 'bookingSuccess'])
        ->name('booking.success');
});
```

---

## 🛠️ BƯỚC 5: TẠO SUCCESS COUNTDOWN JS

**File**: `public/js/success-countdown.js`

```javascript
/**
 * Success page countdown timer
 * Auto redirect to home after 30 seconds
 */

let countdown = 30;
const countdownEl = document.getElementById('countdown');

const timer = setInterval(() => {
    countdown--;

    if (countdownEl) {
        countdownEl.textContent = countdown;
    }

    if (countdown <= 0) {
        clearInterval(timer);
        window.location.href = '/';
    }
}, 1000);

// Stop countdown if user navigates away
window.addEventListener('beforeunload', () => {
    clearInterval(timer);
});
```

---

## ✅ TEST & VERIFY

### Test Cases:

1. **Process Booking**:
   - Chọn ghế → Click "Tiếp tục"
   - Redirect đến payment page
   - Booking record được tạo với status 'pending'
   - Ghế chuyển từ 'available' → 'reserved'

2. **Payment Countdown**:
   - Timer hiển thị 10:00
   - Đếm ngược mỗi giây
   - Khi < 1 phút → chuyển màu đỏ
   - Khi hết giờ → disable button, redirect

3. **Confirm Payment**:
   - Click "Thanh toán"
   - Booking status → 'confirmed'
   - Payment status → 'paid'
   - Ghế: 'reserved' → 'booked'
   - Redirect đến success page

4. **QR Codes**:
   - Mỗi vé có 1 QR riêng
   - Couple seats share 1 QR
   - QR hiển thị đúng
   - Download QR thành công

5. **Transaction Rollback**:
   - Nếu có lỗi giữa chừng
   - Database rollback
   - Ghế quay về 'available'

---

## 🎯 THỰC HÀNH

### Bài tập 1: Email confirmation
Gửi email với booking details và QR codes sau khi thanh toán thành công.

### Bài tập 2: Print ticket
Thêm button "In vé" để in booking confirmation.

### Bài tập 3: Refund
Implement chức năng hoàn tiền khi hủy vé.

---

## 📝 TÓM TẮT

Đã hoàn thành:
- ✅ Payment processing với DB Transaction
- ✅ QR code generation (unique per ticket)
- ✅ Mock payment gateway
- ✅ Countdown timer
- ✅ Success page với QR display

**Bài tiếp**: [10. Review System →](10_review_system.md)

---

**Bài trước**: [← 08. Seat Selection](08_seat_selection.md)
**Series**: Cinebook Tutorial
