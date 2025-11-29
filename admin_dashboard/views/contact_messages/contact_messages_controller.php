<?php
require_once __DIR__ . '/contact_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['mark_read'])) {
        markContactAsRead($db, $_POST['id']);
        $success = "Marked as read.";
    }

    if (isset($_POST['mark_new'])) {
        markContactAsNew($db, $_POST['id']);
        $success = "Marked as new.";
    }

    if (isset($_POST['delete_message'])) {
        deleteContactMessage($db, $_POST['id']);
        $success = "Message deleted.";
    }

    header("Location: /cinema-website/admin_dashboard/admin_dashboard.php?view=contact_messages&message=" . urlencode($success));
    exit;
}
