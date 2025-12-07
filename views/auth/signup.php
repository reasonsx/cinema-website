<?php
session_start();
require_once '../../backend/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($firstname === '' || $lastname === '') {
        $_SESSION['error'] = 'First and last name are required.';
        header("Location: signup.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format.';
        header("Location: signup.php");
        exit;
    }

    if (strlen($email) > 255) {
        $_SESSION['error'] = 'Email is too long.';
        header("Location: signup.php");
        exit;
    }

    if ($password !== $confirm) {
        $_SESSION['error'] = 'Passwords do not match.';
        header("Location: signup.php");
        exit;
    }

    if (strlen($password) < 8) {
        $_SESSION['error'] = 'Password must be at least 8 characters long.';
        header("Location: signup.php");
        exit;
    }

    try {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = 'Email already registered.';
            header("Location: signup.php");
            exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("
            INSERT INTO users (email, password, firstname, lastname, isAdmin)
            VALUES (?, ?, ?, ?, 0)
        ");

        $stmt->execute([$email, $hash, $firstname, $lastname]);

        $_SESSION['success'] = 'Account created successfully! Please log in.';
        header('Location: login.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error.';
        header("Location: signup.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include '../../shared/head.php'; ?>

<body class="bg-black text-black font-sans">

<?php include '../../shared/header.php'; ?>

<section class="flex justify-center items-center min-h-[70vh] bg-black px-4 py-10">
    <div class="w-full max-w-md rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl p-8">

        <h2 class="text-4xl font-[Limelight] tracking-wide text-center text-[var(--secondary)] mb-8">
            CREATE ACCOUNT
        </h2>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-400 text-center mb-4"><?= $_SESSION['error'] ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <p class="text-green-400 text-center mb-4"><?= $_SESSION['success'] ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="POST" class="flex flex-col gap-5">

            <input id="firstname" type="text" name="firstname" placeholder="First Name" required>
            <input id="lastname" type="text" name="lastname" placeholder="Last Name" required>
            <input id="email" type="email" name="email" placeholder="Email" required>

            <div class="relative">
                <input id="signup-password" type="password" name="password" placeholder="Password" required>
                <button type="button" id="toggle-signup-password"
                        class="absolute right-3 top-1/2 -translate-y-1/2">
                    <i class="pi pi-eye"></i>
                </button>
            </div>

            <div class="relative">
                <input id="signup-confirm-password" type="password" name="confirm_password"
                       placeholder="Confirm Password" required>
                <button type="button" id="toggle-signup-confirm-password"
                        class="absolute right-3 top-1/2 -translate-y-1/2">
                    <i class="pi pi-eye"></i>
                </button>
            </div>

            <button id="signup-button" type="submit"
                    class="btn-full w-full opacity-50 cursor-not-allowed" disabled>
                <i class="pi pi-user-plus"></i> Sign Up
            </button>

        </form>

        <p class="text-center text-sm text-white/60 mt-6">
            Already have an account?
            <a href="login.php" class="text-[var(--secondary)]">Login</a>
        </p>

    </div>
</section>

<script>
    function setupToggle(inputId, btnId) {
        const input = document.getElementById(inputId);
        const btn = document.getElementById(btnId);

        btn.addEventListener('click', () => {
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            btn.innerHTML = isPass ? '<i class="pi pi-eye-slash"></i>' : '<i class="pi pi-eye"></i>';
        });
    }

    setupToggle('signup-password', 'toggle-signup-password');
    setupToggle('signup-confirm-password', 'toggle-signup-confirm-password');

    const signupButton = document.getElementById('signup-button');
    const fields = [
        'firstname', 'lastname', 'email',
        'signup-password', 'signup-confirm-password'
    ].map(id => document.getElementById(id));

    function validate() {
        const filled = fields.every(f => f.value.trim() !== '');
        signupButton.disabled = !filled;
        signupButton.classList.toggle('opacity-50', !filled);
        signupButton.classList.toggle('cursor-not-allowed', !filled);
    }

    fields.forEach(f => f.addEventListener('input', validate));
</script>

<?php include '../../shared/footer.php'; ?>

</body>
</html>
