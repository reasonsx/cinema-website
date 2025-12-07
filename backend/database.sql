DROP DATABASE IF EXISTS cinema;
CREATE DATABASE cinema;
USE cinema;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    isAdmin BOOLEAN DEFAULT FALSE
);

INSERT INTO users (email, password, firstname, lastname, isAdmin) VALUES
('admin@admin.com', '$2y$10$WCWVWm076L247LVseNjmoOjjK0wfh89U7iOBNKggl7LPfkKhU5vnW', 'Admin', 'Admin', TRUE),
('john@example.com', '123', 'John', 'Doe', FALSE),
('jane@example.com', '123', 'Jane', 'Smith', FALSE),
('bob@example.com', '123', 'Bob', 'Brown', FALSE),
('alice@example.com', '123', 'Alice', 'Johnson', FALSE),
('mike@example.com', '123', 'Mike', 'Taylor', FALSE);

CREATE TABLE movies (
id INT AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(255) NOT NULL,
release_year YEAR NOT NULL,
rating VARCHAR(10),
genre VARCHAR(100),
language VARCHAR(50),
description TEXT,
length INT,
poster VARCHAR(255),
trailer_url VARCHAR(255) DEFAULT NULL
);

INSERT INTO movies (title, release_year, rating, genre, language, description, length, poster, trailer_url)
VALUES ('Inception', 2010, 'PG-13', 'Sci-Fi, Thriller', 'English',
        'A thief who steals corporate secrets through dream-sharing technology.', 148, 'images/inception.jpg',
        'https://www.youtube.com/watch?v=YoHD9XEInc0'),
       ('The Matrix', 1999, 'R', 'Sci-Fi, Action', 'English',
        'A hacker discovers the truth about his reality and fights against machines.', 136, 'images/matrix.jpg',
        'https://www.youtube.com/watch?v=vKQi3bBA1y8'),
       ('Interstellar', 2014, 'PG-13', 'Sci-Fi, Adventure, Drama', 'English',
        'Explorers travel through a wormhole to ensure humanity''s survival.', 169, 'images/interstellar.jpg',
        'https://www.youtube.com/watch?v=zSWdZVtXT7E'),
       ('The Dark Knight', 2008, 'PG-13', 'Action, Crime, Drama', 'English', 'Batman battles the Joker in Gotham City.',
        152, 'images/darkknight.jpg', 'https://www.youtube.com/watch?v=EXeTwQWrcwY'),
       ('Avatar', 2009, 'PG-13', 'Sci-Fi, Adventure', 'English',
        'Humans exploit Pandora while a soldier joins the Na''vi.', 162, 'images/avatar.jpg',
        'https://www.youtube.com/watch?v=5PSNL1qE6VY'),
       ('Pulp Fiction', 1994, 'R', 'Crime, Drama', 'English', 'Crime stories intersect in Tarantino''s classic.', 154,
        'images/pulpfiction.jpg', 'https://www.youtube.com/watch?v=s7EdQ4FqbhY'),
       ('The Shawshank Redemption', 1994, 'R', 'Drama', 'English',
        'A banker is imprisoned for murder and forms a profound friendship while planning an escape.',
        142, 'images/shawshank.jpg',
        'https://www.youtube.com/watch?v=6hB3S9bIaco'),
       ('Gladiator', 2000, 'R', 'Action, Drama', 'English',
        'A betrayed Roman general seeks revenge against the corrupt emperor who murdered his family.',
        155, 'images/gladiator.jpg',
        'https://www.youtube.com/watch?v=owK1qxDselE'),
       ('The Godfather', 1972, 'R', 'Crime, Drama', 'English',
        'The aging patriarch of an organized crime dynasty transfers control to his reluctant son.',
        175, 'images/godfather.jpg',
        'https://www.youtube.com/watch?v=sY1S34973zA'),
       ('Spider-Man: No Way Home', 2021, 'PG-13', 'Action, Sci-Fi, Adventure', 'English',
        'Peter Parker seeks help from Doctor Strange to fix the multiverse after his identity is exposed.',
        148, 'images/spiderman_nwh.jpg',
        'https://www.youtube.com/watch?v=JfVOs4VSpmA'),
       ('Dune: Part One', 2021, 'PG-13', 'Sci-Fi, Adventure', 'English',
        'A gifted young man must travel to the galaxy’s most dangerous planet to secure his family’s future.',
        155, 'images/dune.jpg',
        'https://www.youtube.com/watch?v=n9xhJrPXop4');

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

CREATE TABLE actorAppearIn (
    actor_id INT NOT NULL,
    movie_id INT NOT NULL,
    PRIMARY KEY (actor_id, movie_id),
    FOREIGN KEY (actor_id) REFERENCES actors(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

INSERT INTO actorAppearIn VALUES
(1,1),(2,1), 
(3,2),(4,2),
(5,3),(6,3),
(7,4),(8,4),
(9,5),(10,5), 
(11,6),(12,6); 

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
(1,1),(1,3),(1,4),
(2,2),(3,2),       
(4,5),             
(5,6);             

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

CREATE TABLE IF NOT EXISTS seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    screening_room_id INT NOT NULL,
    `row_number` VARCHAR(5) NOT NULL,
    seat_number INT NOT NULL,
    FOREIGN KEY (screening_room_id) REFERENCES screening_rooms(id) ON DELETE CASCADE
);

INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES
(1,'A',1),(1,'A',2),(1,'A',3),(1,'A',4),(1,'A',5),(1,'A',6),(1,'A',7),(1,'A',8),(1,'A',9),(1,'A',10),
(1,'B',1),(1,'B',2),(1,'B',3),(1,'B',4),(1,'B',5),(1,'B',6),(1,'B',7),(1,'B',8),(1,'B',9),(1,'B',10),
(1,'C',1),(1,'C',2),(1,'C',3),(1,'C',4),(1,'C',5),(1,'C',6),(1,'C',7),(1,'C',8),(1,'C',9),(1,'C',10),
(1,'D',1),(1,'D',2),(1,'D',3),(1,'D',4),(1,'D',5),(1,'D',6),(1,'D',7),(1,'D',8),(1,'D',9),(1,'D',10),
(1,'E',1),(1,'E',2),(1,'E',3),(1,'E',4),(1,'E',5),(1,'E',6),(1,'E',7),(1,'E',8),(1,'E',9),(1,'E',10);

INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES
(2,'A',1),(2,'A',2),(2,'A',3),(2,'A',4),(2,'A',5),
(2,'B',1),(2,'B',2),(2,'B',3),(2,'B',4),(2,'B',5),
(2,'C',1),(2,'C',2),(2,'C',3),(2,'C',4),(2,'C',5),
(2,'D',1),(2,'D',2),(2,'D',3),(2,'D',4),(2,'D',5);

INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES
(3,'A',1),(3,'A',2),(3,'A',3),(3,'A',4),(3,'A',5),(3,'A',6),(3,'A',7),(3,'A',8),(3,'A',9),(3,'A',10),
(3,'B',1),(3,'B',2),(3,'B',3),(3,'B',4),(3,'B',5),(3,'B',6),(3,'B',7),(3,'B',8),(3,'B',9),(3,'B',10),
(3,'C',1),(3,'C',2),(3,'C',3),(3,'C',4),(3,'C',5),(3,'C',6),(3,'C',7),(3,'C',8),(3,'C',9),(3,'C',10);

INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES
(4,'A',1),(4,'A',2),(4,'A',3),(4,'A',4),(4,'A',5),(4,'A',6),(4,'A',7),(4,'A',8),(4,'A',9),(4,'A',10),
(4,'B',1),(4,'B',2),(4,'B',3),(4,'B',4),(4,'B',5),(4,'B',6),(4,'B',7),(4,'B',8),(4,'B',9),(4,'B',10),
(4,'C',1),(4,'C',2),(4,'C',3),(4,'C',4),(4,'C',5),(4,'C',6),(4,'C',7),(4,'C',8),(4,'C',9),(4,'C',10),
(4,'D',1),(4,'D',2),(4,'D',3),(4,'D',4),(4,'D',5),(4,'D',6),(4,'D',7),(4,'D',8),(4,'D',9),(4,'D',10),
(4,'E',1),(4,'E',2),(4,'E',3),(4,'E',4),(4,'E',5),(4,'E',6),(4,'E',7),(4,'E',8),(4,'E',9),(4,'E',10),
(4,'F',1),(4,'F',2),(4,'F',3),(4,'F',4),(4,'F',5),(4,'F',6),(4,'F',7),(4,'F',8),(4,'F',9),(4,'F',10);

INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES
(5,'A',1),(5,'A',2),(5,'A',3),(5,'A',4),(5,'A',5),(5,'A',6),(5,'A',7),(5,'A',8),(5,'A',9),(5,'A',10),
(5,'B',1),(5,'B',2),(5,'B',3),(5,'B',4),(5,'B',5),(5,'B',6),(5,'B',7),(5,'B',8),(5,'B',9),(5,'B',10),
(5,'C',1),(5,'C',2),(5,'C',3),(5,'C',4),(5,'C',5),(5,'C',6),(5,'C',7),(5,'C',8),(5,'C',9),(5,'C',10),
(5,'D',1),(5,'D',2),(5,'D',3),(5,'D',4),(5,'D',5),(5,'D',6),(5,'D',7),(5,'D',8),(5,'D',9),(5,'D',10);

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
(1, 1, '2025-10-21 18:00:00', '2025-10-21 20:30:00'),
(2, 2, '2025-10-22 19:00:00', '2025-10-22 21:20:00'),
(3, 3, '2025-10-23 14:00:00', '2025-10-23 16:50:00'),
(4, 4, '2025-10-23 20:00:00', '2025-10-23 22:30:00'),
(5, 1, '2025-10-24 16:00:00', '2025-10-24 18:45:00'),
(6, 5, '2025-10-25 20:30:00', '2025-10-25 23:00:00'),
(1, 1, '2025-10-26 14:00:00', '2025-10-26 16:30:00'),
(1, 4, '2025-10-26 18:30:00', '2025-10-26 21:00:00'),
(1, 1, '2025-10-27 12:00:00', '2025-10-27 14:30:00'),
(1, 1, '2025-10-27 18:00:00', '2025-10-27 20:30:00'),
(1, 4, '2025-10-28 20:00:00', '2025-10-28 22:30:00'),
(1, 4, '2025-10-29 21:00:00', '2025-10-29 23:30:00'),
(2, 2, '2025-10-26 17:00:00', '2025-10-26 19:20:00'),
(2, 4, '2025-10-26 21:30:00', '2025-10-26 23:50:00'),
(2, 5, '2025-10-27 15:00:00', '2025-10-27 17:20:00'),
(2, 2, '2025-10-27 19:30:00', '2025-10-27 21:50:00'),
(2, 5, '2025-10-28 20:00:00', '2025-10-28 22:20:00'),
(2, 4, '2025-10-29 22:30:00', '2025-10-30 00:50:00'),
(3, 1, '2025-10-26 13:30:00', '2025-10-26 16:15:00'),
(3, 4, '2025-10-26 19:00:00', '2025-10-26 22:00:00'),
(3, 1, '2025-10-27 14:30:00', '2025-10-27 17:15:00'),
(3, 4, '2025-10-27 20:30:00', '2025-10-27 23:20:00'),
(3, 1, '2025-10-28 12:00:00', '2025-10-28 14:45:00'),
(4, 4, '2025-10-26 21:00:00', '2025-10-26 23:30:00'),
(4, 1, '2025-10-27 17:30:00', '2025-10-27 20:00:00'),
(4, 2, '2025-10-28 22:30:00', '2025-10-29 00:50:00'),
(4, 1, '2025-10-29 17:00:00', '2025-10-29 19:30:00'),
(4, 4, '2025-10-30 20:30:00', '2025-10-30 23:00:00'),
(5, 4, '2025-10-26 10:30:00', '2025-10-26 13:15:00'),
(5, 1, '2025-10-26 17:00:00', '2025-10-26 19:45:00'),
(5, 3, '2025-10-26 20:00:00', '2025-10-26 22:45:00'),
(5, 4, '2025-10-27 19:00:00', '2025-10-27 21:45:00'),
(5, 1, '2025-10-28 14:00:00', '2025-10-28 16:45:00'),
(6, 5, '2025-10-26 15:00:00', '2025-10-26 17:30:00'),
(6, 1, '2025-10-26 19:30:00', '2025-10-26 22:00:00'),
(6, 2, '2025-10-27 22:30:00', '2025-10-28 01:00:00'),
(6, 5, '2025-10-28 19:00:00', '2025-10-28 21:30:00'),
(6, 1, '2025-10-29 22:00:00', '2025-10-30 00:30:00'),
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
(1, 1, '2025-10-30 14:00:00', '2025-10-30 16:30:00'),
(1, 4, '2025-10-30 18:00:00', '2025-10-30 20:30:00'),
(1, 1, '2025-10-31 12:00:00', '2025-10-31 14:30:00'),
(1, 4, '2025-10-31 16:00:00', '2025-10-31 18:30:00'),
(1, 1, '2025-11-01 14:30:00', '2025-11-01 17:00:00'),
(1, 4, '2025-11-01 19:00:00', '2025-11-01 21:30:00'),
(2, 5, '2025-10-30 15:00:00', '2025-10-30 17:20:00'),
(2, 2, '2025-10-30 18:00:00', '2025-10-30 20:20:00'),
(2, 4, '2025-10-31 20:00:00', '2025-10-31 22:20:00'),
(2, 5, '2025-11-01 13:00:00', '2025-11-01 15:20:00'),
(2, 2, '2025-11-01 16:00:00', '2025-11-01 18:20:00'),
(2, 4, '2025-11-02 19:00:00', '2025-11-02 21:20:00'),
(3, 1, '2025-10-30 13:30:00', '2025-10-30 16:15:00'),
(3, 4, '2025-10-30 17:30:00', '2025-10-30 20:15:00'),
(3, 1, '2025-10-31 14:00:00', '2025-10-31 16:45:00'),
(3, 4, '2025-10-31 18:00:00', '2025-10-31 20:45:00'),
(3, 1, '2025-11-01 12:00:00', '2025-11-01 14:45:00'),
(3, 4, '2025-11-01 15:30:00', '2025-11-01 18:15:00'),
(4, 4, '2025-10-31 21:00:00', '2025-10-31 23:30:00'),
(4, 1, '2025-11-01 17:30:00', '2025-11-01 20:00:00'),
(4, 2, '2025-11-01 21:30:00', '2025-11-02 00:00:00'),
(4, 4, '2025-11-02 20:00:00', '2025-11-02 22:30:00'),
(4, 1, '2025-11-03 16:00:00', '2025-11-03 18:30:00'),
(4, 2, '2025-11-03 19:00:00', '2025-11-03 21:30:00'),
(5, 4, '2025-10-31 10:00:00', '2025-10-31 12:45:00'),
(5, 1, '2025-10-31 14:00:00', '2025-10-31 16:45:00'),
(5, 3, '2025-10-31 17:00:00', '2025-10-31 19:45:00'),
(5, 4, '2025-11-01 16:00:00', '2025-11-01 18:45:00'),
(5, 1, '2025-11-01 19:00:00', '2025-11-01 21:45:00'),
(5, 3, '2025-11-02 12:00:00', '2025-11-02 14:45:00'),
(6, 5, '2025-10-31 15:30:00', '2025-10-31 18:00:00'),
(6, 1, '2025-10-31 19:00:00', '2025-10-31 21:30:00'),
(6, 2, '2025-11-01 20:00:00', '2025-11-01 22:30:00'),
(6, 5, '2025-11-02 18:00:00', '2025-11-02 20:30:00'),
(6, 1, '2025-11-02 21:00:00', '2025-11-02 23:30:00'),
(6, 2, '2025-11-03 17:00:00', '2025-11-03 19:30:00');

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



CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    date_added DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO news (title, content, date_added) VALUES
('New IMAX Screen Installed!', 'We are excited to announce the installation of a brand new IMAX screen in our cinema for a truly immersive experience.', '2025-10-10 12:00:00'),
('Halloween Horror Night 2025', 'Join us on October 31st for a special late-night marathon of classic horror movies!', '2025-10-15 09:30:00'),
('Student Discount Week', 'Show your student ID at the counter and enjoy 30% off all tickets from October 20th to 27th.', '2025-10-18 11:45:00'),
('Avatar Returns in 3D', 'Experience the magic of Pandora again in our newly upgraded 3D projection room.', '2025-10-19 16:00:00'),
('Technical Upgrade Completed', 'All screening rooms have been equipped with new Dolby Atmos sound systems for enhanced audio experience.', '2025-10-17 14:20:00');

CREATE TABLE contact_messages (
                                  id INT AUTO_INCREMENT PRIMARY KEY,
                                  name VARCHAR(100) NOT NULL,
                                  email VARCHAR(150) NOT NULL,
                                  subject VARCHAR(150) NOT NULL,
                                  message TEXT NOT NULL,
                                  ip VARCHAR(45),
                                  user_agent TEXT,
                                  status ENUM('new','read') NOT NULL DEFAULT 'new',
                                  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                  INDEX idx_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO contact_messages (name, email, subject, message, ip, user_agent, status, created_at)
VALUES
    ('John Carter', 'john.carter@example.com', 'Inquiry about screening times',
     'Hi, I wanted to know if Inception will be showing next weekend?',
     '192.168.1.12', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'new', '2025-10-25 09:45:00'),

    ('Lisa Brown', 'lisa.brown@example.com', 'Ticket refund request',
     'Hello, I booked tickets for Avatar yesterday but need to reschedule. Can I get a refund?',
     '192.168.1.25', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_0_0)', 'read', '2025-10-26 15:30:00'),

    ('Tommy Nguyen', 'tommy.nguyen@example.com', 'Lost item in cinema',
     'Hi, I think I left my wallet in the IMAX hall after the 8 PM screening. Please check.',
     '10.0.0.55', 'Mozilla/5.0 (Linux; Android 14)', 'new', '2025-10-27 18:20:00'),

    ('Sarah Miller', 'sarah.miller@example.com', 'Great experience!',
     'Just wanted to say the new Dolby Atmos sound system is incredible. Keep it up!',
     '172.16.0.33', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_1 like Mac OS X)', 'read', '2025-10-28 11:10:00'),

    ('David Smith', 'david.smith@example.com', 'Website issue',
     'When I try to book seats for The Dark Knight, I get a payment error. Please help.',
     '203.0.113.17', 'Mozilla/5.0 (Windows NT 11.0; Win64; x64)', 'new', '2025-10-28 13:42:00');

ALTER TABLE users
ADD reset_token VARCHAR(255),
ADD reset_expires DATETIME;


CREATE OR REPLACE VIEW view_full_bookings AS
SELECT 
    b.id AS booking_id,
    b.user_id,
    u.firstname,
    u.lastname,
    u.email,
    b.screening_id,
    s.start_time,
    s.end_time,
    m.title AS movie_title,
    r.name AS room_name,
    r.seat_price,
    b.total_price
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN screenings s ON b.screening_id = s.id
JOIN movies m ON s.movie_id = m.id
JOIN screening_rooms r ON s.screening_room_id = r.id;


CREATE OR REPLACE VIEW view_screenings_full AS
SELECT 
     s.id AS id,
    s.movie_id,
    s.screening_room_id,
    s.start_time,
    s.end_time,
    m.title AS movie_title,
    r.name AS room_name,
    r.seat_price
FROM screenings s
JOIN movies m ON s.movie_id = m.id
JOIN screening_rooms r ON s.screening_room_id = r.id;


CREATE TRIGGER trg_prevent_screening_delete
BEFORE DELETE ON screenings
FOR EACH ROW
BEGIN
    IF EXISTS (SELECT 1 FROM bookings WHERE screening_id = OLD.id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot delete screening: bookings exist';
    END IF;
END;

CREATE TRIGGER trg_prevent_double_booking
BEFORE INSERT ON booking_seats
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM booking_seats
        WHERE seat_id = NEW.seat_id AND screening_id = NEW.screening_id
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Seat already booked for this screening';
    END IF;
END;
 

 CREATE TABLE content_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tag VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(255) NULL,
    text TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO content_blocks (tag, title, text) VALUES
('contact_address', 'Address', 'Spangsbjerg Kirkevej 101B, 6700 Esbjerg, Denmark'),
('contact_phone', 'Phone', '+45 12 34 56 78'),
('contact_email', 'Email', 'contact@cinema-eclipse.com'),
('contact_hours', 'Opening Hours', 'Monday – Thursday: 10:00 – 22:00<br>Friday – Saturday: 10:00 – 00:00<br>Sunday: 12:00 – 20:00'),
('about_us_text', 'About Us', 'Our cinema was founded with one simple goal: to bring unforgettable film experiences to our community. From timeless classics to the newest blockbusters, we’ve built a space where movie lovers can escape into storytelling, immerse themselves in breathtaking sound, and enjoy the magic of the big screen.<br><br>We believe that a cinema should be more than just a place to watch movies — it should be a gathering spot for friends, families, and film enthusiasts. That’s why we focus on comfort, modern technology, and friendly service. Whether youre here for a premiere night, a cozy late show, or a special event, we strive to make every visit feel special.');


INSERT INTO screenings (movie_id, screening_room_id, start_time, end_time) VALUES
(1, 1, '2026-01-25 18:00:00', '2026-01-25 20:30:00'), 
(2, 2, '2026-01-25 19:00:00', '2026-01-25 21:20:00'),
(3, 3, '2026-01-26 14:00:00', '2026-01-26 16:50:00'),
(4, 4, '2026-01-26 20:00:00', '2026-01-26 22:30:00'),
(5, 1, '2026-01-27 16:00:00', '2026-01-27 18:45:00'),
(6, 5, '2026-01-27 20:30:00', '2026-01-27 23:00:00'),
(7, 2, '2026-01-28 17:00:00', '2026-01-28 19:20:00'),
(8, 4, '2026-01-28 19:00:00', '2026-01-28 21:35:00'),
(9, 3, '2026-01-29 15:00:00', '2026-01-29 17:55:00'),
(10, 1, '2026-01-29 18:00:00', '2026-01-29 20:28:00'),
(11, 5, '2026-01-30 16:00:00', '2026-01-30 18:35:00');

