<?php
// signup.php
include 'header.php';
?>

<section class="flex justify-center items-center min-h-[80vh] bg-light px-4">
    <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
        <h2 class="text-3xl font-header mb-6 text-center text-primary">Create an Account</h2>
        <form action="signup_process.php" method="POST" class="flex flex-col gap-4">
            <input type="text" name="name" placeholder="Full Name" required class="px-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
            <input type="email" name="email" placeholder="Email" required class="px-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
            <input type="password" name="password" placeholder="Password" required class="px-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required class="px-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
            <button type="submit" class="btn w-full text-center">
                <i class="pi pi-user-plus"></i> Sign Up
            </button>
        </form>
        <p class="text-center text-sm text-gray-500 mt-4">
            Already have an account? <a href="login.php" class="text-primary hover:text-secondary">Login</a>
        </p>
    </div>
</section>

<?php include 'footer.php'; ?>
