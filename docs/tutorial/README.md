# 📚 CINEBOOK TUTORIAL - HƯỚNG DẪN XÂY DỰNG TỪ ĐẦU

Chào mừng bạn đến với **Cinebook Tutorial Series** - hướng dẫn chi tiết, từng bước để xây dựng một hệ thống đặt vé xem phim hoàn chỉnh bằng Laravel!

---

## 🎯 GIỚI THIỆU

Series hướng dẫn này được thiết kế để giúp bạn:
- ✅ Tự tay xây dựng một ứng dụng web thực tế từ con số 0
- ✅ Hiểu sâu về Laravel framework và các best practices
- ✅ Nắm vững full-stack development (Backend + Frontend)
- ✅ Có một dự án hoàn chỉnh để đưa vào portfolio

**Đối tượng**: Người mới học Laravel hoặc muốn nâng cao kỹ năng thông qua dự án thực tế.

**Yêu cầu**:
- Kiến thức cơ bản về PHP, HTML, CSS, JavaScript
- Đã cài đặt PHP 8.2+, Composer, MySQL, Node.js
- Kiên trì và sẵn sàng học hỏi!

---

## 📋 DANH SÁCH BÀI HỌC

### 🏗️ **PHẦN 1: CHUẨN BỊ & THIẾT LẬP** (Ngày 1-2)

| # | Bài học | Nội dung | Thời gian |
|---|---------|----------|-----------|
| 00 | [Start Here](00_start_here.md) | Tổng quan dự án, yêu cầu kỹ thuật, lộ trình học | 15 phút |
| 01 | [Laravel Setup](01_laravel_setup.md) | Cài đặt Laravel, cấu hình database, setup Vite | 45 phút |
| 02 | [Database Design](02_database_design.md) | Thiết kế schema, tạo 13 tables, import sample data | 90 phút |

**Kết quả**: Bạn có Laravel project với database hoàn chỉnh

---

### 🎨 **PHẦN 2: XÂY DỰNG NỀN TẢNG** (Ngày 3-4)

| # | Bài học | Nội dung | Thời gian |
|---|---------|----------|-----------|
| 03 | [Models Step by Step](03_models_step_by_step.md) | Tạo 13 Models, định nghĩa relationships, helper methods | 75 phút |
| 04 | [Authentication](04_authentication.md) | Đăng nhập, đăng ký, logout, session management | 60 phút |
| 05 | [Frontend Basics](05_frontend_basics.md) | Layout master, header, footer, navigation, CSS structure | 60 phút |

**Kết quả**: Hệ thống auth hoạt động + giao diện cơ bản

---

### 🎬 **PHẦN 3: TÍNH NĂNG NGƯỜI DÙNG** (Ngày 5-7)

| # | Bài học | Nội dung | Thời gian |
|---|---------|----------|-----------|
| 06 | [Movie Features](06_movie_features.md) | Trang chủ, danh sách phim, chi tiết, tìm kiếm | 90 phút |
| 07 | [Booking System](07_booking_system.md) | Chọn suất chiếu, xác nhận đặt vé, countdown timer | 90 phút |
| 08 | [Seat Selection](08_seat_selection.md) | Interactive seat map, couple seat logic, JavaScript | 120 phút |
| 09 | [Payment & QR](09_payment_qr.md) | Mock payment, QR code generation, booking success | 90 phút |
| 10 | [Review System](10_review_system.md) | Đánh giá phim, rating, CRUD reviews | 60 phút |

**Kết quả**: Toàn bộ tính năng cho người dùng cuối

---

### 👨‍💼 **PHẦN 4: ADMIN & HOÀN THIỆN** (Ngày 8-10)

| # | Bài học | Nội dung | Thời gian |
|---|---------|----------|-----------|
| 11 | [Admin Panel](11_admin_panel.md) | Dashboard, quản lý movies/rooms/showtimes/bookings/users/reviews, QR check-in | 120 phút |
| 12 | [Final Touches](12_final_touches.md) | Testing, bug fixes, optimization, deployment, bảo mật | 90 phút |

**Kết quả**: Dự án hoàn chỉnh, sẵn sàng deploy!

---

## ⏱️ TỔNG THỜI GIAN

- **Học full-time** (8h/ngày): **10-12 ngày**
- **Học part-time** (2-3h/ngày): **3-4 tuần**
- **Học cuối tuần** (4-6h/ngày): **4-5 tuần**

---

## 🎓 CÁCH SỬ DỤNG SERIES NÀY

### ✅ Quy tắc vàng

1. **Đọc tuần tự**: Bắt đầu từ bài 00, KHÔNG skip
2. **Gõ code bằng tay**: Đừng copy-paste toàn bộ
3. **Test sau mỗi bước**: Verify ngay sau khi code
4. **Làm bài tập**: Mỗi bài có phần "Thực hành" - hãy làm!
5. **Commit thường xuyên**: Mỗi tính năng xong → git commit

### 📖 Cấu trúc mỗi bài học

```
🎯 Mục tiêu bài học
📚 Kiến thức cần biết
🛠️ Các bước thực hiện (với code đầy đủ)
💡 Giải thích chi tiết
✅ Test & Verify
🎯 Thực hành
🐛 Troubleshooting
📝 Tóm tắt
🚀 Bước tiếp theo
```

### 🔑 Ký hiệu trong hướng dẫn

- 📝 **Ghi chú**: Thông tin bổ sung
- ⚠️ **Cảnh báo**: Lỗi thường gặp, cần chú ý
- 💡 **Mẹo**: Tips & tricks hữu ích
- ✅ **Checkpoint**: Điểm kiểm tra tiến độ
- 🔍 **Giải thích sâu**: Kiến thức nâng cao
- 🎯 **Mục tiêu**: Kết quả cần đạt được

---

## 🗂️ CẤU TRÚC DỰ ÁN CUỐI CÙNG

Sau khi hoàn thành series, bạn sẽ có:

```
cinebook/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/              # 8 admin controllers
│   │   │   ├── AdminDashboardController.php
│   │   │   ├── AdminMovieController.php
│   │   │   ├── AdminBookingController.php
│   │   │   ├── AdminShowtimeController.php
│   │   │   ├── AdminRoomController.php
│   │   │   ├── AdminUserController.php
│   │   │   ├── AdminReviewController.php
│   │   │   └── QRCheckInController.php
│   │   ├── User/
│   │   │   └── ProfileController.php
│   │   ├── BookingController.php
│   │   ├── MovieController.php
│   │   ├── LoginController.php
│   │   ├── PaymentController.php
│   │   ├── ReviewController.php
│   │   ├── ShowtimeController.php
│   │   └── SearchController.php
│   └── Models/                 # 13 models
│       ├── User.php
│       ├── Movie.php
│       ├── Booking.php
│       ├── BookingSeat.php
│       ├── Showtime.php
│       ├── Room.php
│       ├── Seat.php
│       ├── Review.php
│       └── ...
├── resources/
│   ├── views/                  # 44+ blade templates
│   │   ├── layouts/
│   │   │   └── main.blade.php
│   │   ├── partials/
│   │   │   ├── header.blade.php
│   │   │   └── footer.blade.php
│   │   ├── homepage.blade.php
│   │   ├── movie/
│   │   ├── booking/
│   │   ├── admin/
│   │   ├── profile/
│   │   └── ...
│   ├── css/                    # 16 CSS files
│   │   ├── app.css
│   │   ├── root.css
│   │   ├── base.css
│   │   └── ...
│   └── js/
│       └── app.js
├── public/
│   └── js/                     # 6 client-side JS files
│       ├── seat_map.js
│       ├── booking-countdown.js
│       ├── qr_checkin.js
│       └── ...
├── routes/
│   └── web.php                 # 41+ routes
├── database/
│   ├── migrations/
│   └── seeders/
├── mySQL/
│   ├── schema.sql              # Database schema
│   └── data.sql                # Sample data
└── ...
```

---

## 🎁 TÍNH NĂNG SẼ XÂY DỰNG

### Cho Khách Hàng:
- ✅ Xem danh sách phim (đang chiếu, sắp chiếu)
- ✅ Tìm kiếm phim theo tên
- ✅ Chi tiết phim với trailer & reviews
- ✅ Chọn suất chiếu
- ✅ Interactive seat map (Standard, VIP, Couple seats)
- ✅ Đặt vé với countdown timer 10 phút
- ✅ Mock payment (VNPay, MoMo)
- ✅ Nhận QR code để check-in
- ✅ Quản lý profile & lịch sử đặt vé
- ✅ Viết review & đánh giá phim

### Cho Admin:
- ✅ Dashboard với thống kê
- ✅ Quản lý phim (CRUD)
- ✅ Quản lý phòng chiếu & ghế
- ✅ Quản lý suất chiếu
- ✅ Xem & quản lý đặt vé
- ✅ Quản lý người dùng
- ✅ Quản lý reviews
- ✅ QR check-in system

---

## 💻 TECH STACK

| Technology | Version | Mục đích |
|------------|---------|----------|
| **Laravel** | 12.x | Backend framework |
| **PHP** | 8.2+ | Server-side language |
| **MySQL** | 8.0+ | Database |
| **Blade** | - | Templating engine |
| **Vite** | 5.x | Frontend build tool |
| **JavaScript** | ES6+ | Client-side interactivity |
| **CSS3** | - | Styling (custom, no framework) |
| **QR Code** | simplesoftwareio/simple-qrcode | QR generation |

---

## 📚 TÀI LIỆU THAM KHẢO

- [Laravel Documentation](https://laravel.com/docs) - Official docs
- [PHP Manual](https://www.php.net/manual/) - PHP reference
- [MySQL Docs](https://dev.mysql.com/doc/) - Database docs
- [MDN Web Docs](https://developer.mozilla.org) - HTML/CSS/JS
- [Eloquent ORM](https://laravel.com/docs/eloquent) - Database ORM

---

## 🐛 KHI GẶP VẤN ĐỀ

### Trước khi hỏi, hãy:
1. ✅ Đọc lại hướng dẫn cẩn thận
2. ✅ Kiểm tra error message trong terminal/browser
3. ✅ Verify đã làm đúng tất cả các bước
4. ✅ Check file paths, typos, syntax errors
5. ✅ Google error message
6. ✅ Tìm trên Stack Overflow

### Common Issues:
- **"Class not found"**: Chạy `composer dump-autoload`
- **"SQLSTATE[HY000]"**: Kiểm tra `.env` database config
- **"Route not found"**: Chạy `php artisan route:list` để verify
- **CSS/JS không load**: Check Vite đang chạy (`npm run dev`)
- **500 error**: Check `storage/logs/laravel.log`

---

## 🎊 LỜI KHUYÊN

### Để học hiệu quả:

1. **Kiên trì**: Đừng bỏ cuộc khi gặp lỗi - debug là cách học tốt nhất!
2. **Thực hành**: Code nhiều hơn đọc, làm nhiều hơn xem
3. **Ghi chú**: Viết lại những gì bạn học, giải thích bằng lời của mình
4. **Tốc độ của bạn**: Mỗi người học với tốc độ khác nhau - đừng vội!
5. **Hỏi & Chia sẻ**: Tham gia community, hỏi khi cần, giúp người khác khi có thể

### Nhớ rằng:
> "The expert in anything was once a beginner."
> — Helen Hayes

> "Code is like humor. When you have to explain it, it's bad."
> — Cory House

---

## 📊 TIẾN ĐỘ CỦA BẠN

Đánh dấu ✅ khi hoàn thành mỗi bài:

### Phần 1: Chuẩn bị (Ngày 1-2)
- [ ] 00. Start Here
- [ ] 01. Laravel Setup
- [ ] 02. Database Design

### Phần 2: Nền tảng (Ngày 3-4)
- [ ] 03. Models
- [ ] 04. Authentication
- [ ] 05. Frontend Basics

### Phần 3: User Features (Ngày 5-7)
- [ ] 06. Movie Features
- [ ] 07. Booking System
- [ ] 08. Seat Selection
- [ ] 09. Payment & QR
- [ ] 10. Review System

### Phần 4: Admin & Hoàn thiện (Ngày 8-10)
- [ ] 11. Admin Panel
- [ ] 12. Final Touches

---

## 🚀 BẮT ĐẦU NGAY!

Sẵn sàng chưa? Hãy bắt đầu với:

👉 **[00. Start Here - Tổng quan dự án](00_start_here.md)**

---

## 📞 HỖ TRỢ

Nếu bạn gặp vấn đề hoặc có câu hỏi:
1. Đọc lại hướng dẫn cẩn thận
2. Check phần Troubleshooting trong mỗi bài
3. Tìm kiếm trên Google với error message
4. Hỏi trên Laravel Vietnam Community

---

## 📄 LICENSE

Tutorial này được tạo ra cho mục đích giáo dục. Bạn có thể tự do sử dụng, chỉnh sửa và chia sẻ.

---

**Good luck và chúc bạn học tốt!** 🎉

*Happy Coding!* 💻✨
