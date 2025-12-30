#screen_type
INSERT INTO screen_types (id, name, price) VALUES
(1, '2D', 0),
(2, '3D', 40000),
(3, 'IMAX 4D', 60000);

#seat_types
INSERT INTO seat_types (id, name, base_price, description) VALUES
(1, 'standard', 0, 'Ghế thường'),
(2, 'vip', 10000, 'Ghế VIP'),
(3, 'couple', 20000, 'Ghế đôi');

#rooms
INSERT INTO rooms (id, name, total_rows, seats_per_row, screen_type_id) VALUES
(1, 'Room 1', 8, 18, 1), -- 2D
(2, 'Room 2', 8, 18, 1), -- 2D
(3, 'Room 3', 8, 18, 2), -- 3D
(4, 'Room 4', 8, 18, 3); -- IMAX 4D

#movies
INSERT INTO movies 
(title, genre, language, director, duration, release_date, age_rating, status, description)
VALUES
-- Action
('Action Hero', 'Action', 'English', 'John Lee', 120, '2024-11-01', 'T16', 'now_showing', 'Action movie 1'),
('Fast Strike', 'Action', 'English', 'David Kim', 110, '2024-12-01', 'T16', 'now_showing', 'Action movie 2'),
('Last Warrior', 'Action', 'English', 'Alan Wong', 130, '2023-06-01', 'T18', 'ended', 'Action movie 3'),

-- Comedy
('Funny Days', 'Comedy', 'English', 'Tom White', 100, '2024-10-10', 'P', 'now_showing', 'Comedy movie 1'),
('Laugh Out Loud', 'Comedy', 'English', 'Peter Chan', 95, '2024-12-15', 'P', 'coming_soon', 'Comedy movie 2'),
('Crazy Family', 'Comedy', 'English', 'Sam Lee', 105, '2023-05-01', 'P', 'ended', 'Comedy movie 3'),

-- Horror
('Night Fear', 'Horror', 'English', 'James Wan', 110, '2024-10-20', 'T18', 'now_showing', 'Horror movie 1'),
('Dark House', 'Horror', 'English', 'Mike Chen', 115, '2024-12-25', 'T18', 'coming_soon', 'Horror movie 2'),
('Bloody Room', 'Horror', 'English', 'Ken Park', 100, '2022-10-01', 'T18', 'ended', 'Horror movie 3'),

-- Romance
('Love Story', 'Romance', 'English', 'Anna Smith', 120, '2024-11-15', 'T13', 'now_showing', 'Romance movie 1'),
('Forever Us', 'Romance', 'English', 'Linda Ray', 125, '2024-12-30', 'T13', 'coming_soon', 'Romance movie 2'),
('Broken Heart', 'Romance', 'English', 'Chris Nolan', 118, '2023-02-01', 'T16', 'ended', 'Romance movie 3'),

-- Sci-Fi
('Future World', 'Sci-Fi', 'English', 'Ridley Scott', 135, '2024-12-05', 'T13', 'coming_soon', 'Sci-fi movie 1'),
('Space War', 'Sci-Fi', 'English', 'George Lucas', 140, '2023-08-01', 'T13', 'ended', 'Sci-fi movie 2'),
('AI Reborn', 'Sci-Fi', 'English', 'Steven AI', 128, '2024-11-20', 'T16', 'now_showing', 'Sci-fi movie 3');

#seats
DELIMITER $$

CREATE PROCEDURE seed_seats()
BEGIN
    DECLARE r INT DEFAULT 1;
    DECLARE s INT;
    DECLARE row_char CHAR(1);
    DECLARE seat_type INT;

    WHILE r <= 4 DO
        SET row_char = 'A';
        WHILE row_char <= 'H' DO
            SET s = 1;
            WHILE s <= 18 DO

                IF row_char = 'H' THEN
                    SET seat_type = 3; -- couple
                ELSEIF row_char IN ('C','D','E') AND s BETWEEN 7 AND 12 THEN
                    SET seat_type = 2; -- vip
                ELSE
                    SET seat_type = 1; -- standard
                END IF;

                INSERT INTO seats (room_id, seat_row, seat_number, seat_code, seat_type_id)
                VALUES (r, row_char, s, CONCAT(row_char, s), seat_type);

                SET s = s + 1;
            END WHILE;

            SET row_char = CHAR(ASCII(row_char) + 1);
        END WHILE;

        SET r = r + 1;
    END WHILE;
END$$

DELIMITER ;

CALL seed_seats();
DROP PROCEDURE seed_seats;

#users
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@cinebook.com', '123456', 'admin'),
('User One', 'user1@gmail.com', '123456', 'user'),
('User Two', 'user2@gmail.com', '123456', 'user');
