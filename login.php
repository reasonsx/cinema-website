<?php
// login.php
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-light text-black font-sans">

<?php include 'header.php'; ?>

<section class="flex justify-center items-center min-h-[80vh] bg-light px-4">
    <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
        <h2 class="text-3xl font-header mb-6 text-center text-primary">Log in</h2>
        <form action="login_process.php" method="POST" class="flex flex-col gap-4">
            <input type="email" name="email" placeholder="Email" required class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">

            <div class="relative">
                <input id="login-password" type="password" name="password" placeholder="Password" required class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-full">
                <button type="button" id="toggle-login-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black focus:outline-none">
                    <i class="pi pi-eye"></i>
                </button>
            </div>

            <div class="text-right">
                <a href="forgot_password.php" class="text-sm text-primary hover:text-secondary">Forgot your password?</a>
            </div>

            <button type="submit" class="btn w-full text-center justify-center items-center">
                <i class="pi pi-sign-in"></i> Login
            </button>
        </form>

        <script>
            const loginPassword = document.getElementById('login-password');
            const toggleLogin = document.getElementById('toggle-login-password');
            toggleLogin.addEventListener('click', () => {
                const type = loginPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                loginPassword.setAttribute('type', type);
                toggleLogin.innerHTML = type === 'password' ? '<i class="pi pi-eye"></i>' : '<i class="pi pi-eye-slash"></i>';
            });
        </script>

        <p class="text-center text-sm text-gray-500 mt-4">
            Don't have an account? <a href="signup.php" class="text-primary hover:text-secondary">Sign Up</a>
        </p>
    </div>
</section>

<?php include 'footer.php'; ?>
