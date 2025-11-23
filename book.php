<?php
require_once __DIR__ . '/include/connection.php';
require_once __DIR__ . '/auth/session.php'; // <-- your SessionManager class
require_once __DIR__ . '/admin_dashboard/views/screenings/screenings_functions.php';
require_once __DIR__ . '/admin_dashboard/views/screening_rooms/screening_rooms_functions.php';

// Initialize session manager
$session = new SessionManager($db);

// Require login, redirect back to current page if not logged in
$currentUrl = $_SERVER['REQUEST_URI']; // includes query string
$session->requireLogin($currentUrl);


// Get logged-in user ID
$userId = $session->getUserId();

// Fetch screening ID from GET
$screeningId = $_GET['screening_id'] ?? null;
if (!$screeningId) {
    echo "<p class='text-center text-red-500 mt-10'>No screening selected.</p>";
    exit;
}

// Fetch screening
$screening = getScreeningById($db, $screeningId);
if (!$screening) {
    echo "<p class='text-center text-red-500 mt-10'>Invalid screening selected.</p>";
    exit;
}

// Get seats for room
$seats = getSeatsByRoom($db, $screening['screening_room_id']);

// Get already booked seats
$stmt = $db->prepare("SELECT seat_id FROM booking_seats WHERE screening_id = ?");
$stmt->execute([$screeningId]);
$bookedSeats = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Booking logic
$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedSeats = $_POST['seats'] ?? [];

    if (empty($selectedSeats)) {
        $errors[] = "Please select at least one seat.";
    } else {
        $_SESSION['selected_seats'] = $selectedSeats;
        $_SESSION['selected_screening'] = $screeningId;
        header("Location: checkout.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-black text-white font-sans">
<?php include 'header.php'; ?>

<section class="px-6 md:px-8 py-10">
    <div class="mx-auto max-w-7xl">
        <h1 class="text-4xl md:text-5xl font-[Limelight] text-[#F8A15A] mb-4">
            Select Seats for <?= htmlspecialchars($screening['movie_title']) ?>
        </h1>
        <p class="mb-6 text-white/80">
            Room: <?= htmlspecialchars($screening['room_name']) ?> |
            Start: <?= htmlspecialchars($screening['start_time']) ?> |
            End: <?= htmlspecialchars($screening['end_time']) ?>
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

        <form method="POST" id="seatForm" class="space-y-6">
            <div class="flex justify-center flex-col items-center">
                <div class="mb-3 text-sm text-white/70">Screen</div>
                <div class="h-2 w-80 bg-gradient-to-r from-white/10 via-white/30 to-white/10 rounded-full mb-8"></div>
            </div>

            <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-2 justify-center">
                <?php foreach ($seats as $seat): 
    // Safely get seat fields (some DBs might return seat_id instead of id)
    $seatId = $seat['id'] ?? $seat['seat_id'] ?? null;
    $row = $seat['row_number'] ?? $seat['row'] ?? '';
    $number = $seat['seat_number'] ?? $seat['number'] ?? '';
    $seatCode = htmlspecialchars($row . $number);

    if (!$seatId) continue; // skip malformed rows

    $isBooked = in_array($seatId, $bookedSeats);
?>

                <label 
                    class="seat relative flex items-center justify-center h-10 w-10 rounded-lg text-sm font-semibold 
                        <?= $isBooked 
                            ? 'bg-red-600/70 text-white cursor-not-allowed opacity-70' 
                            : 'bg-green-600 text-white cursor-pointer hover:bg-green-500 transition' ?>">
                    <input type="checkbox" name="seats[]" value="<?= $seatId ?>" <?= $isBooked ? 'disabled' : '' ?> hidden>
                    <?= $seatCode ?>
                </label>
                <?php endforeach; ?>
            </div>

            <div class="flex justify-center mt-8">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-full bg-[var(--secondary)] px-8 py-3 text-sm font-semibold text-black hover:shadow-[0_0_25px_var(--secondary)] transition">
                    <i class="pi pi-ticket"></i>
                    Book Selected Seats
                </button>
            </div>
        </form>
    </div>
</section>

<script>
    const seatLabels = document.querySelectorAll('.seat input[type="checkbox"]:not(:disabled)');
    seatLabels.forEach(checkbox => {
        const label = checkbox.closest('.seat');
        label.addEventListener('click', () => {
            if (checkbox.disabled) return;
            checkbox.checked = !checkbox.checked;
            label.classList.toggle('bg-orange-400', checkbox.checked);
            label.classList.toggle('text-black', checkbox.checked);
            label.classList.toggle('bg-green-600', !checkbox.checked);
            label.classList.toggle('text-white', !checkbox.checked);
        });
    });
</script>

<?php include 'footer.php'; ?>
</body>
</html>
