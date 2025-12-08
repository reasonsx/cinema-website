<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Define constants from .env
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);

// Build DSN dynamically
define('DSN', "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8");
