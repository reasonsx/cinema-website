<?php
require_once __DIR__ . '/actors_functions.php';
$actors = getActors($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_actor'])) {
        [$success, $error] = addActorHandler($db, $_POST);
    }

    if (isset($_POST['edit_actor'])) {
        [$success, $error] = editActorHandler($db, $_POST);
    }

    if (isset($_POST['delete_actor'])) {
        [$success, $error] = deleteActor($db, $_POST['delete_actor_id']);
    }

    header("Location: /cinema-website/admin_dashboard/admin_dashboard.php?view=actors&message=" . urlencode($success ?: $error));
    exit;
}
