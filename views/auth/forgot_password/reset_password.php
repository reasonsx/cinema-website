<?php
session_start();
require_once '../../../backend/connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['token'])) {
    die("Invalid reset link.");
}

$token = $_GET['token'];

$stmt = $db->prepare("
    SELECT id FROM users 
    WHERE reset_token = ?
    AND reset_expires > NOW()
");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("This reset link is expired or invalid.");
}

$success = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("
            UPDATE users 
            SET password=?, reset_token=NULL, reset_expires=NULL
            WHERE id=?
        ");
        $stmt->execute([$hashed, $user['id']]);
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include '../../../shared/head.php'; ?>

<body class="bg-black text-white font-sans">

<?php include '../../../shared/header.php'; ?>

<section class="flex justify-center items-center min-h-[70vh] bg-black px-4 py-10">
    <div class="w-full max-w-md rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl p-8">

        <h2 class="text-4xl font-[Limelight] tracking-wide text-center text-[var(--secondary)] mb-6">
            Reset Password
        </h2>

        <?php if ($success): ?>
            <p class="text-[var(--secondary)] text-center mb-4">
                Password updated! You can now 
                <a href="../login.php" class="underline hover:text-white">log in</a>.
            </p>
        <?php elseif ($error): ?>
            <p class="text-white/80 text-center mb-4"><?= $error ?></p>
        <?php endif; ?>


        <?php if (!$success): ?>
        <form method="POST" class="flex flex-col gap-5">
            <input type="password"
                   name="password"
                   placeholder="New password"
                   required
                   class="px-4 py-3 rounded-xl text-black focus:outline-none">

            <button type="submit"
                    class="btn-full w-full">
                <i class="pi pi-key"></i> Reset Password
            </button>
        </form>
        <?php endif; ?>

        <p class="text-center text-white/60 text-sm mt-6">
            Remembered your password?
            <a href="login.php" class="text-[var(--secondary)] hover:text-white">Login</a>
        </p>

    </div>
</section>

<?php include '../../../shared/footer.php'; ?>

</body>
</html>
