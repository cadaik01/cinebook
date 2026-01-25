# CHEATSHEET: QUẢN LÝ SUẤT CHIẾU (SHOWTIMES)
## Scheduling + Dynamic Pricing + Conflict Detection

---

## 🎯 MỤC ĐÍCH

Suất chiếu là nơi kết nối **PHIM** với **PHÒNG** tại một **THỜI ĐIỂM**:
- Lên lịch chiếu phim
- Tính giá vé tự động (Dynamic Pricing)
- Phát hiện xung đột thời gian
- Quản lý trạng thái suất chiếu

---

## 📁 FILES LIÊN QUAN

```
Controller: app/Http/Controllers/Admin/AdminShowtimeController.php
Model:      app/Models/Showtime.php
Views:      resources/views/admin/showtimes/
            ├── index.blade.php
            ├── create.blade.php
            ├── edit.blade.php
            └── show.blade.php
```

---

## 🗄️ DATABASE SCHEMA

### Table: showtimes

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| movie_id | BIGINT | FK to movies |
| room_id | BIGINT | FK to rooms |
| show_date | DATE | Ngày chiếu |
| start_time | TIME | Giờ bắt đầu |
| end_time | TIME | Giờ kết thúc (tính tự động) |
| base_price | DECIMAL | Giá vé gốc |
| status | ENUM | scheduled, ongoing, completed, cancelled |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

## 🔗 QUAN HỆ (RELATIONSHIPS)

```php
// Showtime.php
class Showtime extends Model
{
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class);
    }

    // Accessor: Lấy danh sách ghế đã đặt
    public function getBookedSeatsAttribute()
    {
        return $this->bookingSeats()
            ->whereHas('booking', fn($q) => $q->whereIn('status', ['confirmed', 'pending']))
            ->pluck('seat_id')
            ->toArray();
    }
}
```

---

## 💰 CÔNG THỨC TÍNH GIÁ (DYNAMIC PRICING)

### Công thức tổng quát

```
Giá cuối = Giá gốc × (1 + Phụ thu màn hình) × (1 + Phụ thu ghế) × (1 + Phụ thu giờ)
```

### Phụ thu theo loại màn hình

| Screen Type | Surcharge | Ví dụ (Base 100k) |
|-------------|-----------|-------------------|
| 2D | 0% | 100,000 |
| 3D | +20% | 120,000 |
| IMAX | +50% | 150,000 |
| 4DX | +80% | 180,000 |

### Phụ thu theo loại ghế

| Seat Type | Surcharge | Ví dụ (Base 100k) |
|-----------|-----------|-------------------|
| Standard | 0% | 100,000 |
| VIP | +50% | 150,000 |
| Couple | +100% | 200,000 (cho cặp) |

### Phụ thu theo khung giờ

| Time Slot | Condition | Surcharge |
|-----------|-----------|-----------|
| Morning | 09:00 - 12:00 | -10% (giảm) |
| Afternoon | 12:00 - 17:00 | 0% |
| Evening (Prime) | 17:00 - 21:00 | +20% |
| Late Night | 21:00 - 24:00 | 0% |

### Phụ thu theo ngày

| Day | Surcharge |
|-----|-----------|
| Monday - Thursday | 0% |
| Friday | +10% |
| Saturday - Sunday | +20% |
| Holidays | +30% |

### Ví dụ tính toán

```
Input:
- Base price: 100,000 VND
- Screen: 3D (+20%)
- Seat: VIP (+50%)
- Time: 19:00 Saturday (+20% time + 20% weekend)

Calculation:
100,000 × 1.2 (3D) × 1.5 (VIP) × 1.2 (evening) × 1.2 (weekend)
= 100,000 × 1.2 × 1.5 × 1.44
= 259,200 VND

Hoặc đơn giản hơn:
= Base × (1 + 0.2 + 0.5 + 0.2 + 0.2)
= 100,000 × 2.1
= 210,000 VND
```

---

## 💻 CODE QUAN TRỌNG

### Tạo suất chiếu với conflict check

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'movie_id' => 'required|exists:movies,id',
        'room_id' => 'required|exists:rooms,id',
        'show_date' => 'required|date|after_or_equal:today',
        'start_time' => 'required|date_format:H:i',
        'base_price' => 'required|numeric|min:0',
    ]);

    // Lấy thông tin phim để tính end_time
    $movie = Movie::find($validated['movie_id']);
    $startTime = Carbon::parse($validated['start_time']);
    $endTime = $startTime->copy()->addMinutes($movie->duration + 15); // +15 phút dọn dẹp

    // CHECK CONFLICT
    $conflict = Showtime::where('room_id', $validated['room_id'])
        ->where('show_date', $validated['show_date'])
        ->where('status', '!=', 'cancelled')
        ->where(function ($query) use ($startTime, $endTime) {
            // Overlap check: NOT (end1 <= start2 OR start1 >= end2)
            $query->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime->format('H:i'))
                  ->where('end_time', '>', $startTime->format('H:i'));
            });
        })
        ->exists();

    if ($conflict) {
        return back()->withErrors([
            'start_time' => 'Phòng này đã có suất chiếu trong khung giờ này!'
        ])->withInput();
    }

    // Tạo showtime
    Showtime::create([
        'movie_id' => $validated['movie_id'],
        'room_id' => $validated['room_id'],
        'show_date' => $validated['show_date'],
        'start_time' => $startTime->format('H:i'),
        'end_time' => $endTime->format('H:i'),
        'base_price' => $validated['base_price'],
        'status' => 'scheduled',
    ]);

    return redirect()->route('admin.showtimes.index')
        ->with('success', 'Suất chiếu đã được tạo!');
}
```

### Thuật toán Conflict Detection

```
Hai khoảng thời gian KHÔNG overlap khi:
   end1 <= start2 OR start1 >= end2

Hai khoảng thời gian CÓ overlap khi:
   NOT (end1 <= start2 OR start1 >= end2)
   = start1 < end2 AND end1 > start2

Ví dụ:
Suất 1: 14:00 - 16:00
Suất 2: 15:00 - 17:00

start1 (14:00) < end2 (17:00) ✓
end1 (16:00) > start2 (15:00) ✓
→ CÓ OVERLAP!

Suất 1: 14:00 - 16:00
Suất 2: 16:30 - 18:30

start1 (14:00) < end2 (18:30) ✓
end1 (16:00) > start2 (16:30) ✗
→ KHÔNG OVERLAP ✓
```

### Tính giá vé động

```php
// Trong Model Showtime hoặc Service
public function calculatePrice($seatType)
{
    $price = $this->base_price;

    // Screen type surcharge
    $screenSurcharges = [
        '2D' => 0,
        '3D' => 0.2,
        'IMAX' => 0.5,
        '4DX' => 0.8,
    ];
    $price *= (1 + ($screenSurcharges[$this->room->screen_type] ?? 0));

    // Seat type surcharge
    $seatSurcharges = [
        'standard' => 0,
        'vip' => 0.5,
        'couple' => 1.0,
    ];
    $price *= (1 + ($seatSurcharges[$seatType] ?? 0));

    // Time surcharge
    $hour = (int) Carbon::parse($this->start_time)->format('H');
    if ($hour >= 17 && $hour < 21) {
        $price *= 1.2; // Prime time
    } elseif ($hour >= 9 && $hour < 12) {
        $price *= 0.9; // Morning discount
    }

    // Weekend surcharge
    $dayOfWeek = Carbon::parse($this->show_date)->dayOfWeek;
    if (in_array($dayOfWeek, [0, 6])) { // Sunday = 0, Saturday = 6
        $price *= 1.2;
    }

    return round($price, -3); // Làm tròn nghìn
}
```

---

## 🕐 END TIME CALCULATION

### Tại sao cần tính end_time?

```
1. Để check conflict chính xác
2. User biết phim kết thúc lúc nào
3. Admin biết khi nào phòng trống

Formula:
end_time = start_time + movie.duration + buffer_time

buffer_time = 15-20 phút (dọn dẹp, quảng cáo)
```

### Code

```php
$movie = Movie::find($movieId);
$startTime = Carbon::parse('14:00');

// Phim 120 phút + 15 phút buffer
$endTime = $startTime->copy()->addMinutes($movie->duration + 15);
// 14:00 + 135 phút = 16:15
```

---

## 🎨 UI COMPONENTS

### Index Page - Calendar View

```
┌─────────────────────────────────────────────────────────────┐
│  LỊCH CHIẾU                    [+ Thêm suất chiếu]         │
├─────────────────────────────────────────────────────────────┤
│  Ngày: [< 15/01/2024 >]    Phòng: [All ▼]                  │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Room 1                                                     │
│  ├─ 09:00-11:00 │ Aquaman 2      │ 100k │ [Edit] [Cancel] │
│  ├─ 14:00-16:00 │ Wonka          │ 100k │ [Edit] [Cancel] │
│  └─ 19:00-21:30 │ Aquaman 2      │ 120k │ [Edit] [Cancel] │
│                                                             │
│  Room 2                                                     │
│  ├─ 10:00-12:00 │ Migration      │ 90k  │ [Edit] [Cancel] │
│  └─ 20:00-22:00 │ Wonka          │ 110k │ [Edit] [Cancel] │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Create Form

```
┌─────────────────────────────────────────────────────────────┐
│  TẠO SUẤT CHIẾU MỚI                                        │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Phim *            [Aquaman 2 (120 phút) ▼]                │
│                                                             │
│  Phòng chiếu *     [Room 1 - 2D (120 ghế) ▼]               │
│                                                             │
│  Ngày chiếu *      [__/__/____]  (>= hôm nay)              │
│                                                             │
│  Giờ bắt đầu *     [__:__]                                 │
│                                                             │
│  Giờ kết thúc      [16:15] (tự động tính)                  │
│                                                             │
│  Giá vé gốc *      [100,000] VND                           │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ PREVIEW GIÁ VÉ:                                     │   │
│  │ • Standard: 100,000 VND                             │   │
│  │ • VIP: 150,000 VND (+50%)                          │   │
│  │ • Couple: 200,000 VND (+100%)                      │   │
│  │ * Giá trên chưa bao gồm phụ thu giờ/ngày           │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│                              [Hủy]  [Tạo suất chiếu]       │
└─────────────────────────────────────────────────────────────┘
```

---

## ❓ CÂU HỎI THƯỜNG GẶP

### Q: "Giải thích thuật toán conflict detection?"

```
"Hai suất chiếu xung đột khi thời gian của chúng overlap.
Công thức check overlap: start1 < end2 AND end1 > start2

Ví dụ:
- Suất A: 14:00-16:00
- Suất B: 15:00-17:00
- 14:00 < 17:00 (true) AND 16:00 > 15:00 (true) → CONFLICT!

- Suất A: 14:00-16:00
- Suất C: 16:30-18:30
- 14:00 < 18:30 (true) AND 16:00 > 16:30 (false) → OK!

Điều kiện check chỉ áp dụng cho cùng phòng và cùng ngày."
```

### Q: "Tại sao cần buffer time?"

```
"Buffer time = thời gian giữa 2 suất chiếu, thường 15-20 phút.
Dùng để:
1. Dọn dẹp phòng sau suất chiếu
2. Chiếu quảng cáo/trailer trước phim
3. Cho khán giả vào chỗ ngồi

Nếu không có buffer, phim kết thúc 16:00 mà suất sau bắt đầu 16:00
→ Không kịp dọn dẹp, khách cũ và mới đụng nhau."
```

### Q: "Dynamic pricing hoạt động thế nào?"

```
"Giá vé không cố định mà phụ thuộc nhiều yếu tố:

1. Screen type: IMAX, 4DX đắt hơn 2D
2. Seat type: VIP, Couple đắt hơn Standard
3. Time slot: Tối đắt hơn sáng (prime time)
4. Day: Weekend đắt hơn weekday

Điều này giúp:
- Tối đa hóa doanh thu (charge more khi demand cao)
- Điều tiết khách (giảm giá buổi sáng để thu hút)
- Phản ánh đúng giá trị (IMAX experience đáng giá hơn)"
```

### Q: "Có thể sửa suất chiếu đã có booking không?"

```
"Phụ thuộc vào policy:
- Thay đổi giờ: Nên thông báo cho khách đã đặt
- Thay đổi phòng: Phải đảm bảo ghế đã đặt vẫn tồn tại
- Hủy suất chiếu: Phải refund hoặc đổi vé cho khách

Trong hệ thống hiện tại:
- Không cho sửa nếu đã có booking confirmed
- Có thể cancel và tạo suất mới"
```

---

## 🎯 DEMO TIPS

### Chuẩn bị

```
✅ 10-15 suất chiếu cho ngày hôm nay và mai
✅ Phân bố đều các phòng
✅ Có suất sáng, chiều, tối
✅ Có ít nhất 1 suất đã full/gần full (để show seat map)
```

### Khi demo

```
1. "Đây là trang quản lý suất chiếu - lịch chiếu của rạp"

2. Demo TẠO SUẤT:
   - Chọn phim "End time tự động tính dựa trên duration"
   - Chọn phòng
   - Chọn ngày giờ
   - Nhập giá gốc "Giá này sẽ được điều chỉnh theo loại ghế"

3. Demo CONFLICT:
   - Thử tạo suất trùng giờ với suất đã có
   - "Hệ thống phát hiện và báo lỗi"
   - "Không cho tạo 2 suất cùng phòng cùng thời gian"

4. Giải thích PRICING:
   - "Giá gốc 100k, VIP sẽ là 150k, Couple là 200k"
   - "Tối thứ 7 sẽ có thêm phụ thu 20%"
```

---

## 📊 SHOWTIME STATUSES

| Status | Ý nghĩa | Cho phép |
|--------|---------|----------|
| scheduled | Đã lên lịch, chưa đến giờ | Edit, Cancel |
| ongoing | Đang chiếu | View only |
| completed | Đã kết thúc | View only |
| cancelled | Đã hủy | View only |

### Auto Status Update (Scheduled Task)

```php
// Chạy mỗi phút
Showtime::where('status', 'scheduled')
    ->where('show_date', today())
    ->where('start_time', '<=', now()->format('H:i'))
    ->update(['status' => 'ongoing']);

Showtime::where('status', 'ongoing')
    ->where('show_date', '<=', today())
    ->where('end_time', '<', now()->format('H:i'))
    ->update(['status' => 'completed']);
```

---

## 📝 GHI NHỚ NHANH

```
✓ Showtime = Movie + Room + DateTime
✓ end_time = start_time + duration + 15 phút buffer
✓ Conflict check: start1 < end2 AND end1 > start2
✓ Dynamic pricing: Screen + Seat + Time + Day surcharges
✓ Không cho edit/delete nếu có booking
✓ Status: scheduled → ongoing → completed
```

