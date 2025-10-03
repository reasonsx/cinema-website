<?php
// signup.php
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-light text-black font-sans">

<?php include 'header.php'; ?>

<section class="flex justify-center items-center min-h-[80vh] bg-light px-4">
    <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
        <h2 class="text-3xl font-header mb-6 text-center text-primary">Create an Account</h2>
        <form action="signup_process.php" method="POST" class="flex flex-col gap-4">
            <input type="text" name="name" placeholder="Full Name" required class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
            <input type="email" name="email" placeholder="Email" required class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">

            <!-- Password Field -->
            <div class="relative">
                <input id="signup-password" type="password" name="password" placeholder="Password" required class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-full">
                <button type="button" id="toggle-signup-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black focus:outline-none">
                    <i class="pi pi-eye"></i>
                </button>
            </div>

            <!-- Confirm Password Field -->
            <div class="relative">
                <input id="signup-confirm-password" type="password" name="confirm_password" placeholder="Confirm Password" required class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-full">
                <button type="button" id="toggle-signup-confirm-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black focus:outline-none">
                    <i class="pi pi-eye"></i>
                </button>
            </div>

            <button type="submit" class="btn w-full text-center justify-center items-center">
                <i class="pi pi-user-plus"></i> Sign Up
            </button>
        </form>
        <p class="text-center text-sm text-gray-500 mt-4">
            Already have an account? <a href="login.php" class="text-primary hover:text-secondary">Login</a>
        </p>
    </div>
</section>

<script>
    // Toggle password visibility
    const signupPassword = document.getElementById('signup-password');
    const toggleSignup = document.getElementById('toggle-signup-password');
    toggleSignup.addEventListener('click', () => {
        const type = signupPasswo
