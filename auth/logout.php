<?php
session_start();
require_once '../include/connection.php';

if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}

session_unset();
session_destroy();

setcookie('remember_token', '', time() - 3600, '/', '', isset($_SERVER['HTTPS']), true);

header("Location: /cinema-website/auth/login.php");
exit;
