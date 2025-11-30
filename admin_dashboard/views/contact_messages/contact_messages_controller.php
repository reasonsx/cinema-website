<?php
//require_once __DIR__ . '/contact_functions.php';
//
//$messages = listContactMessages($db);
//
//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//
//    $id = intval($_POST['id'] ?? 0);
//
//    if (isset($_POST['mark_read'])) {
//        markContactAsRead($db, $id);
//        $success = "Marked as read.";
//    }
//
//    if (isset($_POST['mark_new'])) {
//        markContactAsNew($db, $id);
//        $success = "Marked as new.";
//    }
//
//    if (isset($_POST['delete_message'])) {
//        deleteContactMessage($db, $id);
//        $success = "Message deleted.";
//    }
//
//    header("Location: /cinema-website/admin_dashboard/admin_dashboard.php?view=contact_messages&message=" . urlencode($success));
//    exit;
//}
