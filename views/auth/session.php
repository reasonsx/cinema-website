<?php
/**
 * SessionManager handles user session lifecycle including:
 * - Starting and validating sessions
 * - Enforcing automatic session timeouts
 * - Requiring authentication for protected pages
 * - Safely logging users out
 *
 * It provides a centralized and secure way to manage authentication state
 * across the application.
 */

class SessionManager
{
    private PDO $db;
    private int $timeout;

    public function __construct(PDO $db, int $timeout = 1800)
    {
        $this->db = $db;
        $this->timeout = $timeout;
        $this->startSession();
        $this->checkTimeout();
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function checkTimeout(): void
    {
        if (
            isset($_SESSION['user_id'], $_SESSION['LAST_ACTIVITY']) &&
            (time() - $_SESSION['LAST_ACTIVITY']) > $this->timeout
        ) {
            $this->logout();
            header("Location: /cinema-website/views/auth/login.php?timeout=1");
            exit;
        }

        if (isset($_SESSION['user_id'])) {
            $_SESSION['LAST_ACTIVITY'] = time();
        }
    }

    public function requireLogin(
        string $redirectUrl = '/cinema-website/views/profile/profile.php'
    ): void {
        if (!isset($_SESSION['user_id'])) {
            header(
                "Location: /cinema-website/views/auth/login.php?redirect=" .
                urlencode($redirectUrl)
            );
            exit;
        }
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 3600,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        setcookie(
            'remember_token',
            '',
            time() - 3600,
            '/',
            '',
            isset($_SERVER['HTTPS']),
            true
        );

        header("Location: /cinema-website/views/auth/login.php?logged_out=1");
        exit;
    }

    public function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public function isAdmin(): bool
    {
        return $_SESSION['isAdmin'] ?? false;
    }
}
