<?php

require_once __DIR__ . '/../../../backend/connection.php';
require_once __DIR__ . '/../actors/actors_functions.php';      // needed for getActorsList()
require_once __DIR__ . '/../directors/directors_functions.php'; // needed for getDirectorsList()
require_once __DIR__ . '/movies_functions.php';                // movie logic

// Load data required by the view
$movies = getMovies($db);
$allActors = getActorsList($db);
$allDirectors = getDirectorsList($db);

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_movie'])) {
        [$success, $error] = addMovieHandler($db, $_POST, $_FILES);
    }

    if (isset($_POST['edit_movie'])) {
        [$success, $error] = editMovieHandler($db, $_POST, $_FILES);
    }

    if (isset($_POST['delete_movie'])) {
        [$success, $error] = deleteMovie($db, $_POST['delete_movie']);
    }

    // Redirect back with message
    header("Location: /cinema-website/admin_dashboard/admin_dashboard.php?view=movies&message=" . urlencode($success ?: $error));
    exit;
}