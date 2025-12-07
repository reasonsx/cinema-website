<?php
// Session lifetime configuration
$sessionLifetime = 3600;

session_set_cookie_params([
    'lifetime' => $sessionLifetime,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Start session
session_start();

require_once '../backend/connection.php';

// Inactivity timeout
$timeoutDuration = 1800;

// Destroy session if inactive
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeoutDuration) {
    session_unset();
    session_destroy();
}

// Update last activity timestamp
$_SESSION['LAST_ACTIVITY'] = time();

// Default redirect after login
$redirect = '/cinema-website/views/profile/profile.php';

// Check redirect parameter
if (!empty($_GET['redirect'])) {
    $redirectPath = filter_var($_GET['redirect'], FILTER_SANITIZE_URL);
    if (str_starts_with($redirectPath, '/')) {
        $redirect = $redirectPath;
    }
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Read inputs
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format.';
        header("Location: login.php");
        exit;
    }

    // Email length validation
    if (strlen($email) > 255) {
        $_SESSION['error'] = 'Email too long.';
        header("Location: login.php");
        exit;
    }

    try {
        // Look up user
        $stmt = $db->prepare("
            SELECT id, password, firstname, lastname, isAdmin
            FROM users
            WHERE email = ?
        ");

        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Validate credentials
        if ($user && password_verify($password, $user['password'])) {

            // Store session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['isAdmin'] = $user['isAdmin'];

            // Success: redirect
            header("Location: $redirect");
            exit;
        }

        // Wrong login
        $_SESSION['error'] = 'Invalid email or password.';

    } catch (PDOException $e) {
        // DB error
        $_SESSION['error'] = 'Database error.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include '../shared/head.php'; ?>

<body class="bg-black text-black font-sans">

<?php include '../shared/header.php'; ?>

<section class="flex justify-center items-center min-h-[70vh] bg-black px-4 py-10">
    <div class="w-full max-w-md rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl p-8">

        <h2 class="text-4xl font-[Limelight] tracking-wide text-center text-[var(--secondary)] mb-8">
            LOGIN
        </h2>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-400 text-center mb-4"><?= $_SESSION['error'] ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" class="flex flex-col gap-5">

            <input type="email" name="email" placeholder="Enter your email" required>

            <div class="relative">
                <input id="login-password" type="password" name="password" placeholder="Enter your password" required>

                <button type="button" id="toggle-login-password"
                        class="absolute right-3 top-1/2 -translate-y-1/2">
                    <i class="pi pi-eye"></i>
                </button>
            </div>

            <button type="submit" class="btn-full w-full">
                <i class="pi pi-sign-in"></i> Login
            </button>

        </form>

        <p class="text-center text-sm text-white/60 mt-6">
            Don't have an account?
            <a href="signup.php" class="text-[var(--secondary)]">Sign Up</a>
        </p>

        <p class="text-center text-sm text-white/60 mt-2">
            <a href="forgot_password.php" class="text-[var(--secondary)]">Forgot your password?</a>
        </p>

    </div>
</section>

<script>
    // Password visibility toggle
    const loginPassword = document.getElementById('login-password');
    const toggleLogin = document.getElementById('toggle-login-password');

    toggleLogin.addEventListener('click', () => {
        const isPass = loginPassword.type === 'password';
        loginPassword.type = isPass ? 'text' : 'password';
        toggleLogin.innerHTML = isPass ? '<i class="pi pi-eye-slash"></i>' : '<i class="pi pi-eye"></i>';
    });
</script>

<?php include '../shared/footer.php'; ?>

</body>
</html>
