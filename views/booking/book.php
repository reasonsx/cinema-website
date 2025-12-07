<?php
require_once __DIR__ . '/../../backend/connection.php';
require_once __DIR__ . '/../../views/auth/session.php'; 
require_once __DIR__ . '/../../admin_dashboard/views/screenings/screenings_functions.php';
require_once __DIR__ . '/../../admin_dashboard/views/screening_rooms/screening_rooms_functions.php';

$session = new SessionManager($db);
$currentUrl = $_SERVER['REQUEST_URI']; 
$session->requireLogin($currentUrl);


$userId = $session->getUserId();

$screeningId = $_GET['screening_id'] ?? null;
if (!$screeningId) {
    echo "<p class='text-center text-red-500 mt-10'>No screening selected.</p>";
    exit;
}

$screening = getScreeningById($db, $screeningId);
if (!$screening) {
    echo "<p class='text-center text-red-500 mt-10'>Invalid screening selected.</p>";
    exit;
}

$start = strtotime($screening['start_time']);
$end = strtotime($screening['end_time']);

$runtimeMinutes = round(($end - $start) / 60);

$runtimeFormatted = sprintf(
    "%dh %02dm",
    floor($runtimeMinutes / 60),
    $runtimeMinutes % 60
);

$seats = getSeatsByRoom($db, $screening['screening_room_id']);

$stmt = $db->prepare("SELECT seat_id FROM booking_seats WHERE screening_id = ?");
$stmt->execute([$screeningId]);
$bookedSeats = $stmt->fetchAll(PDO::FETCH_COLUMN);

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

<?php include __DIR__ . '/../../shared/head.php'; ?>

<body class="bg-black text-white font-sans">

<?php include __DIR__ . '/../../shared/header.php'; ?>

<section class="px-6 md:px-8 py-14">
    <div class="mx-auto max-w-7xl rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl p-10">

        <!-- Title -->
        <h1 class="text-4xl md:text-5xl font-[Limelight] text-[var(--secondary)] mb-4 text-center">
            Select Your Seats
        </h1>

        <!-- Screening info -->
        <p class="text-center text-white/70 mb-10 leading-relaxed flex flex-col md:flex-row md:justify-center md:items-center gap-1 md:gap-4">

            <!-- Movie -->
            <span>
                <i class="pi pi-video mr-1 text-[var(--secondary)]"></i>
                <span class="text-white font-semibold">Movie:</span>
                <?= htmlspecialchars($screening['movie_title']) ?>
             </span>

            <!-- Dot separator -->
            <span class="hidden md:inline">•</span>

            <!-- Room -->
            <span>
                 <i class="pi pi-building mr-1 text-[var(--secondary)]"></i>
                 <span class="text-white font-semibold">Room:</span>
                 <?= htmlspecialchars($screening['room_name']) ?>
            </span>

            <span class="hidden md:inline">•</span>
            <!-- Date -->
            <span>
                <i class="pi pi-calendar mr-1 text-[var(--secondary)]"></i>
                <span class="text-white font-semibold">Date:</span>
                <?= date('D, M j', strtotime($screening['start_time'])) ?>
            </span>

            <span class="hidden md:inline">•</span>

            <!-- Time -->
            <span>
                <i class="pi pi-clock mr-1 text-[var(--secondary)]"></i>
                <span class="text-white font-semibold">Time:</span>
                <?= date('H:i', strtotime($screening['start_time'])) ?> –
                <?= date('H:i', strtotime($screening['end_time'])) ?>
            </span>

            <span class="hidden md:inline">•</span>

            <!-- Runtime -->
            <span>
                <i class="pi pi-hourglass mr-1 text-[var(--secondary)]"></i>
                <span class="text-white font-semibold">Runtime:</span>
                <?= $runtimeFormatted ?> (<?= $runtimeMinutes ?> min)
            </span>
        </p>

        <!-- Error box -->
        <?php if ($errors): ?>
            <div class="rounded-xl bg-red-800/40 border border-red-600/40 p-4 mb-4 text-red-300 text-center">
                <?php foreach ($errors as $e): ?>
                    <p><?= $e ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Gap warning -->
        <p id="gap-warning"
           class="hidden rounded-xl bg-red-800/40 border border-red-600/40 p-4 mb-6 text-red-300 text-center">
        </p>

        <!-- Screen -->
        <div class="flex flex-col items-center mb-10">
            <div class="text-sm text-white/70 mb-2">SCREEN</div>
            <div class="h-2 w-72 md:w-96 bg-gradient-to-r from-white/10 via-white/40 to-white/10 rounded-full"></div>
        </div>

        <!-- Legend -->
        <div class="flex items-center justify-center gap-6 text-sm text-white/80 mb-4">

            <div class="flex items-center gap-2">
                <span class="inline-block w-4 h-4 rounded bg-green-600"></span>
                <span>Available</span>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-block w-4 h-4 rounded bg-orange-400"></span>
                <span>Selected</span>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-block w-4 h-4 rounded bg-red-600/70 opacity-60"></span>
                <span>Occupied</span>
            </div>

        </div>
        <!-- Seat selection form -->
        <form method="POST" id="seatForm" class="space-y-10">

            <!-- Seats grid -->
            <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-2 justify-center">

                <?php foreach ($seats as $seat):
                    $seatId = $seat['id'] ?? $seat['seat_id'];
                    $row = $seat['row_number'] ?? $seat['row'];
                    $number = $seat['seat_number'] ?? $seat['number'];
                    $seatCode = htmlspecialchars($row . $number);

                    if (!$seatId) continue;

                    $isBooked = in_array($seatId, $bookedSeats);
                    ?>

                    <label class="
                    seat relative flex items-center justify-center h-10 w-10 rounded-lg text-xs font-bold transition
                    <?= $isBooked
                        ? 'bg-red-600/70 text-white cursor-not-allowed opacity-60'
                        : 'bg-green-600 text-white cursor-pointer hover:bg-green-500'
                    ?>
                ">
                        <input type="checkbox"
                               name="seats[]"
                               value="<?= $seatId ?>"
                            <?= $isBooked ? 'disabled' : '' ?>
                               hidden
                        >
                        <?= $seatCode ?>
                    </label>

                <?php endforeach; ?>

            </div>

            <!-- Book button -->
            <div class="text-center mt-8">
                <button type="submit" class="btn-full px-10">
                    <i class="pi pi-ticket"></i>
                    Book Selected Seats
                </button>
            </div>

        </form>
    </div>
</section>

<script>
    function groupSeats() {
        const rows = {};
        document.querySelectorAll('.seat').forEach(label => {
            const seatText = label.textContent.trim();
            const row = seatText[0];
            const seatNumber = parseInt(seatText.substring(1));
            const checkbox = label.querySelector('input');

            if (!rows[row]) rows[row] = [];
            rows[row].push({
                number: seatNumber,
                selected: checkbox.checked,
                disabled: checkbox.disabled,
                checkbox,
                label
            });
        });

        for (const row in rows) {
            rows[row].sort((a, b) => a.number - b.number);
        }

        return rows;
    }

    function causesGap(rows) {
        for (const row in rows) {
            const seats = rows[row];

            for (let i = 0; i < seats.length - 2; i++) {
                const a = seats[i];
                const b = seats[i + 1];
                const c = seats[i + 2];

                if (a.selected && !b.selected && !b.disabled && c.selected) return true;

                if (a.disabled && !b.selected && !b.disabled && c.selected) return true;

                if (a.selected && !b.selected && !b.disabled && c.disabled) return true;
            }
        }
        return false;
    }

    const MAX_SEATS = 6;

    function selectedSeatCount() {
        return document.querySelectorAll('.seat input[type="checkbox"]:checked').length;
    }

    function showWarning(message) {
        const msg = document.getElementById("gap-warning");
        msg.classList.remove("hidden");
        msg.textContent = message;
    }

    function hideWarning() {
        document.getElementById("gap-warning").classList.add("hidden");
    }

    document.querySelectorAll('.seat input[type="checkbox"]:not(:disabled)').forEach(checkbox => {
        const label = checkbox.closest('.seat');

        label.addEventListener('click', event => {
            event.preventDefault();

            const newState = !checkbox.checked;

            if (newState === true && selectedSeatCount() >= MAX_SEATS) {
                showWarning("You can book a maximum of 6 seats per booking. For larger groups, please contact us by email.");
                return;
            }

            checkbox.checked = newState;

            label.classList.toggle('bg-orange-400', checkbox.checked);
            label.classList.toggle('text-black', checkbox.checked);
            label.classList.toggle('bg-green-600', !checkbox.checked);
            label.classList.toggle('text-white', !checkbox.checked);

            const rows = groupSeats();
            if (causesGap(rows)) {

                checkbox.checked = !newState;
                label.classList.toggle('bg-orange-400', checkbox.checked);
                label.classList.toggle('text-black', checkbox.checked);
                label.classList.toggle('bg-green-600', !checkbox.checked);
                label.classList.toggle('text-white', !checkbox.checked);

                showWarning("You cannot leave a single empty seat gap!");
                return;
            }

            hideWarning();
        });
    });
</script>

<?php include __DIR__ . '/../../shared/footer.php'; ?>

</body>
</html>
