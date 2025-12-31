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
(title, genre, language, director, cast, duration, release_date, age_rating, status,
 poster_url, trailer_url, description, rating_avg)
VALUES

-- ================= NOW SHOWING (5) =================
('Dune: Part Two','Sci-Fi','English','Denis Villeneuve',
 'Timothée Chalamet, Zendaya, Rebecca Ferguson',166,'2024-03-01','T13','now_showing',
 'https://image.tmdb.org/t/p/w500/gho58bYmw9juYXm3O5q2Trb6n4x.jpg',
 'https://www.youtube.com/watch?v=Way9Dexny3w',
 'Paul Atreides unites with the Fremen to seek revenge against conspirators while fulfilling his destiny on the harsh desert planet Arrakis.',
 4.80),

('Godzilla x Kong: The New Empire','Action','English','Adam Wingard',
 'Rebecca Hall, Brian Tyree Henry, Dan Stevens',115,'2024-03-29','T13','now_showing',
 'https://image.tmdb.org/t/p/w500/z1p34vh7dEOnLDmyCrlUVLuoDzd.jpg',
 'https://www.youtube.com/watch?v=lV1OOlGwExM',
 'Godzilla and Kong must join forces to face a colossal new threat that challenges the balance of nature and humanity.',
 3.60),

('Inside Out 2','Comedy','English','Kelsey Mann',
 'Amy Poehler, Maya Hawke, Kensington Tallman',96,'2024-06-14','P','now_showing',
 'https://image.tmdb.org/t/p/w500/4Y1WNkd88JXmGfhtWR7dmDAo1T2.jpg',
 'https://www.youtube.com/watch?v=LEjhY15eCx0',
 'Riley enters her teenage years as new emotions emerge, causing unexpected chaos inside her mind and changing her emotional balance.',
 4.10),

('A Quiet Place: Day One','Horror','English','Michael Sarnoski',
 'Lupita Nyong’o, Joseph Quinn, Alex Wolff',99,'2024-06-28','T16','now_showing',
 'https://image.tmdb.org/t/p/w500/yrpPYKijwdMHyTGIOd1iK1h0Xno.jpg',
 'https://www.youtube.com/watch?v=YPY7J-flzE8',
 'The origin story reveals how terrifying alien creatures invaded Earth and forced humanity to survive in complete silence.',
 3.40),

('Civil War','Drama','English','Alex Garland',
 'Kirsten Dunst, Wagner Moura, Cailee Spaeny',109,'2024-04-12','T16','now_showing',
 'https://image.tmdb.org/t/p/w500/sh7Rg8Er3tFcN9BpKIPOMvALgZd.jpg',
 'https://www.youtube.com/watch?v=aDyQxtg0V2w',
 'Journalists travel across a divided America during a modern civil war while documenting the collapse of society and truth.',
 3.90),

-- ================= COMING SOON (5) =================
('Avatar 3','Sci-Fi','English','James Cameron',
 'Sam Worthington, Zoe Saldaña, Sigourney Weaver',180,'2025-12-19','T13','coming_soon',
 'https://image.tmdb.org/t/p/w500/8S6iJXw1yTj6kRXKuX3sI5Yucs5.jpg',
 'https://www.youtube.com/watch?v=d9MyW72ELq0',
 'The next chapter in the Avatar saga explores new regions of Pandora while expanding the conflict between humans and the Na’vi.',
 0),

('The Batman: Part II','Action','English','Matt Reeves',
 'Robert Pattinson, Zoë Kravitz, Barry Keoghan',155,'2025-10-03','T16','coming_soon',
 'https://image.tmdb.org/t/p/w500/4zQp9Q2Z0nX0Z2k0n2j1Z7r7k7P.jpg',
 'https://www.youtube.com/watch?v=mqqft2x_Aa4',
 'Batman continues his battle against crime in Gotham City while facing deeper psychological and moral challenges.',
 0),

('Frozen III','Comedy','English','Jennifer Lee',
 'Idina Menzel, Kristen Bell, Jonathan Groff',115,'2025-11-27','P','coming_soon',
 'https://image.tmdb.org/t/p/w500/6k8Y6n9P4zEw2r1jK0oJ7KqkN8Q.jpg',
 'https://www.youtube.com/watch?v=TbQm5doF_Uc',
 'Elsa and Anna embark on a new journey that tests their bond while uncovering secrets about their magical origins.',
 0),

('Kung Fu Panda 5','Comedy','English','Mike Mitchell',
 'Jack Black, Awkwafina, Dustin Hoffman',110,'2025-06-01','P','coming_soon',
 'https://image.tmdb.org/t/p/w500/kuf6dutpsT0vSVehic3EZIhhu9P.jpg',
 'https://www.youtube.com/watch?v=_inKs4eeHiI',
 'Po faces his greatest challenge yet as he trains a new generation of warriors while protecting the Valley of Peace.',
 0),

('Mission: Impossible – Dead Reckoning Part Two','Action','English','Christopher McQuarrie',
 'Tom Cruise, Hayley Atwell, Simon Pegg',170,'2025-05-23','T13','coming_soon',
 'https://image.tmdb.org/t/p/w500/5eY0s8R5G7V9X0lP1r9r8pF0WcA.jpg',
 'https://www.youtube.com/watch?v=fsQgc9pCyDU',
 'Ethan Hunt confronts the ultimate consequences of his choices while racing against time to stop a global catastrophe.',
 0),

-- ================= ENDED (5) =================
('Avengers: Endgame','Action','English','Anthony Russo, Joe Russo',
 'Robert Downey Jr., Chris Evans, Scarlett Johansson',181,'2019-04-26','T13','ended',
 'https://image.tmdb.org/t/p/w500/or06FN3Dka5tukK1e9sl16pB3iy.jpg',
 'https://www.youtube.com/watch?v=TcMBFSGVi1c',
 'The Avengers assemble one final time to undo the devastation caused by Thanos and restore balance to the universe.',
 4.90),

('Titanic','Drama','English','James Cameron',
 'Leonardo DiCaprio, Kate Winslet',195,'1997-12-19','T13','ended',
 'https://image.tmdb.org/t/p/w500/9xjZS2rlVxm8SFx8kPC3aIGCOYQ.jpg',
 'https://www.youtube.com/watch?v=kVrqfYjkTdQ',
 'A timeless love story unfolds aboard the ill-fated Titanic as two young lovers face tragedy and fate.',
 4.50),

('The Conjuring','Horror','English','James Wan',
 'Patrick Wilson, Vera Farmiga',112,'2013-07-19','T16','ended',
 'https://image.tmdb.org/t/p/w500/wVYREutTvI2tmxr6ujrHT704wGF.jpg',
 'https://www.youtube.com/watch?v=k10ETZ41q5o',
 'Paranormal investigators help a family terrorized by dark supernatural forces inside their remote farmhouse.',
 4.80),

('Forrest Gump','Drama','English','Robert Zemeckis',
 'Tom Hanks, Robin Wright',142,'1994-07-06','P','ended',
 'https://image.tmdb.org/t/p/w500/arw2vcBveWOVZr6pxd9XTd1TdQa.jpg',
 'https://www.youtube.com/watch?v=bLvqoHBptjg',
 'A simple man unintentionally influences historical events while pursuing love, friendship, and personal happiness.',
 4.80),

('Inception','Sci-Fi','English','Christopher Nolan',
 'Leonardo DiCaprio, Joseph Gordon-Levitt',148,'2010-07-16','T13','ended',
 'https://image.tmdb.org/t/p/w500/9gk7adHYeDvHkCSEqAvQNLV5Uge.jpg',
 'https://www.youtube.com/watch?v=YoHD9XEInc0',
 'A skilled thief enters people’s dreams to steal secrets, but a final mission pushes reality and imagination to the limit.',
 4.70);


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
