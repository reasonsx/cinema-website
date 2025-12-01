<?php
session_start();
require_once __DIR__ . '/../../backend/connection.php';
require_once __DIR__ . '/../../admin_dashboard/views/screenings/screenings_functions.php';
require_once __DIR__ . '/../../admin_dashboard/views/screening_rooms/screening_rooms_functions.php';

// Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$screeningId = $_SESSION['selected_screening'] ?? null;
$selectedSeats = $_SESSION['selected_seats'] ?? [];

if (!$screeningId || empty($selectedSeats)) {
    header("Location: index.php");
    exit;
}

$screening = getScreeningById($db, $screeningId);

// Get seat price
$stmt = $db->prepare("SELECT seat_price FROM screening_rooms WHERE id = ?");
$stmt->execute([$screening['screening_room_id']]);
$seatPrice = $stmt->fetchColumn();
$totalPrice = $seatPrice * count($selectedSeats);

?>
<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/../../shared/head.php'; ?>
<body class="bg-black text-white">
<?php include __DIR__ . '/../../shared/header.php'; ?>

<section class="px-6 md:px-8 py-10 max-w-3xl mx-auto">
    <h1 class="text-4xl font-[Limelight] text-[#F8A15A] mb-6">Checkout</h1>

    <div class="bg-white/10 rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($screening['movie_title']) ?></h2>
        <p class="text-white/80 mb-2">Room: <?= htmlspecialchars($screening['room_name']) ?></p>
        <p class="text-white/80 mb-2">Time: <?= htmlspecialchars($screening['start_time']) ?> - <?= htmlspecialchars($screening['end_time']) ?></p>
        <p class="text-white/80">Selected seats: 
            <?= implode(', ', array_map('htmlspecialchars', $selectedSeats)) ?>
        </p>
        <p class="mt-3 font-bold text-lg">Total: $<?= number_format($totalPrice, 2) ?></p>
    </div>

    <form action="payment.php" method="POST">
        <input type="hidden" name="screening_id" value="<?= $screeningId ?>">
        <button type="submit"
            class="w-full md:w-auto inline-flex items-center justify-center gap-2 rounded-full bg-[var(--secondary)] px-8 py-3 text-sm font-semibold text-black hover:shadow-[0_0_25px_var(--secondary)] transition">
            <i class="pi pi-credit-card"></i>
            Proceed to Payment
        </button>
    </form>
</section>

<?php include __DIR__ . '/../../shared/footer.php'; ?>
</body>
</html>
