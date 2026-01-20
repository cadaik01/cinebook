# 01. THIẾT LẬP LARAVEL PROJECT

## 🎯 Mục tiêu bài học

Sau bài học này, bạn sẽ có:
- ✅ Laravel project mới hoàn chỉnh
- ✅ Database connection đã cấu hình
- ✅ Dependencies đã cài đặt
- ✅ Vite đã setup cho frontend
- ✅ Project chạy thành công trên localhost

**Thời gian ước tính**: 30-45 phút

---

## 📚 Kiến thức cần biết

- Command line cơ bản (cd, mkdir, dir/ls)
- Composer là gì (PHP package manager)
- NPM là gì (Node package manager)
- File .env trong Laravel

---

## 🛠️ BƯỚC 1: TẠO LARAVEL PROJECT

### 1.1. Mở Terminal/Command Prompt

**Windows**:
```bash
# Mở CMD hoặc PowerShell
# Navigate đến thư mục htdocs của XAMPP
cd C:\xampp\htdocs
```

**Mac/Linux**:
```bash
# Mở Terminal
# Navigate đến thư mục web của bạn
cd ~/Sites
# hoặc
cd /var/www/html
```

### 1.2. Tạo Laravel Project

```bash
composer create-project laravel/laravel cinebook
```

**Giải thích**:
- `composer create-project`: Lệnh tạo project mới từ package
- `laravel/laravel`: Package Laravel official
- `cinebook`: Tên thư mục project

⏳ **Đợi 2-5 phút** để Composer download và cài đặt.

**Kết quả**:
```
✔ Application ready! Build something amazing.
```

### 1.3. Di chuyển vào thư mục project

```bash
cd cinebook
```

### 1.4. Verify cài đặt

```bash
php artisan --version
```

**Kết quả mong đợi**:
```
Laravel Framework 12.x.x
```

✅ **Checkpoint**: Nếu thấy version Laravel, bạn đã tạo project thành công!

---

## 🛠️ BƯỚC 2: CẤU HÌNH DATABASE

### 2.1. Tạo Database trong MySQL

**Cách 1: Dùng phpMyAdmin** (XAMPP)
1. Mở trình duyệt: `http://localhost/phpmyadmin`
2. Click tab "Databases"
3. Tên database: `cinebook`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

**Cách 2: Dùng MySQL CLI**
```bash
# Mở MySQL client
mysql -u root -p

# Tạo database (trong MySQL prompt)
CREATE DATABASE cinebook CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Kiểm tra
SHOW DATABASES;

# Thoát
exit;
```

✅ **Checkpoint**: Database `cinebook` đã xuất hiện trong danh sách

### 2.2. Cấu hình file .env

Mở file `.env` trong thư mục project (dùng VSCode, Notepad++, v.v.)

**Tìm các dòng sau** (khoảng dòng 10-15):
```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

**Sửa thành**:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cinebook
DB_USERNAME=root
DB_PASSWORD=
```

📝 **Ghi chú**:
- `DB_CONNECTION`: Loại database (mysql, sqlite, pgsql)
- `DB_HOST`: Địa chỉ server (localhost = 127.0.0.1)
- `DB_PORT`: Cổng MySQL (mặc định 3306)
- `DB_DATABASE`: Tên database vừa tạo
- `DB_USERNAME`: User MySQL (mặc định root trong XAMPP)
- `DB_PASSWORD`: Mật khẩu (để trống nếu dùng XAMPP mặc định)

⚠️ **Cảnh báo**:
- Nếu XAMPP của bạn có mật khẩu root, điền vào `DB_PASSWORD`
- Không commit file `.env` lên Git (đã có trong .gitignore)

### 2.3. Test kết nối database

```bash
php artisan migrate
```

**Nếu thành công**, bạn sẽ thấy:
```
Migration table created successfully.
Migrating: xxxxx_create_users_table
Migrated:  xxxxx_create_users_table (25.34ms)
...
```

**Nếu lỗi**, kiểm tra:
- [ ] MySQL server đã chạy chưa? (XAMPP Control Panel → MySQL → Start)
- [ ] Tên database đúng chưa?
- [ ] Username/password đúng chưa?

✅ **Checkpoint**: Migration chạy thành công, các bảng đã được tạo

---

## 🛠️ BƯỚC 3: CÀI ĐẶT DEPENDENCIES

### 3.1. Cài đặt NPM packages

```bash
npm install
```

⏳ **Đợi 1-3 phút** để NPM download packages.

**Kết quả**:
```
added XXX packages
```

### 3.2. Cài đặt QR Code package

```bash
composer require simplesoftwareio/simple-qrcode
```

📝 **Ghi chú**: Package này dùng để tạo mã QR cho vé xem phim.

**Kết quả**:
```
Using version ^4.2 for simplesoftwareio/simple-qrcode
...
Package manifest generated successfully.
```

### 3.3. Verify packages đã cài

**Kiểm tra composer.json**:
```bash
composer show simplesoftwareio/simple-qrcode
```

**Kết quả**:
```
name     : simplesoftwareio/simple-qrcode
versions : * 4.2.x
...
```

✅ **Checkpoint**: Package đã được cài đặt thành công

---

## 🛠️ BƯỚC 4: SETUP VITE CHO FRONTEND

### 4.1. Tạo file vite.config.js

File này đã có sẵn khi tạo Laravel project. Mở và kiểm tra:

**File**: `vite.config.js`
```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

📝 **Ghi chú**: Config này cho Vite biết compile file nào.

### 4.2. Cấu hình resources/css/app.css

Mở file `resources/css/app.css` và **xóa toàn bộ nội dung cũ**, thay bằng:

```css
/* Base Reset & Variables */
@import './root.css';
@import './base.css';

/* Components */
@import './buttons.css';
@import './header.css';
@import './footer.css';
```

📝 **Ghi chú**: Chúng ta sẽ tạo các file CSS riêng cho từng component.

### 4.3. Tạo file resources/css/root.css

**File**: `resources/css/root.css`
```css
/* CSS Variables - Màu sắc và spacing chung cho toàn bộ project */

:root {
  /* Colors - Brand */
  --primary-color: #e50914;        /* Màu đỏ chủ đạo (như Netflix) */
  --primary-hover: #f40612;        /* Màu đỏ hover */
  --primary-dark: #b20710;         /* Màu đỏ đậm */

  /* Colors - Neutral */
  --bg-dark: #141414;              /* Background tối */
  --bg-dark-secondary: #1a1a1a;    /* Background tối phụ */
  --bg-card: #2a2a2a;              /* Background card */
  --text-primary: #ffffff;         /* Text màu trắng */
  --text-secondary: #b3b3b3;       /* Text xám nhạt */
  --text-muted: #808080;           /* Text xám đậm */

  /* Colors - Status */
  --success-color: #46d369;        /* Màu xanh lá (thành công) */
  --warning-color: #ffa500;        /* Màu cam (cảnh báo) */
  --error-color: #f44336;          /* Màu đỏ (lỗi) */
  --info-color: #2196f3;           /* Màu xanh dương (thông tin) */

  /* Spacing */
  --spacing-xs: 0.25rem;   /* 4px */
  --spacing-sm: 0.5rem;    /* 8px */
  --spacing-md: 1rem;      /* 16px */
  --spacing-lg: 1.5rem;    /* 24px */
  --spacing-xl: 2rem;      /* 32px */
  --spacing-2xl: 3rem;     /* 48px */

  /* Border Radius */
  --radius-sm: 4px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-xl: 16px;
  --radius-full: 9999px;

  /* Shadows */
  --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);
  --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.4);
  --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.5);
  --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.6);

  /* Transitions */
  --transition-fast: 150ms ease;
  --transition-base: 250ms ease;
  --transition-slow: 350ms ease;

  /* Typography */
  --font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  --font-size-xs: 0.75rem;    /* 12px */
  --font-size-sm: 0.875rem;   /* 14px */
  --font-size-base: 1rem;     /* 16px */
  --font-size-lg: 1.125rem;   /* 18px */
  --font-size-xl: 1.25rem;    /* 20px */
  --font-size-2xl: 1.5rem;    /* 24px */
  --font-size-3xl: 2rem;      /* 32px */
  --font-size-4xl: 2.5rem;    /* 40px */

  /* Layout */
  --container-max-width: 1400px;
  --header-height: 70px;
  --footer-height: 200px;

  /* Z-index */
  --z-dropdown: 1000;
  --z-sticky: 1020;
  --z-fixed: 1030;
  --z-modal-backdrop: 1040;
  --z-modal: 1050;
  --z-popover: 1060;
  --z-tooltip: 1070;
}

/* Seat Type Colors */
:root {
  --seat-available: #4caf50;      /* Ghế trống - xanh lá */
  --seat-selected: var(--primary-color);  /* Ghế đang chọn - đỏ */
  --seat-booked: #9e9e9e;         /* Ghế đã đặt - xám */
  --seat-vip: #ffd700;            /* Ghế VIP - vàng */
  --seat-couple: #ff69b4;         /* Ghế couple - hồng */
}
```

📝 **Giải thích**:
- CSS Variables giúp tái sử dụng màu sắc, spacing dễ dàng
- Dùng `var(--primary-color)` để áp dụng biến
- Dễ thay đổi theme chỉ bằng cách sửa ở một chỗ

### 4.4. Tạo file resources/css/base.css

**File**: `resources/css/base.css`
```css
/* Base styles - CSS reset và typography cơ bản */

/* Box-sizing reset */
*,
*::before,
*::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

/* Body */
body {
  font-family: var(--font-family);
  font-size: var(--font-size-base);
  line-height: 1.6;
  color: var(--text-primary);
  background-color: var(--bg-dark);
  margin: 0;
  padding: 0;
  min-height: 100vh;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Typography */
h1, h2, h3, h4, h5, h6 {
  margin: 0;
  font-weight: 600;
  line-height: 1.2;
  color: var(--text-primary);
}

h1 { font-size: var(--font-size-4xl); }
h2 { font-size: var(--font-size-3xl); }
h3 { font-size: var(--font-size-2xl); }
h4 { font-size: var(--font-size-xl); }
h5 { font-size: var(--font-size-lg); }
h6 { font-size: var(--font-size-base); }

p {
  margin: 0 0 var(--spacing-md) 0;
}

a {
  color: var(--primary-color);
  text-decoration: none;
  transition: color var(--transition-fast);
}

a:hover {
  color: var(--primary-hover);
}

/* Lists */
ul, ol {
  list-style: none;
  margin: 0;
  padding: 0;
}

/* Images */
img {
  max-width: 100%;
  height: auto;
  display: block;
}

/* Container */
.container {
  width: 100%;
  max-width: var(--container-max-width);
  margin: 0 auto;
  padding: 0 var(--spacing-lg);
}

/* Layout */
main {
  min-height: calc(100vh - var(--header-height) - var(--footer-height));
  padding: var(--spacing-xl) 0;
}

/* Utility Classes */
.text-center { text-align: center; }
.text-left { text-align: left; }
.text-right { text-align: right; }

.mt-sm { margin-top: var(--spacing-sm); }
.mt-md { margin-top: var(--spacing-md); }
.mt-lg { margin-top: var(--spacing-lg); }
.mt-xl { margin-top: var(--spacing-xl); }

.mb-sm { margin-bottom: var(--spacing-sm); }
.mb-md { margin-bottom: var(--spacing-md); }
.mb-lg { margin-bottom: var(--spacing-lg); }
.mb-xl { margin-bottom: var(--spacing-xl); }

.hidden { display: none !important; }
.visible { display: block !important; }
```

### 4.5. Tạo file resources/css/buttons.css

**File**: `resources/css/buttons.css`
```css
/* Button styles */

.btn {
  display: inline-block;
  padding: var(--spacing-sm) var(--spacing-lg);
  font-size: var(--font-size-base);
  font-weight: 500;
  text-align: center;
  text-decoration: none;
  border: none;
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all var(--transition-base);
  font-family: inherit;
  line-height: 1.5;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Primary button */
.btn-primary {
  background-color: var(--primary-color);
  color: var(--text-primary);
}

.btn-primary:hover:not(:disabled) {
  background-color: var(--primary-hover);
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

/* Secondary button */
.btn-secondary {
  background-color: transparent;
  color: var(--text-primary);
  border: 2px solid var(--text-secondary);
}

.btn-secondary:hover:not(:disabled) {
  border-color: var(--text-primary);
  background-color: rgba(255, 255, 255, 0.1);
}

/* Success button */
.btn-success {
  background-color: var(--success-color);
  color: var(--text-primary);
}

.btn-success:hover:not(:disabled) {
  background-color: #3cb354;
}

/* Danger button */
.btn-danger {
  background-color: var(--error-color);
  color: var(--text-primary);
}

.btn-danger:hover:not(:disabled) {
  background-color: #d32f2f;
}

/* Button sizes */
.btn-sm {
  padding: var(--spacing-xs) var(--spacing-md);
  font-size: var(--font-size-sm);
}

.btn-lg {
  padding: var(--spacing-md) var(--spacing-xl);
  font-size: var(--font-size-lg);
}

/* Full width button */
.btn-block {
  display: block;
  width: 100%;
}
```

📝 **Ghi chú**: Các file CSS khác (header.css, footer.css) sẽ tạo ở các bài sau.

---

## 🛠️ BƯỚC 5: CHẠY PROJECT LẦN ĐẦU

### 5.1. Start Laravel development server

```bash
php artisan serve
```

**Kết quả**:
```
Starting Laravel development server: http://127.0.0.1:8000
```

🌐 **Mở trình duyệt**: `http://localhost:8000`

Bạn sẽ thấy trang Laravel mặc định với logo Laravel.

### 5.2. Start Vite dev server (Terminal mới)

Mở **terminal/CMD thứ 2** (đừng tắt terminal đang chạy `php artisan serve`):

```bash
npm run dev
```

**Kết quả**:
```
VITE v5.x.x  ready in xxx ms

➜  Local:   http://localhost:5173/
➜  Network: use --host to expose
```

📝 **Ghi chú**:
- Laravel server: `http://localhost:8000` (backend + render views)
- Vite server: `http://localhost:5173` (compile CSS/JS)
- Cần chạy **CẢ HAI** khi development

### 5.3. Test hot reload

1. Giữ nguyên 2 terminals đang chạy
2. Mở file `resources/views/welcome.blade.php`
3. Sửa dòng 26 (hoặc tương tự) từ:
   ```html
   Laravel
   ```
   Thành:
   ```html
   Cinebook Project
   ```
4. Save file
5. Refresh trình duyệt `http://localhost:8000`

✅ **Checkpoint**: Trang web tự động cập nhật với text mới!

---

## 🛠️ BƯỚC 6: TẠO CẤU TRÚC THƯ MỤC

### 6.1. Tạo thư mục cho CSS files

```bash
# Windows (CMD)
mkdir resources\css\pages
mkdir resources\css\components

# Mac/Linux hoặc Windows (PowerShell/Git Bash)
mkdir -p resources/css/pages
mkdir -p resources/css/components
```

### 6.2. Tạo thư mục cho JavaScript files

```bash
# Windows (CMD)
mkdir public\js

# Mac/Linux hoặc Windows (PowerShell/Git Bash)
mkdir -p public/js
```

### 6.3. Tạo thư mục cho Controllers

```bash
# Windows (CMD)
mkdir app\Http\Controllers\Admin
mkdir app\Http\Controllers\User

# Mac/Linux hoặc Windows (PowerShell/Git Bash)
mkdir -p app/Http/Controllers/Admin
mkdir -p app/Http/Controllers/User
```

### 6.4. Tạo thư mục cho Views

```bash
# Windows (CMD)
mkdir resources\views\layouts
mkdir resources\views\partials
mkdir resources\views\admin
mkdir resources\views\movie
mkdir resources\views\booking
mkdir resources\views\login
mkdir resources\views\profile
mkdir resources\views\reviews

# Mac/Linux hoặc Windows (PowerShell/Git Bash)
mkdir -p resources/views/layouts
mkdir -p resources/views/partials
mkdir -p resources/views/admin
mkdir -p resources/views/movie
mkdir -p resources/views/booking
mkdir -p resources/views/login
mkdir -p resources/views/profile
mkdir -p resources/views/reviews
```

### 6.5. Verify cấu trúc thư mục

```bash
# Windows
tree /F resources\views

# Mac/Linux
tree resources/views
# hoặc
ls -R resources/views
```

**Kết quả mong đợi**:
```
resources/views/
├── layouts/
├── partials/
├── admin/
├── movie/
├── booking/
├── login/
├── profile/
├── reviews/
└── welcome.blade.php
```

---

## 🛠️ BƯỚC 7: CẤU HÌNH GIT

### 7.1. Initialize Git repository

```bash
git init
```

📝 **Ghi chú**: Laravel đã có file `.gitignore` sẵn.

### 7.2. Kiểm tra .gitignore

Mở file `.gitignore` và verify có các dòng sau:

```
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
```

✅ **Quan trọng**: `.env` phải có trong .gitignore để không commit thông tin nhạy cảm.

### 7.3. First commit

```bash
git add .
git commit -m "Initial Laravel project setup for Cinebook"
```

**Kết quả**:
```
[main (root-commit) xxxxxx] Initial Laravel project setup for Cinebook
 XX files changed, XXXX insertions(+)
```

---

## ✅ TEST & VERIFY

### Checklist hoàn thành

- [ ] Laravel project tạo thành công
- [ ] Database `cinebook` đã tạo trong MySQL
- [ ] File `.env` đã cấu hình đúng database
- [ ] `php artisan migrate` chạy thành công
- [ ] QR Code package đã cài đặt
- [ ] NPM packages đã cài đặt
- [ ] CSS files (root.css, base.css, buttons.css) đã tạo
- [ ] `php artisan serve` chạy được
- [ ] `npm run dev` chạy được
- [ ] Truy cập `http://localhost:8000` thấy trang Laravel
- [ ] Cấu trúc thư mục đã tạo đầy đủ
- [ ] Git repository đã initialize

### Test commands

```bash
# Test Laravel
php artisan --version
# Kết quả: Laravel Framework 12.x.x

# Test Database connection
php artisan db:show
# Kết quả: Hiển thị thông tin database

# Test Composer packages
composer show | grep qrcode
# Kết quả: simplesoftwareio/simple-qrcode

# Test NPM
npm list --depth=0
# Kết quả: Danh sách packages
```

---

## 🎯 THỰC HÀNH

### Bài tập 1: Customize welcome page
1. Mở file `resources/views/welcome.blade.php`
2. Thay đổi tiêu đề thành "Welcome to Cinebook"
3. Thêm một đoạn text giới thiệu về dự án
4. Verify thay đổi trên trình duyệt

### Bài tập 2: Test CSS variables
1. Tạo file `resources/css/test.css`
2. Thêm vào app.css: `@import './test.css';`
3. Viết CSS dùng biến `var(--primary-color)`
4. Test trên browser

### Bài tập 3: Database connection
1. Tạo migration mới: `php artisan make:migration create_test_table`
2. Edit migration file
3. Run migration: `php artisan migrate`
4. Kiểm tra trong phpMyAdmin

---

## 🐛 TROUBLESHOOTING - LỖI THƯỜNG GẶP

### Lỗi 1: "composer: command not found"
**Nguyên nhân**: Composer chưa cài hoặc chưa thêm vào PATH

**Giải pháp**:
```bash
# Download và cài Composer từ getcomposer.org
# Sau đó restart terminal
```

### Lỗi 2: "SQLSTATE[HY000] [1045] Access denied"
**Nguyên nhân**: Sai username/password MySQL

**Giải pháp**:
1. Kiểm tra MySQL đã chạy chưa (XAMPP Control Panel)
2. Verify username/password trong `.env`
3. Test login MySQL: `mysql -u root -p`

### Lỗi 3: "npm ERR! code ENOENT"
**Nguyên nhân**: Node.js/NPM chưa cài

**Giải pháp**:
```bash
# Download và cài Node.js từ nodejs.org
# Chọn LTS version
# Restart terminal sau khi cài
```

### Lỗi 4: "Address already in use"
**Nguyên nhân**: Port 8000 đang được dùng

**Giải pháp**:
```bash
# Dùng port khác
php artisan serve --port=8001
```

### Lỗi 5: Migration lỗi "Syntax error"
**Nguyên nhân**: MySQL version cũ hoặc strict mode

**Giải pháp**:
1. Kiểm tra MySQL version: `mysql --version` (cần >= 8.0)
2. Hoặc tắt strict mode trong `config/database.php`

---

## 📝 TÓM TẮT

### Đã học được gì?

1. **Tạo Laravel project** với Composer
2. **Cấu hình database** trong .env
3. **Cài đặt packages** (Composer & NPM)
4. **Setup Vite** cho frontend development
5. **Tạo CSS structure** với variables
6. **Chạy development servers** (Laravel + Vite)
7. **Tổ chức thư mục** theo chuẩn MVC
8. **Initialize Git** repository

### Files đã tạo

```
cinebook/
├── .env                    # Database config
├── vite.config.js          # Vite configuration
├── resources/
│   └── css/
│       ├── app.css         # Main CSS file
│       ├── root.css        # CSS variables
│       ├── base.css        # Base styles
│       └── buttons.css     # Button styles
└── (structure folders)
```

### Commands cần nhớ

```bash
# Development
php artisan serve          # Start Laravel server
npm run dev               # Start Vite dev server

# Database
php artisan migrate       # Run migrations
php artisan db:show       # Show DB info

# Package management
composer install          # Install PHP packages
npm install              # Install Node packages

# Utilities
php artisan list          # List all artisan commands
php artisan route:list    # List all routes
```

---

## 🚀 BƯỚC TIẾP THEO

Trong bài tiếp theo ([02_database_design.md](02_database_design.md)), bạn sẽ:

1. Thiết kế database schema hoàn chỉnh
2. Tạo tất cả tables cần thiết
3. Định nghĩa relationships giữa các tables
4. Import sample data
5. Hiểu rõ về database normalization

**Thời gian ước tính**: 60-90 phút

---

**Bài trước**: [← 00. Start Here](00_start_here.md)
**Bài tiếp**: [02. Database Design →](02_database_design.md)

**Series**: Cinebook Tutorial - Step by Step
**Cập nhật**: January 2026
