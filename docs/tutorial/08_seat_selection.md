# 08. GIAO DIỆN CHỌN GHẾ INTERACTIVE

## 🎯 Mục tiêu bài học

Sau bài học này, bạn sẽ có:
- ✅ Interactive seat map với visual feedback
- ✅ Logic chọn ghế Couple (chọn theo cặp)
- ✅ Hiển thị ghế available/booked/selected
- ✅ JavaScript xử lý seat selection
- ✅ AJAX lưu ghế đã chọn
- ✅ Responsive seat layout

**Thời gian ước tính**: 90-120 phút

---

## 📚 Kiến thức cần biết

- JavaScript DOM manipulation
- Event listeners
- Array methods (map, filter, includes)
- AJAX với fetch API
- CSS Grid/Flexbox
- LocalStorage basics

---

## 🛠️ BƯỚC 1: TẠO SEAT MAP VIEW

### 1.1. Tạo Blade Template

**File**: `resources/views/booking/seat_map.blade.php`

```blade
@extends('layouts.main')

@section('title', 'Chọn ghế - ' . $showtime->movie->title)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/seat_map.css') }}">
@endsection

@section('content')
<div class="seat-selection-container">
    <div class="container">
        <!-- Movie Info Header -->
        <div class="movie-info-header">
            <div class="movie-poster">
                <img src="{{ $showtime->movie->poster_url }}" alt="{{ $showtime->movie->title }}">
            </div>
            <div class="movie-details">
                <h1>{{ $showtime->movie->title }}</h1>
                <div class="showtime-info">
                    <p><strong>Phòng:</strong> {{ $showtime->room->name }}</p>
                    <p><strong>Suất chiếu:</strong> {{ $showtime->show_date->format('d/m/Y') }} - {{ $showtime->show_time->format('H:i') }}</p>
                    <p><strong>Thời lượng:</strong> {{ $showtime->movie->duration }} phút</p>
                </div>
            </div>
        </div>

        <!-- Seat Legend -->
        <div class="seat-legend">
            <div class="legend-item">
                <div class="seat-icon available"></div>
                <span>Ghế trống</span>
            </div>
            <div class="legend-item">
                <div class="seat-icon selected"></div>
                <span>Ghế đang chọn</span>
            </div>
            <div class="legend-item">
                <div class="seat-icon booked"></div>
                <span>Ghế đã đặt</span>
            </div>
            <div class="legend-item">
                <div class="seat-icon vip"></div>
                <span>Ghế VIP</span>
            </div>
            <div class="legend-item">
                <div class="seat-icon couple"></div>
                <span>Ghế Couple</span>
            </div>
        </div>

        <!-- Screen -->
        <div class="screen-container">
            <div class="screen">MÀN HÌNH</div>
        </div>

        <!-- Seat Map -->
        <div class="seat-map" id="seatMap">
            @php
                $seatLayout = $showtime->room->seats()
                    ->orderBy('seat_row')
                    ->orderBy('seat_number')
                    ->get()
                    ->groupBy('seat_row');
            @endphp

            @foreach($seatLayout as $row => $seats)
                <div class="seat-row" data-row="{{ $row }}">
                    <div class="row-label">{{ $row }}</div>
                    <div class="seats-container">
                        @foreach($seats as $seat)
                            @php
                                // Get seat status from showtime_seats
                                $showtimeSeat = $showtime->showtimeSeats()
                                    ->where('seat_id', $seat->id)
                                    ->first();

                                $status = $showtimeSeat ? $showtimeSeat->status : 'available';
                                $isAvailable = $status === 'available';
                                $seatTypeClass = '';

                                switch($seat->seat_type_id) {
                                    case 1: $seatTypeClass = 'standard'; break;
                                    case 2: $seatTypeClass = 'vip'; break;
                                    case 3: $seatTypeClass = 'couple'; break;
                                }
                            @endphp

                            <div class="seat {{ $seatTypeClass }} {{ $status }}"
                                 data-seat-id="{{ $seat->id }}"
                                 data-seat-code="{{ $seat->seat_code }}"
                                 data-seat-type="{{ $seat->seat_type_id }}"
                                 data-seat-row="{{ $seat->seat_row }}"
                                 data-seat-number="{{ $seat->seat_number }}"
                                 data-status="{{ $status }}"
                                 {{ !$isAvailable ? 'disabled' : '' }}>
                                <span class="seat-number">{{ $seat->seat_code }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="row-label">{{ $row }}</div>
                </div>
            @endforeach
        </div>

        <!-- Selected Seats Summary -->
        <div class="selection-summary">
            <div class="summary-content">
                <div class="selected-seats-list">
                    <h3>Ghế đã chọn:</h3>
                    <div id="selectedSeatsList" class="seats-list">
                        <p class="no-seats">Chưa chọn ghế nào</p>
                    </div>
                </div>

                <div class="price-summary">
                    <div class="price-row">
                        <span>Tổng số ghế:</span>
                        <span id="totalSeats">0</span>
                    </div>
                    <div class="price-row total">
                        <span>Tổng tiền (ước tính):</span>
                        <span id="totalPrice">0 ₫</span>
                    </div>
                    <p class="price-note">* Giá chính xác sẽ được hiển thị ở bước tiếp theo</p>
                </div>
            </div>

            <div class="action-buttons">
                <a href="{{ route('movie.showtimes', $showtime->movie_id) }}" class="btn btn-secondary">
                    Quay lại
                </a>
                <button type="button" id="confirmSeatsBtn" class="btn btn-primary" disabled>
                    Tiếp tục
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form to submit selected seats -->
<form id="seatSelectionForm" action="{{ route('showtime.book', $showtime->id) }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="seat_ids" id="seatIdsInput">
</form>
@endsection

@section('scripts')
<script>
    // Pass data from Laravel to JavaScript
    const SHOWTIME_ID = {{ $showtime->id }};
    const SEAT_PRICES = @json($showtimePrices);
</script>
<script src="{{ asset('js/seat_map.js') }}"></script>
@endsection
```

---

## 🛠️ BƯỚC 2: TẠO CSS CHO SEAT MAP

### 2.1. Tạo file CSS

**File**: `resources/css/seat_map.css`

```css
/* ============================================
   SEAT MAP STYLES
   ============================================ */

.seat-selection-container {
    background-color: var(--bg-dark);
    min-height: 100vh;
    padding: var(--spacing-xl) 0;
}

/* Movie Info Header */
.movie-info-header {
    display: flex;
    gap: var(--spacing-lg);
    background-color: var(--bg-card);
    padding: var(--spacing-lg);
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-xl);
}

.movie-poster {
    flex-shrink: 0;
}

.movie-poster img {
    width: 120px;
    height: 180px;
    object-fit: cover;
    border-radius: var(--radius-md);
}

.movie-details h1 {
    font-size: var(--font-size-2xl);
    margin-bottom: var(--spacing-md);
    color: var(--text-primary);
}

.showtime-info p {
    margin-bottom: var(--spacing-sm);
    color: var(--text-secondary);
}

/* Seat Legend */
.seat-legend {
    display: flex;
    justify-content: center;
    gap: var(--spacing-xl);
    flex-wrap: wrap;
    margin-bottom: var(--spacing-xl);
    padding: var(--spacing-md);
    background-color: var(--bg-card);
    border-radius: var(--radius-md);
}

.legend-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    color: var(--text-secondary);
    font-size: var(--font-size-sm);
}

.seat-icon {
    width: 30px;
    height: 30px;
    border-radius: var(--radius-sm);
    border: 2px solid transparent;
}

.seat-icon.available {
    background-color: var(--seat-available);
}

.seat-icon.selected {
    background-color: var(--seat-selected);
    border-color: var(--primary-hover);
}

.seat-icon.booked {
    background-color: var(--seat-booked);
    cursor: not-allowed;
}

.seat-icon.vip {
    background-color: var(--seat-vip);
}

.seat-icon.couple {
    background-color: var(--seat-couple);
}

/* Screen */
.screen-container {
    display: flex;
    justify-content: center;
    margin-bottom: var(--spacing-2xl);
    perspective: 1000px;
}

.screen {
    background: linear-gradient(to bottom, #333, #666);
    color: var(--text-primary);
    padding: var(--spacing-md) var(--spacing-2xl);
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
    font-size: var(--font-size-lg);
    font-weight: 600;
    text-align: center;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.5);
    transform: rotateX(-10deg);
    width: 80%;
    max-width: 600px;
}

/* Seat Map */
.seat-map {
    max-width: 900px;
    margin: 0 auto var(--spacing-2xl);
    padding: var(--spacing-lg);
    background-color: var(--bg-card);
    border-radius: var(--radius-lg);
}

.seat-row {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-md);
    justify-content: center;
}

.row-label {
    width: 30px;
    text-align: center;
    font-weight: 600;
    color: var(--text-secondary);
    font-size: var(--font-size-lg);
}

.seats-container {
    display: flex;
    gap: var(--spacing-xs);
    flex-wrap: wrap;
    justify-content: center;
}

/* Individual Seat */
.seat {
    width: 35px;
    height: 35px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--transition-base);
    position: relative;
    border: 2px solid transparent;
    font-size: var(--font-size-xs);
    font-weight: 500;
}

.seat-number {
    color: white;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

/* Standard Seat */
.seat.standard.available {
    background-color: var(--seat-available);
}

.seat.standard.available:hover {
    transform: scale(1.15);
    box-shadow: var(--shadow-md);
}

/* VIP Seat */
.seat.vip.available {
    background-color: var(--seat-vip);
}

.seat.vip.available:hover {
    transform: scale(1.15);
    box-shadow: 0 0 10px var(--seat-vip);
}

/* Couple Seat */
.seat.couple.available {
    background-color: var(--seat-couple);
    width: 50px; /* Wider for couple seats */
}

.seat.couple.available:hover {
    transform: scale(1.1);
    box-shadow: 0 0 10px var(--seat-couple);
}

/* Selected State */
.seat.selected {
    background-color: var(--seat-selected) !important;
    border-color: var(--primary-hover);
    transform: scale(1.1);
    box-shadow: 0 0 15px var(--seat-selected);
}

/* Booked/Reserved State */
.seat.booked,
.seat.reserved,
.seat.locked {
    background-color: var(--seat-booked);
    cursor: not-allowed;
    opacity: 0.5;
}

.seat[disabled] {
    pointer-events: none;
}

/* Selection Summary */
.selection-summary {
    background-color: var(--bg-card);
    padding: var(--spacing-xl);
    border-radius: var(--radius-lg);
    max-width: 900px;
    margin: 0 auto;
    position: sticky;
    bottom: var(--spacing-lg);
}

.summary-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-xl);
    margin-bottom: var(--spacing-lg);
}

.selected-seats-list h3 {
    margin-bottom: var(--spacing-md);
    color: var(--text-primary);
}

.seats-list {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-sm);
}

.seat-tag {
    background-color: var(--primary-color);
    color: var(--text-primary);
    padding: var(--spacing-xs) var(--spacing-md);
    border-radius: var(--radius-full);
    font-size: var(--font-size-sm);
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.seat-tag .remove-seat {
    cursor: pointer;
    font-weight: bold;
    margin-left: var(--spacing-xs);
}

.seat-tag .remove-seat:hover {
    color: var(--error-color);
}

.no-seats {
    color: var(--text-muted);
    font-style: italic;
}

/* Price Summary */
.price-summary {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
}

.price-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-sm) 0;
    color: var(--text-secondary);
}

.price-row.total {
    border-top: 2px solid var(--text-muted);
    padding-top: var(--spacing-md);
    margin-top: var(--spacing-sm);
    font-size: var(--font-size-lg);
    font-weight: 600;
    color: var(--text-primary);
}

.price-row.total span:last-child {
    color: var(--primary-color);
    font-size: var(--font-size-xl);
}

.price-note {
    font-size: var(--font-size-xs);
    color: var(--text-muted);
    font-style: italic;
    margin-top: var(--spacing-sm);
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: var(--spacing-md);
    justify-content: center;
}

.action-buttons .btn {
    flex: 1;
    max-width: 200px;
}

/* Responsive */
@media (max-width: 768px) {
    .movie-info-header {
        flex-direction: column;
        text-align: center;
    }

    .movie-poster {
        margin: 0 auto;
    }

    .seat-legend {
        gap: var(--spacing-md);
    }

    .summary-content {
        grid-template-columns: 1fr;
    }

    .seat {
        width: 30px;
        height: 30px;
        font-size: 10px;
    }

    .seat.couple {
        width: 45px;
    }

    .screen {
        font-size: var(--font-size-base);
        padding: var(--spacing-sm) var(--spacing-lg);
    }
}
```

---

## 🛠️ BƯỚC 3: TẠO JAVASCRIPT LOGIC

### 3.1. Tạo file seat_map.js

**File**: `public/js/seat_map.js`

```javascript
/**
 * SEAT MAP - Interactive Seat Selection
 * Features:
 * - Select/deselect seats
 * - Couple seat logic (must select both)
 * - Visual feedback
 * - Price calculation
 * - Form submission
 */

class SeatMap {
    constructor() {
        this.selectedSeats = [];
        this.seatPrices = window.SEAT_PRICES || {};
        this.showtimeId = window.SHOWTIME_ID;

        this.init();
    }

    init() {
        this.bindEvents();
        this.loadSavedSelection();
    }

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Click on seats
        document.querySelectorAll('.seat.available').forEach(seat => {
            seat.addEventListener('click', (e) => this.handleSeatClick(e));
        });

        // Confirm button
        const confirmBtn = document.getElementById('confirmSeatsBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => this.confirmSelection());
        }

        // Remove seat tags
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-seat')) {
                const seatId = parseInt(e.target.dataset.seatId);
                this.deselectSeat(seatId);
            }
        });
    }

    /**
     * Handle seat click event
     */
    handleSeatClick(event) {
        const seat = event.currentTarget;
        const seatId = parseInt(seat.dataset.seatId);
        const seatTypeId = parseInt(seat.dataset.seatType);
        const seatCode = seat.dataset.seatCode;
        const status = seat.dataset.status;

        // Check if seat is available
        if (status !== 'available') {
            return;
        }

        // Check if already selected
        const isSelected = this.selectedSeats.some(s => s.id === seatId);

        if (isSelected) {
            this.deselectSeat(seatId);
        } else {
            this.selectSeat(seatId, seatCode, seatTypeId, seat);
        }
    }

    /**
     * Select a seat
     */
    selectSeat(seatId, seatCode, seatTypeId, seatElement) {
        // If couple seat, must select both
        if (seatTypeId === 3) {
            this.selectCoupleSeat(seatId, seatCode, seatElement);
        } else {
            // Regular seat (Standard or VIP)
            this.addSeatToSelection(seatId, seatCode, seatTypeId);
            seatElement.classList.add('selected');
        }

        this.updateUI();
    }

    /**
     * Select couple seat (must select both seats in pair)
     */
    selectCoupleSeat(seatId, seatCode, seatElement) {
        const seatRow = seatElement.dataset.seatRow;
        const seatNumber = parseInt(seatElement.dataset.seatNumber);

        // Couple seats are in pairs: odd with even (1-2, 3-4, 5-6, etc.)
        const isOdd = seatNumber % 2 === 1;
        const pairNumber = isOdd ? seatNumber + 1 : seatNumber - 1;
        const pairCode = `${seatRow}${pairNumber}`;

        // Find pair seat element
        const pairSeat = document.querySelector(
            `.seat[data-seat-code="${pairCode}"]`
        );

        if (!pairSeat) {
            alert('Không tìm thấy ghế cặp!');
            return;
        }

        // Check if pair is available
        if (pairSeat.dataset.status !== 'available') {
            alert(`Ghế ${pairCode} không khả dụng. Vui lòng chọn cặp ghế khác.`);
            return;
        }

        // Check if pair is already selected
        const pairId = parseInt(pairSeat.dataset.seatId);
        const pairAlreadySelected = this.selectedSeats.some(s => s.id === pairId);

        if (pairAlreadySelected) {
            // If pair already selected, deselect both
            this.deselectSeat(seatId);
            this.deselectSeat(pairId);
            return;
        }

        // Select both seats
        this.addSeatToSelection(seatId, seatCode, 3);
        this.addSeatToSelection(pairId, pairCode, 3);

        seatElement.classList.add('selected');
        pairSeat.classList.add('selected');
    }

    /**
     * Add seat to selection array
     */
    addSeatToSelection(seatId, seatCode, seatTypeId) {
        this.selectedSeats.push({
            id: seatId,
            code: seatCode,
            typeId: seatTypeId
        });

        this.saveSelection();
    }

    /**
     * Deselect a seat
     */
    deselectSeat(seatId) {
        const seat = this.selectedSeats.find(s => s.id === seatId);
        if (!seat) return;

        // If couple seat, deselect both
        if (seat.typeId === 3) {
            const seatElement = document.querySelector(
                `.seat[data-seat-id="${seatId}"]`
            );
            const seatRow = seatElement.dataset.seatRow;
            const seatNumber = parseInt(seatElement.dataset.seatNumber);
            const isOdd = seatNumber % 2 === 1;
            const pairNumber = isOdd ? seatNumber + 1 : seatNumber - 1;
            const pairCode = `${seatRow}${pairNumber}`;

            const pairSeat = document.querySelector(
                `.seat[data-seat-code="${pairCode}"]`
            );
            const pairId = pairSeat ? parseInt(pairSeat.dataset.seatId) : null;

            // Remove both from selection
            this.selectedSeats = this.selectedSeats.filter(
                s => s.id !== seatId && s.id !== pairId
            );

            // Remove visual selection
            seatElement.classList.remove('selected');
            if (pairSeat) {
                pairSeat.classList.remove('selected');
            }
        } else {
            // Regular seat
            this.selectedSeats = this.selectedSeats.filter(s => s.id !== seatId);

            const seatElement = document.querySelector(
                `.seat[data-seat-id="${seatId}"]`
            );
            if (seatElement) {
                seatElement.classList.remove('selected');
            }
        }

        this.saveSelection();
        this.updateUI();
    }

    /**
     * Update UI with selected seats and price
     */
    updateUI() {
        const selectedList = document.getElementById('selectedSeatsList');
        const totalSeatsEl = document.getElementById('totalSeats');
        const totalPriceEl = document.getElementById('totalPrice');
        const confirmBtn = document.getElementById('confirmSeatsBtn');

        // Update selected seats list
        if (this.selectedSeats.length === 0) {
            selectedList.innerHTML = '<p class="no-seats">Chưa chọn ghế nào</p>';
            confirmBtn.disabled = true;
        } else {
            const seatTags = this.selectedSeats.map(seat => `
                <div class="seat-tag">
                    <span>${seat.code}</span>
                    <span class="remove-seat" data-seat-id="${seat.id}">✕</span>
                </div>
            `).join('');

            selectedList.innerHTML = seatTags;
            confirmBtn.disabled = false;
        }

        // Update total seats
        totalSeatsEl.textContent = this.selectedSeats.length;

        // Calculate and update total price (estimated)
        const totalPrice = this.calculateTotalPrice();
        totalPriceEl.textContent = this.formatPrice(totalPrice);
    }

    /**
     * Calculate total price (estimated)
     * Note: Actual price will be calculated server-side
     */
    calculateTotalPrice() {
        let total = 0;

        this.selectedSeats.forEach(seat => {
            // Get price for this seat type
            const price = this.seatPrices[seat.typeId] || 0;
            total += parseFloat(price);
        });

        return total;
    }

    /**
     * Format price to VND
     */
    formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    }

    /**
     * Save selection to localStorage
     */
    saveSelection() {
        const key = `seat_selection_${this.showtimeId}`;
        localStorage.setItem(key, JSON.stringify(this.selectedSeats));
    }

    /**
     * Load saved selection from localStorage
     */
    loadSavedSelection() {
        const key = `seat_selection_${this.showtimeId}`;
        const saved = localStorage.getItem(key);

        if (saved) {
            try {
                const seats = JSON.parse(saved);

                // Reselect seats
                seats.forEach(seat => {
                    const seatElement = document.querySelector(
                        `.seat[data-seat-id="${seat.id}"]`
                    );

                    if (seatElement && seatElement.dataset.status === 'available') {
                        seatElement.classList.add('selected');
                    }
                });

                this.selectedSeats = seats;
                this.updateUI();
            } catch (e) {
                console.error('Error loading saved selection:', e);
                localStorage.removeItem(key);
            }
        }
    }

    /**
     * Clear saved selection
     */
    clearSavedSelection() {
        const key = `seat_selection_${this.showtimeId}`;
        localStorage.removeItem(key);
    }

    /**
     * Confirm selection and submit
     */
    confirmSelection() {
        if (this.selectedSeats.length === 0) {
            alert('Vui lòng chọn ít nhất một ghế!');
            return;
        }

        // Get seat IDs
        const seatIds = this.selectedSeats.map(s => s.id);

        // Set hidden input value
        document.getElementById('seatIdsInput').value = JSON.stringify(seatIds);

        // Submit form
        document.getElementById('seatSelectionForm').submit();
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new SeatMap();
});
```

---

## 🛠️ BƯỚC 4: CẬP NHẬT CONTROLLER

### 4.1. Update BookingController

Thêm method `seatMap` vào **`app/Http/Controllers/BookingController.php`**:

```php
/**
 * Show seat map for showtime.
 */
public function seatMap($showtimeId)
{
    $showtime = Showtime::with([
        'movie',
        'room.seats.seatType',
        'showtimeSeats',
        'showtimePrices.seatType'
    ])->findOrFail($showtimeId);

    // Check if showtime is in the past
    if ($showtime->isPast()) {
        return redirect()->back()
            ->with('error', 'Suất chiếu này đã kết thúc.');
    }

    // Get prices for each seat type
    $showtimePrices = $showtime->showtimePrices->pluck('price', 'seat_type_id');

    return view('booking.seat_map', compact('showtime', 'showtimePrices'));
}
```

---

## 🛠️ BƯỚC 5: THÊM ROUTES

**File**: `routes/web.php`

```php
// Seat selection
Route::get('/showtimes/{id}/seats', [BookingController::class, 'seatMap'])
    ->name('showtime.seats');
```

---

## ✅ TEST & VERIFY

### Test Cases:

1. **Chọn ghế Standard**:
   - Click vào ghế Standard
   - Ghế chuyển màu đỏ
   - Hiển thị trong danh sách ghế đã chọn
   - Tổng tiền cập nhật

2. **Chọn ghế VIP**:
   - Click vào ghế VIP (màu vàng)
   - Giá cao hơn Standard

3. **Chọn ghế Couple**:
   - Click vào 1 ghế Couple
   - Cả 2 ghế trong cặp đều được chọn
   - Nếu ghế cặp đã booked → hiện alert

4. **Bỏ chọn ghế**:
   - Click lại vào ghế đã chọn
   - Ghế quay về trạng thái available
   - Ghế couple: bỏ cả 2 ghế

5. **Persist selection**:
   - Chọn ghế
   - Refresh page
   - Ghế vẫn được chọn (localStorage)

6. **Submit form**:
   - Chọn ghế
   - Click "Tiếp tục"
   - Redirect đến trang confirm

---

## 🎯 THỰC HÀNH

### Bài tập 1: Thêm giới hạn số ghế
Giới hạn tối đa 10 ghế mỗi lần đặt:

```javascript
selectSeat(seatId, seatCode, seatTypeId, seatElement) {
    // Check max seats
    if (this.selectedSeats.length >= 10) {
        alert('Bạn chỉ được chọn tối đa 10 ghế!');
        return;
    }
    // ... rest of code
}
```

### Bài tập 2: Thêm hover effect
Khi hover vào ghế couple, highlight cả 2 ghế.

### Bài tập 3: Responsive mobile
Tối ưu layout cho mobile (ghế nhỏ hơn, scroll horizontal).

---

## 🐛 TROUBLESHOOTING

### Lỗi 1: "Ghế cặp không được chọn cùng lúc"
**Nguyên nhân**: Logic tính pair number sai

**Giải pháp**: Kiểm tra công thức `isOdd` và `pairNumber`

### Lỗi 2: "LocalStorage không hoạt động"
**Nguyên nhân**: Browser blocking localStorage

**Giải pháp**: Kiểm tra browser settings, dùng incognito mode test

### Lỗi 3: "Giá hiển thị 0 VND"
**Nguyên nhân**: `SEAT_PRICES` không được pass từ Laravel

**Giải pháp**: Verify biến `$showtimePrices` trong controller

---

## 📝 TÓM TẮT

Đã hoàn thành:
- ✅ Interactive seat map với visual feedback
- ✅ Couple seat logic (chọn cặp)
- ✅ LocalStorage persistence
- ✅ Price calculation (estimated)
- ✅ Responsive design

**Bài tiếp**: [09. Payment & QR Code →](09_payment_qr.md)

---

**Bài trước**: [← 07. Booking System](07_booking_system.md)
**Series**: Cinebook Tutorial
