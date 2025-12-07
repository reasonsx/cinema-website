<?php
session_start();

require_once '../backend/connection.php';
require_once '../backend/email_config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_POST['email'])) {
    die("No email received.");
}

$email = trim($_POST['email']);

// ✅ Find user
$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ Security: do NOT reveal if user exists
if ($user) {

    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

    $stmt = $db->prepare("
        UPDATE users 
        SET reset_token = ?, reset_expires = ? 
        WHERE email = ?
    ");
    $stmt->execute([$token, $expires, $email]);

    $resetLink = "http://localhost/cinema-website/auth/reset_password.php?token=$token";

    sendMail(
        $email,
        "Password Reset",
        "Click this link to reset your password:\n\n$resetLink"
    );
}

// ✅ ALWAYS show success (anti-account enumeration)
$_SESSION['success'] = "If that email exists, a reset link was sent.";
header("Location: forgot_password.php");
exit;
