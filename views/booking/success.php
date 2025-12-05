<?php
session_start();
require_once __DIR__ . '/../../backend/connection.php';
require_once __DIR__ . '/../../backend/stripe_config.php';

// Get session ID from Stripe redirect
$session_id = $_GET['session_id'] ?? null;

if (!$session_id) {
    header("Location: ../../index.php");
    exit;
}

// Retrieve Stripe objects
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

// Fetch screening (movie, time, screening room)
$stmt = $db->prepare("
    SELECT 
        m.title AS movie_title,
        s.start_time,
        s.end_time,
        r.name AS room_name
    FROM screenings s
    JOIN movies m ON m.id = s.movie_id
    JOIN screening_rooms r ON r.id = s.screening_room_id
    WHERE s.id = ?
");
$stmt->execute([$screeningId]);
$details = $stmt->fetch(PDO::FETCH_ASSOC);

// Format data
$movieTitle = $details['movie_title'];
$startTime = date('D, d M Y • H:i', strtotime($details['start_time']));
$endTime = date('H:i', strtotime($details['end_time']));
$roomName = $details['room_name'];

// Clear seat/session data
unset($_SESSION['selected_screening'], $_SESSION['selected_seats']);
?>

<!DOCTYPE html>
<html lang="en">

<?php include __DIR__ . '/../../shared/head.php'; ?>

<body class="bg-black text-white font-sans">

<?php include __DIR__ . '/../../shared/header.php'; ?>

<!-- SUCCESS PAGE -->
<section class="px-6 md:px-8 py-20 flex justify-center min-h-[60vh]">
    <div class="w-full max-w-lg rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl p-8">

        <!-- Success Icon -->
        <div class="text-center mb-6">
            <div class="flex justify-center mb-4">
                <span class="flex items-center justify-center w-20 h-20 rounded-full bg-green-600/20 border border-green-500/30">
                    <i class="pi pi-check text-green-400 text-4xl"></i>
                </span>
            </div>

            <h1 class="text-4xl font-[Limelight] text-[var(--secondary)]">Booking Confirmed!</h1>
            <p class="text-white/60 mt-2">Your tickets are ready</p>
        </div>

        <!-- Ticket Summary -->
        <div class="bg-black/30 border border-white/10 rounded-2xl p-6 space-y-4 shadow-inner">

            <!-- Movie Title -->
            <div>
                <p class="text-sm text-white/60 uppercase">Movie</p>
                <p class="text-xl font-semibold text-white">
                    <?= htmlspecialchars($movieTitle) ?>
                </p>
            </div>

            <!-- Date, Time -->
            <div>
                <p class="text-sm text-white/60 uppercase">Date & Time</p>
                <p class="text-lg text-[var(--secondary)] font-semibold">
                    <?= $startTime ?> – <?= $endTime ?>
                </p>
            </div>

            <!-- Screening Room -->
            <div>
                <p class="text-sm text-white/60 uppercase">Screening Room</p>
                <p class="text-lg font-semibold text-white"><?= htmlspecialchars($roomName) ?></p>
            </div>

            <!-- Seats -->
            <div>
                <p class="text-sm text-white/60 uppercase">Seats</p>
                <p class="text-lg font-semibold text-[var(--secondary)]">
                    <?= implode(', ', array_map('htmlspecialchars', $selectedSeats)) ?>
                </p>
            </div>

            <!-- Booking ID -->
            <div>
                <p class="text-sm text-white/60 uppercase">Booking ID</p>
                <p class="text-lg font-semibold text-white"><?= $bookingId ?></p>
            </div>

            <!-- Total Paid -->
            <div class="pt-4 border-t border-white/10">
                <p class="text-sm text-white/60 uppercase">Total Paid</p>
                <p class="text-3xl font-bold text-[var(--secondary)]">
                    $<?= number_format($totalPrice, 2) ?>
                </p>
            </div>
        </div>

        <!-- Back Home -->
        <div class="text-center mt-10">
            <a href="/cinema-website/index.php"
               class="btn-full inline-flex items-center gap-2 px-8 py-3 rounded-full text-black">
                <i class="pi pi-arrow-left"></i>
                Back to Home
            </a>
        </div>

    </div>
</section>

<?php include __DIR__ . '/../../shared/footer.php'; ?>

</body>
</html>
