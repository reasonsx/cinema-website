<?php
session_start();
require_once 'include/connection.php';
require_once 'include/stripe_config.php';

$session_id = $_GET['session_id'] ?? null;

if (!$session_id) {
    header("Location: index.php");
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
<head>
    <meta charset="UTF-8">
    <title>Payment Success</title>
</head>
<body>
<h1>Payment Successful!</h1>
<p>Your booking ID is: <?= $bookingId ?></p>
<p>Thank you for your purchase.</p>
<a href="index.php">Go back to home</a>
</body>
</html>
