#CREATE DATABASE
CREATE DATABASE IF NOT EXISTS cinema_booking
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE cinema_booking;

#USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) UNIQUE,
    city VARCHAR(100),
    avatar_url TEXT,
    role ENUM('user','admin') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_role (role)
);

#MOVIES
CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    genre VARCHAR(100),
    language VARCHAR(50),
    director VARCHAR(150),
    actors TEXT,
    duration INT,
    release_date DATE,
    age_rating ENUM('P','T13','T16','T18','C'),
    status ENUM('now_showing','coming_soon','ended'),
    poster_url TEXT,
    trailer_url TEXT,
    description TEXT,
    rating_avg DECIMAL(3,2),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_movies_release (release_date),
    INDEX idx_movies_status (status),
    INDEX idx_movies_rating (rating_avg)
);

#THEATERS
CREATE TABLE theaters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    city VARCHAR(100),
    address VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

#ROOMS
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    theater_id INT NOT NULL,
    name VARCHAR(50),
    total_rows INT,
    seats_per_row INT,
    screen_type ENUM('2D','3D','IMAX'),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_rooms_theater (theater_id),
    FOREIGN KEY (theater_id) REFERENCES theaters(id)
);

#SEAT_TYPES
CREATE TABLE seat_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    base_price DECIMAL(10,2) NOT NULL,
    description TEXT
);

#SEATS
CREATE TABLE seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    seat_row VARCHAR(5),
    seat_number INT,
    seat_code VARCHAR(10),
    seat_type_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_seats_room (room_id),
    INDEX idx_seats_type (seat_type_id),
    UNIQUE KEY uq_room_seat (room_id, seat_code),
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    FOREIGN KEY (seat_type_id) REFERENCES seat_types(id)
);

#SHOWTIMES
CREATE TABLE showtimes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    room_id INT NOT NULL,
    show_date DATE,
    show_time TIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_show_date (show_date),
    INDEX idx_show_time (show_time),
    FOREIGN KEY (movie_id) REFERENCES movies(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

#SHOWTIMES_PRICES
CREATE TABLE showtime_prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    showtime_id INT NOT NULL,
    seat_type_id INT NOT NULL,
    price DECIMAL(10,2),
    FOREIGN KEY (showtime_id) REFERENCES showtimes(id),
    FOREIGN KEY (seat_type_id) REFERENCES seat_types(id)
);

#SHOWTIMES_SEATS
CREATE TABLE showtime_seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    showtime_id INT NOT NULL,
    seat_id INT NOT NULL,
    status ENUM('available','booked','locked') DEFAULT 'available',
    INDEX idx_status (status),
    FOREIGN KEY (showtime_id) REFERENCES showtimes(id),
    FOREIGN KEY (seat_id) REFERENCES seats(id)
);

#BOOKINGS
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    showtime_id INT NOT NULL,
    booking_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_price DECIMAL(10,2),
    status ENUM('pending','confirmed','cancelled','expired'),
    payment_method ENUM('cash','card','momo','vnpay'),
    payment_status ENUM('pending','paid','refunded'),
    qr_code TEXT,
    expired_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_booking_status (status),
    INDEX idx_payment_status (payment_status),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (showtime_id) REFERENCES showtimes(id)
);

#BOOKING_SEATS
CREATE TABLE booking_seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    showtime_id INT NOT NULL,
    seat_id INT NOT NULL,
    price DECIMAL(10,2),
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (showtime_id) REFERENCES showtimes(id),
    FOREIGN KEY (seat_id) REFERENCES seats(id)
);

#REVIEWS
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    rating TINYINT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_movie_review (movie_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (movie_id) REFERENCES movies(id)
);
