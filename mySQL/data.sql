-- screen_type
INSERT INTO screen_types (id, name, price) VALUES
(1, '2D', 0),
(2, '3D', 40000),
(3, 'IMAX 4D', 60000);

-- seat_types
INSERT INTO seat_types (id, name, base_price, description) VALUES
(1, 'standard', 0, 'standard seat'),
(2, 'vip', 10000, 'VIP seat'),
(3, 'couple', 20000, 'Couple seat');

-- rooms
INSERT INTO rooms (id, name, total_rows, seats_per_row, screen_type_id) VALUES
(1, 'Room 1', 8, 18, 1), -- 2D
(2, 'Room 2', 8, 18, 1), -- 2D
(3, 'Room 3', 8, 18, 2), -- 3D
(4, 'Room 4', 8, 18, 3); -- IMAX 4D

-- movies
INSERT INTO movies
(title, genre, language, director, cast, duration, release_date, age_rating, status, poster_url, trailer_url, description, rating_avg)
VALUES

-- ================= NOW SHOWING (15) =================
(
'Avengers: Endgame','Action','English','Anthony Russo, Joe Russo',
'Robert Downey Jr., Chris Evans, Chris Hemsworth, Scarlett Johansson, Mark Ruffalo',
181,'2025-12-20','T13','now_showing',
'https://i.postimg.cc/NGPSNkJj/Avengers-Endgame.jpg',
'https://www.youtube.com/watch?v=TcMBFSGVi1c',
'After the devastating events caused by Thanos, the universe is left in ruins. With half of all life erased, the remaining Avengers struggle to cope with loss, guilt, and failure. When a new opportunity emerges to reverse the catastrophe, the team reunites for one final mission. This emotional and action-packed conclusion delivers epic battles, heartfelt sacrifices, and a powerful farewell to beloved heroes, serving as the culmination of over a decade of storytelling in the Marvel Cinematic Universe.',
4.8
),
(
'John Wick: Chapter 4','Action','English','Chad Stahelski',
'Keanu Reeves, Donnie Yen, Bill Skarsgård, Laurence Fishburne, Ian McShane',
169,'2026-01-05','T18','now_showing',
'https://i.postimg.cc/zvJdvVGY/John_Wick_Chapter_4.jpg',
'https://www.youtube.com/watch?v=qEVUtrk8_B4',
'John Wick uncovers a path to defeating the High Table, but before earning his freedom, he must confront a powerful new enemy with alliances spanning the globe. From New York to Paris, the assassin faces relentless battles that push his skills and endurance to the limit. With breathtaking choreography, intense gun-fu action, and deeper exploration of John’s code and motivations, this chapter elevates the franchise to its most ambitious level yet.',
4.7
),
(
'Parasite','Drama','Korean','Bong Joon-ho',
'Song Kang-ho, Lee Sun-kyun, Cho Yeo-jeong, Choi Woo-shik, Park So-dam',
132,'2025-12-10','T16','now_showing',
'https://i.postimg.cc/N0fZJCc9/Parasite.jpg',
'https://www.youtube.com/watch?v=5xH0HfJHsaY',
'Greed and class discrimination threaten the symbiotic relationship between the wealthy Park family and the struggling Kim family. As deception deepens, tensions escalate into shocking consequences. Blending dark comedy, suspense, and social commentary, this Oscar-winning film offers a sharp and unsettling reflection on inequality in modern society.',
4.9
),
(
'Train to Busan','Action','Korean','Yeon Sang-ho',
'Gong Yoo, Ma Dong-seok, Jung Yu-mi, Kim Su-an, Choi Woo-shik',
118,'2025-12-18','T18','now_showing',
'https://i.postimg.cc/65JgJpXY/Train_to_Busan.jpg',
'https://www.youtube.com/watch?v=pyWuHv2-Abk',
'As a zombie outbreak spreads rapidly across South Korea, passengers trapped on a high-speed train to Busan must fight for survival. Amid the chaos, personal sacrifices and human compassion emerge. The film combines intense action, emotional storytelling, and social critique, redefining the zombie genre with heart and urgency.',
4.6
),
(
'The Dark Knight','Action','English','Christopher Nolan',
'Christian Bale, Heath Ledger, Aaron Eckhart, Gary Oldman, Michael Caine',
152,'2025-12-25','T16','now_showing',
'https://i.postimg.cc/d0HpzRs7/The_Dark_Knight.jpg',
'https://www.youtube.com/watch?v=EXeTwQWrcwY',
'Batman faces his greatest psychological and moral challenge when the Joker emerges, spreading chaos throughout Gotham City. As lines between heroism and vigilantism blur, the Dark Knight must confront the true cost of justice. Featuring an iconic performance by Heath Ledger, this film is a profound exploration of order, chaos, and sacrifice.',
4.9
),
(
'Avatar','Action','English','James Cameron',
'Sam Worthington, Zoe Saldana, Sigourney Weaver, Stephen Lang, Michelle Rodriguez',
162,'2025-12-15','T13','now_showing',
'https://i.postimg.cc/kD3y0GRB/Avatar.jpg',
'https://www.youtube.com/watch?v=5PSNL1qE6VY',
'Jake Sully, a paraplegic former Marine, is sent to the distant moon Pandora as part of the Avatar Program. As he becomes immersed in the Na’vi culture, he finds himself torn between his mission and protecting a world he grows to love. With groundbreaking visuals and an immersive alien ecosystem, Avatar delivers a powerful message about colonialism, environmentalism, and identity.',
4.6
),
(
'La La Land','Romance','English','Damien Chazelle',
'Ryan Gosling, Emma Stone, John Legend, Rosemarie DeWitt, J.K. Simmons',
128,'2026-01-10','T13','now_showing',
'https://i.postimg.cc/GmRWYhgB/La_La_Land.jpg',
'https://www.youtube.com/watch?v=0pdqf4P9MB8',
'Set in modern-day Los Angeles, this musical romance follows a jazz pianist and an aspiring actress as they fall in love while pursuing their dreams. As ambition and reality collide, the film explores the sacrifices required to achieve success and the bittersweet nature of love.',
4.5
),
(
'Your Name','Animation','Japanese','Makoto Shinkai',
'Ryunosuke Kamiki, Mone Kamishiraishi, Masami Nagasawa, Etsuko Ichihara, Ryo Narita',
112,'2025-12-22','T13','now_showing',
'https://i.postimg.cc/NfqSqjc7/Your_Name.jpg',
'https://www.youtube.com/watch?v=xU47nhruN-Q',
'Two teenagers living in different parts of Japan mysteriously begin swapping bodies. As they struggle to understand this strange connection, a deeper bond forms across time and space. Visually stunning and emotionally resonant, the film blends romance, fantasy, and destiny.',
4.7
),
(
'Spirited Away','Animation','Japanese','Hayao Miyazaki',
'Rumi Hiiragi, Miyu Irino, Mari Natsuki, Takashi Naito, Yasuko Sawaguchi',
125,'2025-12-28','P','now_showing',
'https://i.postimg.cc/hGPNyZBq/Spirited_Away.jpg',
'https://www.youtube.com/watch?v=ByXuk9QqQkk',
'Chihiro wanders into a mysterious spirit world ruled by gods, witches, and strange creatures. Forced to work in a bathhouse to save her parents, she embarks on a journey of courage and self-discovery. A timeless animated masterpiece filled with imagination and emotional depth.',
4.8
),
(
'Intouchables','Comedy','French','Olivier Nakache, Éric Toledano',
'François Cluzet, Omar Sy, Anne Le Ny, Audrey Fleurot, Clotilde Mollet',
112,'2026-01-12','P','now_showing',
'https://i.postimg.cc/jSTpNd3S/Intouchables.jpg',
'https://www.youtube.com/watch?v=34WIbmXkewU',
'Based on a true story, this heartwarming comedy follows the unlikely friendship between a wealthy quadriplegic and his caregiver from a disadvantaged background. Through humor and honesty, the film explores dignity, empathy, and the joy of human connection.',
4.6
),
(
'Toy Story','Animation','English','John Lasseter',
'Tom Hanks, Tim Allen, Don Rickles, Jim Varney, Annie Potts',
81,'2025-12-20','P','now_showing',
'https://i.postimg.cc/1RN2jNLg/Toy_Story.jpg',
'https://www.youtube.com/watch?v=KYz2wyBy3kc',
'When a new toy named Buzz Lightyear arrives, Woody feels threatened and jealous. As rivalry turns into friendship, the toys learn about loyalty and teamwork. Toy Story marked the beginning of a new era in animation with its charm, humor, and heartfelt storytelling.',
4.6
),
(
'The Conjuring','Horror','English','James Wan',
'Vera Farmiga, Patrick Wilson, Lili Taylor, Ron Livingston, Shanley Caswell',
112,'2026-01-08','T18','now_showing',
'https://i.postimg.cc/25cMPdk8/The_Conjuring.jpg',
'https://www.youtube.com/watch?v=k10ETZ41q5o',
'Paranormal investigators Ed and Lorraine Warren assist a family terrorized by a dark presence in their farmhouse. Drawing from real-life case files, the film delivers relentless suspense, atmospheric dread, and masterful horror storytelling.',
4.5
),
(
'Furie','Action','Vietnamese','Le Van Kiet',
'Ngo Thanh Van, Phan Thanh Nhien, Pham Anh Khoa, Mai Cat Vi, Nguyen Thanh Hoa',
98,'2025-12-18','T18','now_showing',
'https://i.postimg.cc/Ghv0HsQZ/Furie.jpg',
'https://www.youtube.com/watch?v=XiS8wL8jz3k',
'A former gangster living a quiet life is forced back into the criminal underworld when her daughter is kidnapped. Featuring intense hand-to-hand combat and emotional stakes, Furie showcases Vietnamese action cinema on an international level.',
4.4
),
(
'Forrest Gump','Drama','English','Robert Zemeckis',
'Tom Hanks, Robin Wright, Gary Sinise, Sally Field, Mykelti Williamson',
142,'2025-12-05','P','now_showing',
'https://i.postimg.cc/Fs0t7JZB/Forrest_Gump.jpg',
'https://www.youtube.com/watch?v=bLvqoHBptjg',
'Through innocence and perseverance, Forrest Gump experiences extraordinary moments across several decades of American history. The film is a heartfelt exploration of destiny, love, and the simple truths that shape a meaningful life.',
4.7
),
(
'Train to Busan','Action','Korean','Yeon Sang-ho',
'Gong Yoo, Ma Dong-seok, Jung Yu-mi, Kim Su-an, Choi Woo-shik',
118,'2026-01-02','T18','now_showing',
'https://i.postimg.cc/65JgJpXY/Train_to_Busan.jpg',
'https://www.youtube.com/watch?v=pyWuHv2-Abk',
'During a sudden zombie outbreak, passengers aboard a train fight desperately for survival while confronting moral choices and personal sacrifice. The film combines relentless action with emotional depth, redefining the zombie genre.',
4.6
),

-- ================= COMING SOON (15) =================
(
'Dune: Part Two','Action','English','Denis Villeneuve',
'Timothée Chalamet, Zendaya, Rebecca Ferguson, Josh Brolin, Austin Butler',
165,'2026-02-15','T13','coming_soon',
'https://i.postimg.cc/KvtXRgNp/Dune.jpg',
'https://www.youtube.com/watch?v=n9xhJrPXop4',
'Paul Atreides embraces his destiny among the Fremen while leading a resistance against the forces that destroyed his family. As political intrigue, prophecy, and war collide, Paul must choose between love and the fate of the universe. The film expands the rich world-building of Arrakis with breathtaking visuals, complex characters, and epic scale, continuing one of the most ambitious science-fiction sagas of modern cinema.',
4.6
),
(
'Oppenheimer','Drama','English','Christopher Nolan',
'Cillian Murphy, Emily Blunt, Robert Downey Jr., Matt Damon, Florence Pugh',
180,'2026-02-20','T16','coming_soon',
'https://i.postimg.cc/52mZmdDh/Oppenheimer.jpg',
'https://www.youtube.com/watch?v=uYPbbksJxIg',
'This biographical drama chronicles the life of J. Robert Oppenheimer, the brilliant physicist behind the Manhattan Project. As scientific triumph turns into moral conflict, the film explores ambition, responsibility, and the devastating consequences of innovation. Told through Nolan’s signature nonlinear storytelling, the film is both intellectually gripping and emotionally intense.',
4.7
),
(
'Weathering With You','Animation','Japanese','Makoto Shinkai',
'Kotaro Daigo, Nana Mori, Tsubasa Honda, Shun Oguri, Sakura Ando',
114,'2026-02-25','T13','coming_soon',
'https://i.postimg.cc/15h2h3QD/Weathering_With_You.jpg',
'https://www.youtube.com/watch?v=Q6iK6DjV_iE',
'A runaway high school boy meets a mysterious girl who possesses the ability to manipulate weather. As their bond deepens, they must confront the consequences of altering nature itself. With stunning animation and heartfelt storytelling, the film blends romance, fantasy, and environmental themes into a deeply emotional experience.',
4.5
),
(
'Finding Nemo','Animation','English','Andrew Stanton',
'Albert Brooks, Ellen DeGeneres, Alexander Gould, Willem Dafoe, Brad Garrett',
100,'2026-03-01','P','coming_soon',
'https://i.postimg.cc/Fs0t7JZP/Finding_Nemo.jpg',
'https://www.youtube.com/watch?v=SPHfeNgogVs',
'After his son Nemo is captured by a diver, an overprotective clownfish embarks on a perilous journey across the ocean. Along the way, he encounters unforgettable characters and learns the importance of trust and letting go. A heartwarming animated adventure filled with humor, emotion, and stunning underwater visuals.',
4.6
),
(
'Decision to Leave','Drama','Korean','Park Chan-wook',
'Park Hae-il, Tang Wei, Lee Jung-hyun, Go Kyung-pyo, Park Yong-woo',
138,'2026-03-05','T16','coming_soon',
'https://i.postimg.cc/PfgsXMmM/Decision_to_Leave.jpg',
'https://www.youtube.com/watch?v=Z9FJxZ2kTfs',
'A meticulous detective investigating a suspicious death becomes emotionally entangled with the deceased man’s wife. As obsession grows, the boundary between duty and desire begins to blur. Stylishly directed with layered storytelling, the film is a haunting romantic thriller exploring guilt, longing, and moral ambiguity.',
4.4
),
(
'Paddington','Comedy','English','Paul King',
'Ben Whishaw, Hugh Bonneville, Sally Hawkins, Nicole Kidman, Julie Walters',
95,'2026-03-10','P','coming_soon',
'https://i.postimg.cc/JzQ9QVS9/Paddington.jpg',
'https://www.youtube.com/watch?v=7bZFr2IA0Bo',
'A polite and curious bear from Peru travels to London in search of a new home. Taken in by the Brown family, Paddington’s kindness and optimism bring warmth and laughter to everyone he meets. A charming family film celebrating acceptance, empathy, and the meaning of home.',
4.3
),
(
'Rurouni Kenshin','Action','Japanese','Keishi Otomo',
'Takeru Satoh, Emi Takei, Munetaka Aoki, Yosuke Eguchi, Koji Kikkawa',
134,'2026-03-15','T16','coming_soon',
'https://i.postimg.cc/Jh4S6YL1/Rurouni_Kenshin.jpg',
'https://www.youtube.com/watch?v=YFWDv6bC1h4',
'A former assassin vows never to kill again while protecting the innocent during Japan’s turbulent Meiji era. Haunted by his past, Kenshin Himura must confront old enemies and inner demons. The film delivers stylish sword fights combined with emotional depth and moral reflection.',
4.4
),
(
'Blue Is the Warmest Color','Romance','French','Abdellatif Kechiche',
'Adèle Exarchopoulos, Léa Seydoux, Salim Kechiouche, Aurélien Recoing, Mona Walravens',
180,'2026-03-20','T18','coming_soon',
'https://i.postimg.cc/Kc6SG5nr/Blue_Is_the_Warmest_Colo.jpg',
'https://www.youtube.com/watch?v=EO0abB0jH9c',
'This intimate coming-of-age romance follows Adèle as she navigates love, identity, and heartbreak through her intense relationship with Emma. Told with raw honesty and emotional realism, the film explores desire, vulnerability, and the complexity of human connection.',
4.2
),
(
'Intimate Strangers','Drama','Korean','Lee Jae-kyoo',
'Cho Jin-woong, Kim Ji-soo, Park Sung-woong, Lee Seo-jin, Yum Jung-ah',
115,'2026-03-25','T16','coming_soon',
'https://i.postimg.cc/pr20rpLb/Intimate_Strangers.jpg',
'https://www.youtube.com/watch?v=kbmG5C8F9W8',
'During a dinner gathering, seven friends decide to share every message and phone call they receive. What begins as a playful game soon reveals hidden secrets that challenge trust, relationships, and personal boundaries. A gripping drama that reflects modern intimacy and digital vulnerability.',
4.3
),
(
'The Medium','Horror','Thai','Banjong Pisanthanakun',
'Narilya Gulmongkolpech, Sawanee Utoomma, Sirani Yankittikan, Yassaka Chaisorn, Boonsong Nakphoo',
130,'2026-04-01','T18','coming_soon',
'https://i.postimg.cc/NFpdHBKg/The_Medium.jpg',
'https://www.youtube.com/watch?v=wDtJ3M4arIc',
'A documentary crew follows a family of shamans in rural Thailand, uncovering terrifying supernatural possession rooted in ancient beliefs. Blending realism with escalating horror, the film delivers a deeply unsettling experience that explores faith, inheritance, and spiritual terror.',
4.1
),

-- ================= ENDED (5) =================
(
'Titanic','Romance','English','James Cameron',
'Leonardo DiCaprio, Kate Winslet, Billy Zane, Kathy Bates, Frances Fisher',
195,'1997-12-19','T13','ended',
'https://i.postimg.cc/d1gX0qTq/Titanic.jpg',
'https://www.youtube.com/watch?v=kVrqfYjkTdQ',
'A timeless epic romance set aboard the ill-fated RMS Titanic. As social class divides and human ambition collide, two young lovers fight to hold on to hope amid disaster. The film combines sweeping emotion, historical spectacle, and unforgettable performances, making it one of the most iconic films in cinematic history.',
4.8
),
(
'Forrest Gump','Drama','English','Robert Zemeckis',
'Tom Hanks, Robin Wright, Gary Sinise, Mykelti Williamson, Sally Field',
142,'1994-07-06','P','ended',
'https://i.postimg.cc/Fs0t7JZB/Forrest_Gump.jpg',
'https://www.youtube.com/watch?v=bLvqoHBptjg',
'Forrest Gump, a kind-hearted man with limited intelligence, unwittingly influences several major historical events in the United States. Through love, loss, and perseverance, the film celebrates innocence, resilience, and the unpredictable journey of life.',
4.7
),
(
'The Shawshank Redemption','Drama','English','Frank Darabont',
'Tim Robbins, Morgan Freeman, Bob Gunton, William Sadler, Clancy Brown',
142,'1994-09-23','T13','ended',
'https://i.postimg.cc/ZRXgqTyk/The_Shawshank_Redemption.jpg',
'https://www.youtube.com/watch?v=NmzuHjWmXOc',
'Two imprisoned men bond over decades, finding solace and redemption through acts of decency and hope. This powerful drama explores friendship, freedom, and the human spirit, earning its reputation as one of the greatest films ever made.',
4.9
),
(
'The Wailing','Horror','Korean','Na Hong-jin',
'Kwak Do-won, Hwang Jung-min, Chun Woo-hee, Kunimura Jun, Kim Hwan-hee',
156,'2016-05-12','T18','ended',
'https://i.postimg.cc/76jcLxTk/The_Wailing.jpg',
'https://www.youtube.com/watch?v=43uAputjI4k',
'A mysterious illness spreads through a remote village following the arrival of a stranger. As paranoia and supernatural terror escalate, a police officer struggles to uncover the horrifying truth. The film masterfully blends folklore, horror, and psychological tension.',
4.5
),
(
'Call Me by Your Name','Romance','English','Luca Guadagnino',
'Timothée Chalamet, Armie Hammer, Michael Stuhlbarg, Amira Casar, Esther Garrel',
132,'2017-11-24','T16','ended',
'https://i.postimg.cc/44Dkf1zv/Call_Me_by_Your_Name.jpg',
'https://www.youtube.com/watch?v=Z9AYPxH5NTM',
'During a summer in northern Italy, a deep and transformative romance blossoms between a teenage boy and a visiting scholar. Tenderly directed, the film captures first love, emotional awakening, and the bittersweet nature of growing up.',
4.6
),
(
'Belle','Animation','Japanese','Mamoru Hosoda',
'Kaho Nakamura, Takeru Satoh, Koji Yakusho, Ikura, Ryō Narita',
121,'2021-07-16','P','ended',
'https://i.postimg.cc/tRKQqzhW/Belle.jpg',
'https://www.youtube.com/watch?v=izIycj3j4Ow',
'A shy teenage girl discovers a massive virtual world where she can reinvent herself as a global icon. Through music and digital identity, she confronts personal trauma and finds the courage to connect with others in real life.',
4.4
),
(
'The Nun II','Horror','English','Michael Chaves',
'Taissa Farmiga, Jonas Bloquet, Storm Reid, Anna Popplewell, Bonnie Aarons',
110,'2023-09-08','T18','ended',
'https://i.postimg.cc/MTnNxcXz/The_Nun_II.jpg',
'https://www.youtube.com/watch?v=QF-oyCwaArU',
'A new chapter in the Conjuring Universe follows Sister Irene as she confronts a demonic entity terrorizing Europe. Dark, atmospheric, and suspenseful, the film expands the mythology of Valak with chilling consequences.',
4.0
);


-- seats
-- first 4 rooms, rows A-H, seats 1-18
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

-- then update some seats to vip

UPDATE seats
SET seat_type_id = 2
WHERE seat_row IN ('C','D','E')
  AND seat_number BETWEEN 7 AND 12;


-- then update some seats to couple

UPDATE seats
SET seat_type_id = 3
WHERE seat_row = 'H';



-- users
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@cinebook.com', '123456', 'admin'),
('User One', 'user1@gmail.com', '123456', 'user'),
('User Two', 'user2@gmail.com', '123456', 'user');

-- showtimes
INSERT INTO showtimes (movie_id, room_id, show_date, show_time)
VALUES
-- Movie 1
(1, 1, '2025-01-10', '10:00:00'),
(1, 1, '2025-01-10', '13:00:00'),
(1, 2, '2025-01-10', '16:00:00'),
(1, 2, '2025-01-11', '18:30:00'),
(1, 1, '2025-01-12', '20:45:00'),

-- Movie 2
(2, 1, '2025-01-10', '11:00:00'),
(2, 2, '2025-01-10', '14:30:00'),
(2, 1, '2025-01-11', '17:00:00'),
(2, 2, '2025-01-11', '19:30:00'),
(2, 1, '2025-01-12', '21:00:00');