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

// 3. Validate user input
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$subject || !$message) {
    $_SESSION['contact_status'] = ['type' => 'error', 'msg' => 'All fields are required.'];
    header("Location: /cinema-website/#contact-us");
    exit;
}

// Message length validation
if (strlen($message) < 10 || strlen($message) > 1000) {
    $_SESSION['contact_status'] = [
        'type' => 'error',
        'msg'  => 'Message must be between 10 and 1000 characters.'
    ];
    header("Location: /cinema-website/#contact-us");
    exit;
}

// 4. (Removed) Saving to database is disabled

// 5. Send email to cinema staff
$body = "
<h2 style='margin:10px;'>New Contact Message</h2>
<p style='margin:10px;'><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
<p style='margin:10px;'><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
<p style='margin:10px;'><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>

<hr style='margin:10px 0;'>

<p style='margin:10px;'><strong>Message:</strong></p>
<p style='margin:10px;'>" . nl2br(htmlspecialchars($message)) . "</p>
";

$sent = sendMail(
    "contact@cinema-eclipse.com",
    "New Message: $subject",
    $body,
    $email,
    $name
);

// 6. Store status message for frontend display
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

// Redirect back to contact section anchor
header("Location: /cinema-website/#contact-us");
exit;
