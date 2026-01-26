# CHEATSHEET: QUẢN LÝ ĐẶT VÉ (BOOKINGS)
## Lifecycle + Seat Locking + QR Generation

---

## 🎯 MỤC ĐÍCH

Booking là **core business** của hệ thống đặt vé:
- Theo dõi tất cả đơn đặt vé
- Quản lý trạng thái booking
- Xử lý hủy vé và hoàn tiền
- Tạo QR code cho check-in

---

## 📁 FILES LIÊN QUAN

```
Controller: app/Http/Controllers/Admin/AdminBookingController.php
Models:     app/Models/Booking.php
            app/Models/BookingSeat.php
Views:      resources/views/admin/bookings/
            ├── index.blade.php
            ├── show.blade.php
            └── (không có create - user tạo từ frontend)
```

---

## 🗄️ DATABASE SCHEMA

### Table: bookings

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| user_id | BIGINT | FK to users |
| showtime_id | BIGINT | FK to showtimes |
| booking_code | VARCHAR(20) | Mã đặt vé unique (BK20240115001) |
| total_price | DECIMAL | Tổng tiền |
| status | ENUM | pending, confirmed, checked_in, completed, cancelled, expired |
| payment_method | VARCHAR(50) | cash, card, momo, vnpay |
| payment_status | ENUM | pending, paid, refunded |
| notes | TEXT | Ghi chú |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### Table: booking_seats

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| booking_id | BIGINT | FK to bookings |
| showtime_id | BIGINT | FK to showtimes |
| seat_id | BIGINT | FK to seats |
| price | DECIMAL | Giá của ghế này |
| qr_code | VARCHAR(255) | QR code unique cho ghế này |
| checked_in_at | TIMESTAMP | Thời điểm check-in |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

## 🔗 QUAN HỆ (RELATIONSHIPS)

```php
// Booking.php
class Booking extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class);
    }

    public function seats()
    {
        return $this->belongsToMany(Seat::class, 'booking_seats')
            ->withPivot(['price', 'qr_code', 'checked_in_at']);
    }
}

// BookingSeat.php
class BookingSeat extends Model
{
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }
}
```

---

## 🔄 BOOKING LIFECYCLE (Vòng đời đặt vé)

### State Diagram

```
                    ┌──────────────┐
                    │   PENDING    │ ← User chọn ghế, chưa thanh toán
                    └──────┬───────┘
                           │
            ┌──────────────┼──────────────┐
            │              │              │
            ▼              ▼              ▼
    ┌───────────┐  ┌───────────┐  ┌───────────┐
    │  EXPIRED  │  │ CANCELLED │  │ CONFIRMED │ ← Đã thanh toán
    └───────────┘  └───────────┘  └─────┬─────┘
    (15 phút)      (User/Admin)         │
                                        ▼
                               ┌────────────────┐
                               │  CHECKED_IN    │ ← Đã scan QR
                               └───────┬────────┘
                                       │
                                       ▼
                               ┌────────────────┐
                               │   COMPLETED    │ ← Phim đã chiếu xong
                               └────────────────┘
```

### Status Definitions

| Status | Ý nghĩa | Ghế bị lock? | Có thể hủy? |
|--------|---------|--------------|-------------|
| pending | Đang chờ thanh toán | Có (15 phút) | Tự động sau 15p |
| confirmed | Đã thanh toán | Có | Admin cancel |
| checked_in | Đã vào rạp | Có | Không |
| completed | Đã xem xong | Có | Không |
| cancelled | Đã hủy | Không | - |
| expired | Hết hạn thanh toán | Không | - |

---

## 🔒 SEAT LOCKING MECHANISM

### Vấn đề Race Condition

```
Scenario KHÔNG có locking:
1. User A chọn ghế A1 lúc 10:00:00
2. User B chọn ghế A1 lúc 10:00:01
3. User A submit đặt vé lúc 10:00:30
4. User B submit đặt vé lúc 10:00:31
5. Cả 2 đều đặt được A1! → CONFLICT!
```

### Giải pháp: Temporary Lock

```php
// Khi user chọn ghế
public function selectSeat(Request $request)
{
    $seatId = $request->seat_id;
    $showtimeId = $request->showtime_id;

    // Check if seat is already booked or locked
    $isBooked = BookingSeat::where('showtime_id', $showtimeId)
        ->where('seat_id', $seatId)
        ->whereHas('booking', fn($q) =>
            $q->whereIn('status', ['pending', 'confirmed'])
        )
        ->exists();

    if ($isBooked) {
        return response()->json(['error' => 'Ghế đã được đặt'], 400);
    }

    // Create pending booking (locks the seat)
    DB::transaction(function () use ($seatId, $showtimeId, $request) {
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'showtime_id' => $showtimeId,
            'status' => 'pending',
            // ...
        ]);

        BookingSeat::create([
            'booking_id' => $booking->id,
            'showtime_id' => $showtimeId,
            'seat_id' => $seatId,
        ]);
    });

    return response()->json(['success' => true]);
}
```

### Auto Expire (Scheduled Task)

```php
// Chạy mỗi phút
public function expirePendingBookings()
{
    Booking::where('status', 'pending')
        ->where('created_at', '<', now()->subMinutes(15))
        ->update(['status' => 'expired']);
}
```

---

## 🎫 QR CODE GENERATION

### Cấu trúc QR Code

```php
// Mỗi ghế có 1 QR riêng
$qrCode = hash('sha256',
    $bookingId . '-' .
    $seatId . '-' .
    $showtimeId . '-' .
    config('app.key')
);

// Ví dụ output:
// a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6
```

### Tại sao dùng SHA-256?

```
1. UNIQUE: Mỗi combination cho ra hash khác nhau
2. SECURE: Không thể đoán ngược
3. FIXED LENGTH: Luôn 64 ký tự
4. FAST: Tính toán nhanh
```

### Code Generation

```php
// BookingSeat.php
protected static function boot()
{
    parent::boot();

    static::creating(function ($bookingSeat) {
        $bookingSeat->qr_code = hash('sha256',
            $bookingSeat->booking_id . '-' .
            $bookingSeat->seat_id . '-' .
            $bookingSeat->showtime_id . '-' .
            config('app.key') . '-' .
            Str::random(8) // Extra randomness
        );
    });
}
```

---

## 💻 CODE QUAN TRỌNG

### Index với Filter

```php
public function index(Request $request)
{
    $query = Booking::with(['user', 'showtime.movie', 'showtime.room']);

    // Filter by status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter by date range
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    // Search by booking code or user
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('booking_code', 'like', "%{$search}%")
              ->orWhereHas('user', fn($q) =>
                  $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
              );
        });
    }

    $bookings = $query->latest()->paginate(15)->withQueryString();

    return view('admin.bookings.index', compact('bookings'));
}
```

### Cancel Booking

```php
public function cancel(Booking $booking)
{
    // Check if can cancel
    if (!in_array($booking->status, ['pending', 'confirmed'])) {
        return back()->with('error', 'Không thể hủy booking này!');
    }

    // Check if showtime already started
    $showtime = $booking->showtime;
    $showtimeStart = Carbon::parse($showtime->show_date . ' ' . $showtime->start_time);

    if ($showtimeStart->isPast()) {
        return back()->with('error', 'Suất chiếu đã bắt đầu, không thể hủy!');
    }

    // Update status
    $booking->update([
        'status' => 'cancelled',
        'payment_status' => $booking->payment_status === 'paid' ? 'refunded' : 'pending',
    ]);

    // NOTE: Seats are automatically "freed" because we check status in queries

    return back()->with('success', 'Đã hủy booking thành công!');
}
```

### Show Booking Detail

```php
public function show(Booking $booking)
{
    $booking->load([
        'user',
        'showtime.movie',
        'showtime.room',
        'bookingSeats.seat',
    ]);

    return view('admin.bookings.show', compact('booking'));
}
```

---

## 🎨 UI COMPONENTS

### Index Page

```
┌─────────────────────────────────────────────────────────────┐
│  QUẢN LÝ ĐẶT VÉ                                            │
├─────────────────────────────────────────────────────────────┤
│  Search: [___________]  Status: [All ▼]  Date: [__] - [__] │
│                                              [Tìm kiếm]    │
├─────────────────────────────────────────────────────────────┤
│  # │ Mã đặt vé    │ Khách hàng │ Phim      │ Tổng   │Status│
│────┼──────────────┼────────────┼───────────┼────────┼──────│
│  1 │ BK240115001  │ Nguyễn A   │ Aquaman 2 │ 300k   │ ✓    │
│  2 │ BK240115002  │ Trần B     │ Wonka     │ 200k   │ ⏳   │
│  3 │ BK240115003  │ Lê C       │ Migration │ 150k   │ ✗    │
├─────────────────────────────────────────────────────────────┤
│  Tổng: 156 booking │ Confirmed: 120 │ Pending: 25 │ ...   │
└─────────────────────────────────────────────────────────────┘
```

### Detail Page

```
┌─────────────────────────────────────────────────────────────┐
│  CHI TIẾT BOOKING #BK240115001                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  THÔNG TIN KHÁCH HÀNG          THÔNG TIN SUẤT CHIẾU        │
│  ├─ Tên: Nguyễn Văn A          ├─ Phim: Aquaman 2          │
│  ├─ Email: a@email.com         ├─ Phòng: Room 1 (2D)       │
│  └─ Phone: 0901234567          ├─ Ngày: 15/01/2024         │
│                                └─ Giờ: 19:00 - 21:15       │
│                                                             │
│  DANH SÁCH GHẾ                                             │
│  ┌──────┬─────────┬──────────┬────────────┬──────────────┐ │
│  │ Ghế  │ Loại    │ Giá      │ QR Code    │ Check-in     │ │
│  ├──────┼─────────┼──────────┼────────────┼──────────────┤ │
│  │ E5   │ VIP     │ 150,000  │ [QR]       │ ✓ 18:45      │ │
│  │ E6   │ VIP     │ 150,000  │ [QR]       │ ✓ 18:45      │ │
│  └──────┴─────────┴──────────┴────────────┴──────────────┘ │
│                                                             │
│  TỔNG CỘNG: 300,000 VND        Status: ✓ CONFIRMED        │
│  Payment: MoMo                  Paid at: 15/01 14:30       │
│                                                             │
│  [In vé]  [Gửi email]  [Hủy booking]                       │
└─────────────────────────────────────────────────────────────┘
```

---

## ❓ CÂU HỎI THƯỜNG GẶP

### Q: "Làm sao tránh 2 người đặt cùng 1 ghế?"

```
"Sử dụng 2 cơ chế:

1. Database level:
   - Unique constraint trên (showtime_id, seat_id) trong booking_seats
   - Nếu insert duplicate → Database reject

2. Application level:
   - Khi user chọn ghế → Tạo booking pending ngay
   - Ghế bị 'lock' trong 15 phút
   - User khác query sẽ thấy ghế đã taken

3. Transaction:
   - Check availability và create booking trong cùng transaction
   - Đảm bảo atomic operation"
```

### Q: "Pending booking expire như thế nào?"

```
"Scheduled task chạy mỗi phút:
- Query bookings có status = 'pending'
- Và created_at < now() - 15 phút
- Update status = 'expired'

Khi status = expired:
- Ghế được 'giải phóng' tự động
- Vì query available seats check status != expired

Không cần xóa booking_seats, chỉ cần đổi status."
```

### Q: "QR code unique như thế nào?"

```
"QR code được tạo bằng SHA-256 hash của:
- booking_id
- seat_id
- showtime_id
- app.key (secret)
- random string

Kết quả là string 64 ký tự, unique cho mỗi ghế của mỗi booking.
Không thể đoán, không thể giả mạo."
```

### Q: "Hủy booking thì ghế có được free không?"

```
"Có, nhưng không cần làm gì cả!

Query ghế available:
SELECT * FROM seats
WHERE id NOT IN (
    SELECT seat_id FROM booking_seats
    WHERE showtime_id = ? AND booking.status IN ('pending', 'confirmed')
)

Khi booking cancelled:
- status đổi thành 'cancelled'
- Query trên sẽ không còn tìm thấy
- Ghế tự động available cho người khác"
```

---

## 🎯 DEMO TIPS

### Chuẩn bị

```
✅ 20-30 bookings với đủ các status
✅ Một số booking cùng user (để show history)
✅ Một số booking đã check-in (để show QR works)
✅ Một booking pending (để demo cancel/expire)
```

### Khi demo

```
1. "Đây là trang quản lý tất cả đơn đặt vé"

2. Demo FILTER:
   - Filter theo status "Chỉ xem confirmed"
   - Search theo mã booking hoặc tên khách

3. Demo DETAIL:
   - Click vào 1 booking
   - Chỉ thông tin khách, suất chiếu, danh sách ghế
   - "Mỗi ghế có QR code riêng để check-in"

4. Demo CANCEL (nếu có booking pending):
   - "Admin có thể hủy booking chưa check-in"
   - Cancel → Ghế được giải phóng

5. Giải thích LIFECYCLE:
   - "Booking đi qua các trạng thái từ pending đến completed"
   - "Pending tự động expire sau 15 phút nếu không thanh toán"
```

---

## 📊 BOOKING STATISTICS

### Query thống kê

```php
// Thống kê theo status
$stats = Booking::selectRaw('status, COUNT(*) as count')
    ->groupBy('status')
    ->pluck('count', 'status');

// Doanh thu theo ngày
$dailyRevenue = Booking::where('status', 'confirmed')
    ->whereDate('created_at', today())
    ->sum('total_price');

// Top users
$topUsers = User::withCount(['bookings' => fn($q) =>
        $q->where('status', 'confirmed')
    ])
    ->orderByDesc('bookings_count')
    ->limit(10)
    ->get();
```

---

## 📝 GHI NHỚ NHANH

```
✓ Booking lifecycle: pending → confirmed → checked_in → completed
✓ Pending expire sau 15 phút
✓ Mỗi ghế có QR code riêng (SHA-256 hash)
✓ Seat lock bằng status, không cần table riêng
✓ Cancel booking → ghế tự động free (nhờ query logic)
✓ Transaction khi create booking để tránh race condition
✓ Unique constraint: (showtime_id, seat_id) trong booking_seats
```

