<?php
session_start();

require_once __DIR__ . '/../../backend/connection.php';
require_once __DIR__ . '/../../backend/stripe_config.php'; // Loads Stripe API key from .env
require_once __DIR__ . '/../../admin_dashboard/views/screenings/screenings_functions.php';
require_once __DIR__ . '/../../admin_dashboard/views/screening_rooms/screening_rooms_functions.php';

// Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../profile/login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$screeningId = $_SESSION['selected_screening'] ?? null;
$selectedSeats = $_SESSION['selected_seats'] ?? [];

// Validate session data
if (!$screeningId || empty($selectedSeats)) {
    header("Location: ../../home/index.php");
    exit;
}

// Get screening
$screening = getScreeningById($db, $screeningId);

// Get seat price
$stmt = $db->prepare("SELECT seat_price FROM screening_rooms WHERE id = ?");
$stmt->execute([$screening['screening_room_id']]);
$seatPrice = $stmt->fetchColumn();

// Total
$totalPrice = $seatPrice * count($selectedSeats);

// Stripe Checkout handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $screening['movie_title'] . " - Seats: " . implode(', ', $selectedSeats),
                    ],
                    'unit_amount' => $totalPrice * 100, // cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'http://localhost/cinema-website/views/booking/success.php?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'http://localhost/cinema-website/views/booking/payment.php',
            'metadata' => [
                'user_id' => $userId,
                'screening_id' => $screeningId,
                'seats' => implode(',', $selectedSeats),
            ],
        ]);

        header("Location: " . $checkout_session->url);
        exit;

    } catch (\Stripe\Exception\ApiErrorException $e) {
        $error = $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<?php include __DIR__ . '/../../shared/head.php'; ?>

<body class="bg-black text-white font-sans">

<?php include __DIR__ . '/../../shared/header.php'; ?>

<!-- CHECKOUT PAGE -->
<section class="px-6 md:px-8 py-12 flex justify-center min-h-[70vh]">
    <div class="w-full max-w-md rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl p-8">

        <h1 class="text-4xl font-[Limelight] text-[var(--secondary)] mb-8 text-center">
            Checkout
        </h1>

        <?php if (!empty($error)): ?>
            <div class="rounded-lg bg-red-800/40 p-4 mb-6 text-red-300">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Summary -->
        <div class="space-y-3 text-white/90 text-lg mb-6">

            <p>
                <strong class="text-white">Movie:</strong><br>
                <?= htmlspecialchars($screening['movie_title']) ?>
            </p>

            <p>
                <strong class="text-white">Seats:</strong><br>
                <span class="text-[var(--secondary)] font-semibold">
                    <?= implode(', ', array_map('htmlspecialchars', $selectedSeats)) ?>
                </span>
            </p>

            <p>
                <strong class="text-white">Tickets:</strong><br>
                <?= count($selectedSeats) ?> ticket<?= count($selectedSeats) > 1 ? 's' : '' ?>
            </p>

            <p>
                <strong class="text-white">Price per ticket:</strong><br>
                <span class="text-[var(--secondary)]">$<?= number_format($seatPrice, 2) ?></span>
            </p>

            <p class="pt-4 border-t border-white/10 text-2xl font-bold text-white">
                Total:
                <span class="text-[var(--secondary)]">$<?= number_format($totalPrice, 2) ?></span>
            </p>

        </div>

        <!-- Payment Button -->
        <form method="POST" class="text-center mt-8">
            <button type="submit" class="btn-full w-full">
                <i class="pi pi-credit-card"></i>
                Pay $<?= number_format($totalPrice, 2) ?>
            </button>
        </form>

    </div>
</section>

<?php include __DIR__ . '/../../shared/footer.php'; ?>

</body>
</html>
