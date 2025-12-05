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

require_once '../backend/connection.php';

// --- Handle session timeout (30 min inactivity) ---
$timeoutDuration = 1800;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeoutDuration) {
    session_unset();
    session_destroy();
}
$_SESSION['LAST_ACTIVITY'] = time();

// --- Determine redirect after login ---
$redirect = '/cinema-website/views/profile/profile.php';
if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
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
<body class="bg-black text-black font-sans">
<?php include '../shared/header.php'; ?>

<section class="flex justify-center items-center min-h-[70vh] bg-black px-4 py-10">
    <div class="w-full max-w-md rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl p-8">

        <h2 class="text-4xl font-[Limelight] tracking-wide text-center text-[var(--secondary)] mb-8">
            LOGIN
        </h2>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-400 text-center mb-4"><?= $_SESSION['error'];
                unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form action="" method="POST" class="flex flex-col gap-5">

            <!-- Email -->
            <div>
                <input type="email" name="email"
                       placeholder="Enter your email"
                       required
                >
            </div>

            <!-- Password -->
            <div class="relative">
                <input id="login-password" type="password" name="password"
                       placeholder="Enter your password"
                       required
                >

                <button type="button" id="toggle-login-password"
                        class="absolute right-3 top-1/2 -translate-y-1/2">
                    <i class="pi pi-eye"></i>
                </button>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-full w-full">
                <i class="pi pi-sign-in"></i> Login
            </button>

        </form>

        <p class="text-center text-sm text-white/60 mt-6">
            Don't have an account?
            <a href="signup.php" class="text-[var(--secondary)]">Sign Up</a>
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
