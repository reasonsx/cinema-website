<?php
session_start();
require_once 'include/connection.php';
require_once 'admin_dashboard/includes/screenings.php';
require_once 'admin_dashboard/includes/screening_rooms.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$screeningId = $_GET['screening_id'] ?? null;

if (!$screeningId) {
    echo "<p class='text-center text-red-500 mt-10'>No screening selected.</p>";
    exit;
}

// Fetch screening info
$screening = getScreeningById($db, $screeningId);
if (!$screening) {
    echo "<p class='text-center text-red-500 mt-10'>Invalid screening selected.</p>";
    exit;
}

// Fetch all seats for the room
$seats = getSeatsByRoom($db, $screening['screening_room_id']);

// Fetch already booked seats
$stmt = $db->prepare("
    SELECT seat_id FROM booking_seats 
    WHERE screening_id = ?
");
$stmt->execute([$screeningId]);
$bookedSeats = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Handle booking submission
$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedSeats = $_POST['seats'] ?? [];

    if (empty($selectedSeats)) {
        $errors[] = "Please select at least one seat.";
    } else {
        // Check for conflicts (seat already booked)
        $placeholders = implode(',', array_fill(0, count($selectedSeats), '?'));
        $checkStmt = $db->prepare("
            SELECT COUNT(*) FROM booking_seats 
            WHERE screening_id = ? AND seat_id IN ($placeholders)
        ");
        $checkStmt->execute(array_merge([$screeningId], $selectedSeats));
        if ($checkStmt->fetchColumn() > 0) {
            $errors[] = "One or more selected seats are already booked.";
        } else {
            // Calculate total price
            $stmtPrice = $db->prepare("SELECT seat_price FROM screening_rooms WHERE id = ?");
            $stmtPrice->execute([$screening['screening_room_id']]);
            $seatPrice = $stmtPrice->fetchColumn();
            $totalPrice = $seatPrice * count($selectedSeats);

            // Insert booking
            $stmtBooking = $db->prepare("INSERT INTO bookings (user_id, screening_id, total_price) VALUES (?, ?, ?)");
            $stmtBooking->execute([$userId, $screeningId, $totalPrice]);
            $bookingId = $db->lastInsertId();

            // Insert seats
            $stmtSeats = $db->prepare("INSERT INTO booking_seats (booking_id, seat_id, screening_id) VALUES (?, ?, ?)");
            foreach ($selectedSeats as $seatId) {
                $stmtSeats->execute([$bookingId, $seatId, $screeningId]);
            }

            $success = "Booking successful! Total: $" . number_format($totalPrice, 2);
            // Refresh booked seats
            $stmt->execute([$screeningId]);
            $bookedSeats = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book: <?= htmlspecialchars($screening['movie_title']) ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .seat { display:inline-block; width:30px; height:30px; margin:3px; text-align:center; vertical-align:middle; line-height:30px; border-radius:4px; cursor:pointer; }
        .available { background-color:#4caf50; color:white; }
        .booked { background-color:#f44336; color:white; cursor:not-allowed; }
        .selected { background-color:#ff9800; color:white; }
    </style>
</head>
<body>
    <h1>Book Screening: <?= htmlspecialchars($screening['movie_title']) ?></h1>
    <p>Room: <?= htmlspecialchars($screening['room_name']) ?> | Start: <?= $screening['start_time'] ?> | End: <?= $screening['end_time'] ?></p>

    <?php if ($errors): ?>
        <div class="errors" style="color:red;">
            <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success" style="color:green;">
            <p><?= $success ?></p>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="seats-container">
            <?php foreach ($seats as $seat): 
                $seatId = $seat['id'] ?? null; // ensure seat has ID
                $seatCode = $seat['row_number'] . $seat['seat_number'];
                $class = in_array($seatId, $bookedSeats) ? 'booked' : 'available';
            ?>
                <label class="seat <?= $class ?>">
                    <input type="checkbox" name="seats[]" value="<?= $seatId ?>" <?= $class==='booked' ? 'disabled' : '' ?> hidden>
                    <?= $seatCode ?>
                </label>
            <?php endforeach; ?>
        </div>
        <br>
        <button type="submit">Book Selected Seats</button>
    </form>

    <script>
        const seatLabels = document.querySelectorAll('.seat.available');
        seatLabels.forEach(label => {
            label.addEventListener('click', () => {
                const checkbox = label.querySelector('input');
                checkbox.checked = !checkbox.checked;
                label.classList.toggle('selected');
            });
        });
    </script>
</body>
</html>
