<?php
require_once 'include/connection.php';
require_once 'auth/session.php';
require_once 'admin_dashboard/views/users/users_functions.php';
require_once 'admin_dashboard/views/bookings/bookings_functions.php';

// Initialize session manager and require login
$session = new SessionManager($db);
$session->requireLogin();

$userId = $session->getUserId();
$isAdmin = $session->isAdmin();

// Fetch user info
$user = getUserById($db, $userId);
if (!$user) {
    $session->logout();
    header("Location: /cinema-website/auth/login.php");
    exit;
}

// Determine if edit mode is active
$isEditing = isset($_GET['edit']) && $_GET['edit'] === 'true';

// Handle profile update
$updateMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['current_password'])) {
    [$successMsg, $errorMsg] = updateUserProfile($db, $userId, [
        'firstname'        => $_POST['firstname'],
        'lastname'         => $_POST['lastname'],
        'email'            => $_POST['email'],
        'current_password' => $_POST['current_password'],
        'new_password'     => $_POST['new_password'] ?? ''
    ]);

    if ($successMsg) {
        $updateMessage = '<p class="text-green-400">' . htmlspecialchars($successMsg) . '</p>';
        $_SESSION['firstname'] = $_POST['firstname'];
        $_SESSION['lastname']  = $_POST['lastname'];
        $user = getUserById($db, $userId); // refresh updated info
        $isEditing = false; // return to view mode
    } else {
        $updateMessage = '<p class="text-red-400">' . htmlspecialchars($errorMsg) . '</p>';
    }
}

// Fetch booking history
$bookings = getBookingsByUserId($db, $userId);

// Sanitize for display
$firstname = htmlspecialchars($user['firstname']);
$lastname  = htmlspecialchars($user['lastname']);
$email     = htmlspecialchars($user['email']);
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-black text-white font-sans">
<?php include 'header.php'; ?>

<section class="px-6 md:px-8 py-10 max-w-4xl mx-auto">
    <h1 class="text-4xl font-[Limelight] tracking-wide text-secondary mb-8">My Profile</h1>

    <!-- PROFILE CARD -->
    <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur p-8 mb-10 shadow-2xl">
        <div class="flex flex-col items-center gap-4">

            <!-- Avatar -->
            <div class="w-24 h-24 rounded-full bg-white/10 border border-white/20
                        flex items-center justify-center text-3xl font-bold text-secondary shadow-xl">
                <?= strtoupper($firstname[0] . ($lastname[0] ?? '')) ?>
            </div>

            <h2 class="text-2xl font-semibold"><?= "$firstname $lastname" ?></h2>
            <p class="text-white/60"><?= $email ?></p>

            <?= $updateMessage ?>

            <?php if ($isEditing): ?>
                <!-- EDIT PROFILE -->
                <form method="POST" class="mt-6 w-full max-w-md space-y-4">

                    <input type="text" name="firstname" value="<?= $firstname ?>" placeholder="First name"
                           required class="w-full p-3 rounded-xl bg-black/30 border border-white/10 text-white">

                    <input type="text" name="lastname" value="<?= $lastname ?>" placeholder="Last name"
                           required class="w-full p-3 rounded-xl bg-black/30 border border-white/10 text-white">

                    <input type="email" name="email" value="<?= $email ?>" placeholder="Email"
                           required class="w-full p-3 rounded-xl bg-black/30 border border-white/10 text-white">

                    <input type="password" name="new_password" placeholder="New password (optional)"
                           class="w-full p-3 rounded-xl bg-black/30 border border-white/10 text-white">

                    <input type="password" name="current_password"
                           placeholder="Current password (required)"
                           required class="w-full p-3 rounded-xl bg-black/30 border border-white/10 text-white">

                    <div class="flex gap-3">
                        <button type="submit"
                                class="flex-1 bg-[var(--secondary)] text-black font-semibold py-3 rounded-full">
                            Save Changes
                        </button>

                        <a href="profile.php"
                           class="flex-1 border border-white/20 text-white font-semibold py-3 rounded-full
                                  hover:bg-white/10 transition text-center">
                            Cancel
                        </a>
                    </div>
                </form>

            <?php else: ?>
                <!-- VIEW MODE BUTTONS -->
                <div class="mt-6 flex flex-wrap justify-center gap-3">

                    <a href="profile.php?edit=true"
                       class="btn">
                        <i class="pi pi-user-edit"></i>
                        Edit Profile
                    </a>

                    <?php if ($isAdmin): ?>
                        <a href="/cinema-website/admin_dashboard.php"
                           class="btn">
                            <i class="pi pi-cog"></i>
                            Go to Admin Dashboard
                        </a>
                    <?php endif; ?>

                    <a href="/cinema-website/auth/logout.php"
                       class="px-6 py-3 rounded-full border border-white/20 hover:bg-white/10 transition font-semibold">
                        Logout
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- BOOKING HISTORY CARD -->
    <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur p-8 shadow-2xl">
        <h2 class="text-2xl font-semibold mb-4 text-secondary flex items-center gap-2">
            <i class="pi pi-ticket"></i> Booking History
        </h2>

        <?php if (empty($bookings)): ?>
            <p class="text-white/50 italic">You haven’t made any bookings yet.</p>

        <?php else: ?>
            <ul class="space-y-4">
                <?php foreach ($bookings as $b): ?>
                    <li class="rounded-xl border border-white/10 bg-black/20 px-4 py-4 flex items-center justify-between hover:bg-black/30 transition">
                        <div>
                            <p class="font-semibold"><?= htmlspecialchars($b['title']) ?></p>
                            <p class="text-white/50 text-sm">
                                <?= date("M d, Y - H:i", strtotime($b['start_time'])) ?>
                            </p>
                        </div>
                        <p class="text-secondary font-semibold">$<?= number_format($b['total_price'], 2) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
