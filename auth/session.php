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

   public function requireLogin(string $redirectUrl = '/cinema-website/views/profile/profile.php'): void {
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login page with `redirect` parameter
        header("Location: /cinema-website/auth/login.php?redirect=" . urlencode($redirectUrl));
        exit;
    }
}

public function logout(): void
{
    // Make sure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Clear session array
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

    // Destroy the session
    session_destroy();

    // Remove remember token cookie (if used)
    setcookie('remember_token', '', time() - 3600, '/', '', isset($_SERVER['HTTPS']), true);

    // Redirect IMMEDIATELY after logout
    header("Location: /cinema-website/auth/login.php?logged_out=1");
    exit;
}



    public function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public function isAdmin(): bool {
        return $_SESSION['isAdmin'] ?? false;
    }
}
