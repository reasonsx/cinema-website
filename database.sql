-- database.sql
DROP DATABASE IF EXISTS cinema;
CREATE DATABASE cinema;
USE cinema;

-- Tables
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL
);
