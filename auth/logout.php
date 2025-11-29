<?php
require_once '../backend/connection.php';
require_once '../auth/session.php'; // if using SessionManager

$session = new SessionManager($db);
$session->logout();

header("Location: /cinema-website/auth/login.php");
exit;
