<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function invalidateCsrfToken() {
    unset($_SESSION['csrf_token']);
}

/*  AUTO VALIDATE ALL POST REQUESTS */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($token)) {
        die("❌ CSRF validation failed.");
    }
    invalidateCsrfToken(); // prevent replay
}
