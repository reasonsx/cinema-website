<?php
// seats.php

// Get movie and booking info from previous form
$movie_id = $_GET['movie_id'] ?? 1;
$venue = $_GET['venue'] ?? 'Cinema 1';
$day = $_GET['day'] ?? '';
$time = $_GET['time'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-light text-black font-sans">

<?php include 'header.php'; ?>

<section class="py-12 px-6 md:px-16 bg-light">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8">

        <!-- Movie & Booking Info -->
        <h2 class="text-3xl font-header text-primary mb-6 text-center">Choose Your Seats</h2>
        <p class="text-center text-gray-600 mb-8">
            <strong>Venue:</strong> <?= htmlspecialchars($venue) ?> |
            <strong>Date:</strong> <?= htmlspecialchars($day) ?> |
            <strong>Time:</strong> <?= htmlspecialchars($time) ?>
        </p>

        <!-- Screen -->
        <div class="w-full h-6 bg-gray-800 rounded-md mb-6 flex items-center justify-center text-white text-sm">
            SCREEN
        </div>

        <!-- Seats Grid -->
        <form action="confirm_booking.php" method="POST" id="seatForm" class="flex flex-col gap-6">
            <div class="grid grid-cols-10 gap-3 justify-center">
                <?php
                // Example: 50 seats (5 rows x 10 cols)
                $rows = 5;
                $cols = 10;
                $takenSeats = ['A3','A4','C7']; // Example taken seats

                $rowLetters = range('A','Z');
                for ($r = 0; $r < $rows; $r++) {
                    for ($c = 1; $c <= $cols; $c++) {
                        $seatId = $rowLetters[$r] . $c;
                        $isTaken = in_array($seatId, $takenSeats);
                        ?>
                        <button type="button"
                                class="seat w-10 h-10 rounded-md flex items-center justify-center text-xs font-bold
                                       <?= $isTaken ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600' ?>"
                                data-seat="<?= $seatId ?>"
                            <?= $isTaken ? 'disabled' : '' ?>>
                            <?= $seatId ?>
                        </button>
                        <?php
                    }
                }
                ?>
            </div>

            <!-- Hidden input for selected seats -->
            <input type="hidden" name="selected_seats" id="selectedSeats">

            <!-- Hidden booking data -->
            <input type="hidden" name="movie_id" value="<?= htmlspecialchars($movie_id) ?>">
            <input type="hidden" name="venue" value="<?= htmlspecialchars($venue) ?>">
            <input type="hidden" name="day" value="<?= htmlspecialchars($day) ?>">
            <input type="hidden" name="time" value="<?= htmlspecialchars($time) ?>">

            <!-- Submit -->
            <button type="submit" class="btn w-full justify-center">
                <i class="pi pi-check"></i> Confirm Selection
            </button>
        </form>
    </div>
</section>

<script>
    // JS for selecting seats
    const seats = document.querySelectorAll('.seat:not([disabled])');
    const selectedSeatsInput = document.getElementById('selectedSeats');
    let selected = [];

    seats.forEach(seat => {
        seat.addEventListener('click', () => {
            const seatId = seat.getAttribute('data-seat');
            if (selected.includes(seatId)) {
                selected = selected.filter(s => s !== seatId);
                seat.classList.remove('bg-blue-500');
                seat.classList.add('bg-green-500');
            } else {
                selected.push(seatId);
                seat.classList.remove('bg-green-500');
                seat.classList.add('bg-blue-500');
            }
            selectedSeatsInput.value = selected.join(',');
        });
    });
</script>

<?php include 'footer.php'; ?>
</body>
</html>
