<?php

require_once __DIR__ . '/../../../backend/connection.php';
require_once __DIR__ . '/screenings_functions.php';
require_once __DIR__ . '/../movies/movies_functions.php';
require_once __DIR__ . '/../screening_rooms/screening_rooms_functions.php';

// Load required lists
$screenings     = getScreenings($db);
$movies         = getMovies($db);
$screeningRooms = getScreeningRooms($db);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_screening'])) {
        [$success, $error] = addScreening($db, $_POST);
    }

    if (isset($_POST['edit_screening'])) {
        [$success, $error] = editScreening($db, $_POST);
    }

    if (isset($_POST['delete_screening'])) {
        [$success, $error] = deleteScreening($db, $_POST['screening_id']);
    }

    header(
        "Location: /cinema-website/admin_dashboard/admin_dashboard.php?view=screenings&message="
        . urlencode($success ?: $error)
    );
    exit;
}
