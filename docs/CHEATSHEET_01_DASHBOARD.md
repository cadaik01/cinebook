# CHEATSHEET: DASHBOARD
## Trung tâm điều khiển của Admin Panel

---

## 🎯 MỤC ĐÍCH

Dashboard là trang đầu tiên admin nhìn thấy sau khi đăng nhập. Mục tiêu:
- Cung cấp **cái nhìn tổng quan** về tình hình kinh doanh
- Hiển thị **KPIs quan trọng** không cần click thêm
- Cho phép **truy cập nhanh** đến các chức năng khác

---

## 📁 FILES LIÊN QUAN

```
Controller: app/Http/Controllers/Admin/AdminDashboardController.php
View:       resources/views/admin/dashboard.blade.php
Route:      GET /admin/dashboard
```

---

## 📊 CÁC THÀNH PHẦN CHÍNH

### 1. KPI Cards (Thẻ số liệu)

| Card | Ý nghĩa | Query |
|------|---------|-------|
| Total Revenue | Tổng doanh thu từ booking confirmed | `SUM(total_price) WHERE status='confirmed'` |
| Total Bookings | Số lượng đơn đặt vé | `COUNT(*) FROM bookings` |
| Total Users | Số người dùng đăng ký | `COUNT(*) FROM users` |
| Total Movies | Số phim trong hệ thống | `COUNT(*) FROM movies` |

### 2. Revenue Chart (Biểu đồ doanh thu)

```
Loại: Line Chart hoặc Bar Chart
Data: Doanh thu 7 ngày gần nhất
X-axis: Ngày
Y-axis: Doanh thu (VND)
```

### 3. Recent Bookings (Đơn đặt gần đây)

```
Hiển thị: 5-10 booking mới nhất
Thông tin: User, Phim, Suất chiếu, Giá, Trạng thái
Sắp xếp: created_at DESC
```

### 4. Popular Movies (Phim được đặt nhiều)

```
Hiển thị: Top 5 phim có nhiều booking nhất
Thông tin: Tên phim, Số vé bán, Doanh thu
```

---

## 💻 CODE QUAN TRỌNG

### Query tính tổng doanh thu

```php
$totalRevenue = Booking::where('status', 'confirmed')
    ->sum('total_price');
```

### Query doanh thu theo ngày (7 ngày)

```php
$revenueByDay = Booking::where('status', 'confirmed')
    ->where('created_at', '>=', now()->subDays(7))
    ->selectRaw('DATE(created_at) as date, SUM(total_price) as revenue')
    ->groupBy('date')
    ->orderBy('date')
    ->get();
```

### Query phim phổ biến

```php
$popularMovies = Movie::withCount(['showtimes as bookings_count' => function ($query) {
        $query->join('bookings', 'showtimes.id', '=', 'bookings.showtime_id')
              ->where('bookings.status', 'confirmed');
    }])
    ->orderByDesc('bookings_count')
    ->limit(5)
    ->get();
```

---

## 🔄 FLOW XỬ LÝ

```
User truy cập /admin/dashboard
         ↓
Middleware kiểm tra: auth + role:admin
         ↓
AdminDashboardController@index
         ↓
Query các số liệu từ database
         ↓
Trả về view với data
         ↓
View render cards + charts
```

---

## 🎨 UI/UX NOTES

### Layout

```
┌─────────────────────────────────────────────────────────────┐
│  DASHBOARD                                                  │
├─────────────┬─────────────┬─────────────┬─────────────────┤
│  💰 Revenue │  📋 Bookings│  👥 Users   │  🎬 Movies      │
│  50,000,000 │     1,234   │     567     │      89         │
├─────────────┴─────────────┴─────────────┴─────────────────┤
│                                                             │
│  [=========== REVENUE CHART (7 days) ============]         │
│                                                             │
├────────────────────────────┬────────────────────────────────┤
│  RECENT BOOKINGS           │  POPULAR MOVIES                │
│  • Booking #123 - 150k     │  1. Aquaman 2 - 500 vé        │
│  • Booking #122 - 200k     │  2. Wonka - 350 vé            │
│  • Booking #121 - 180k     │  3. Migration - 280 vé        │
└────────────────────────────┴────────────────────────────────┘
```

### Màu sắc

| Thành phần | Màu | Ý nghĩa |
|------------|-----|---------|
| Revenue Card | Green | Tiền = xanh lá |
| Bookings Card | Blue | Primary action |
| Users Card | Purple | People |
| Movies Card | Orange | Entertainment |

---

## ❓ CÂU HỎI THƯỜNG GẶP

### Q: "Tại sao cần Dashboard?"

```
"Dashboard giúp admin có cái nhìn tổng quan ngay khi đăng nhập,
không cần click vào từng mục để biết tình hình.
Đây là best practice trong mọi hệ thống quản trị."
```

### Q: "Doanh thu tính như thế nào?"

```
"Doanh thu = SUM(total_price) của các booking có status = 'confirmed'.
Booking pending hoặc cancelled không tính vào doanh thu."
```

### Q: "Tại sao chỉ hiển thị 7 ngày?"

```
"7 ngày là khoảng thời gian đủ để thấy xu hướng gần đây
mà không quá dài gây khó đọc. Admin có thể vào báo cáo chi tiết
nếu cần xem khoảng thời gian khác."
```

### Q: "Có real-time không?"

```
"Hiện tại data refresh khi reload trang.
Nếu cần real-time, có thể implement bằng:
- AJAX polling mỗi 30 giây
- WebSocket với Laravel Echo
- Server-Sent Events"
```

---

## 🎯 DEMO TIPS

### Chuẩn bị data

```
✅ Có ít nhất 20-30 booking confirmed
✅ Booking trải đều 7 ngày qua (để chart đẹp)
✅ Có nhiều phim khác nhau (để top movies có ý nghĩa)
✅ Revenue > 0 (không để dashboard trống)
```

### Khi demo

```
1. "Đây là Dashboard - trang tổng quan của admin"
2. Chỉ vào từng KPI card, giải thích ngắn
3. "Biểu đồ này cho thấy xu hướng doanh thu 7 ngày qua"
4. "Admin có thể thấy ngay phim nào đang hot"
5. "Từ đây có thể click vào bất kỳ mục nào để xem chi tiết"
```

### Câu hay để nói

```
"Dashboard được thiết kế theo nguyên tắc 'glanceable' -
admin chỉ cần liếc qua là nắm được tình hình,
không cần đọc hay click gì thêm."
```

---

## 🔧 CẢI TIẾN CÓ THỂ LÀM

| Tính năng | Độ khó | Giá trị |
|-----------|--------|---------|
| So sánh với tuần trước | Easy | High |
| Filter theo date range | Medium | High |
| Export PDF | Medium | Medium |
| Real-time updates | Hard | Medium |
| Dự đoán doanh thu | Hard | High |

---

## 📝 GHI NHỚ NHANH

```
✓ Dashboard = Tổng quan, không chi tiết
✓ KPIs: Revenue, Bookings, Users, Movies
✓ Chart: 7 ngày gần nhất
✓ Query: SUM, COUNT, GROUP BY
✓ Refresh: Khi reload trang
```

