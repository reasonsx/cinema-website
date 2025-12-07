<?php
require_once '../backend/connection.php';

if (!isset($_GET['token'])) {
    die("Invalid reset link.");
}

$token = $_GET['token'];

$stmt = $db->prepare("
    SELECT id FROM users 
    WHERE reset_token = ?
    AND reset_expires > NOW()
");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("This reset link is expired or invalid.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $db->prepare("
        UPDATE users 
        SET password=?, reset_token=NULL, reset_expires=NULL
        WHERE id=?
    ");
    $stmt->execute([$hashed, $user['id']]);

    echo "✅ Password updated! You can now log in.";
    exit;
}
?>

<form method="POST">
    <input type="password" name="password" placeholder="New password" required>
    <button type="submit">Reset Password</button>
</form>
