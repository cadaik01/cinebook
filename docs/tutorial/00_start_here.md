# 🎬 CINEBOOK - HƯỚNG DẪN XÂY DỰNG DỰ ÁN TỪ ĐẦU

## Chào mừng bạn đến với Cinebook Tutorial Series!

Series hướng dẫn này sẽ giúp bạn **tự tay xây dựng** một hệ thống đặt vé xem phim hoàn chỉnh từ con số 0. Mỗi bước đều được giải thích chi tiết, mỗi dòng code đều có ý nghĩa rõ ràng.

---

## 📚 TỔNG QUAN DỰ ÁN

### Cinebook là gì?
Cinebook là một **hệ thống đặt vé xem phim trực tuyến** được xây dựng bằng Laravel. Người dùng có thể:
- Xem danh sách phim đang chiếu và sắp chiếu
- Đặt vé online với giao diện chọn ghế trực quan
- Thanh toán và nhận mã QR để check-in tại rạp
- Viết đánh giá (review) phim sau khi xem
- Quản lý hồ sơ cá nhân và lịch sử đặt vé

Quản trị viên (Admin) có thể:
- Quản lý phim, phòng chiếu, suất chiếu
- Quản lý người dùng và đặt vé
- Quét mã QR để check-in khách hàng
- Xem thống kê và báo cáo

### Tại sao nên học qua dự án này?
✅ **Thực tế**: Giải quyết vấn đề thực tế trong kinh doanh
✅ **Đầy đủ**: Bao gồm tất cả các tính năng của một ứng dụng web hiện đại
✅ **Có chiều sâu**: Authentication, Authorization, Database relationships, Transactions
✅ **Frontend + Backend**: Học cả hai mặt của web development
✅ **Best Practices**: Code được tổ chức theo chuẩn Laravel

---

## 🎯 MỤC TIÊU HỌC TẬP

Sau khi hoàn thành series này, bạn sẽ:

### 1. Backend Skills (Laravel)
- [x] Cài đặt và cấu hình Laravel project
- [x] Thiết kế database schema với relationships phức tạp
- [x] Tạo Models với Eloquent ORM
- [x] Viết Controllers xử lý business logic
- [x] Định nghĩa Routes và Middleware
- [x] Xử lý Authentication & Authorization
- [x] Sử dụng Database Transactions
- [x] Query optimization với Eager Loading

### 2. Frontend Skills
- [x] Xây dựng giao diện với Blade Templates
- [x] CSS responsive design
- [x] JavaScript interactive features
- [x] AJAX requests
- [x] LocalStorage & SessionStorage

### 3. Advanced Features
- [x] QR Code generation & validation
- [x] Countdown timer với persistence
- [x] Complex seat selection logic
- [x] Payment flow simulation
- [x] Review system với rating

### 4. Development Tools
- [x] Git version control
- [x] Composer package management
- [x] NPM & Vite build tool
- [x] MySQL database management
- [x] Debugging & troubleshooting

---

## 📋 DANH SÁCH BÀI HỌC

### **Phần 1: Chuẩn Bị & Thiết Lập** (Ngày 1-2)
1. [00_start_here.md](00_start_here.md) - Tổng quan dự án *(BẠN ĐANG Ở ĐÂY)*
2. [01_laravel_setup.md](01_laravel_setup.md) - Cài đặt Laravel và môi trường
3. [02_database_design.md](02_database_design.md) - Thiết kế database schema

### **Phần 2: Xây Dựng Nền Tảng** (Ngày 3-4)
4. [03_models_step_by_step.md](03_models_step_by_step.md) - Tạo Models từng bước
5. [04_authentication.md](04_authentication.md) - Hệ thống đăng nhập/đăng ký
6. [05_frontend_basics.md](05_frontend_basics.md) - Layout và giao diện cơ bản

### **Phần 3: Tính Năng Người Dùng** (Ngày 5-7)
7. [06_movie_features.md](06_movie_features.md) - Xem phim, tìm kiếm, chi tiết
8. [07_booking_system.md](07_booking_system.md) - Hệ thống đặt vé cơ bản
9. [08_seat_selection.md](08_seat_selection.md) - Giao diện chọn ghế interactive
10. [09_payment_qr.md](09_payment_qr.md) - Thanh toán và QR code
11. [10_review_system.md](10_review_system.md) - Đánh giá & Review phim

### **Phần 4: Admin & Hoàn Thiện** (Ngày 8-10)
12. [11_admin_panel.md](11_admin_panel.md) - Admin panel đầy đủ
13. [12_final_touches.md](12_final_touches.md) - Hoàn thiện, testing, deploy

---

## 🛠️ YÊU CẦU KỸ THUẬT

### Phần mềm cần cài đặt:

| Phần mềm | Phiên bản | Mục đích |
|----------|-----------|----------|
| **PHP** | 8.2 hoặc cao hơn | Backend language |
| **Composer** | 2.x | PHP package manager |
| **Node.js** | 18.x hoặc cao hơn | Frontend build tools |
| **MySQL** | 8.0 hoặc cao hơn | Database |
| **Git** | Latest | Version control |
| **XAMPP/WAMP** | Latest | Local server (tùy chọn) |

### Kiến thức nền tảng cần có:

#### ✅ **Bắt buộc**:
- HTML & CSS cơ bản
- JavaScript cơ bản (biến, hàm, DOM)
- PHP cơ bản (biến, mảng, loops, functions)
- SQL cơ bản (SELECT, INSERT, UPDATE, DELETE)

#### 👍 **Nên có** (sẽ học trong quá trình):
- MVC pattern
- OOP trong PHP
- Laravel basics
- Git basics

#### 🌟 **Không bắt buộc** (sẽ được giải thích):
- Advanced Laravel features
- Database design patterns
- JavaScript ES6+
- CSS Grid/Flexbox

---

## ⏱️ THỜI GIAN DỰ KIẾN

### Lộ trình học đề xuất:

**Học Full-time** (8 giờ/ngày): **10-12 ngày**
- Ngày 1-2: Setup & Database
- Ngày 3-4: Models & Auth
- Ngày 5-7: User features
- Ngày 8-10: Admin & Polish

**Học Part-time** (2-3 giờ/ngày): **3-4 tuần**
- Tuần 1: Setup, Database, Models
- Tuần 2: Auth, Frontend, Movies
- Tuần 3: Booking system, Payment
- Tuần 4: Admin, Review, Deploy

**Học cuối tuần** (4-6 giờ/ngày): **4-5 tuần**
- Tuần 1-2: Backend foundation
- Tuần 3: User features
- Tuần 4: Admin panel
- Tuần 5: Testing & Polish

> **Lưu ý**: Đây chỉ là ước tính. Tốc độ học phụ thuộc vào kinh nghiệm của bạn.

---

## 📖 CÁCH SỬ DỤNG SERIES NÀY

### Quy tắc vàng:

1. **Đọc tuần tự**: Bắt đầu từ bài 01, đừng skip
2. **Gõ code bằng tay**: Đừng copy-paste, hãy gõ để hiểu
3. **Test sau mỗi bước**: Chạy thử ngay sau khi code xong
4. **Đặt câu hỏi**: Nếu không hiểu, đọc lại hoặc tìm hiểu thêm
5. **Làm bài tập**: Mỗi bài có phần "Thử thách" cuối - hãy làm!

### Cấu trúc mỗi bài học:

```markdown
## Mục tiêu
- Bạn sẽ học gì trong bài này

## Kiến thức cần biết
- Những gì cần hiểu trước khi bắt đầu

## Bước thực hiện
1. Bước 1 - Tạo file X
   - Code chi tiết
   - Giải thích từng dòng

2. Bước 2 - ...

## Giải thích
- Tại sao làm như vậy?
- Cách hoạt động?

## Test & Verify
- Cách kiểm tra đã đúng chưa

## Thử thách
- Bài tập để củng cố kiến thức

## Tổng kết
- Điểm quan trọng cần nhớ
```

### Ký hiệu trong hướng dẫn:

- 📝 **Ghi chú**: Thông tin bổ sung
- ⚠️ **Cảnh báo**: Lỗi thường gặp
- 💡 **Mẹo**: Tips hữu ích
- ✅ **Checkpoint**: Điểm kiểm tra
- 🔍 **Giải thích sâu**: Kiến thức nâng cao
- 🎯 **Mục tiêu**: Kết quả cần đạt

---

## 🗂️ CẤU TRÚC DỰ ÁN CUỐI CÙNG

Sau khi hoàn thành, dự án sẽ có cấu trúc:

```
cinebook/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Admin/           # 8 admin controllers
│   │       ├── User/            # User profile controller
│   │       ├── BookingController.php
│   │       ├── MovieController.php
│   │       ├── LoginController.php
│   │       ├── PaymentController.php
│   │       ├── ReviewController.php
│   │       ├── ShowtimeController.php
│   │       └── SearchController.php
│   └── Models/
│       ├── User.php             # 13 models total
│       ├── Movie.php
│       ├── Booking.php
│       ├── BookingSeat.php
│       ├── Showtime.php
│       ├── Room.php
│       ├── Seat.php
│       └── ...
├── resources/
│   ├── views/                   # 44 blade templates
│   │   ├── layouts/
│   │   ├── partials/
│   │   ├── admin/
│   │   ├── booking/
│   │   ├── movie/
│   │   ├── profile/
│   │   └── ...
│   ├── css/                     # 16 CSS files
│   └── js/                      # JavaScript
├── public/
│   └── js/                      # 6 client-side JS files
├── routes/
│   └── web.php                  # 41+ routes
├── database/
│   ├── migrations/
│   └── seeders/
├── config/
├── .env
├── composer.json
├── package.json
└── vite.config.js
```

---

## 💾 TÍNH NĂNG CHÍNH SẼ XÂY DỰNG

### 1. Hệ Thống Xem Phim
- ✅ Trang chủ với phim nổi bật
- ✅ Danh sách phim: Đang chiếu, Sắp chiếu
- ✅ Chi tiết phim: Poster, trailer, mô tả, thời lượng
- ✅ Tìm kiếm phim theo tên
- ✅ Lọc theo thể loại
- ✅ Hiển thị rating trung bình

### 2. Hệ Thống Đặt Vé
- ✅ Chọn suất chiếu (ngày, giờ, phòng)
- ✅ Seat Map interactive (chọn ghế)
- ✅ Hỗ trợ 3 loại ghế: Standard, VIP, Couple
- ✅ Couple seat logic (chọn theo cặp)
- ✅ Countdown timer 10 phút
- ✅ Tính giá tự động
- ✅ Xác nhận đặt vé

### 3. Thanh Toán & QR
- ✅ Mock payment gateway (VNPay, MoMo)
- ✅ Tạo mã QR cho mỗi vé
- ✅ 1 QR cho couple seat pair
- ✅ Download/Print QR codes
- ✅ Admin QR check-in system

### 4. Review & Rating
- ✅ Đánh giá phim (1-5 sao)
- ✅ Viết review (chỉ sau khi xem)
- ✅ Chỉnh sửa/xóa review của mình
- ✅ Hiển thị tất cả reviews
- ✅ Tự động cập nhật rating trung bình

### 5. User Profile
- ✅ Xem hồ sơ cá nhân
- ✅ Chỉnh sửa thông tin
- ✅ Đổi mật khẩu
- ✅ Lịch sử đặt vé
- ✅ Danh sách reviews đã viết

### 6. Admin Panel
- ✅ Dashboard với thống kê
- ✅ Quản lý phim (CRUD)
- ✅ Quản lý phòng & ghế
- ✅ Quản lý suất chiếu
- ✅ Quản lý đặt vé
- ✅ Quản lý người dùng
- ✅ Quản lý reviews
- ✅ QR Check-in system

---

## 🎓 ĐIỀU KIỆN ĐỂ BẮT ĐẦU

### Checklist trước khi bắt đầu bài 01:

- [ ] Đã cài đặt PHP 8.2+
- [ ] Đã cài đặt Composer
- [ ] Đã cài đặt Node.js & NPM
- [ ] Đã cài đặt MySQL
- [ ] Đã cài đặt Git
- [ ] Có editor/IDE (VSCode khuyến nghị)
- [ ] Hiểu cơ bản về MVC pattern
- [ ] Sẵn sàng dành thời gian học tập

### Nếu chưa có, hãy xem:

**Cài đặt XAMPP** (Windows):
1. Download từ https://www.apachefriends.org
2. Cài đặt với PHP 8.2+
3. Bật Apache & MySQL

**Cài đặt Composer**:
1. Download từ https://getcomposer.org/download/
2. Chạy installer
3. Verify: `composer --version`

**Cài đặt Node.js**:
1. Download từ https://nodejs.org
2. Chọn LTS version
3. Verify: `node --version` và `npm --version`

**Cài đặt Git**:
1. Download từ https://git-scm.com
2. Cài đặt với default settings
3. Verify: `git --version`

---

## 🚀 BƯỚC TIẾP THEO

### Sẵn sàng chưa? Hãy bắt đầu!

👉 **Bài tiếp theo**: [01_laravel_setup.md](01_laravel_setup.md)

Trong bài tiếp theo, bạn sẽ:
1. Tạo Laravel project mới
2. Cấu hình database connection
3. Cài đặt dependencies
4. Setup Vite cho frontend
5. Tạo cấu trúc thư mục ban đầu

### Thời gian ước tính: 30-45 phút

---

## 📞 HỖ TRỢ & RESOURCES

### Tài liệu tham khảo:
- [Laravel Documentation](https://laravel.com/docs) - Official docs
- [PHP Manual](https://www.php.net/manual/en/) - PHP reference
- [MySQL Documentation](https://dev.mysql.com/doc/) - Database docs
- [MDN Web Docs](https://developer.mozilla.org) - HTML/CSS/JS

### Khi gặp lỗi:
1. Đọc lại hướng dẫn cẩn thận
2. Kiểm tra error message
3. Google error message
4. Tìm trên Stack Overflow
5. Kiểm tra Laravel/PHP version

### Tips học hiệu quả:
- Gõ code bằng tay, không copy-paste
- Chạy test sau mỗi bước nhỏ
- Commit code sau mỗi tính năng hoàn thành
- Nghỉ giải lao 10 phút/giờ
- Đừng cố gắng hiểu 100% ngay - làm trước, hiểu sau

---

## 🎊 LỜI KHUYÊN CUỐI

Xây dựng một dự án hoàn chỉnh **không dễ**, nhưng **hoàn toàn khả thi** nếu bạn:

1. **Kiên trì**: Đừng bỏ cuộc khi gặp lỗi
2. **Tập trung**: Học một bài một, đừng nhảy cóc
3. **Thực hành**: Code nhiều hơn đọc
4. **Ghi chú**: Viết ra những gì bạn học được
5. **Kiên nhẫn**: Mỗi người có tốc độ học khác nhau

### Nhớ rằng:
> "Every expert was once a beginner. Every master was once a disaster."

Chúc bạn học tốt! 🚀

---

**Series**: Cinebook Tutorial
**Tác giả**: Based on existing Cinebook project
**Cập nhật**: January 2026
**Bài tiếp theo**: [01. Laravel Setup →](01_laravel_setup.md)
