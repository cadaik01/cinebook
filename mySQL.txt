#Users
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    city VARCHAR(100),
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

#Movies
CREATE TABLE movies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    genre VARCHAR(100),
    language VARCHAR(100),
    duration INT,
    trailer_url VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

#theaters
CREATE TABLE theaters (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    city VARCHAR(100),
    address VARCHAR(255),
    seat_capacity INT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

#showtime
CREATE TABLE showtimes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    movie_id BIGINT UNSIGNED NOT NULL,
    theater_id BIGINT UNSIGNED NOT NULL,
    show_date DATE,
    show_time TIME,
    price_gold DECIMAL(8,2),
    price_platinum DECIMAL(8,2),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (theater_id) REFERENCES theaters(id) ON DELETE CASCADE
);

#Booking
CREATE TABLE bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    showtime_id BIGINT UNSIGNED NOT NULL,
    booking_date DATETIME,
    total_price DECIMAL(10,2),
    status VARCHAR(50) DEFAULT 'confirmed',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE
);

#Room
CREATE TABLE rooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    theater_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(50) NOT NULL,
    seat_capacity INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);


#Seat
CREATE TABLE seats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id BIGINT UNSIGNED NOT NULL,
    seat_row CHAR(1) NOT NULL,
    seat_number INT NOT NULL,
    seat_code VARCHAR(5) NOT NULL,
    seat_type VARCHAR(20) NOT NULL DEFAULT 'standard',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

#Booking Seat
CREATE TABLE booking_seats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    seat_id BIGINT UNSIGNED NOT NULL,

    CONSTRAINT fk_booking_seats_booking
        FOREIGN KEY (booking_id)
        REFERENCES bookings(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_booking_seats_seat
        FOREIGN KEY (seat_id)
        REFERENCES seats(id)
        ON DELETE CASCADE,

    UNIQUE (booking_id, seat_id)
);

#Review
CREATE TABLE reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    movie_id BIGINT UNSIGNED NOT NULL,
    rating INT,
    comment TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);
