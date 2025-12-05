<?php
session_start();
require_once '../backend/connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<?php include '../shared/head.php'; ?>

<body class="bg-black text-white font-sans">

<?php include '../shared/header.php'; ?>

<section class="flex justify-center items-center min-h-[70vh] bg-black px-4 py-10">
    <div class="w-full max-w-md rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl p-8">

        <h2 class="text-4xl font-[Limelight] tracking-wide text-center text-[var(--secondary)] mb-6">
            FORGOT PASSWORD
        </h2>

        <p class="text-center text-white/70 text-sm mb-6">
            Enter your email and we’ll send you a link to reset your password.
        </p>

        <form action="forgot_password_process.php" method="POST" class="flex flex-col gap-5">

            <!-- Email -->
            <input type="email"
                   name="email"
                   placeholder="Enter your email"
                   required>

            <!-- Submit Button -->
            <button id="forgot-button"
                    type="submit"
                    class="btn-full w-full opacity-50 cursor-not-allowed"
                    disabled>
                <i class="pi pi-envelope"></i> Send Reset Link
            </button>
        </form>

        <p class="text-center text-white/60 text-sm mt-6">
            Remembered your password?
            <a href="login.php" class="text-[var(--secondary)] hover:text-white">Login</a>
        </p>

    </div>
</section>

<script>
    const forgotEmail = document.getElementById('forgot-email');
    const emailInput = document.querySelector('input[name="email"]');
    const forgotButton = document.getElementById('forgot-button');

    // Enable button only when email is not empty
    emailInput.addEventListener('input', () => {
        if (emailInput.value.trim() !== "") {
            forgotButton.disabled = false;
            forgotButton.classList.remove("opacity-50", "cursor-not-allowed");
        } else {
            forgotButton.disabled = true;
            forgotButton.classList.add("opacity-50", "cursor-not-allowed");
        }
    });
</script>

<?php include '../shared/footer.php'; ?>

</body>
</html>
