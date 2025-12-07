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

// ✅ Check if user exists
$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = "No account found with that email.";
    header("Location: forgot_password.php");
    exit;
}

// ✅ Create reset token
$token = bin2hex(random_bytes(32));
$expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

// ✅ Store token in database
$stmt = $db->prepare("
    UPDATE users 
    SET reset_token = ?, reset_expires = ? 
    WHERE email = ?
");
$stmt->execute([$token, $expires, $email]);

// ✅ Create reset link
$resetLink = "http://localhost/cinema-website/views/reset_password.php?token=$token";

// ✅ Send email
$result = sendMail(
    $email,
    "Password Reset",
    "Click this link to reset your password:\n\n$resetLink"
);

if ($result === true) {
    $_SESSION['success'] = "Password reset email sent!";
} else {
    $_SESSION['error'] = $result;
}

header("Location: forgot_password.php");
exit;
