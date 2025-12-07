<?php
session_start();
require_once __DIR__ . '/../../backend/connection.php';
require_once __DIR__ . '/../../admin_dashboard/views/screenings/screenings_functions.php';
require_once __DIR__ . '/../../admin_dashboard/views/screening_rooms/screening_rooms_functions.php';

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
$screeningDate = date('D, M j', strtotime($screening['start_time']));
$startTimeFormatted = date('H:i', strtotime($screening['start_time']));
$endTimeFormatted = date('H:i', strtotime($screening['end_time']));

$start = strtotime($screening['start_time']);
$end = strtotime($screening['end_time']);

$runtimeMinutes = round(($end - $start) / 60);
$runtimeHours = floor($runtimeMinutes / 60);
$runtimeRemaining = $runtimeMinutes % 60;

$runtimeFormatted = "{$runtimeHours}h {$runtimeRemaining}m";

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

<!-- CHECKOUT -->
<section class="px-6 md:px-8 py-10 h-[70vh] max-w-3xl mx-auto">

    <h1 class="text-5xl font-[Limelight] text-[var(--secondary)] mb-10 text-center">
        Checkout
    </h1>

    <!-- Checkout Summary -->
    <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl p-8 mb-10">

        <h2 class="text-3xl font-[Limelight] text-white mb-4">
            <?= htmlspecialchars($screening['movie_title']) ?>
        </h2>

        <div class="space-y-2 text-white/80 text-lg">
            <p><strong class="text-white">Room:</strong> <?= htmlspecialchars($screening['room_name']) ?></p>
            <p>
                <strong class="text-white">Date:</strong>
                <?= $screeningDate ?>
            </p>

            <p>
                <strong class="text-white">Time:</strong>
                <?= $startTimeFormatted ?> - <?= $endTimeFormatted ?>

            </p>

            <p>
                <strong class="text-white">Runtime:</strong>
                <?= $runtimeFormatted ?>
                <span class="text-white/50">(<?= $runtimeMinutes ?> min)</span>
            </p>

            <p>
                <strong class="text-white">Seats:</strong>
                <span class="text-[var(--secondary)] font-semibold">
                <?= implode(', ', array_map('htmlspecialchars', $selectedSeats)) ?>
            </span>
            </p>

            <p>
                <strong class="text-white">Tickets:</strong>
                <?= count($selectedSeats) ?> ticket<?= count($selectedSeats) > 1 ? 's' : '' ?>
            </p>

            <p>
                <strong class="text-white">Price per ticket:</strong>
                <span class="text-[var(--secondary)]">$<?= number_format($seatPrice, 2) ?></span>
            </p>
        </div>

        <div class="mt-6 pt-4 border-t border-white/10">
            <p class="text-2xl font-bold text-white">
                Total:
                <span class="text-[var(--secondary)]">$<?= number_format($totalPrice, 2) ?></span>
            </p>
        </div>
    </div>

    <!-- Payment Button -->
    <form action="payment.php" method="POST" class="text-center">
        <input type="hidden" name="screening_id" value="<?= $screeningId ?>">
        <button type="submit" class="btn-full w-full md:w-auto px-10">
            <i class="pi pi-credit-card"></i>
            Proceed to Payment
        </button>
    </form>

</section>


<?php include __DIR__ . '/../../shared/footer.php'; ?>

</body>
</html>
