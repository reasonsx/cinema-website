<?php
// forgot_password.php
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../head.php'; ?>
<body class="bg-light text-black font-sans">

<?php include '../header.php'; ?>

<section class="flex justify-center items-center min-h-[80vh] bg-light px-4">
    <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
        <h2 class="text-3xl font-header mb-6 text-center text-primary">Forgot Password</h2>
        <p class="text-center text-gray-600 mb-6">
            Enter your email address, and we'll send you a link to reset your password.
        </p>

        <form action="forgot_password_process.php" method="POST" class="flex flex-col gap-4">
            <!-- Email -->
            <div class="flex flex-col">
                <label for="forgot-email" class="sr-only">Email</label>
                <input id="forgot-email" type="email" name="email" placeholder="Email" required
                       class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <!-- Submit Button -->
            <button id="forgot-button" type="submit"
                    class="btn w-full text-center justify-center items-center opacity-50 cursor-not-allowed"
                    disabled>
                <i class="pi pi-envelope"></i> Send Reset Link
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-4">
            Remembered your password? <a href="login.php" class="text-primary hover:text-secondary">Login</a>
        </p>
    </div>
</section>

<script>
    const forgotEmail = document.getElementById('forgot-email');
    const forgotButton = document.getElementById('forgot-button');

    forgotEmail.addEventListener('input', () => {
        if (forgotEmail.value.trim() !== '') {
            forgotButton.disabled = false;
            forgotButton.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            forgotButton.disabled = true;
            forgotButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
    });
</script>

<?php include '../footer.php'; ?>
</body>
</html>
