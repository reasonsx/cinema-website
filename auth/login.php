<?php
session_start();
require_once '../include/connection.php';

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

            if ($user['isAdmin']) {
    header('Location: /cinema-website/profile.php');
} else {
    header('Location: /cinema-website/profile.php');
}

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
<?php include '../head.php'; ?>
<body class="bg-light text-black font-sans">

<?php include '../header.php'; ?>

<section class="flex justify-center items-center min-h-[80vh] bg-light px-4">
    <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
        <h2 class="text-3xl font-header mb-6 text-center text-primary">Log in</h2>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p class="text-red-500 text-center mb-4">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<p class="text-green-500 text-center mb-4">' . $_SESSION['success'] . '</p>';
            unset($_SESSION['success']);
        }
        ?>

        <form action="" method="POST" class="flex flex-col gap-4">

            <!-- Email -->
            <div class="flex flex-col">
                <label for="login-email" class="sr-only">Email</label>
                <input id="login-email" type="email" name="email" placeholder="Email" required
                       class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <!-- Password -->
            <div class="flex flex-col relative">
                <label for="login-password" class="sr-only">Password</label>
                <input id="login-password" type="password" name="password" placeholder="Password" required
                       class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-full">
                <button type="button" id="toggle-login-password"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black focus:outline-none">
                    <i class="pi pi-eye"></i>
                </button>
            </div>

            <!-- Forgot Password Link -->
            <div class="text-right">
                <a href="forgot_password.php" class="text-sm text-primary hover:text-secondary">Forgot your
                    password?</a>
            </div>

            <!-- Submit Button -->
            <button id="login-button" type="submit"
                    class="btn w-full text-center justify-center items-center opacity-50 cursor-not-allowed"
                    disabled>
                <i class="pi pi-sign-in"></i> Login
            </button>

        </form>

        <script>
            const loginInputs = [
                document.getElementById('login-email'),
                document.getElementById('login-password')
            ];
            const loginButton = document.getElementById('login-button');

            function checkLoginInputs() {
                const allFilled = loginInputs.every(input => input.value.trim() !== '');
                loginButton.disabled = !allFilled;

                if (allFilled) {
                    loginButton.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    loginButton.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }

            loginInputs.forEach(input => input.addEventListener('input', checkLoginInputs));

            // Toggle password visibility
            const loginPassword = document.getElementById('login-password');
            const toggleLogin = document.getElementById('toggle-login-password');
            toggleLogin.addEventListener('click', () => {
                const type = loginPassword.type === 'password' ? 'text' : 'password';
                loginPassword.type = type;
                toggleLogin.innerHTML = type === 'password' ? '<i class="pi pi-eye"></i>' : '<i class="pi pi-eye-slash"></i>';
            });
        </script>

        <p class="text-center text-sm text-gray-500 mt-4">
            Don't have an account? <a href="signup.php" class="text-primary hover:text-secondary">Sign Up</a>
        </p>
    </div>
</section>

<?php include '../footer.php'; ?>
</body>
</html>
