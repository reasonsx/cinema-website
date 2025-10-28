DROP DATABASE IF EXISTS cinema;
CREATE DATABASE cinema;
USE cinema;

-- USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    isAdmin BOOLEAN DEFAULT FALSE
);

-- Admin + regular users
INSERT INTO users (email, password, firstname, lastname, isAdmin) VALUES
('admin@admin.com', '$2y$10$WCWVWm076L247LVseNjmoOjjK0wfh89U7iOBNKggl7LPfkKhU5vnW', 'Admin', 'Admin', TRUE),
('john@example.com', '123', 'John', 'Doe', FALSE),
('jane@example.com', '123', 'Jane', 'Smith', FALSE),
('bob@example.com', '123', 'Bob', 'Brown', FALSE),
('alice@example.com', '123', 'Alice', 'Johnson', FALSE),
('mike@example.com', '123', 'Mike', 'Taylor', FALSE);

-- MOVIES
CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    release_year YEAR NOT NULL,
    rating VARCHAR(10),
    description TEXT,
    length INT,
    poster VARCHAR(255)
);

INSERT INTO movies (title, release_year, rating, description, length, poster) VALUES
('Inception', 2010, 'PG-13', 'A thief who steals corporate secrets through dream-sharing technology.', 148, 'images/inception.jpg'),
('The Matrix', 1999, 'R', 'A hacker discovers the truth about his reality and fights against machines.', 136, 'images/matrix.jpg'),
('Interstellar', 2014, 'PG-13', 'Explorers travel through a wormhole to ensure humanity''s survival.', 169, 'images/interstellar.jpg'),
('The Dark Knight', 2008, 'PG-13', 'Batman battles the Joker in Gotham City.', 152, 'images/darkknight.jpg'),
('Avatar', 2009, 'PG-13', 'Humans exploit Pandora while a soldier joins the Na''vi.', 162, 'images/avatar.jpg'),
('Pulp Fiction', 1994, 'R', 'Crime stories intersect in Tarantino''s classic.', 154, 'images/pulpfiction.jpg');

-- ACTORS
CREATE TABLE actors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE,
    gender ENUM('Male','Female','Other'),
    description TEXT
);

INSERT INTO actors (first_name, last_name, date_of_birth, gender, description) VALUES
('Leonardo', 'DiCaprio', '1974-11-11', 'Male', 'Known for Inception and The Revenant.'),
('Joseph', 'Gordon-Levitt', '1981-02-17', 'Male', 'American actor known for Inception.'),
('Keanu', 'Reeves', '1964-09-02', 'Male', 'Known for The Matrix and John Wick.'),
('Carrie-Anne', 'Moss', '1967-08-21', 'Female', 'Known for The Matrix series.'),
('Matthew', 'McConaughey', '1969-11-04', 'Male', 'Known for Interstellar and Dallas Buyers Club.'),
('Anne', 'Hathaway', '1982-11-12', 'Female', 'Known for Interstellar and Les Misérables.'),
('Christian', 'Bale', '1974-01-30', 'Male', 'Known for The Dark Knight trilogy.'),
('Heath', 'Ledger', '1979-04-04', 'Male', 'Known for The Dark Knight and Brokeback Mountain.'),
('Sam', 'Worthington', '1976-08-02', 'Male', 'Starred in Avatar.'),
('Zoe', 'Saldana', '1978-06-19', 'Female', 'Played Neytiri in Avatar.'),
('John', 'Travolta', '1954-02-18', 'Male', 'Starred in Pulp Fiction.'),
('Samuel', 'Jackson', '1948-12-21', 'Male', 'Starred in Pulp Fiction.');

-- ACTOR↔MOVIE
CREATE TABLE actorAppearIn (
    actor_id INT NOT NULL,
    movie_id INT NOT NULL,
    PRIMARY KEY (actor_id, movie_id),
    FOREIGN KEY (actor_id) REFERENCES actors(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

INSERT INTO actorAppearIn VALUES
(1,1),(2,1), -- Inception
(3,2),(4,2), -- The Matrix
(5,3),(6,3), -- Interstellar
(7,4),(8,4), -- Dark Knight
(9,5),(10,5), -- Avatar
(11,6),(12,6); -- Pulp Fiction

-- DIRECTORS
CREATE TABLE directors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE,
    gender ENUM('Male','Female','Other'),
    description TEXT
);

INSERT INTO directors (first_name, last_name, date_of_birth, gender, description) VALUES
('Christopher', 'Nolan', '1970-07-30', 'Male', 'Director of Inception, Interstellar, and The Dark Knight.'),
('Lana', 'Wachowski', '1965-06-21', 'Female', 'Co-director of The Matrix series.'),
('Lilly', 'Wachowski', '1967-12-29', 'Female', 'Co-director of The Matrix series.'),
('James', 'Cameron', '1954-08-16', 'Male', 'Director of Avatar and Titanic.'),
('Quentin', 'Tarantino', '1963-03-27', 'Male', 'Director of Pulp Fiction.');

CREATE TABLE directorDirects (
    director_id INT NOT NULL,
    movie_id INT NOT NULL,
    PRIMARY KEY (director_id, movie_id),
    FOREIGN KEY (director_id) REFERENCES directors(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

INSERT INTO directorDirects VALUES
(1,1),(1,3),(1,4), -- Nolan
(2,2),(3,2),       -- Wachowskis
(4,5),             -- Cameron
(5,6);             -- Tarantino

-- SCREENING ROOMS
CREATE TABLE screening_rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    seat_price DECIMAL(6,2) NOT NULL DEFAULT 90.00
);

INSERT INTO screening_rooms (name, seat_price) VALUES
('Main Hall', 90.00),
('VIP Room', 120.00),
('Kids Room', 70.00),
('IMAX', 150.00),
('Classic Room', 80.00);

-- SEATS
CREATE TABLE IF NOT EXISTS seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    screening_room_id INT NOT NULL,
    `row_number` VARCHAR(5) NOT NULL,
    seat_number INT NOT NULL,
    FOREIGN KEY (screening_room_id) REFERENCES screening_rooms(id) ON DELETE CASCADE
);

-- Seats for Main Hall (50 seats, A–E × 10)
INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES
(1,'A',1),(1,'A',2),(1,'A',3),(1,'A',4),(1,'A',5),(1,'A',6),(1,'A',7),(1,'A',8),(1,'A',9),(1,'A',10),
(1,'B',1),(1,'B',2),(1,'B',3),(1,'B',4),(1,'B',5),(1,'B',6),(1,'B',7),(1,'B',8),(1,'B',9),(1,'B',10),
(1,'C',1),(1,'C',2),(1,'C',3),(1,'C',4),(1,'C',5),(1,'C',6),(1,'C',7),(1,'C',8),(1,'C',9),(1,'C',10),
(1,'D',1),(1,'D',2),(1,'D',3),(1,'D',4),(1,'D',5),(1,'D',6),(1,'D',7),(1,'D',8),(1,'D',9),(1,'D',10),
(1,'E',1),(1,'E',2),(1,'E',3),(1,'E',4),(1,'E',5),(1,'E',6),(1,'E',7),(1,'E',8),(1,'E',9),(1,'E',10);

-- Seats for VIP Room (20 seats, A–D × 5)
INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES
(2,'A',1),(2,'A',2),(2,'A',3),(2,'A',4),(2,'A',5),
(2,'B',1),(2,'B',2),(2,'B',3),(2,'B',4),(2,'B',5),
(2,'C',1),(2,'C',2),(2,'C',3),(2,'C',4),(2,'C',5),
(2,'D',1),(2,'D',2),(2,'D',3),(2,'D',4),(2,'D',5);

-- Seats for Kids Room (30 seats, A–C × 10)
INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES
(3,'A',1),(3,'A',2),(3,'A',3),(3,'A',4),(3,'A',5),(3,'A',6),(3,'A',7),(3,'A',8),(3,'A',9),(3,'A',10),
(3,'B',1),(3,'B',2),(3,'B',3),(3,'B',4),(3,'B',5),(3,'B',6),(3,'B',7),(3,'B',8),(3,'B',9),(3,'B',10),
(3,'C',1),(3,'C',2),(3,'C',3),(3,'C',4),(3,'C',5),(3,'C',6),(3,'C',7),(3,'C',8),(3,'C',9),(3,'C',10);

-- Seats for IMAX (60 seats, A–F × 10)
INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES
(4,'A',1),(4,'A',2),(4,'A',3),(4,'A',4),(4,'A',5),(4,'A',6),(4,'A',7),(4,'A',8),(4,'A',9),(4,'A',10),
(4,'B',1),(4,'B',2),(4,'B',3),(4,'B',4),(4,'B',5),(4,'B',6),(4,'B',7),(4,'B',8),(4,'B',9),(4,'B',10),
(4,'C',1),(4,'C',2),(4,'C',3),(4,'C',4),(4,'C',5),(4,'C',6),(4,'C',7),(4,'C',8),(4,'C',9),(4,'C',10),
(4,'D',1),(4,'D',2),(4,'D',3),(4,'D',4),(4,'D',5),(4,'D',6),(4,'D',7),(4,'D',8),(4,'D',9),(4,'D',10),
(4,'E',1),(4,'E',2),(4,'E',3),(4,'E',4),(4,'E',5),(4,'E',6),(4,'E',7),(4,'E',8),(4,'E',9),(4,'E',10),
(4,'F',1),(4,'F',2),(4,'F',3),(4,'F',4),(4,'F',5),(4,'F',6),(4,'F',7),(4,'F',8),(4,'F',9),(4,'F',10);

-- Seats for Classic Room (40 seats, A–D × 10)
INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES
(5,'A',1),(5,'A',2),(5,'A',3),(5,'A',4),(5,'A',5),(5,'A',6),(5,'A',7),(5,'A',8),(5,'A',9),(5,'A',10),
(5,'B',1),(5,'B',2),(5,'B',3),(5,'B',4),(5,'B',5),(5,'B',6),(5,'B',7),(5,'B',8),(5,'B',9),(5,'B',10),
(5,'C',1),(5,'C',2),(5,'C',3),(5,'C',4),(5,'C',5),(5,'C',6),(5,'C',7),(5,'C',8),(5,'C',9),(5,'C',10),
(5,'D',1),(5,'D',2),(5,'D',3),(5,'D',4),(5,'D',5),(5,'D',6),(5,'D',7),(5,'D',8),(5,'D',9),(5,'D',10);

-- SCREENINGS
CREATE TABLE screenings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    screening_room_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (screening_room_id) REFERENCES screening_rooms(id) ON DELETE CASCADE
);
INSERT INTO screenings (movie_id, screening_room_id, start_time, end_time) VALUES
-- Core initial screenings
(1, 1, '2025-10-21 18:00:00', '2025-10-21 20:30:00'),
(2, 2, '2025-10-22 19:00:00', '2025-10-22 21:20:00'),
(3, 3, '2025-10-23 14:00:00', '2025-10-23 16:50:00'),
(4, 4, '2025-10-23 20:00:00', '2025-10-23 22:30:00'),
(5, 1, '2025-10-24 16:00:00', '2025-10-24 18:45:00'),
(6, 5, '2025-10-25 20:30:00', '2025-10-25 23:00:00'),

-- Inception (Main Hall, IMAX)
(1, 1, '2025-10-26 14:00:00', '2025-10-26 16:30:00'),
(1, 4, '2025-10-26 18:30:00', '2025-10-26 21:00:00'),
(1, 1, '2025-10-27 12:00:00', '2025-10-27 14:30:00'),
(1, 1, '2025-10-27 18:00:00', '2025-10-27 20:30:00'),
(1, 4, '2025-10-28 20:00:00', '2025-10-28 22:30:00'),
(1, 4, '2025-10-29 21:00:00', '2025-10-29 23:30:00'),

-- The Matrix (Classic Room, IMAX, VIP)
(2, 2, '2025-10-26 17:00:00', '2025-10-26 19:20:00'),
(2, 4, '2025-10-26 21:30:00', '2025-10-26 23:50:00'),
(2, 5, '2025-10-27 15:00:00', '2025-10-27 17:20:00'),
(2, 2, '2025-10-27 19:30:00', '2025-10-27 21:50:00'),
(2, 5, '2025-10-28 20:00:00', '2025-10-28 22:20:00'),
(2, 4, '2025-10-29 22:30:00', '2025-10-30 00:50:00'),

-- Interstellar (Main Hall, IMAX)
(3, 1, '2025-10-26 13:30:00', '2025-10-26 16:15:00'),
(3, 4, '2025-10-26 19:00:00', '2025-10-26 22:00:00'),
(3, 1, '2025-10-27 14:30:00', '2025-10-27 17:15:00'),
(3, 4, '2025-10-27 20:30:00', '2025-10-27 23:20:00'),
(3, 1, '2025-10-28 12:00:00', '2025-10-28 14:45:00'),

-- The Dark Knight (IMAX, Main Hall, VIP)
(4, 4, '2025-10-26 21:00:00', '2025-10-26 23:30:00'),
(4, 1, '2025-10-27 17:30:00', '2025-10-27 20:00:00'),
(4, 2, '2025-10-28 22:30:00', '2025-10-29 00:50:00'),
(4, 1, '2025-10-29 17:00:00', '2025-10-29 19:30:00'),
(4, 4, '2025-10-30 20:30:00', '2025-10-30 23:00:00'),

-- Avatar (IMAX, Main Hall, Kids Room)
(5, 4, '2025-10-26 10:30:00', '2025-10-26 13:15:00'),
(5, 1, '2025-10-26 17:00:00', '2025-10-26 19:45:00'),
(5, 3, '2025-10-26 20:00:00', '2025-10-26 22:45:00'),
(5, 4, '2025-10-27 19:00:00', '2025-10-27 21:45:00'),
(5, 1, '2025-10-28 14:00:00', '2025-10-28 16:45:00'),

-- Pulp Fiction (Classic Room, Main Hall, VIP)
(6, 5, '2025-10-26 15:00:00', '2025-10-26 17:30:00'),
(6, 1, '2025-10-26 19:30:00', '2025-10-26 22:00:00'),
(6, 2, '2025-10-27 22:30:00', '2025-10-28 01:00:00'),
(6, 5, '2025-10-28 19:00:00', '2025-10-28 21:30:00'),
(6, 1, '2025-10-29 22:00:00', '2025-10-30 00:30:00'),

-- Continuous schedule into November (for realism)
(1, 1, '2025-11-01 14:00:00', '2025-11-01 16:30:00'),
(2, 2, '2025-11-01 17:00:00', '2025-11-01 19:20:00'),
(3, 4, '2025-11-01 20:00:00', '2025-11-01 22:50:00'),
(4, 5, '2025-11-02 18:00:00', '2025-11-02 20:30:00'),
(5, 1, '2025-11-02 21:00:00', '2025-11-02 23:45:00'),
(6, 2, '2025-11-03 19:30:00', '2025-11-03 22:00:00'),
(1, 4, '2025-11-04 16:30:00', '2025-11-04 19:00:00'),
(2, 5, '2025-11-04 20:00:00', '2025-11-04 22:20:00'),
(3, 3, '2025-11-05 15:00:00', '2025-11-05 17:45:00'),
(4, 1, '2025-11-05 18:30:00', '2025-11-05 21:00:00'),
(5, 4, '2025-11-06 21:00:00', '2025-11-06 23:45:00'),
(6, 5, '2025-11-06 22:30:00', '2025-11-07 01:00:00'),

-- Inception (Main Hall + IMAX, multiple times per day)
(1, 1, '2025-10-30 14:00:00', '2025-10-30 16:30:00'),
(1, 4, '2025-10-30 18:00:00', '2025-10-30 20:30:00'),
(1, 1, '2025-10-31 12:00:00', '2025-10-31 14:30:00'),
(1, 4, '2025-10-31 16:00:00', '2025-10-31 18:30:00'),
(1, 1, '2025-11-01 14:30:00', '2025-11-01 17:00:00'),
(1, 4, '2025-11-01 19:00:00', '2025-11-01 21:30:00'),

-- The Matrix (Classic + VIP + IMAX)
(2, 5, '2025-10-30 15:00:00', '2025-10-30 17:20:00'),
(2, 2, '2025-10-30 18:00:00', '2025-10-30 20:20:00'),
(2, 4, '2025-10-31 20:00:00', '2025-10-31 22:20:00'),
(2, 5, '2025-11-01 13:00:00', '2025-11-01 15:20:00'),
(2, 2, '2025-11-01 16:00:00', '2025-11-01 18:20:00'),
(2, 4, '2025-11-02 19:00:00', '2025-11-02 21:20:00'),

-- Interstellar (Main Hall + IMAX)
(3, 1, '2025-10-30 13:30:00', '2025-10-30 16:15:00'),
(3, 4, '2025-10-30 17:30:00', '2025-10-30 20:15:00'),
(3, 1, '2025-10-31 14:00:00', '2025-10-31 16:45:00'),
(3, 4, '2025-10-31 18:00:00', '2025-10-31 20:45:00'),
(3, 1, '2025-11-01 12:00:00', '2025-11-01 14:45:00'),
(3, 4, '2025-11-01 15:30:00', '2025-11-01 18:15:00'),

-- The Dark Knight (IMAX, Main Hall, VIP)
(4, 4, '2025-10-31 21:00:00', '2025-10-31 23:30:00'),
(4, 1, '2025-11-01 17:30:00', '2025-11-01 20:00:00'),
(4, 2, '2025-11-01 21:30:00', '2025-11-02 00:00:00'),
(4, 4, '2025-11-02 20:00:00', '2025-11-02 22:30:00'),
(4, 1, '2025-11-03 16:00:00', '2025-11-03 18:30:00'),
(4, 2, '2025-11-03 19:00:00', '2025-11-03 21:30:00'),

-- Avatar (IMAX + Main Hall + Kids Room)
(5, 4, '2025-10-31 10:00:00', '2025-10-31 12:45:00'),
(5, 1, '2025-10-31 14:00:00', '2025-10-31 16:45:00'),
(5, 3, '2025-10-31 17:00:00', '2025-10-31 19:45:00'),
(5, 4, '2025-11-01 16:00:00', '2025-11-01 18:45:00'),
(5, 1, '2025-11-01 19:00:00', '2025-11-01 21:45:00'),
(5, 3, '2025-11-02 12:00:00', '2025-11-02 14:45:00'),

-- Pulp Fiction (Classic + Main Hall + VIP)
(6, 5, '2025-10-31 15:30:00', '2025-10-31 18:00:00'),
(6, 1, '2025-10-31 19:00:00', '2025-10-31 21:30:00'),
(6, 2, '2025-11-01 20:00:00', '2025-11-01 22:30:00'),
(6, 5, '2025-11-02 18:00:00', '2025-11-02 20:30:00'),
(6, 1, '2025-11-02 21:00:00', '2025-11-02 23:30:00'),
(6, 2, '2025-11-03 17:00:00', '2025-11-03 19:30:00');

-- BOOKINGS
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    screening_id INT NOT NULL,
    booking_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_price DECIMAL(10,2),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (screening_id) REFERENCES screenings(id) ON DELETE CASCADE
);

CREATE TABLE booking_seats (
    booking_id INT NOT NULL,
    seat_id INT NOT NULL,
    screening_id INT NOT NULL,
    PRIMARY KEY (booking_id, seat_id, screening_id),
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_id) REFERENCES seats(id) ON DELETE CASCADE,
    FOREIGN KEY (screening_id) REFERENCES screenings(id) ON DELETE CASCADE,
    UNIQUE (seat_id, screening_id)
);

-- BOOKING DATA
INSERT INTO bookings (user_id, screening_id, total_price)
VALUES (2, 1, 180.00);
SET @b1 = LAST_INSERT_ID();
INSERT INTO booking_seats VALUES (@b1, 1, 1), (@b1, 2, 1);

INSERT INTO bookings (user_id, screening_id, total_price)
VALUES (3, 2, 240.00);
SET @b2 = LAST_INSERT_ID();
INSERT INTO booking_seats VALUES (@b2, 51, 2), (@b2, 52, 2);

INSERT INTO bookings (user_id, screening_id, total_price)
VALUES (4, 3, 140.00);
SET @b3 = LAST_INSERT_ID();
INSERT INTO booking_seats VALUES (@b3, 91, 3), (@b3, 92, 3);

INSERT INTO bookings (user_id, screening_id, total_price)
VALUES (5, 4, 300.00);
SET @b4 = LAST_INSERT_ID();
INSERT INTO booking_seats VALUES (@b4, 121, 4), (@b4, 122, 4);

INSERT INTO bookings (user_id, screening_id, total_price)
VALUES (6, 5, 160.00);
SET @b5 = LAST_INSERT_ID();
INSERT INTO booking_seats VALUES (@b5, 11, 5), (@b5, 12, 5);



-- NEWS TABLE
CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    date_added DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- SAMPLE NEWS DATA
INSERT INTO news (title, content, date_added) VALUES
('New IMAX Screen Installed!', 'We are excited to announce the installation of a brand new IMAX screen in our cinema for a truly immersive experience.', '2025-10-10 12:00:00'),
('Halloween Horror Night 2025', 'Join us on October 31st for a special late-night marathon of classic horror movies!', '2025-10-15 09:30:00'),
('Student Discount Week', 'Show your student ID at the counter and enjoy 30% off all tickets from October 20th to 27th.', '2025-10-18 11:45:00'),
('Avatar Returns in 3D', 'Experience the magic of Pandora again in our newly upgraded 3D projection room.', '2025-10-19 16:00:00'),
('Technical Upgrade Completed', 'All screening rooms have been equipped with new Dolby Atmos sound systems for enhanced audio experience.', '2025-10-17 14:20:00');