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
            <!-- Full Name -->
            <div class="flex flex-col">
                <label for="name" class="sr-only">Full Name</label>
                <input id="name" type="text" name="name" placeholder="Full Name" required
                       class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <!-- Email -->
            <div class="flex flex-col">
                <label for="email" class="sr-only">Email</label>
                <input id="email" type="email" name="email" placeholder="Email" required
                       class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <!-- Password -->
            <div class="flex flex-col relative">
                <label for="signup-password" class="sr-only">Password</label>
                <input id="signup-password" type="password" name="password" placeholder="Password" required
                       class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-full">
                <button type="button" id="toggle-signup-password"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black focus:outline-none">
                    <i class="pi pi-eye"></i>
                </button>
            </div>

            <!-- Confirm Password -->
            <div class="flex flex-col relative">
                <label for="signup-confirm-password" class="sr-only">Confirm Password</label>
                <input id="signup-confirm-password" type="password" name="confirm_password" placeholder="Confirm Password" required
                       class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-full">
                <button type="button" id="toggle-signup-confirm-password"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black focus:outline-none">
                    <i class="pi pi-eye"></i>
                </button>
            </div>


            <button id="signup-button" type="submit" class="btn w-full text-center justify-center items-center opacity-50 cursor-not-allowed" disabled>
                <i class="pi pi-user-plus"></i> Sign Up
            </button>

        </form>
        <p class="text-center text-sm text-gray-500 mt-4">
            Already have an account? <a href="login.php" class="text-primary hover:text-secondary">Login</a>
        </p>
    </div>
</section>

<script>
    const signupInputs = [
        document.getElementById('name'),
        document.getElementById('email'),
        document.getElementById('signup-password'),
        document.getElementById('signup-confirm-password')
    ];
    const signupButton = document.getElementById('signup-button');

    function checkInputs() {
        const allFilled = signupInputs.every(input => input.value.trim() !== '');
        signupButton.disabled = !allFilled;

        if (allFilled) {
            signupButton.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            signupButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    signupInputs.forEach(input => {
        input.addEventListener('input', checkInputs);
    });
</script>



<?php include 'footer.php'; ?>

</body>
</html>
