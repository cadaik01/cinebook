# CHEAT SHEET BẢO VỆ ĐỒ ÁN - CINEBOOK
## In ra 1-2 trang để ôn nhanh

---

## 🎯 CON SỐ QUAN TRỌNG

| Thành phần | Số lượng |
|------------|----------|
| Admin Controllers | 9 |
| View files | 18+ |
| Loại ghế | 3 (Standard, VIP +50%, Couple +100%) |
| Trạng thái booking | 6 (pending, confirmed, checked_in, completed, cancelled, expired) |
| Lớp bảo mật | 5 (Auth, Middleware, CSRF, ORM, Blade) |

---

## 🔧 TECH STACK

```
Backend:  PHP 8.x + Laravel 10.x
Database: MySQL 8.x
Frontend: Bootstrap 5 + jQuery
Pattern:  MVC + Repository (partial)
Security: bcrypt, CSRF token, Eloquent ORM
```

---

## 📊 CÔNG THỨC GIÁ VÉ

```
Giá cuối = Giá gốc + Phụ thu ghế + Phụ thu giờ

Phụ thu ghế:
- Standard: +0%
- VIP: +50%
- Couple: +100%

Phụ thu giờ:
- Giờ vàng (18-21h): +20%
- Cuối tuần: +15%
```

---

## 🔐 5 LỚP BẢO MẬT

1. **Authentication** - Laravel Auth + bcrypt password
2. **Authorization** - Middleware role:admin
3. **CSRF Protection** - Token trong mọi form
4. **SQL Injection** - Eloquent ORM prepared statements
5. **XSS** - Blade {{ }} auto-escaping

---

## 🔄 BOOKING FLOW

```
Chọn phim → Chọn suất → Chọn ghế → Xác nhận giá
     ↓
Thanh toán → Tạo booking → Generate QR → Gửi email
     ↓
Đến rạp → Scan QR → Check-in → Xem phim
```

---

## 💡 CÂU TRẢ LỜI MẪU

### "Tại sao chọn Laravel?"
> "Ecosystem hoàn chỉnh, convention over configuration, cộng đồng lớn, phù hợp timeline dự án."

### "Nếu 1 triệu user?"
> "Đã chuẩn bị: Index database, Eager Loading, stateless design. Khi cần: Redis cache, Read replica, microservices."

### "Khó khăn nhất?"
> "Race condition khi đặt ghế. Giải quyết bằng Database Transaction + Unique constraint."

### "Không biết trả lời?"
> "Em chưa tìm hiểu sâu về vấn đề này, nhưng em sẽ research về [keyword] và tham khảo documentation."

---

## ⏱️ DEMO SCRIPT (7 phút)

| Thời gian | Nội dung |
|-----------|----------|
| 0:00-0:30 | Giới thiệu tổng quan |
| 0:30-1:30 | Dashboard + KPIs |
| 1:30-2:30 | Quản lý phim (CRUD) |
| 2:30-3:30 | Quản lý phòng + sơ đồ ghế |
| 3:30-4:30 | User: Chọn phim + suất chiếu |
| 4:30-5:30 | User: Chọn ghế (ĐIỂM NHẤN) |
| 5:30-6:30 | QR Check-in |
| 6:30-7:00 | Kết thúc, sẵn sàng Q&A |

---

## 🚨 KHI GẶP SỰ CỐ

| Sự cố | Xử lý |
|-------|-------|
| Server không chạy | Chiếu video backup |
| Tính năng lỗi | Giải thích qua code |
| Database trống | Import SQL backup |
| Không biết trả lời | Thành thật + nêu hướng tìm hiểu |

---

## 📝 THUẬT NGỮ PHẢI BIẾT

| Thuật ngữ | Nghĩa đơn giản |
|-----------|----------------|
| MVC | Tách Model-View-Controller |
| Middleware | Bảo vệ kiểm tra trước khi vào |
| Eloquent | Thao tác DB bằng PHP |
| Migration | Version control cho database |
| Transaction | Tất cả hoặc không gì cả |
| N+1 Problem | Query quá nhiều trong loop |
| Eager Loading | Load data liên quan cùng lúc |

---

## ✅ CHECKLIST TRƯỚC KHI VÀO

```
□ XAMPP đang chạy
□ Browser mở sẵn localhost
□ Data demo đã chuẩn bị
□ Backup (video, SQL, screenshots)
□ Uống nước, hít thở sâu
□ Tự tin và bình tĩnh!
```

---

## 🎤 PATTERN TRẢ LỜI

```
1. NGHE hết câu hỏi
2. DỪNG 2-3 giây suy nghĩ
3. TRẢ LỜI có cấu trúc: "Em có 3 điểm..."
4. KẾT THÚC: "Không biết em trả lời đã đủ chưa ạ?"
```

---

## 💪 MINDSET

```
✓ Tôi là CHUYÊN GIA về project này
✓ Hội đồng MUỐN tôi đỗ
✓ Sai không sao, quan trọng là CÁCH XỬ LÝ
✓ Tự tin nhưng không kiêu ngạo
```

---

**GHI NHỚ:**
> "Không ai hiểu project này hơn bạn. Bạn đã code từng dòng, fix từng bug. Hãy tự tin chia sẻ!"

