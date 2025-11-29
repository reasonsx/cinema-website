<?php
// --- Secure session setup ---
$sessionLifetime = 3600; // session cookie valid for 1 hour
session_set_cookie_params([
    'lifetime' => $sessionLifetime,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

require_once '../include/connection.php';

// --- Handle session timeout (30 min inactivity) ---
$timeoutDuration = 1800;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeoutDuration) {
    session_unset();
    session_destroy();
}
$_SESSION['LAST_ACTIVITY'] = time();

// --- Determine redirect after login ---
$redirect = '/cinema-website/profile.php'; // default redirect
if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
    // sanitize redirect URL
    $redirectPath = filter_var($_GET['redirect'], FILTER_SANITIZE_URL);
    if (str_starts_with($redirectPath, '/')) {
        $redirect = $redirectPath;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $db->prepare("SELECT id, password, firstname, lastname, isAdmin FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['isAdmin'] = $user['isAdmin'];

            // Redirect to the previous page
            header("Location: $redirect");
            exit;
        } else {
            $_SESSION['error'] = 'Invalid email or password!';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../shared/head.php'; ?>
<body class="bg-light text-black font-sans">
<?php include '../shared/header.php'; ?>

<section class="flex justify-center items-center min-h-[80vh] bg-light px-4">
    <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
        <h2 class="text-3xl font-header mb-6 text-center text-primary">Log in</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-500 text-center mb-4"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form action="" method="POST" class="flex flex-col gap-4">
            <input type="email" name="email" placeholder="Email" required
                   class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">

            <div class="relative">
                <input id="login-password" type="password" name="password" placeholder="Password" required
                       class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-full">
                <button type="button" id="toggle-login-password"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black focus:outline-none">
                    <i class="pi pi-eye"></i>
                </button>
            </div>

            <button type="submit"
                    class="btn w-full text-center justify-center items-center bg-primary text-white hover:bg-secondary">
                <i class="pi pi-sign-in"></i> Login
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-4">
            Don't have an account? <a href="signup.php" class="text-primary hover:text-secondary">Sign Up</a>
        </p>
    </div>
</section>

<?php include '../shared/footer.php'; ?>

<script>
    const loginPassword = document.getElementById('login-password');
    const toggleLogin = document.getElementById('toggle-login-password');
    toggleLogin.addEventListener('click', () => {
        const type = loginPassword.type === 'password' ? 'text' : 'password';
        loginPassword.type = type;
        toggleLogin.innerHTML = type === 'password'
            ? '<i class="pi pi-eye"></i>'
            : '<i class="pi pi-eye-slash"></i>';
    });
</script>
</body>
</html>
