<?php
// admin_includes.php

// Core dependencies
require_once __DIR__ . '/../backend/connection.php';
require_once __DIR__ . '/../shared/csrf.php';
require_once __DIR__ . '/../auth/session.php';

// View-specific functions
require_once __DIR__ . '/views/actors/actors_functions.php';
require_once __DIR__ . '/views/directors/directors_functions.php';
require_once __DIR__ . '/views/movies/movies_functions.php';
require_once __DIR__ . '/views/users/users_functions.php';
require_once __DIR__ . '/views/screening_rooms/screening_rooms_functions.php';
require_once __DIR__ . '/views/screenings/screenings_functions.php';
require_once __DIR__ . '/views/bookings/bookings_functions.php';
require_once __DIR__ . '/views/news/news_functions.php';
// require_once __DIR__ . '/../views/contact_messages/contact_functions.php';
