<?php
// confirm_booking.php

// Get booking details from query params (in practice, you’d store this in session or DB)
$movie_id = $_GET['movie_id'] ?? 1;
$venue = $_GET['venue'] ?? 'Cinema 1';
$day = $_GET['day'] ?? '2025-10-05';
$time = $_GET['time'] ?? '20:00';
$seats = $_GET['seats'] ?? ''; // e.g. "A1,A2,A3"
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-light text-black font-sans">

<?php include 'header.php'; ?>

<section class="py-12 px-6 md:px-16 bg-white">
    <div class="max-w-3xl mx-auto bg-light rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-header text-primary mb-6 text-center">Confirm Your Booking</h2>

        <!-- Booking Summary -->
        <div class="mb-8">
            <h3 class="text-xl font-header text-secondary mb-2">Booking Details</h3>
            <ul class="text-gray-700 space-y-1">
                <li><strong>Movie:</strong> Movie Title (placeholder)</li>
                <li><strong>Venue:</strong> <?= htmlspecialchars($venue) ?></li>
                <li><strong>Date:</strong> <?= htmlspecialchars($day) ?></li>
                <li><strong>Time:</strong> <?= htmlspecialchars($time) ?></li>
                <li><strong>Seats:</strong> <?= htmlspecialchars($seats) ?></li>
            </ul>
        </div>

        <!-- Ticket Selection -->
        <form action="payment.php" method="POST" id="ticketForm" class="flex flex-col gap-6">
            <h3 class="text-xl font-header text-secondary mb-2">Select Ticket Types</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Adult -->
                <div class="flex flex-col">
                    <label class="mb-1">Adult (100 DKK)</label>
                    <input type="number" name="adult" id="adult" min="0" value="0"
                           class="px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary">
                </div>
                <!-- Child -->
                <div class="flex flex-col">
                    <label class="mb-1">Child (70 DKK)</label>
                    <input type="number" name="child" id="child" min="0" value="0"
                           class="px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary">
                </div>
                <!-- Senior -->
                <div class="flex flex-col">
                    <label class="mb-1">Senior (80 DKK)</label>
                    <input type="number" name="senior" id="senior" min="0" value="0"
                           class="px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <!-- Total -->
            <div class="text-xl font-bold text-center mt-4">
                Total: <span id="total">0</span> DKK
            </div>

            <!-- Hidden fields to pass booking details -->
            <input type="hidden" name="movie_id" value="<?= htmlspecialchars($movie_id) ?>">
            <input type="hidden" name="venue" value="<?= htmlspecialchars($venue) ?>">
            <input type="hidden" name="day" value="<?= htmlspecialchars($day) ?>">
            <input type="hidden" name="time" value="<?= htmlspecialchars($time) ?>">
            <input type="hidden" name="seats" value="<?= htmlspecialchars($seats) ?>">

            <!-- Continue -->
            <button type="submit" class="btn w-full text-center justify-center items-center mt-6">
                <i class="pi pi-credit-card"></i> Proceed to Payment
            </button>
        </form>
    </div>
</section>

<script>
    const prices = { adult: 100, child: 70, senior: 80 };
    const inputs = {
        adult: document.getElementById('adult'),
        child: document.getElementById('child'),
        senior: document.getElementById('senior')
    };
    const totalSpan = document.getElementById('total');

    function updateTotal() {
        let total = 0;
        for (let type in inputs) {
            total += (parseInt(inputs[type].value) || 0) * prices[type];
        }
        totalSpan.textContent = total;
    }

    Object.values(inputs).forEach(input => {
        input.addEventListener('input', updateTotal);
    });

    updateTotal(); // initialize
</script>

<?php include 'footer.php'; ?>
</body>
</html>
