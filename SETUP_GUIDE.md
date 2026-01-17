# 🎬 CineBook - Hướng Dẫn Cài Đặt Project

## 📋 Yêu cầu
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL
- XAMPP (hoặc tương tự)

## 🚀 Các Bước Cài Đặt

### 1️⃣ Copy file cấu hình môi trường
```bash
# Windows (PowerShell)
copy .env.example .env

# Linux/Mac
cp .env.example .env
```

### 2️⃣ Cấu hình file .env
Mở file `.env` và cập nhật các thông tin sau:

```env
APP_NAME=CineBook
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database - Cấu hình MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cinebook
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

### 3️⃣ Generate Application Key
```bash
php artisan key:generate
```

### 4️⃣ Cài đặt Dependencies

**PHP Dependencies:**
```bash
composer install
```

**JavaScript Dependencies:**
```bash
npm install
```

### 5️⃣ Tạo Database

**Cách 1: Sử dụng phpMyAdmin**
- Mở phpMyAdmin (http://localhost/phpmyadmin)
- Tạo database mới tên `cinebook`
- Import file `mySQL/mySQL.sql`
- Import dữ liệu mẫu từ `mySQL/data.sql`

**Cách 2: Sử dụng Command Line**
```bash
# Tạo database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS cinebook;"

# Import schema
mysql -u root cinebook < mySQL/mySQL.sql

# Import data
mysql -u root cinebook < mySQL/data.sql
```

### 6️⃣ Chạy Migration (nếu cần)
```bash
php artisan migrate
```

### 7️⃣ Seed dữ liệu mẫu (optional)
```bash
php artisan db:seed
```

### 8️⃣ Link storage
```bash
php artisan storage:link
```

### 9️⃣ Chạy Development Server

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```
Truy cập: http://localhost:8000

**Terminal 2 - Vite Dev Server:**
```bash
npm run dev
```

## ✅ Kiểm Tra

1. Mở trình duyệt: http://localhost:8000
2. Nếu thấy trang chủ CineBook → Thành công! 🎉

## 🔧 Xử Lý Lỗi Thường Gặp

### Lỗi: "APP_URL is undefined"
**Nguyên nhân:** Chưa có file `.env`

**Giải pháp:**
```bash
copy .env.example .env
php artisan key:generate
npm run dev
```

### Lỗi: "Database connection failed"
**Nguyên nhân:** MySQL chưa chạy hoặc cấu hình DB sai

**Giải pháp:**
1. Mở XAMPP và start MySQL
2. Kiểm tra lại thông tin trong `.env`:
   - DB_DATABASE=cinebook
   - DB_USERNAME=root
   - DB_PASSWORD= (để trống nếu dùng XAMPP mặc định)

### Lỗi: "SQLSTATE[HY000] [1049] Unknown database"
**Nguyên nhân:** Database chưa được tạo

**Giải pháp:**
```bash
mysql -u root -e "CREATE DATABASE cinebook;"
mysql -u root cinebook < mySQL/mySQL.sql
```

### Lỗi: "Class 'SimpleSoftwareIO\QrCode\Facades\QrCode' not found"
**Nguyên nhân:** Package chưa được cài đặt

**Giải pháp:**
```bash
composer install
```

### Lỗi: "Vite manifest not found"
**Nguyên nhân:** Vite chưa build assets

**Giải pháp:**
```bash
npm install
npm run dev
```

## 📁 Cấu Trúc Project Quan Trọng

```
cinebook/
├── .env                    # File cấu hình (KHÔNG commit vào Git)
├── .env.example           # Template cấu hình
├── mySQL/
│   ├── mySQL.sql          # Database schema
│   └── data.sql           # Dữ liệu mẫu
├── app/
│   ├── Http/Controllers/  # Controllers
│   └── Models/            # Models
├── resources/
│   ├── views/             # Blade templates
│   ├── css/               # CSS files
│   └── js/                # JavaScript files
├── public/
│   ├── css/               # Compiled CSS
│   ├── js/                # JavaScript utilities
│   └── images/            # Hình ảnh
└── routes/
    └── web.php            # Routes

```

## 👥 Thông Tin Đăng Nhập Mặc Định

### Admin Account
- Email: `admin@cinebook.com`
- Password: `admin123`

### User Account
- Email: `user@cinebook.com`
- Password: `user123`

## 🎯 Tính Năng Chính

- ✅ Xem phim đang chiếu & sắp chiếu
- ✅ Đặt vé online với sơ đồ ghế
- ✅ Quét QR code check-in tại rạp
- ✅ Thanh toán VNPay/MoMo (Mock)
- ✅ Quản lý booking với countdown timer
- ✅ Review & rating phim
- ✅ Admin panel quản lý

## 📞 Hỗ Trợ

Nếu gặp vấn đề, kiểm tra:
1. XAMPP đã bật MySQL chưa?
2. File `.env` đã được tạo chưa?
3. `composer install` và `npm install` đã chạy chưa?
4. Database `cinebook` đã được tạo và import data chưa?

---
**Happy Coding! 🚀**
