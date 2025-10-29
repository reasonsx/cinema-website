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
<?php include 'head.php'; ?>
<body class="bg-black text-white font-sans">
<?php include 'header.php'; ?>

<!-- BOOKING HERO -->
<section class="px-6 md:px-8 py-10">
    <div class="mx-auto max-w-7xl">

        <h1 class="text-4xl md:text-5xl font-[Limelight] text-[#F8A15A] mb-4">
            Book: <?= htmlspecialchars($screening['movie_title']) ?>
        </h1>

        <p class="mb-6 text-white/80">
            Room: <?= htmlspecialchars($screening['room_name']) ?> |
            Start: <?= $screening['start_time'] ?> |
            End: <?= $screening['end_time'] ?>
        </p>

        <?php if ($errors): ?>
            <div class="rounded-lg bg-red-800/40 p-4 mb-4">
                <?php foreach ($errors as $e) echo "<p class='text-red-300'>$e</p>"; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="rounded-lg bg-green-800/40 p-4 mb-4">
                <p class="text-green-300"><?= $success ?></p>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-2 mb-6">
                <?php foreach ($seats as $seat): 
                    $seatId = $seat['id'] ?? null;
                    $seatCode = $seat['row_number'] . $seat['seat_number'];
                    $class = in_array($seatId, $bookedSeats) ? 'booked' : 'available';
                ?>
                    <label class="seat flex items-center justify-center h-10 w-10 rounded-lg cursor-pointer text-sm font-semibold <?= $class === 'booked' ? 'bg-red-600 text-white cursor-not-allowed' : 'bg-green-600 text-white' ?>">
                        <input type="checkbox" name="seats[]" value="<?= $seatId ?>" <?= $class==='booked' ? 'disabled' : '' ?> hidden>
                        <?= $seatCode ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center gap-2 rounded-full bg-[var(--secondary)] px-6 py-3 text-sm font-semibold text-black hover:shadow-[0_0_25px_var(--secondary)] transition">
                <i class="pi pi-ticket"></i>
                Book Selected Seats
            </button>
        </form>

    </div>
</section>

<script>
    const seatLabels = document.querySelectorAll('.seat.bg-green-600');
    seatLabels.forEach(label => {
        label.addEventListener('click', () => {
            const checkbox = label.querySelector('input');
            checkbox.checked = !checkbox.checked;
            label.classList.toggle('bg-orange-500');
            label.classList.toggle('text-black');
        });
    });
</script>

<?php include 'footer.php'; ?>
</body>
</html>