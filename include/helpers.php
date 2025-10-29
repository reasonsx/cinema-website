<?php
function showError($code = 404, $message = '') {
    header("Location: /cinema-website/error.php?code=$code&message=" . urlencode($message));
    exit;
}
