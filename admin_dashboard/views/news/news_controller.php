<?php

require_once __DIR__ . '/../../../backend/connection.php';
require_once __DIR__ . '/news_functions.php';

// Load required data
$newsList = getNews($db);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_news'])) {
        [$success, $error] = addNews($db, $_POST);
    }

    if (isset($_POST['edit_news'])) {
        [$success, $error] = editNews($db, $_POST);
    }

    if (isset($_POST['delete_news'])) {
        [$success, $error] = deleteNews($db, $_POST['delete_news_id']);
    }

    header("Location: /cinema-website/admin_dashboard/admin_dashboard.php?view=news&message=" . urlencode($success ?: $error));
    exit;
}
