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
