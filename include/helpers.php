<?php

/**
 * Redirect to the error page with a given code and message.
 *
 * @param int    $code     HTTP-style error code.
 * @param string $message  Optional descriptive message.
 */
function showError($code = 404, $message = '') {
    header("Location: /cinema-website/error.php?code=$code&message=" . urlencode($message));
    exit;
}

/**
 * Safely escape any output for HTML.
 *
 * - Prevents XSS attacks
 * - Can be used everywhere: <?= e($value) ?>
 * - Wrapped in function_exists() to avoid "Cannot redeclare" errors
 */
if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}