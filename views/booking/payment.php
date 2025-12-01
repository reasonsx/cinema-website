<?php
session_start();

require_once __DIR__ . '/../../backend/connection.php';
require_once __DIR__ . '/../../backend/stripe_config.php'; // Loads Stripe API key from .env
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

// Get screening and seat price
$screening = getScreeningById($db, $screeningId);
$stmt = $db->prepare("SELECT seat_price FROM screening_rooms WHERE id = ?");
$stmt->execute([$screening['screening_room_id']]);
$seatPrice = $stmt->fetchColumn();
$totalPrice = $seatPrice * count($selectedSeats);

// Handle form submission
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

        // Redirect to Stripe Checkout
        header("Location: " . $checkout_session->url);
        exit;

    } catch (\Stripe\Exception\ApiErrorException $e) {
        $error = $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <style>
        body { font-family: Arial, sans-serif; background: #000; color: #fff; }
        .container { max-width: 500px; margin: 50px auto; background: #111; padding: 20px; border-radius: 8px; }
        button { padding: 10px 20px; font-size: 16px; background: #F8A15A; border: none; border-radius: 5px; cursor: pointer; color: #000; }
        button:hover { opacity: 0.9; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Checkout</h1>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <p><strong>Movie:</strong> <?= htmlspecialchars($screening['movie_title']) ?></p>
    <p><strong>Seats:</strong> <?= implode(', ', array_map('htmlspecialchars', $selectedSeats)) ?></p>
    <p><strong>Total:</strong> $<?= number_format($totalPrice, 2) ?></p>

    <form method="POST">
        <button type="submit">Pay $<?= number_format($totalPrice, 2) ?></button>
    </form>
</div>
</body>
</html>
