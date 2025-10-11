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

-- Insert admin user
INSERT INTO users (email, password, firstname, lastname, isAdmin)
VALUES ('admin@admin.com', '$2y$10$WCWVWm076L247LVseNjmoOjjK0wfh89U7iOBNKggl7LPfkKhU5vnW', 'Admin', 'Admin', TRUE);

-- Create movies table
CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,      -- Movie ID
    title VARCHAR(255) NOT NULL,            -- Movie title
    release_year YEAR NOT NULL,             -- Release year
    rating VARCHAR(10),                      -- Movie rating (e.g., PG-13)
    description TEXT,                        -- Movie description
    length INT,                              -- Movie length in minutes
    poster VARCHAR(255)                       -- Poster image path or URL
);

-- Insert some sample movies
INSERT INTO movies (title, release_year, rating, description, length, poster) VALUES
('Inception', 2010, 'PG-13', 'A thief who steals corporate secrets through dream-sharing technology.', 148, 'images/inception.jpg'),
('The Matrix', 1999, 'R', 'A hacker discovers the truth about his reality and fights against machines.', 136, 'images/matrix.jpg'),
('Interstellar', 2014, 'PG-13', 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity''s survival.', 169, 'images/interstellar.jpg');


-- Create actors table
CREATE TABLE actors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    description TEXT
);

-- Many-to-many table: actors appear in movies
CREATE TABLE actorAppearIn (
    actor_id INT NOT NULL,
    movie_id INT NOT NULL,
    PRIMARY KEY (actor_id, movie_id),
    FOREIGN KEY (actor_id) REFERENCES actors(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

-- Create directors table
CREATE TABLE directors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    description TEXT
);

-- Many-to-many table: directors direct movies
CREATE TABLE directorDirects (
    director_id INT NOT NULL,
    movie_id INT NOT NULL,
    PRIMARY KEY (director_id, movie_id),
    FOREIGN KEY (director_id) REFERENCES directors(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);


-- Insert sample actors
INSERT INTO actors (first_name, last_name, date_of_birth, gender, description) VALUES
('Leonardo', 'DiCaprio', '1974-11-11', 'Male', 'American actor and producer.'),
('Joseph', 'Gordon-Levitt', '1981-02-17', 'Male', 'American actor known for Inception.'),
('Keanu', 'Reeves', '1964-09-02', 'Male', 'Canadian actor known for The Matrix.'),
('Carrie-Anne', 'Moss', '1967-08-21', 'Female', 'Canadian actress known for The Matrix.'),
('Matthew', 'McConaughey', '1969-11-04', 'Male', 'American actor known for Interstellar.'),
('Anne', 'Hathaway', '1982-11-12', 'Female', 'American actress known for Interstellar.');

-- Link actors to movies
INSERT INTO actorAppearIn (actor_id, movie_id) VALUES
(1, 1), -- Leonardo DiCaprio in Inception
(2, 1), -- Joseph Gordon-Levitt in Inception
(3, 2), -- Keanu Reeves in The Matrix
(4, 2), -- Carrie-Anne Moss in The Matrix
(5, 3), -- Matthew McConaughey in Interstellar
(6, 3); -- Anne Hathaway in Interstellar

-- Insert sample directors
INSERT INTO directors (first_name, last_name, date_of_birth, gender, description) VALUES
('Christopher', 'Nolan', '1970-07-30', 'Male', 'British-American film director.'),
('Lana', 'Wachowski', '1965-06-21', 'Female', 'American film director, co-director of The Matrix.'),
('Lilly', 'Wachowski', '1967-12-29', 'Female', 'American film director, co-director of The Matrix.');

-- Link directors to movies
INSERT INTO directorDirects (director_id, movie_id) VALUES
(1, 1), -- Christopher Nolan directed Inception
(1, 3), -- Christopher Nolan directed Interstellar
(2, 2), -- Lana Wachowski directed The Matrix
(3, 2); -- Lilly Wachowski directed The Matrix


-- Screening Rooms Table
-- Screening Rooms Table (without capacity column)
CREATE TABLE IF NOT EXISTS screening_rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);


-- Example Screening Rooms
INSERT INTO screening_rooms (name) VALUES
('Main Hall'),
('VIP Room'),
('Kids Room');

-- Seats Table
CREATE TABLE IF NOT EXISTS seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    screening_room_id INT NOT NULL,
    `row_number` VARCHAR(5) NOT NULL,  -- backticks because reserved
    seat_number INT NOT NULL,
    FOREIGN KEY (screening_room_id) REFERENCES screening_rooms(id) ON DELETE CASCADE
);

-- Example Seats for Main Hall (50 seats)
INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES
(1,'A',1),(1,'A',2),(1,'A',3),(1,'A',4),(1,'A',5),(1,'A',6),(1,'A',7),(1,'A',8),(1,'A',9),(1,'A',10),
(1,'B',1),(1,'B',2),(1,'B',3),(1,'B',4),(1,'B',5),(1,'B',6),(1,'B',7),(1,'B',8),(1,'B',9),(1,'B',10),
(1,'C',1),(1,'C',2),(1,'C',3),(1,'C',4),(1,'C',5),(1,'C',6),(1,'C',7),(1,'C',8),(1,'C',9),(1,'C',10),
(1,'D',1),(1,'D',2),(1,'D',3),(1,'D',4),(1,'D',5),(1,'D',6),(1,'D',7),(1,'D',8),(1,'D',9),(1,'D',10),
(1,'E',1),(1,'E',2),(1,'E',3),(1,'E',4),(1,'E',5),(1,'E',6),(1,'E',7),(1,'E',8),(1,'E',9),(1,'E',10);

-- Same fix for other rooms:
INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES
(2,'A',1),(2,'A',2),(2,'A',3),(2,'A',4),(2,'A',5),
(2,'B',1),(2,'B',2),(2,'B',3),(2,'B',4),(2,'B',5),
(2,'C',1),(2,'C',2),(2,'C',3),(2,'C',4),(2,'C',5),
(2,'D',1),(2,'D',2),(2,'D',3),(2,'D',4),(2,'D',5);

INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES
(3,'A',1),(3,'A',2),(3,'A',3),(3,'A',4),(3,'A',5),(3,'A',6),(3,'A',7),(3,'A',8),(3,'A',9),(3,'A',10),
(3,'B',1),(3,'B',2),(3,'B',3),(3,'B',4),(3,'B',5),(3,'B',6),(3,'B',7),(3,'B',8),(3,'B',9),(3,'B',10),
(3,'C',1),(3,'C',2),(3,'C',3),(3,'C',4),(3,'C',5),(3,'C',6),(3,'C',7),(3,'C',8),(3,'C',9),(3,'C',10);
