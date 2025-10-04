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
