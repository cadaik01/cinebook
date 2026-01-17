# 02. THIẾT KẾ DATABASE

## 🎯 Mục tiêu bài học

Sau bài học này, bạn sẽ có:
- ✅ Database schema hoàn chỉnh với 13 tables
- ✅ Relationships giữa các tables được định nghĩa rõ ràng
- ✅ Sample data để test
- ✅ Hiểu về database normalization

**Thời gian ước tính**: 60-90 phút

---

## 📚 Kiến thức cần biết

- SQL cơ bản (CREATE TABLE, PRIMARY KEY, FOREIGN KEY)
- Khái niệm relationships: 1-1, 1-n, n-n
- Data types: INT, VARCHAR, TEXT, DECIMAL, DATETIME, ENUM

---

## 🗂️ TỔNG QUAN DATABASE SCHEMA

### Danh sách 13 tables

| # | Table Name | Mô tả | Loại |
|---|------------|-------|------|
| 1 | `users` | Người dùng (khách hàng & admin) | Core |
| 2 | `movies` | Thông tin phim | Core |
| 3 | `genres` | Thể loại phim | Lookup |
| 4 | `movie_genres` | Quan hệ phim-thể loại (n-n) | Junction |
| 5 | `screen_types` | Loại màn hình (2D, 3D, IMAX) | Lookup |
| 6 | `seat_types` | Loại ghế (Standard, VIP, Couple) | Lookup |
| 7 | `rooms` | Phòng chiếu | Core |
| 8 | `seats` | Ghế ngồi trong phòng | Core |
| 9 | `showtimes` | Suất chiếu | Core |
| 10 | `showtime_prices` | Giá vé theo suất & loại ghế | Lookup |
| 11 | `showtime_seats` | Trạng thái ghế trong suất chiếu | Status |
| 12 | `bookings` | Đơn đặt vé | Transaction |
| 13 | `reviews` | Đánh giá phim | Content |

### Sơ đồ quan hệ tổng quan

```
users (1) ──────< bookings (n) ──────< booking_seats (n) ────> seats (1)
  │                   │                                           │
  │                   └──────> showtimes (1)                      │
  │                                │                              │
  └──────< reviews (n) ────> movies (1) <────┐                   │
                                  │           │                   │
                         movie_genres (n-n) genres               │
                                  │                               │
                            showtimes <──────────── rooms ────<───┘
                                  │                   │
                         showtime_prices      screen_types
                                  │
                         showtime_seats
```

---

## 🛠️ BƯỚC 1: TẠO SQL FILE

### 1.1. Tạo thư mục mySQL

```bash
# Windows
mkdir mySQL

# Mac/Linux
mkdir mySQL
```

### 1.2. Tạo file schema SQL

Tạo file **`mySQL/schema.sql`** với nội dung sau:

```sql
-- ============================================
-- CINEBOOK DATABASE SCHEMA
-- Cinema Booking System
-- ============================================

-- Drop database if exists (careful in production!)
DROP DATABASE IF EXISTS cinebook;

-- Create database
CREATE DATABASE cinebook
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE cinebook;

-- ============================================
-- TABLE 1: USERS
-- ============================================
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    city VARCHAR(100),
    avatar_url VARCHAR(500),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 2: GENRES (Lookup table)
-- ============================================
CREATE TABLE genres (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_name (name)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 3: MOVIES
-- ============================================
CREATE TABLE movies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    director VARCHAR(255),
    cast TEXT,
    language VARCHAR(50) DEFAULT 'Vietnamese',
    duration INT NOT NULL COMMENT 'Duration in minutes',
    release_date DATE NOT NULL,
    age_rating ENUM('P', 'T13', 'T16', 'T18', 'C') DEFAULT 'P',
    status ENUM('now_showing', 'coming_soon', 'ended') DEFAULT 'coming_soon',
    poster_url VARCHAR(500),
    trailer_url VARCHAR(500),
    rating_avg DECIMAL(3, 2) DEFAULT 0.00 COMMENT 'Average rating 0-5',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_status (status),
    INDEX idx_release_date (release_date),
    INDEX idx_rating (rating_avg)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 4: MOVIE_GENRES (Junction table n-n)
-- ============================================
CREATE TABLE movie_genres (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    movie_id BIGINT UNSIGNED NOT NULL,
    genre_id BIGINT UNSIGNED NOT NULL,

    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE,
    UNIQUE KEY unique_movie_genre (movie_id, genre_id)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 5: SCREEN_TYPES (Lookup table)
-- ============================================
CREATE TABLE screen_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE COMMENT 'e.g., 2D, 3D, IMAX',
    price DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Additional price for this screen type',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABLE 6: SEAT_TYPES (Lookup table)
-- ============================================
CREATE TABLE seat_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE COMMENT 'Standard, VIP, Couple',
    base_price DECIMAL(10, 2) NOT NULL DEFAULT 50000.00,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABLE 7: ROOMS
-- ============================================
CREATE TABLE rooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    total_rows INT NOT NULL DEFAULT 8,
    seats_per_row INT NOT NULL DEFAULT 20,
    screen_type_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (screen_type_id) REFERENCES screen_types(id),
    INDEX idx_screen_type (screen_type_id)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 8: SEATS
-- ============================================
CREATE TABLE seats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id BIGINT UNSIGNED NOT NULL,
    seat_row VARCHAR(2) NOT NULL COMMENT 'A, B, C, ..., H',
    seat_number INT NOT NULL COMMENT '1, 2, 3, ..., 20',
    seat_code VARCHAR(10) NOT NULL COMMENT 'A1, A2, ..., H20',
    seat_type_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_type_id) REFERENCES seat_types(id),
    UNIQUE KEY unique_seat (room_id, seat_code),
    INDEX idx_room (room_id),
    INDEX idx_seat_type (seat_type_id)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 9: SHOWTIMES
-- ============================================
CREATE TABLE showtimes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    movie_id BIGINT UNSIGNED NOT NULL,
    room_id BIGINT UNSIGNED NOT NULL,
    show_date DATE NOT NULL,
    show_time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    INDEX idx_movie (movie_id),
    INDEX idx_room (room_id),
    INDEX idx_date_time (show_date, show_time)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 10: SHOWTIME_PRICES
-- ============================================
CREATE TABLE showtime_prices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    showtime_id BIGINT UNSIGNED NOT NULL,
    seat_type_id BIGINT UNSIGNED NOT NULL,
    price DECIMAL(10, 2) NOT NULL COMMENT 'Final price = seat_type.base_price + screen_type.price',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_type_id) REFERENCES seat_types(id),
    UNIQUE KEY unique_showtime_seat_type (showtime_id, seat_type_id)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 11: SHOWTIME_SEATS (Status tracking)
-- ============================================
CREATE TABLE showtime_seats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    showtime_id BIGINT UNSIGNED NOT NULL,
    seat_id BIGINT UNSIGNED NOT NULL,
    status ENUM('available', 'reserved', 'booked', 'locked') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_id) REFERENCES seats(id) ON DELETE CASCADE,
    UNIQUE KEY unique_showtime_seat (showtime_id, seat_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 12: BOOKINGS
-- ============================================
CREATE TABLE bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    showtime_id BIGINT UNSIGNED NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'expired') DEFAULT 'pending',
    payment_method ENUM('momo', 'vnpay') DEFAULT 'momo',
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    expired_at TIMESTAMP NULL COMMENT '10 minutes from booking_date',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (showtime_id) REFERENCES showtimes(id),
    INDEX idx_user (user_id),
    INDEX idx_showtime (showtime_id),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 13: BOOKING_SEATS (Each row = 1 ticket)
-- ============================================
CREATE TABLE booking_seats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    showtime_id BIGINT UNSIGNED NOT NULL,
    seat_id BIGINT UNSIGNED NOT NULL,
    price DECIMAL(10, 2) NOT NULL COMMENT 'Price at time of booking',
    qr_code VARCHAR(255) NOT NULL UNIQUE COMMENT 'QR code for check-in',
    qr_status ENUM('active', 'checked', 'cancelled') DEFAULT 'active',
    checked_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (showtime_id) REFERENCES showtimes(id),
    FOREIGN KEY (seat_id) REFERENCES seats(id),
    UNIQUE KEY unique_booking_seat (booking_id, showtime_id, seat_id),
    INDEX idx_qr_code (qr_code),
    INDEX idx_qr_status (qr_status)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 14: REVIEWS
-- ============================================
CREATE TABLE reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    movie_id BIGINT UNSIGNED NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_movie (user_id, movie_id),
    INDEX idx_movie (movie_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB;

-- ============================================
-- DONE: Schema created successfully
-- ============================================
```

📝 **Giải thích các khái niệm quan trọng**:

**PRIMARY KEY**:
- Unique identifier cho mỗi row
- AUTO_INCREMENT: Tự động tăng giá trị

**FOREIGN KEY**:
- Liên kết giữa 2 tables
- ON DELETE CASCADE: Xóa row cha → tự động xóa row con

**UNIQUE KEY**:
- Đảm bảo không duplicate
- Ví dụ: email, (showtime_id, seat_id)

**INDEX**:
- Tăng tốc độ query
- Nên tạo index cho column hay dùng trong WHERE, JOIN

**ENUM**:
- Giới hạn giá trị cho phép
- Ví dụ: status có thể là 'pending', 'confirmed', 'cancelled', 'expired'

---

## 🛠️ BƯỚC 2: IMPORT DATABASE SCHEMA

### 2.1. Import qua MySQL CLI

```bash
mysql -u root -p < mySQL/schema.sql
```

Nhập password (để trống nếu dùng XAMPP default).

### 2.2. Import qua phpMyAdmin

1. Mở `http://localhost/phpmyadmin`
2. Click "Import" tab
3. Click "Choose File" → chọn `mySQL/schema.sql`
4. Click "Go"

✅ **Checkpoint**: Bạn sẽ thấy message "Import has been successfully finished"

### 2.3. Verify tables đã tạo

```bash
mysql -u root -p cinebook
```

Trong MySQL prompt:
```sql
SHOW TABLES;
```

**Kết quả mong đợi**:
```
+---------------------+
| Tables_in_cinebook  |
+---------------------+
| booking_seats       |
| bookings            |
| genres              |
| movie_genres        |
| movies              |
| reviews             |
| rooms               |
| screen_types        |
| seat_types          |
| seats               |
| showtime_prices     |
| showtime_seats      |
| showtimes           |
| users               |
+---------------------+
14 rows in set
```

---

## 🛠️ BƯỚC 3: THÊM SAMPLE DATA

### 3.1. Tạo file data.sql

Tạo file **`mySQL/data.sql`**:

```sql
-- ============================================
-- CINEBOOK SAMPLE DATA
-- ============================================

USE cinebook;

-- ============================================
-- GENRES
-- ============================================
INSERT INTO genres (name) VALUES
('Action'),
('Comedy'),
('Drama'),
('Horror'),
('Sci-Fi'),
('Romance'),
('Thriller'),
('Animation'),
('Documentary'),
('Fantasy');

-- ============================================
-- SCREEN TYPES
-- ============================================
INSERT INTO screen_types (name, price) VALUES
('2D', 0.00),
('3D', 10000.00),
('IMAX', 30000.00),
('4DX', 50000.00);

-- ============================================
-- SEAT TYPES
-- ============================================
INSERT INTO seat_types (id, name, base_price, description) VALUES
(1, 'Standard', 50000.00, 'Standard cinema seat'),
(2, 'VIP', 80000.00, 'Premium comfortable seat with extra legroom'),
(3, 'Couple', 120000.00, 'Double seat for couples (2 seats together)');

-- ============================================
-- USERS
-- ============================================
INSERT INTO users (name, email, password, phone, city, role) VALUES
-- Admin
('Admin User', 'admin@cinebook.com', 'admin123', '0901234567', 'Ho Chi Minh', 'admin'),

-- Regular users
('John Doe', 'john@example.com', 'password123', '0902345678', 'Ho Chi Minh', 'user'),
('Jane Smith', 'jane@example.com', 'password123', '0903456789', 'Hanoi', 'user'),
('Bob Johnson', 'bob@example.com', 'password123', '0904567890', 'Da Nang', 'user'),
('Alice Williams', 'alice@example.com', 'password123', '0905678901', 'Can Tho', 'user');

-- ============================================
-- MOVIES
-- ============================================
INSERT INTO movies (title, description, director, cast, language, duration, release_date, age_rating, status, poster_url, trailer_url, rating_avg) VALUES
-- Now Showing
('Avatar: The Way of Water', 'Set more than a decade after the events of the first film, Avatar: The Way of Water begins to tell the story of the Sully family.', 'James Cameron', 'Sam Worthington, Zoe Saldana, Sigourney Weaver', 'English', 192, '2022-12-16', 'T13', 'now_showing', 'https://example.com/avatar2.jpg', 'https://youtube.com/watch?v=xyz', 4.50),

('The Batman', 'When a killer targets Gotham elite with a series of sadistic machinations, a trail of cryptic clues sends the Batman on an investigation.', 'Matt Reeves', 'Robert Pattinson, Zoë Kravitz, Paul Dano', 'English', 176, '2022-03-04', 'T16', 'now_showing', 'https://example.com/batman.jpg', 'https://youtube.com/watch?v=abc', 4.20),

('Everything Everywhere All at Once', 'An aging Chinese immigrant is swept up in an insane adventure, where she alone can save the world.', 'Daniel Kwan, Daniel Scheinert', 'Michelle Yeoh, Stephanie Hsu, Jamie Lee Curtis', 'English', 139, '2022-03-25', 'T13', 'now_showing', 'https://example.com/everything.jpg', 'https://youtube.com/watch?v=def', 4.80),

-- Coming Soon
('Oppenheimer', 'The story of J. Robert Oppenheimer and the Manhattan Project.', 'Christopher Nolan', 'Cillian Murphy, Emily Blunt, Matt Damon', 'English', 180, '2024-07-21', 'T16', 'coming_soon', 'https://example.com/oppenheimer.jpg', 'https://youtube.com/watch?v=ghi', 0.00),

('Barbie', 'Barbie and Ken are having the time of their lives in the colorful and seemingly perfect world of Barbie Land.', 'Greta Gerwig', 'Margot Robbie, Ryan Gosling, Will Ferrell', 'English', 114, '2024-07-21', 'P', 'coming_soon', 'https://example.com/barbie.jpg', 'https://youtube.com/watch?v=jkl', 0.00),

-- Ended
('Top Gun: Maverick', 'After thirty years, Maverick is still pushing the envelope as a top naval aviator.', 'Joseph Kosinski', 'Tom Cruise, Miles Teller, Jennifer Connelly', 'English', 131, '2022-05-27', 'T13', 'ended', 'https://example.com/topgun.jpg', 'https://youtube.com/watch?v=mno', 4.70);

-- ============================================
-- MOVIE_GENRES
-- ============================================
INSERT INTO movie_genres (movie_id, genre_id) VALUES
-- Avatar: Action, Sci-Fi, Fantasy
(1, 1), (1, 5), (1, 10),
-- The Batman: Action, Thriller, Drama
(2, 1), (2, 7), (2, 3),
-- Everything Everywhere: Action, Comedy, Sci-Fi
(3, 1), (3, 2), (3, 5),
-- Oppenheimer: Drama, Thriller
(4, 3), (4, 7),
-- Barbie: Comedy, Fantasy, Romance
(5, 2), (5, 10), (5, 6),
-- Top Gun: Action, Drama
(6, 1), (6, 3);

-- ============================================
-- ROOMS
-- ============================================
INSERT INTO rooms (name, total_rows, seats_per_row, screen_type_id) VALUES
('Room 1 - 2D', 8, 20, 1),
('Room 2 - 3D', 8, 20, 2),
('Room 3 - IMAX', 10, 24, 3);

-- ============================================
-- SEATS (We'll create for Room 1 only - 160 seats)
-- ============================================
-- Standard seats: A-G (rows 1-7), all 20 seats
-- VIP seats: Row H, seats 1-10
-- Couple seats: Row H, seats 11-20 (5 couple pairs)

-- Rows A-G: Standard seats (140 seats)
INSERT INTO seats (room_id, seat_row, seat_number, seat_code, seat_type_id)
SELECT 1, row_letter, seat_num, CONCAT(row_letter, seat_num), 1
FROM (
    SELECT 'A' AS row_letter UNION SELECT 'B' UNION SELECT 'C' UNION SELECT 'D'
    UNION SELECT 'E' UNION SELECT 'F' UNION SELECT 'G'
) AS rows
CROSS JOIN (
    SELECT 1 AS seat_num UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
    UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
    UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15
    UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20
) AS seat_numbers;

-- Row H: VIP seats 1-10 (10 seats)
INSERT INTO seats (room_id, seat_row, seat_number, seat_code, seat_type_id)
SELECT 1, 'H', seat_num, CONCAT('H', seat_num), 2
FROM (
    SELECT 1 AS seat_num UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
    UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
) AS vip_seats;

-- Row H: Couple seats 11-20 (10 seats = 5 couples)
INSERT INTO seats (room_id, seat_row, seat_number, seat_code, seat_type_id)
SELECT 1, 'H', seat_num, CONCAT('H', seat_num), 3
FROM (
    SELECT 11 AS seat_num UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15
    UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20
) AS couple_seats;

-- ============================================
-- SHOWTIMES (3 showtimes for testing)
-- ============================================
INSERT INTO showtimes (movie_id, room_id, show_date, show_time) VALUES
-- Avatar - Today & Tomorrow
(1, 1, CURDATE(), '14:00:00'),
(1, 1, CURDATE(), '18:00:00'),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '20:00:00'),

-- The Batman
(2, 2, CURDATE(), '15:00:00'),
(2, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '21:00:00'),

-- Everything Everywhere
(3, 1, CURDATE(), '16:30:00');

-- ============================================
-- SHOWTIME_PRICES (Auto-calculate prices)
-- ============================================
-- For each showtime, create prices for all seat types
INSERT INTO showtime_prices (showtime_id, seat_type_id, price)
SELECT
    s.id AS showtime_id,
    st.id AS seat_type_id,
    (st.base_price + scr.price) AS price
FROM showtimes s
CROSS JOIN seat_types st
JOIN rooms r ON s.room_id = r.id
JOIN screen_types scr ON r.screen_type_id = scr.id;

-- ============================================
-- SHOWTIME_SEATS (Initialize all seats as available)
-- ============================================
-- For each showtime in Room 1, create showtime_seats entries
INSERT INTO showtime_seats (showtime_id, seat_id, status)
SELECT s.id, seat.id, 'available'
FROM showtimes s
CROSS JOIN seats seat
WHERE s.room_id = seat.room_id;

-- ============================================
-- REVIEWS (Sample reviews from users)
-- ============================================
INSERT INTO reviews (user_id, movie_id, rating, comment) VALUES
-- User 2 (John) reviews
(2, 1, 5, 'Amazing visual effects! The underwater scenes were breathtaking.'),
(2, 2, 4, 'Dark and gritty. Robert Pattinson nailed the role!'),

-- User 3 (Jane) reviews
(3, 1, 4, 'Great movie but a bit too long. Still enjoyed it!'),
(3, 3, 5, 'Mind-blowing! One of the best movies I have ever seen.'),

-- User 4 (Bob) reviews
(4, 2, 5, 'Perfect Batman movie. The atmosphere was incredible.'),
(4, 6, 5, 'Top Gun Maverick exceeded all expectations!');

-- Update movie average ratings
UPDATE movies m
SET rating_avg = (
    SELECT COALESCE(AVG(r.rating), 0)
    FROM reviews r
    WHERE r.movie_id = m.id
)
WHERE id IN (SELECT DISTINCT movie_id FROM reviews);

-- ============================================
-- DONE: Sample data inserted successfully
-- ============================================
```

### 3.2. Import sample data

```bash
mysql -u root -p cinebook < mySQL/data.sql
```

**Hoặc dùng phpMyAdmin**: Import → Choose File → `data.sql` → Go

✅ **Checkpoint**: Data đã được import thành công

### 3.3. Verify data

```sql
-- Kiểm tra movies
SELECT id, title, status, rating_avg FROM movies;

-- Kiểm tra users
SELECT id, name, email, role FROM users;

-- Kiểm tra showtimes với join
SELECT
    s.id,
    m.title AS movie,
    r.name AS room,
    s.show_date,
    s.show_time
FROM showtimes s
JOIN movies m ON s.movie_id = m.id
JOIN rooms r ON s.room_id = r.id;

-- Kiểm tra seats
SELECT COUNT(*) AS total_seats, seat_type_id
FROM seats
GROUP BY seat_type_id;
```

---

## 📊 HIỂU RÕ VỀ RELATIONSHIPS

### 1. One-to-Many (1-n)

**Ví dụ**: 1 User có nhiều Bookings

```sql
users (1) ────< bookings (n)
```

**Code**:
```sql
-- User table
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255)
);

-- Bookings table
CREATE TABLE bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,  -- Foreign key
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### 2. Many-to-Many (n-n)

**Ví dụ**: Nhiều Movies có nhiều Genres

```sql
movies (n) ──── movie_genres (junction) ──── genres (n)
```

**Code**:
```sql
-- Movies table
CREATE TABLE movies (id BIGINT UNSIGNED PRIMARY KEY);

-- Genres table
CREATE TABLE genres (id BIGINT UNSIGNED PRIMARY KEY);

-- Junction table
CREATE TABLE movie_genres (
    movie_id BIGINT UNSIGNED,
    genre_id BIGINT UNSIGNED,
    FOREIGN KEY (movie_id) REFERENCES movies(id),
    FOREIGN KEY (genre_id) REFERENCES genres(id),
    UNIQUE (movie_id, genre_id)  -- Prevent duplicates
);
```

### 3. Composite Unique Key

**Ví dụ**: 1 User chỉ review 1 Movie 1 lần

```sql
CREATE TABLE reviews (
    user_id BIGINT UNSIGNED,
    movie_id BIGINT UNSIGNED,
    UNIQUE KEY (user_id, movie_id)  -- Composite unique
);
```

---

## 🎯 THỰC HÀNH

### Bài tập 1: Query cơ bản
```sql
-- Lấy tất cả phim đang chiếu
SELECT * FROM movies WHERE status = 'now_showing';

-- Đếm số suất chiếu của từng phim
SELECT movie_id, COUNT(*) AS total_showtimes
FROM showtimes
GROUP BY movie_id;
```

### Bài tập 2: JOIN tables
```sql
-- Lấy thông tin booking kèm user và movie
SELECT
    b.id,
    u.name AS user_name,
    m.title AS movie_title,
    b.total_price
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN showtimes s ON b.showtime_id = s.id
JOIN movies m ON s.movie_id = m.id;
```

### Bài tập 3: Subquery
```sql
-- Lấy phim có rating cao nhất
SELECT * FROM movies
WHERE rating_avg = (SELECT MAX(rating_avg) FROM movies);
```

---

## 📝 TÓM TẮT

### Đã học được gì?

1. **Database design**: 13 tables với relationships rõ ràng
2. **SQL constraints**: PRIMARY KEY, FOREIGN KEY, UNIQUE, INDEX
3. **Data types**: Chọn đúng type cho từng column
4. **Relationships**: 1-1, 1-n, n-n
5. **Sample data**: Tạo data để test

### Files đã tạo

```
mySQL/
├── schema.sql    # Database schema
└── data.sql      # Sample data
```

---

## 🚀 BƯỚC TIẾP THEO

**Bài tiếp**: [03. Models Step by Step →](03_models_step_by_step.md)

Trong bài tiếp theo, bạn sẽ tạo Laravel Models tương ứng với 13 tables vừa tạo.

---

**Bài trước**: [← 01. Laravel Setup](01_laravel_setup.md)
**Series**: Cinebook Tutorial
