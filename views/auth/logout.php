<?php
require_once '../../backend/connection.php';
require_once 'session.php';

$session = new SessionManager($db);
$session->logout();

header("Location: /cinema-website/views/auth/login.php");
exit;
