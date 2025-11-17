<?php
session_start();
require_once 'include/connection.php';
require_once 'stripe_config.php';
require_once 'admin_dashboard/includes/screenings.php';
require_once 'admin_dashboard/includes/screening_rooms.php';

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
$stmt = $db->prepare("SELECT seat_price FROM screening_rooms WHERE id = ?");
$stmt->execute([$screening['screening_room_id']]);
$seatPrice = $stmt->fetchColumn();
$totalPrice = $seatPrice * count($selectedSeats);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create a Stripe Checkout Session
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => $screening['movie_title'] . " - Seats: " . implode(', ', $selectedSeats),
                ],
                'unit_amount' => $totalPrice * 100, // amount in cents
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://localhost/cinema-website/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost/cinema-website/payment.php',
        'metadata' => [
            'user_id' => $userId,
            'screening_id' => $screeningId,
            'seats' => implode(',', $selectedSeats),
        ],
    ]);

    header("Location: " . $checkout_session->url);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
</head>
<body>
<h1>Checkout</h1>
<p>Movie: <?= htmlspecialchars($screening['movie_title']) ?></p>
<p>Seats: <?= implode(', ', array_map('htmlspecialchars', $selectedSeats)) ?></p>
<p>Total: $<?= number_format($totalPrice, 2) ?></p>

<form method="POST">
    <button type="submit">Pay $<?= number_format($totalPrice, 2) ?></button>
</form>
</body>
</html>
