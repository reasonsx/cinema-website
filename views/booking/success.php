<?php
session_start();
require_once __DIR__ . '/../../backend/connection.php';
require_once __DIR__ . '/../../backend/stripe_config.php';

$session_id = $_GET['session_id'] ?? null;

if (!$session_id) {
   header("Location: ../../home/index.php");
    exit;
}

$session = \Stripe\Checkout\Session::retrieve($session_id);
$paymentIntent = \Stripe\PaymentIntent::retrieve($session->payment_intent);

$userId = $session->metadata->user_id;
$screeningId = $session->metadata->screening_id;
$selectedSeats = explode(',', $session->metadata->seats);
$totalPrice = $paymentIntent->amount_received / 100;

// Insert booking
$stmtBooking = $db->prepare("INSERT INTO bookings (user_id, screening_id, total_price) VALUES (?, ?, ?)");
$stmtBooking->execute([$userId, $screeningId, $totalPrice]);
$bookingId = $db->lastInsertId();

// Insert seats
$stmtSeats = $db->prepare("INSERT INTO booking_seats (booking_id, seat_id, screening_id) VALUES (?, ?, ?)");
foreach ($selectedSeats as $seatId) {
    $stmtSeats->execute([$bookingId, $seatId, $screeningId]);
}

// Clear session
unset($_SESSION['selected_screening'], $_SESSION['selected_seats']);
?>

<!DOCTYPE html>
<html lang="en">

<?php include __DIR__ . '/../../shared/head.php'; ?>

<body class="bg-black text-white font-sans">

<?php include __DIR__ . '/../../shared/header.php'; ?>

<section class="px-6 md:px-8 py-20 max-w-3xl mx-auto text-center">
    <h1 class="text-4xl md:text-5xl font-[Limelight] text-[#F8A15A] mb-8">Payment Successful!</h1>

    <div class="bg-white/10 rounded-lg p-8 mb-6 shadow-lg">
        <p class="mb-2 text-lg">Booking ID: <span class="font-bold"><?= $bookingId ?></span></p>
        <p class="mb-2 text-lg">Movie: <span class="font-bold"><?= htmlspecialchars($screening['movie_title'] ?? '') ?></span></p>
        <p class="mb-2 text-lg">Seats: <span class="font-bold"><?= implode(', ', array_map('htmlspecialchars', $selectedSeats)) ?></span></p>
        <p class="mb-2 text-lg">Total Paid: <span class="font-bold">$<?= number_format($totalPrice, 2) ?></span></p>
    </div>

    <a href="index.php"
       class="inline-flex items-center justify-center gap-2 rounded-full bg-[var(--secondary)] px-8 py-3 text-sm font-semibold text-black hover:shadow-[0_0_25px_var(--secondary)] transition">
        <i class="pi pi-arrow-left"></i>
        Go back to home
    </a>
</section>

<?php include __DIR__ . '/../../shared/footer.php'; ?>


</body>
</html>
