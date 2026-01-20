# 07. HỆ THỐNG ĐẶT VÉ CƠ BẢN

## 🎯 Mục tiêu bài học

Sau bài học này, bạn sẽ có:
- ✅ Hệ thống đặt vé cơ bản
- ✅ Validation đầy đủ (couple seats, availability)
- ✅ Transaction để đảm bảo data consistency
- ✅ Trang xác nhận booking
- ✅ Countdown timer 10 phút

**Thời gian ước tính**: 90-105 phút

---

## 📚 Kiến thức cần biết

- Database transactions (ACID)
- Session management
- JavaScript timers
- Laravel Eloquent relationships
- Form validation

---

## 🛠️ BƯỚC 1: TẠO BOOKING CONTROLLER

```bash
php artisan make:controller BookingController
```

**File**: `app/Http/Controllers/BookingController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Showtime;
use App\Models\Room;
use App\Models\Seat;
use App\Models\Booking;

class BookingController extends Controller
{
    /**
     * Display seat map for a specific showtime
     */
    public function seatMap($showtime_id)
    {
        // Get showtime với movie relationship
        $showtime = Showtime::with('movie')->findOrFail($showtime_id);

        // Get room với screen type và pricing
        $room = Room::with('screenType')->findOrFail($showtime->room_id);

        // Get all seats trong room, sắp xếp theo row và number
        $seats = $room->seats()->with('seatType')
            ->orderBy('seat_row', 'asc')
            ->orderBy('seat_number', 'asc')
            ->get();

        // Get booked seats cho showtime này (booked + reserved)
        $bookedSeats = DB::table('showtime_seats')
            ->where('showtime_id', $showtime_id)
            ->whereIn('status', ['booked', 'reserved'])
            ->pluck('seat_id')
            ->toArray();

        return view('booking.seat_map', compact('showtime', 'room', 'seats', 'bookedSeats'));
    }

    /**
     * Process seat booking với validation và pricing
     */
    public function bookSeats(Request $request, $showtime_id)
    {
        // 1. Check user đã đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để đặt vé');
        }

        // 2. Get selected seats từ request
        $seatsInput = $request->input('seats', '[]');
        $selectedSeats = is_array($seatsInput) ? $seatsInput : json_decode($seatsInput, true);

        // 3. Validate input
        if (empty($selectedSeats) || !is_array($selectedSeats)) {
            return redirect()->route('booking.seatmap', $showtime_id)
                ->with('error', 'Vui lòng chọn ít nhất một ghế');
        }

        // 4. Get showtime và room information
        $showtime = Showtime::findOrFail($showtime_id);
        $room = $showtime->room()->with('screenType')->first();

        // 5. Validate và tính giá từng ghế
        $seatDetails = [];
        $totalPrice = 0;
        $validatedCouplePairs = [];

        foreach ($selectedSeats as $seat_id) {
            // Get seat info với pricing
            $seat = Seat::with('seatType')->findOrFail($seat_id);

            // Check if seat đã booked
            $existingBooking = DB::table('showtime_seats')
                ->where('showtime_id', $showtime_id)
                ->where('seat_id', $seat_id)
                ->whereIn('status', ['booked', 'reserved'])
                ->first();

            if ($existingBooking) {
                return redirect()->route('booking.seatmap', $showtime_id)
                    ->with('error', "Ghế {$seat->seat_code} đã được đặt");
            }

            // Validate couple seat (phải chọn theo cặp)
            if ($seat->seat_type_id === 3) { // Couple seat
                $pairKey = $this->getCouplePairKey($seat->seat_code);

                if (!in_array($pairKey, $validatedCouplePairs)) {
                    $validation = $this->validateCoupleSeat($seat, $selectedSeats, $showtime_id);
                    if (!$validation['valid']) {
                        return redirect()->route('booking.seatmap', $showtime_id)
                            ->with('error', $validation['message']);
                    }
                    $validatedCouplePairs[] = $pairKey;
                }
            }

            // Calculate price: base_price + screen_type price
            $seatPrice = ($seat->seatType->base_price ?? 0) + ($room->screenType->price ?? 0);
            $totalPrice += $seatPrice;

            $seatDetails[] = [
                'id' => $seat->id,
                'seat_code' => $seat->seat_code,
                'seat_type' => $seat->seatType->name ?? 'Unknown',
                'price' => $seatPrice,
            ];
        }

        // 6. Get movie info
        $movie = $showtime->movie;

        // 7. Redirect to confirmation page
        return view('booking.confirm', compact('movie', 'showtime', 'room', 'seatDetails', 'totalPrice', 'showtime_id'));
    }

    /**
     * Validate couple seat logic
     */
    private function validateCoupleSeat($seat, $selectedSeats, $showtime_id)
    {
        $rowLetter = substr($seat->seat_code, 0, 1);
        $seatNumber = (int)substr($seat->seat_code, 1);

        // Couple seats đi theo cặp: 1-2, 3-4, 5-6...
        $pairSeatNumber = ($seatNumber % 2 === 1) ? $seatNumber + 1 : $seatNumber - 1;
        $pairSeatCode = $rowLetter . $pairSeatNumber;

        // Find pair seat
        $pairSeat = DB::table('seats')
            ->where('seat_code', $pairSeatCode)
            ->where('room_id', $seat->room_id)
            ->first();

        if (!$pairSeat) {
            return ['valid' => false, 'message' => 'Không tìm thấy ghế đôi tương ứng'];
        }

        // Check if pair seat cũng được chọn
        if (!in_array($pairSeat->id, $selectedSeats)) {
            return ['valid' => false, 'message' => 'Ghế đôi phải được chọn theo cặp'];
        }

        // Check if pair seat đã booked
        $pairBooked = DB::table('showtime_seats')
            ->where('showtime_id', $showtime_id)
            ->where('seat_id', $pairSeat->id)
            ->whereIn('status', ['booked', 'reserved'])
            ->exists();

        if ($pairBooked) {
            return ['valid' => false, 'message' => 'Cặp ghế đôi không khả dụng'];
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Generate unique key cho couple seat pairs
     */
    private function getCouplePairKey($seatCode)
    {
        $rowLetter = substr($seatCode, 0, 1);
        $seatNumber = (int)substr($seatCode, 1);
        $lowerNumber = ($seatNumber % 2 === 1) ? $seatNumber : $seatNumber - 1;
        return $rowLetter . $lowerNumber . '-' . ($lowerNumber + 1);
    }

    /**
     * Cancel reserved seats (khi timeout hoặc back)
     */
    public function cancelReservedSeats(Request $request)
    {
        $showtime_id = $request->input('showtime_id');
        $seats = $request->input('seats', []);

        if (empty($seats)) {
            return response()->json(['success' => false]);
        }

        DB::table('showtime_seats')
            ->where('showtime_id', $showtime_id)
            ->whereIn('seat_id', $seats)
            ->where('status', 'reserved')
            ->delete();

        return response()->json(['success' => true]);
    }
}
```

📝 **Giải thích các khái niệm quan trọng**:

**1. Database Transaction**:
- Đảm bảo tất cả operations thành công hoặc rollback toàn bộ
- Prevent race conditions khi nhiều người đặt cùng lúc

**2. Couple Seat Validation**:
- Ghế đôi phải được chọn theo cặp (1-2, 3-4...)
- Không cho phép chọn lẻ ghế couple

**3. Seat Status**:
- `available`: Ghế trống
- `reserved`: Đang được giữ (10 phút)
- `booked`: Đã thanh toán

---

## 🛠️ BƯỚC 2: TẠO ROUTES

**File**: `routes/web.php`

Thêm routes:

```php
use App\Http\Controllers\BookingController;

// Booking routes (cần đăng nhập)
Route::middleware('auth')->group(function () {
    Route::get('/booking/seatmap/{showtime_id}', [BookingController::class, 'seatMap'])
        ->name('booking.seatmap');

    Route::post('/booking/book-seats/{showtime_id}', [BookingController::class, 'bookSeats'])
        ->name('booking.book-seats');

    Route::post('/booking/cancel-reserved', [BookingController::class, 'cancelReservedSeats'])
        ->name('booking.cancel-reserved');
});
```

---

## 🛠️ BƯỚC 3: TẠO VIEWS

### 3.1. Confirm Booking View

**File**: `resources/views/booking/confirm.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Xác nhận đặt vé')

@push('styles')
<style>
.confirm-container {
    max-width: 800px;
    margin: 0 auto;
    padding: var(--spacing-2xl) var(--spacing-lg);
}

.confirm-card {
    background-color: var(--bg-card);
    border-radius: var(--radius-lg);
    padding: var(--spacing-2xl);
    margin-bottom: var(--spacing-lg);
}

.confirm-title {
    font-size: var(--font-size-2xl);
    margin-bottom: var(--spacing-xl);
    text-align: center;
}

.countdown-timer {
    background-color: var(--error-color);
    color: white;
    padding: var(--spacing-md);
    border-radius: var(--radius-md);
    text-align: center;
    font-size: var(--font-size-xl);
    font-weight: 600;
    margin-bottom: var(--spacing-xl);
}

.movie-info {
    margin-bottom: var(--spacing-xl);
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: var(--spacing-sm) 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.info-label {
    color: var(--text-secondary);
}

.info-value {
    color: var(--text-primary);
    font-weight: 500;
}

.seats-list {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-xl);
}

.seat-tag {
    background-color: var(--primary-color);
    color: white;
    padding: var(--spacing-xs) var(--spacing-md);
    border-radius: var(--radius-md);
    font-size: var(--font-size-sm);
}

.total-price {
    background-color: rgba(229, 9, 20, 0.1);
    border: 2px solid var(--primary-color);
    border-radius: var(--radius-md);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-xl);
}

.total-price-label {
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-sm);
}

.total-price-value {
    font-size: var(--font-size-3xl);
    color: var(--primary-color);
    font-weight: 700;
}

.payment-methods {
    margin-bottom: var(--spacing-xl);
}

.payment-option {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    padding: var(--spacing-md);
    background-color: var(--bg-dark);
    border: 2px solid transparent;
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-sm);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.payment-option:hover {
    border-color: var(--primary-color);
}

.payment-option input[type="radio"] {
    width: 20px;
    height: 20px;
}

.action-buttons {
    display: flex;
    gap: var(--spacing-md);
}
</style>
@endpush

@section('content')
<div class="confirm-container">
    <div class="confirm-card">
        <h1 class="confirm-title">🎫 Xác nhận đặt vé</h1>

        {{-- Countdown Timer --}}
        <div class="countdown-timer" id="countdownTimer">
            ⏰ Thời gian giữ ghế: <span id="timerDisplay">10:00</span>
        </div>

        {{-- Movie Info --}}
        <div class="movie-info">
            <h3 style="margin-bottom: var(--spacing-md);">Thông tin phim</h3>
            <div class="info-row">
                <span class="info-label">Phim:</span>
                <span class="info-value">{{ $movie->title }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ngày chiếu:</span>
                <span class="info-value">{{ $showtime->show_date->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Giờ chiếu:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($showtime->show_time)->format('H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phòng:</span>
                <span class="info-value">{{ $room->name }}</span>
            </div>
        </div>

        {{-- Selected Seats --}}
        <div style="margin-bottom: var(--spacing-xl);">
            <h3 style="margin-bottom: var(--spacing-md);">Ghế đã chọn</h3>
            <div class="seats-list">
                @foreach($seatDetails as $seat)
                    <span class="seat-tag">
                        {{ $seat['seat_code'] }} ({{ $seat['seat_type'] }})
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Price Breakdown --}}
        <div style="margin-bottom: var(--spacing-xl);">
            <h3 style="margin-bottom: var(--spacing-md);">Chi tiết giá</h3>
            @foreach($seatDetails as $seat)
                <div class="info-row">
                    <span class="info-label">{{ $seat['seat_code'] }} - {{ $seat['seat_type'] }}</span>
                    <span class="info-value">{{ number_format($seat['price'], 0, ',', '.') }} ₫</span>
                </div>
            @endforeach
        </div>

        {{-- Total Price --}}
        <div class="total-price">
            <div class="total-price-label">Tổng tiền:</div>
            <div class="total-price-value">{{ number_format($totalPrice, 0, ',', '.') }} ₫</div>
        </div>

        {{-- Payment Methods --}}
        <form action="{{ route('payment.process') }}" method="POST" id="bookingForm">
            @csrf
            <input type="hidden" name="showtime_id" value="{{ $showtime_id }}">
            <input type="hidden" name="total_price" value="{{ $totalPrice }}">
            <input type="hidden" name="seats" value="{{ json_encode(collect($seatDetails)->pluck('id')->toArray()) }}">

            <div class="payment-methods">
                <h3 style="margin-bottom: var(--spacing-md);">Phương thức thanh toán</h3>

                <label class="payment-option">
                    <input type="radio" name="payment_method" value="momo" checked>
                    <span style="font-size: 30px;">📱</span>
                    <span>MoMo</span>
                </label>

                <label class="payment-option">
                    <input type="radio" name="payment_method" value="vnpay">
                    <span style="font-size: 30px;">💳</span>
                    <span>VNPay</span>
                </label>
            </div>

            {{-- Action Buttons --}}
            <div class="action-buttons">
                <button type="button" class="btn btn-secondary btn-lg" onclick="goBack()">
                    ← Quay lại
                </button>
                <button type="submit" class="btn btn-primary btn-lg" style="flex: 1;">
                    Tiếp tục thanh toán →
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Countdown Timer (10 phút)
let timeLeft = 600; // 10 minutes in seconds
const timerDisplay = document.getElementById('timerDisplay');
const countdownTimer = document.getElementById('countdownTimer');

const countdown = setInterval(function() {
    timeLeft--;

    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;

    timerDisplay.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

    // Warning khi còn 1 phút
    if (timeLeft <= 60) {
        countdownTimer.style.animation = 'pulse 1s infinite';
    }

    // Hết giờ
    if (timeLeft <= 0) {
        clearInterval(countdown);
        alert('Hết thời gian giữ ghế! Vui lòng đặt lại.');
        window.location.href = '{{ route("booking.seatmap", $showtime_id) }}';
    }
}, 1000);

function goBack() {
    if (confirm('Bạn có chắc muốn quay lại? Ghế đã chọn sẽ bị hủy.')) {
        clearInterval(countdown);
        window.location.href = '{{ route("booking.seatmap", $showtime_id) }}';
    }
}

// Prevent accidental page close
window.addEventListener('beforeunload', function (e) {
    e.preventDefault();
    e.returnValue = '';
});
</script>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}
</style>
@endpush
```

---

## ✅ TEST & VERIFY

### Test flow đặt vé

1. Truy cập chi tiết phim
2. Click chọn suất chiếu
3. Chọn ghế trên seat map
4. Click "Đặt vé"
5. Xem trang xác nhận với countdown
6. Test timeout (đợi 10 phút)

---

## 📝 TÓM TẮT

Đã tạo:
- BookingController với validation đầy đủ
- Confirm booking view với countdown timer
- Couple seat logic
- Reserved seats tracking

---

## 🚀 BƯỚC TIẾP THEO

**Bài tiếp**: [08. Seat Selection →](08_seat_selection.md)

Tạo giao diện chọn ghế interactive với visual seat map.

---

**Bài trước**: [← 06. Movie Features](06_movie_features.md)
**Series**: Cinebook Tutorial
**Cập nhật**: January 2026
