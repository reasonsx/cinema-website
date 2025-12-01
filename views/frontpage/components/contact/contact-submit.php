<?php
session_start();
require_once __DIR__ . '/../../../../backend/email_config.php';
require_once __DIR__ . '/../../../../backend/connection.php';

// 1. Anti-bot check
if (!empty($_POST['website'])) {
    $_SESSION['contact_status'] = ['type' => 'error', 'msg' => 'Bot detected.'];
    header("Location: /cinema-website/#contact-us");
    exit;
}

// 2. CSRF check
if (!isset($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf'] ?? '')) {
    $_SESSION['contact_status'] = ['type' => 'error', 'msg' => 'Invalid CSRF token.'];
    header("Location: /cinema-website/#contact-us");
    exit;
}

// 3. Validate
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$subject || !$message) {
    $_SESSION['contact_status'] = ['type' => 'error', 'msg' => 'All fields are required.'];
    header("Location: /cinema-website/#contact-us");
    exit;
}

// 4. Save message to DB
$stmt = $db->prepare("
    INSERT INTO contact_messages (name, email, subject, message, created_at)
    VALUES (?, ?, ?, ?, NOW())
");
$stmt->execute([$name, $email, $subject, $message]);

// 5. Send email
$body = "
<strong>New contact message:</strong><br><br>
Name: $name<br>
Email: $email<br><br>
Subject: $subject<br>
Message:<br>
$message
";

$sent = sendMail(
    "contact@cinema-eclipse.com",
    "New Message: $subject",
    $body,
    $email,   // user email
    $name     // user name
);


// Store flash message
if ($sent === true) {
    $_SESSION['contact_status'] = [
        'type' => 'success',
        'msg'  => 'Your message has been sent successfully!'
    ];
} else {
    $_SESSION['contact_status'] = [
        'type' => 'error',
        'msg'  => 'Email failed: ' . $sent
    ];
}

// Redirect back to contact section
header("Location: /cinema-website/#contact-us");
exit;
