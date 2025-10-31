<?php
session_start();
require_once 'include/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'admin_dashboard/includes/screenings.php';
require_once 'admin_dashboard/includes/screening_rooms.php';

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

// Simulate payment success
// payment.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    // Here handle the actual payment
    // Only after payment success, insert booking
    $stmtBooking = $db->prepare("INSERT INTO bookings (user_id, screening_id, total_price) VALUES (?, ?, ?)");
    $stmtBooking->execute([$userId, $screeningId, $totalPrice]);
    $bookingId = $db->lastInsertId();

    $stmtSeats = $db->prepare("INSERT INTO booking_seats (booking_id, seat_id, screening_id) VALUES (?, ?, ?)");
    foreach ($selectedSeats as $seatId) {
        $stmtSeats->execute([$bookingId, $seatId, $screeningId]);
    }

    unset($_SESSION['selected_screening'], $_SESSION['selected_seats']);

    header("Location: success.php?booking_id=$bookingId");
    exit;
}

// Else show payment form (GET request)

?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-black text-white">
<?php include 'header.php'; ?>

<section class="px-6 md:px-8 py-10 max-w-2xl mx-auto">
    <h1 class="text-3xl font-[Limelight] text-[#F8A15A] mb-6">Payment</h1>

    <form method="POST">
        <div class="space-y-4 mb-6">
            <input type="text" name="card_name" placeholder="Name on card" required class="w-full p-2 rounded text-black">
            <input type="text" name="card_number" placeholder="Card number" required class="w-full p-2 rounded text-black">
            <div class="flex gap-2">
                <input type="text" name="expiry" placeholder="MM/YY" required class="w-1/2 p-2 rounded text-black">
                <input type="text" name="cvc" placeholder="CVC" required class="w-1/2 p-2 rounded text-black">
            </div>
        </div>
        <button type="submit"
            class="w-full bg-[var(--secondary)] text-black font-semibold py-3 rounded-full hover:shadow-[0_0_25px_var(--secondary)] transition">
            Pay $<?= number_format($totalPrice, 2) ?>
        </button>
    </form>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
