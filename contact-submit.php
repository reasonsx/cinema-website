<?php
session_start();
require_once 'include/connection.php';
require_once 'admin_dashboard/includes/contact.php';

/* --- Basic protections --- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php#contact-us'); exit; }

if (empty($_SESSION['csrf']) || empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    $_SESSION['flash_error'] = "Security token mismatch. Please try again.";
    header('Location: index.php#contact-us'); exit;
}

if (!empty($_POST['website'])) { // honeypot
    $_SESSION['flash_error'] = "Spam detected.";
    header('Location: index.php#contact-us'); exit;
}

/* --- Collect & validate --- */
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];
if (mb_strlen($name) < 2 || mb_strlen($name) > 100)         $errors[] = "Name must be 2–100 characters.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL))              $errors[] = "Please enter a valid email.";
if (mb_strlen($subject) < 3 || mb_strlen($subject) > 150)    $errors[] = "Subject must be 3–150 characters.";
if (mb_strlen($message) < 10 || mb_strlen($message) > 5000)  $errors[] = "Message must be 10–5000 characters.";
if ($errors) {
    $_SESSION['flash_error'] = implode(' ', $errors);
    header('Location: index.php#contact-us'); exit;
}

/* --- Save --- */
$userId = $_SESSION['user_id'] ?? null; // if you set this on login
[$ok, $err] = addContactMessage($db, [
    'user_id' => $userId,
    'name'    => $name,
    'email'   => $email,
    'subject' => $subject,
    'message' => $message,
    'ip'      => $_SERVER['REMOTE_ADDR'] ?? null,
    'ua'      => $_SERVER['HTTP_USER_AGENT'] ?? null,
]);

if ($err) {
    $_SESSION['flash_error'] = $err;
    header('Location: index.php#contact-us'); exit;
}

/* --- Optional email (PHPMailer recommended in prod) --- */
$to = 'info@mycinema.com';
$subjectLine = "[Contact] $subject";
$body = "From: $name <$email>\n\n$message";
$headers = "From: no-reply@mycinema.com\r\nReply-To: $email\r\n";
@mail($to, $subjectLine, $body, $headers);

$_SESSION['flash_success'] = "Thanks! Your message has been sent.";
header('Location: index.php#contact-us'); exit;
