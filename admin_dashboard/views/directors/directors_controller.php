<?php

require_once __DIR__ . '/../../../include/connection.php';
require_once __DIR__ . '/directors_functions.php';

// Load directors for the view
$directors = getDirectors($db);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_director'])) {
        [$success, $error] = addDirectorHandler($db, $_POST);
    }

    if (isset($_POST['edit_director'])) {
        [$success, $error] = editDirectorHandler($db, $_POST);
    }

    if (isset($_POST['delete_director'])) {
        [$success, $error] = deleteDirector($db, $_POST['delete_director_id']);
    }

    header("Location: /cinema-website/admin_dashboard/admin_dashboard.php?view=directors&message=" . urlencode($success ?: $error));
    exit;
}
