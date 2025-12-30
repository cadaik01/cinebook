#screen_type
INSERT INTO screen_types (id, name, price) VALUES
(1, '2D', 0),
(2, '3D', 40000),
(3, 'IMAX 4D', 60000);

#seat_types
INSERT INTO seat_types (id, name, base_price, description) VALUES
(1, 'standard', 0, 'standard seat'),
(2, 'vip', 10000, 'VIP seat'),
(3, 'couple', 20000, 'Couple seat');

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
//first 4 rooms, rows A-H, seats 1-18
INSERT INTO seats (room_id, seat_row, seat_number, seat_code, seat_type_id)
SELECT
    r.id,
    sr.row_char,
    sn.seat_number,
    CONCAT(sr.row_char, sn.seat_number),
    1 -- standard
FROM rooms r
JOIN (
    SELECT 'A' AS row_char UNION ALL
    SELECT 'B' UNION ALL
    SELECT 'C' UNION ALL
    SELECT 'D' UNION ALL
    SELECT 'E' UNION ALL
    SELECT 'F' UNION ALL
    SELECT 'G' UNION ALL
    SELECT 'H'
) sr
JOIN (
    SELECT 1 AS seat_number UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL
    SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL
    SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL
    SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL SELECT 16 UNION ALL
    SELECT 17 UNION ALL SELECT 18
) sn
WHERE r.id BETWEEN 1 AND 4;

//then update some seats to vip

UPDATE seats
SET seat_type_id = 2
WHERE seat_row IN ('C','D','E')
  AND seat_number BETWEEN 7 AND 12;


//then update some seats to couple

UPDATE seats
SET seat_type_id = 3
WHERE seat_row = 'H';



#users
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@cinebook.com', '123456', 'admin'),
('User One', 'user1@gmail.com', '123456', 'user'),
('User Two', 'user2@gmail.com', '123456', 'user');
