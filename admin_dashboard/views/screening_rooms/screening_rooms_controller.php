<?php
require_once __DIR__ . '/screening_rooms_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_room'])) {
        [$success, $error] = addScreeningRoom($db, $_POST);
    }

    if (isset($_POST['edit_room'])) {
        [$success, $error] = editScreeningRoom($db, $_POST);
    }

    if (isset($_POST['delete_room'])) {
        [$success, $error] = deleteScreeningRoom($db, $_POST['room_id']);
    }

    header("Location: /cinema-website/admin_dashboard/admin_dashboard.php?view=screening_rooms&message=" . urlencode($success ?: $error));
    exit;
}
