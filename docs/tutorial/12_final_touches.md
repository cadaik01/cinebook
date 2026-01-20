# 12. HOÀN THIỆN, TESTING & DEPLOYMENT

## 🎯 Mục tiêu bài học

Sau bài học này, bạn sẽ có:
- ✅ Project hoàn chỉnh, bug-free
- ✅ Testing checklist đầy đủ
- ✅ Performance optimization
- ✅ Hướng dẫn deployment
- ✅ Maintenance tips

**Thời gian ước tính**: 60-90 phút

---

## ✅ TESTING CHECKLIST

### 1. Authentication Testing

```markdown
- [ ] Đăng ký tài khoản mới thành công
- [ ] Validation errors hiển thị đúng
- [ ] Email unique constraint hoạt động
- [ ] Đăng nhập thành công
- [ ] Remember me checkbox hoạt động
- [ ] Đăng xuất thành công
- [ ] Middleware bảo vệ routes
- [ ] Admin/User roles phân quyền đúng
```

### 2. Movie Features Testing

```markdown
- [ ] Trang chủ hiển thị phim đúng
- [ ] Filter theo thể loại hoạt động
- [ ] Search phim hoạt động
- [ ] Pagination hoạt động
- [ ] Chi tiết phim hiển thị đầy đủ
- [ ] Lịch chiếu hiển thị đúng ngày
- [ ] Rating trung bình tính chính xác
```

### 3. Booking System Testing

```markdown
- [ ] Seat map hiển thị đúng layout
- [ ] Ghế đã đặt không thể chọn
- [ ] Couple seats phải chọn theo cặp
- [ ] Pricing calculation chính xác
- [ ] Countdown timer hoạt động
- [ ] Timeout tự động hủy reserved seats
- [ ] Transaction rollback khi lỗi
- [ ] QR code generation hoạt động
- [ ] Payment flow hoàn chỉnh
```

### 4. Review System Testing

```markdown
- [ ] Chỉ user đã xem mới review được
- [ ] Một user chỉ review một movie một lần
- [ ] Edit/Delete own reviews hoạt động
- [ ] Rating tự động update khi có review mới
- [ ] Admin có thể delete mọi reviews
```

### 5. Admin Panel Testing

```markdown
- [ ] Chỉ admin mới truy cập được
- [ ] CRUD movies hoạt động
- [ ] CRUD showtimes hoạt động
- [ ] Manage bookings hoạt động
- [ ] QR scanner hoạt động
- [ ] Statistics hiển thị chính xác
```

---

## 🔧 PERFORMANCE OPTIMIZATION

### 1. Database Optimization

**Kiểm tra và tạo indexes**:

```sql
-- Kiểm tra queries chậm
EXPLAIN SELECT * FROM movies WHERE status = 'now_showing';

-- Tạo indexes nếu chưa có
CREATE INDEX idx_movies_status ON movies(status);
CREATE INDEX idx_showtimes_date ON showtimes(show_date);
CREATE INDEX idx_bookings_user ON bookings(user_id);
```

### 2. Eager Loading

**File**: `app/Http/Controllers/MovieController.php`

```php
// ❌ Bad (N+1 problem)
$movies = Movie::all();
foreach ($movies as $movie) {
    echo $movie->genres; // Query cho mỗi movie
}

// ✅ Good (Eager loading)
$movies = Movie::with(['genres', 'showtimes'])->get();
```

### 3. Caching

**File**: `app/Http/Controllers/HomeController.php`

```php
use Illuminate\Support\Facades\Cache;

public function index()
{
    $nowShowing = Cache::remember('movies.now_showing', 3600, function () {
        return Movie::where('status', 'now_showing')
            ->with('genres')
            ->orderBy('rating_avg', 'desc')
            ->take(6)
            ->get();
    });

    return view('home', compact('nowShowing'));
}
```

### 4. Minify CSS/JS

```bash
# Production build
npm run build

# Verify file sizes
ls -lh public/build/assets/
```

---

## 🚀 DEPLOYMENT GUIDE

### BƯỚC 1: Chuẩn bị Production

**1.1. Update .env**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_DATABASE=cinebook_prod
DB_USERNAME=your_db_user
DB_PASSWORD=your_strong_password

SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=database
```

**1.2. Generate App Key**

```bash
php artisan key:generate
```

**1.3. Optimize Laravel**

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Create cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**1.4. Build Frontend Assets**

```bash
npm run build
```

---

### BƯỚC 2: Deploy lên Shared Hosting (cPanel)

**2.1. Upload files qua FTP**

```
1. Zip toàn bộ project (trừ node_modules, .env, storage/logs)
2. Upload lên thư mục home (không phải public_html)
3. Giải nén file
```

**2.2. Cấu hình public_html**

Di chuyển nội dung thư mục `public` vào `public_html`:

```bash
cp -r cinebook/public/* public_html/
```

**2.3. Update index.php**

**File**: `public_html/index.php`

```php
<?php

// Update paths
require __DIR__.'/../cinebook/vendor/autoload.php';
$app = require_once __DIR__.'/../cinebook/bootstrap/app.php';
```

**2.4. Set Permissions**

```bash
chmod -R 755 cinebook/storage
chmod -R 755 cinebook/bootstrap/cache
```

**2.5. Import Database**

1. Tạo database mới trong cPanel
2. Import file `mySQL/schema.sql`
3. Import file `mySQL/data.sql`
4. Update `.env` với DB credentials

---

### BƯỚC 3: Deploy lên VPS (Ubuntu)

**3.1. Install Requirements**

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-mbstring php8.2-zip -y

# Install MySQL
sudo apt install mysql-server -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y

# Install Nginx
sudo apt install nginx -y
```

**3.2. Upload Project**

```bash
# Clone từ Git
cd /var/www
git clone https://github.com/your-repo/cinebook.git
cd cinebook

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

**3.3. Configure Nginx**

**File**: `/etc/nginx/sites-available/cinebook`

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/cinebook/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Enable site**:

```bash
sudo ln -s /etc/nginx/sites-available/cinebook /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

**3.4. Configure MySQL**

```bash
sudo mysql

CREATE DATABASE cinebook;
CREATE USER 'cinebook_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON cinebook.* TO 'cinebook_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import database
mysql -u cinebook_user -p cinebook < mySQL/schema.sql
mysql -u cinebook_user -p cinebook < mySQL/data.sql
```

**3.5. Setup SSL (Let's Encrypt)**

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d your-domain.com
```

---

## 🔒 SECURITY CHECKLIST

```markdown
- [ ] APP_DEBUG=false trong production
- [ ] APP_KEY được generate
- [ ] Database password mạnh
- [ ] .env không commit lên Git
- [ ] HTTPS enabled
- [ ] CSRF protection enabled
- [ ] SQL injection prevented (dùng Eloquent)
- [ ] XSS prevented (Blade escaping)
- [ ] File upload validation
- [ ] Rate limiting enabled
```

---

## 🐛 COMMON ISSUES & FIXES

### Lỗi 1: 500 Internal Server Error

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check permissions
sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Lỗi 2: Mix Manifest Not Found

```bash
# Rebuild assets
npm run build

# Clear cache
php artisan cache:clear
```

### Lỗi 3: Database Connection Failed

```bash
# Check .env credentials
# Test connection
php artisan db:show
```

### Lỗi 4: Class not found

```bash
# Regenerate autoload
composer dump-autoload
php artisan clear-compiled
php artisan optimize
```

---

## 📊 MONITORING & MAINTENANCE

### 1. Log Monitoring

```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# View Nginx logs
tail -f /var/log/nginx/error.log
```

### 2. Database Backup

**Tạo backup script**:

```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u cinebook_user -p'password' cinebook > backup_$DATE.sql
gzip backup_$DATE.sql

# Delete backups older than 7 days
find . -name "backup_*.sql.gz" -mtime +7 -delete
```

**Cron job** (chạy mỗi ngày lúc 2AM):

```bash
crontab -e

0 2 * * * /path/to/backup.sh
```

### 3. Performance Monitoring

```bash
# Check disk usage
df -h

# Check memory
free -m

# Check MySQL slow queries
sudo mysql
SHOW VARIABLES LIKE 'slow_query_log';
```

---

## 📝 PROJECT SUMMARY

### Tổng kết tính năng đã xây dựng

#### Backend (Laravel)
- ✅ 13 Models với relationships đầy đủ
- ✅ 9 Controllers xử lý business logic
- ✅ Authentication & Authorization
- ✅ Database transactions
- ✅ Form validation
- ✅ Eloquent ORM queries

#### Frontend
- ✅ 44 Blade templates
- ✅ Responsive CSS design
- ✅ JavaScript interactivity
- ✅ AJAX requests
- ✅ LocalStorage management

#### Tính năng chính
- ✅ Đăng ký/Đăng nhập
- ✅ Xem phim (Now Showing, Coming Soon)
- ✅ Tìm kiếm & Filter
- ✅ Đặt vé với seat selection
- ✅ Couple seats logic
- ✅ Payment flow
- ✅ QR code generation
- ✅ Review system
- ✅ Admin panel đầy đủ
- ✅ User profile management

---

## 🎓 NEXT STEPS - Nâng cao hơn

### 1. Email Notifications
```bash
php artisan make:mail BookingConfirmation
```

### 2. Real Payment Gateway
- VNPay integration
- MoMo integration

### 3. Mobile App
- React Native
- Flutter

### 4. Advanced Features
- Seat recommendation AI
- Movie recommendation system
- Social features (share reviews)
- Loyalty program

---

## 🎉 CONGRATULATIONS!

Chúc mừng bạn đã hoàn thành toàn bộ series **Cinebook Tutorial**!

Bạn đã học được:
- ✅ Laravel từ cơ bản đến nâng cao
- ✅ Database design & relationships
- ✅ Authentication & Authorization
- ✅ Complex business logic
- ✅ Frontend development
- ✅ Testing & Deployment

### Điều quan trọng nhất:
> "Bạn đã TỰ TAY xây dựng một dự án hoàn chỉnh!"

---

## 📞 SUPPORT & FEEDBACK

Nếu bạn gặp vấn đề hoặc có góp ý:
- Review lại các bài trước
- Google error messages
- Check Stack Overflow
- Đọc Laravel documentation

**Good luck với career của bạn! 🚀**

---

**Bài trước**: [← 11. Admin Panel](11_admin_panel.md)
**Series**: Cinebook Tutorial - HOÀN THÀNH
**Cập nhật**: January 2026

---

## 📚 RESOURCES

- [Laravel Documentation](https://laravel.com/docs)
- [PHP The Right Way](https://phptherightway.com)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [MDN Web Docs](https://developer.mozilla.org)
- [Stack Overflow](https://stackoverflow.com)

**🎊 HẾT SERIES TUTORIAL 🎊**
