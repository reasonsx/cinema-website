<?php
session_start();
require_once '../backend/connection.php'; // keep this path since your folder is "backend"

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
<body class="bg-light text-black font-sans">

<?php include '../shared/header.php'; ?>

<section class="flex justify-center items-center min-h-[80vh] bg-light px-4">
    <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
        <h2 class="text-3xl font-header mb-6 text-center text-primary">Create an Account</h2>
        <?php
            if (isset($_SESSION['error'])) {
                echo '<p class="text-red-500 text-center mb-4">'.$_SESSION['error'].'</p>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<p class="text-green-500 text-center mb-4">'.$_SESSION['success'].'</p>';
                unset($_SESSION['success']);
            }
            ?>

       <form action="" method="POST" class="flex flex-col gap-4">

           <!-- First Name -->
<div class="flex flex-col">
    <label for="firstname" class="sr-only">First Name</label>
    <input id="firstname" type="text" name="firstname" placeholder="First Name" required
           class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>

<!-- Last Name -->
<div class="flex flex-col">
    <label for="lastname" class="sr-only">Last Name</label>
    <input id="lastname" type="text" name="lastname" placeholder="Last Name" required
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
<!-- Admin Checkbox (development only) -->
<div class="flex items-center gap-2">
    <input type="checkbox" id="isAdmin" name="isAdmin" value="1">
    <label for="isAdmin" class="text-sm text-gray-700">Make user admin (development feature)</label>
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
    document.getElementById('firstname'),
    document.getElementById('lastname'),
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



<?php include '../shared/footer.php'; ?>

</body>
</html>
