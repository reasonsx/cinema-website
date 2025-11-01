<?php
class SessionManager {
    private $db;
    private $timeout;

    public function __construct(PDO $db, int $timeout = 1800) {
        $this->db = $db;
        $this->timeout = $timeout; // 30 minutes by default
        $this->startSession();
        $this->checkTimeout();
        $this->autoLogin();
    }

    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function checkTimeout() {
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $this->timeout) {
            $this->logout();
            header("Location: /cinema-website/auth/login.php?timeout=1");
            exit;
        }
        $_SESSION['LAST_ACTIVITY'] = time();
    }

    private function autoLogin() {
    /*     if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
            $stmt = $this->db->prepare("SELECT id, firstname, lastname, isAdmin FROM users WHERE remember_token = ?");
            $stmt->execute([$_COOKIE['remember_token']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['lastname']  = $user['lastname'];
                $_SESSION['isAdmin']   = $user['isAdmin'];
                $_SESSION['LAST_ACTIVITY'] = time();
            } else {
                setcookie('remember_token', '', time() - 3600);
            }
        } */
    }

    public function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /cinema-website/auth/login.php");
            exit;
        }
    }

public function logout(): void
{
    // Clear PHP session
    $_SESSION = [];
    session_unset();
    session_destroy();

    // Remove old cookie code (optional)
    setcookie('remember_token', '', time() - 3600, '/', '', isset($_SERVER['HTTPS']), true);
}


    public function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public function isAdmin(): bool {
        return $_SESSION['isAdmin'] ?? false;
    }
}
