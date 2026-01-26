# ADMIN PANEL CHEATSHEETS
## Quick Reference cho từng module

---

## Mục đích

Mỗi cheatsheet cung cấp **tất cả thông tin cần thiết** về một module:
- Database schema
- Code quan trọng
- UI components
- Câu hỏi thường gặp
- Demo tips

**In ra hoặc mở sẵn khi bảo vệ!**

---

## Danh sách Cheatsheets

| # | Module | File | Độ quan trọng |
|---|--------|------|---------------|
| 1 | Dashboard | [CHEATSHEET_01_DASHBOARD.md](./CHEATSHEET_01_DASHBOARD.md) | ⭐⭐⭐ |
| 2 | Movies | [CHEATSHEET_02_MOVIES.md](./CHEATSHEET_02_MOVIES.md) | ⭐⭐⭐⭐ |
| 3 | Rooms | [CHEATSHEET_03_ROOMS.md](./CHEATSHEET_03_ROOMS.md) | ⭐⭐⭐⭐⭐ |
| 4 | Showtimes | [CHEATSHEET_04_SHOWTIMES.md](./CHEATSHEET_04_SHOWTIMES.md) | ⭐⭐⭐⭐⭐ |
| 5 | Bookings | [CHEATSHEET_05_BOOKINGS.md](./CHEATSHEET_05_BOOKINGS.md) | ⭐⭐⭐⭐⭐ |
| 6 | Users/Reviews/QR | [CHEATSHEET_06_USERS_REVIEWS_QR.md](./CHEATSHEET_06_USERS_REVIEWS_QR.md) | ⭐⭐⭐⭐ |

---

## Điểm nhấn từng module

### 1. Dashboard
- KPI Cards: Revenue, Bookings, Users, Movies
- Revenue Chart 7 ngày
- Query: SUM, COUNT, GROUP BY

### 2. Movies (CRUD + Many-to-Many)
- **Many-to-Many** với Genres qua pivot table
- **sync()** để update genres
- Validate: title, duration, genres required

### 3. Rooms (Thuật toán ghế) ⭐
- **Thuật toán tạo sơ đồ ghế tự động**
- **DB Transaction** đảm bảo atomic
- 3 loại ghế: Standard, VIP (+50%), Couple (+100%)
- chr(64 + n) convert số → chữ

### 4. Showtimes (Pricing + Conflict) ⭐
- **Dynamic Pricing** theo screen/seat/time/day
- **Conflict Detection** thuật toán overlap
- end_time = start_time + duration + 15 phút buffer

### 5. Bookings (Lifecycle + QR) ⭐
- **Booking Lifecycle**: pending → confirmed → checked_in → completed
- **Seat Locking** bằng status, auto expire 15 phút
- **QR Generation**: SHA-256 hash

### 6. Users/Reviews/QR
- Role protection: không tự demote
- Review moderation: approve/reject
- QR Check-in: one-time use

---

## Thuật toán quan trọng (Hay bị hỏi)

### 1. Tạo sơ đồ ghế
```
for row = 1 to rows:
    rowLabel = chr(64 + row)  // A, B, C...
    for seat = 1 to seats_per_row:
        create seat with type based on row
```

### 2. Conflict Detection
```
Overlap khi: start1 < end2 AND end1 > start2
```

### 3. Dynamic Pricing
```
Giá = Base × (1 + screen%) × (1 + seat%) × (1 + time%) × (1 + day%)
```

### 4. QR Code
```
qr = SHA-256(booking_id + seat_id + showtime_id + app.key)
```

### 5. Seat Availability
```
Available = NOT IN (SELECT seat_id WHERE booking.status IN ['pending', 'confirmed'])
```

---

## Quick Q&A

| Câu hỏi | Trả lời ngắn |
|---------|--------------|
| Tại sao MVC? | Tách biệt trách nhiệm, dễ maintain |
| Tại sao Transaction? | Đảm bảo tất cả hoặc không gì cả |
| Tại sao Eager Loading? | Tránh N+1 query problem |
| Tại sao SHA-256? | Secure, unique, không thể đoán ngược |
| Tại sao sync()? | Xóa cũ, thêm mới, khớp với selection |
| Pending expire sao? | Scheduled task check mỗi phút |

---

## Sử dụng khi bảo vệ

1. **Mở sẵn** các file cheatsheet trên máy
2. **In ra** 1-2 trang quan trọng nhất (Rooms, Showtimes, Bookings)
3. **Đọc trước** phần "Câu hỏi thường gặp" của mỗi module
4. **Luyện** giải thích thuật toán bằng lời

---

**Mỗi cheatsheet là một "bí kíp" cho module đó. Nắm vững = Tự tin bảo vệ!**

