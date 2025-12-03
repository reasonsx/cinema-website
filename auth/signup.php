<?php
session_start();
require_once '../backend/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match!';
    } else {
        try {
            // Check if email already exists
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['error'] = 'Email already registered!';
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                // Check if admin checkbox is checked
                $isAdmin = isset($_POST['isAdmin']) && $_POST['isAdmin'] == '1' ? 1 : 0;

                $stmt = $db->prepare("INSERT INTO users (email, password, firstname, lastname, isAdmin) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$email, $passwordHash, $firstname, $lastname, $isAdmin]);

                $_SESSION['success'] = 'Account created successfully! Please log in.';
                header('Location: login.php');
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        }
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
            CREATE ACCOUNT
        </h2>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<p class="text-red-400 text-center mb-4">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<p class="text-green-400 text-center mb-4">' . $_SESSION['success'] . '</p>';
            unset($_SESSION['success']);
        }
        ?>

        <form action="" method="POST" class="flex flex-col gap-5">

            <!-- First Name -->
            <div>
                <input id="firstname" type="text" name="firstname"
                       placeholder="First Name" required>
            </div>

            <!-- Last Name -->
            <div>
                <input id="lastname" type="text" name="lastname"
                       placeholder="Last Name" required>
            </div>

            <!-- Email -->
            <div>
                <input id="email" type="email" name="email"
                       placeholder="Email" required>
            </div>

            <!-- Password -->
            <div class="relative">
                <input id="signup-password" type="password" name="password"
                       placeholder="Password" required>

                <button type="button" id="toggle-signup-password"
                        class="absolute right-3 top-1/2 -translate-y-1/2">
                    <i class="pi pi-eye"></i>
                </button>
            </div>

            <!-- Confirm Password -->
            <div class="relative">
                <input id="signup-confirm-password" type="password" name="confirm_password"
                       placeholder="Confirm Password" required>

                <button type="button" id="toggle-signup-confirm-password"
                        class="absolute right-3 top-1/2 -translate-y-1/2">
                    <i class="pi pi-eye text-black"></i>
                </button>
            </div>

            <!-- Admin Checkbox (dev only) -->
            <div class="flex items-center gap-2 text-white/80">
                <input type="checkbox" id="isAdmin" name="isAdmin" value="1">
                <label for="isAdmin" class="text-sm">Make user admin (development feature)</label>
            </div>

            <!-- Submit Button -->
            <button id="signup-button" type="submit"
                    class="btn-full w-full opacity-50 cursor-not-allowed">
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
    // Toggle password visibility
    function setupPasswordToggle(inputId, toggleId) {
        const input = document.getElementById(inputId);
        const toggle = document.getElementById(toggleId);

        toggle.addEventListener('click', () => {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            toggle.innerHTML = isPassword ? '<i class="pi pi-eye-slash"></i>' : '<i class="pi pi-eye"></i>';
        });
    }

    // Apply toggles
    setupPasswordToggle('signup-password', 'toggle-signup-password');
    setupPasswordToggle('signup-confirm-password', 'toggle-signup-confirm-password');


    // Enable / disable submit button
    const signupButton = document.getElementById('signup-button');
    const signupInputs = [
        'firstname',
        'lastname',
        'email',
        'signup-password',
        'signup-confirm-password'
    ].map(id => document.getElementById(id));

    function checkInputs() {
        const allFilled = signupInputs.every(input => input.value.trim() !== '');
        signupButton.disabled = !allFilled;

        signupButton.classList.toggle('opacity-50', !allFilled);
        signupButton.classList.toggle('cursor-not-allowed', !allFilled);
    }

    signupInputs.forEach(input => input.addEventListener('input', checkInputs));
</script>

<?php include '../shared/footer.php'; ?>

</body>
</html>
