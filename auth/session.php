<?php

class SessionManager
{
    private PDO $db;
    private int $timeout;

    public function __construct(PDO $db, int $timeout = 1800)
    {
        $this->db = $db;
        $this->timeout = $timeout; // 30 minutes
        $this->startSession();
        $this->checkTimeout();
        $this->autoLogin(); // currently disabled
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
            header("Location: /cinema-website/auth/login.php?timeout=1");
            exit;
        }

        // Only update activity when logged in
        if (isset($_SESSION['user_id'])) {
            $_SESSION['LAST_ACTIVITY'] = time();
        }
    }

    private function autoLogin(): void
    {
        // ✅ Disabled for now – safe to re-enable later if needed
        /*
        if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
            $stmt = $this->db->prepare(
                "SELECT id, firstname, lastname, isAdmin FROM users WHERE remember_token = ?"
            );

            $stmt->execute([$_COOKIE['remember_token']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['user_id']  = (int)$user['id'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['lastname']  = $user['lastname'];
                $_SESSION['isAdmin']   = (bool)$user['isAdmin'];
                $_SESSION['LAST_ACTIVITY'] = time();
            } else {
                setcookie('remember_token', '', time() - 3600, '/');
            }
        }
        */
    }

    public function requireLogin(
        string $redirectUrl = '/cinema-website/views/profile/profile.php'
    ): void {
        if (!isset($_SESSION['user_id'])) {
            header(
                "Location: /cinema-website/auth/login.php?redirect=" .
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

        // Clear session data
        $_SESSION = [];

        // Delete PHP session cookie
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

        // Destroy session
        session_destroy();

        // Remove remember-me cookie (if ever used)
        setcookie(
            'remember_token',
            '',
            time() - 3600,
            '/',
            '',
            isset($_SERVER['HTTPS']),
            true
        );

        // Redirect after logout
        header("Location: /cinema-website/auth/login.php?logged_out=1");
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
