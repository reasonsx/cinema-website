<?php

require_once __DIR__ . '/../../../backend/connection.php';
require_once __DIR__ . '/users_functions.php';

// Load all users for the table
$users = getUsers($db);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_user'])) {
        [$success, $error] = addUser($db, $_POST);
    }

    if (isset($_POST['edit_user'])) {
        [$success, $error] = editUser($db, $_POST);
    }

    if (isset($_POST['delete_user'])) {
        [$success, $error] = deleteUser($db, $_POST['delete_user_id']);
    }

    header("Location: /cinema-website/admin_dashboard/admin_dashboard.php?view=users&message=" . urlencode($success ?: $error));
    exit;
}
