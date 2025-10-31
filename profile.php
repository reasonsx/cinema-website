<?php
session_start();
require_once 'include/connection.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /cinema-website/auth/login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user info
$stmt = $db->prepare("SELECT firstname, lastname, email FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // User not found, log out
    header("Location: /cinema-website/auth/logout.php");
    exit;
}

// Default values
$firstname = $user['firstname'] ?? 'User';
$lastname  = $user['lastname'] ?? '';
$email     = $user['email'] ?? 'No email';
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-black text-white font-sans">
<?php include 'header.php'; ?>

<section class="px-6 md:px-8 py-10 max-w-3xl mx-auto">
    <h1 class="text-4xl font-[Limelight] text-[#F8A15A] mb-6">My Profile</h1>

    <div class="bg-white/10 rounded-lg p-6 flex flex-col items-center gap-4">
        <!-- Placeholder profile picture -->
        <div class="w-24 h-24 rounded-full bg-gray-500 flex items-center justify-center text-2xl font-bold text-white">
            <?= strtoupper($firstname[0] . ($lastname[0] ?? '')) ?>
        </div>

        <h2 class="text-xl font-semibold"><?= htmlspecialchars($firstname . ' ' . $lastname) ?></h2>
        <p class="text-white/80"><?= htmlspecialchars($email) ?></p>

        <div class="mt-4 flex gap-4">
            <a href="edit-profile.php"
               class="px-4 py-2 rounded-full bg-[var(--secondary)] text-black font-semibold hover:shadow-[0_0_18px_var(--secondary)] transition">
                Edit Profile
            </a>
            <a href="/cinema-website/auth/logout.php"
               class="px-4 py-2 rounded-full border border-white/30 text-white hover:bg-white hover:text-black transition">
                Logout
            </a>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
