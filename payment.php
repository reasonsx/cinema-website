<?php
// payment.php

// Retrieve booking data from confirm_booking.php
$movie_id = $_POST['movie_id'] ?? 1;
$venue = $_POST['venue'] ?? 'Cinema 1';
$day = $_POST['day'] ?? '2025-10-05';
$time = $_POST['time'] ?? '20:00';
$seats = $_POST['seats'] ?? '';
$adult = $_POST['adult'] ?? 0;
$child = $_POST['child'] ?? 0;
$senior = $_POST['senior'] ?? 0;

$total = ($adult * 100) + ($child * 70) + ($senior * 80);
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-light text-black font-sans">

<?php include 'header.php'; ?>

<section class="py-12 px-6 md:px-16 bg-white">
    <div class="max-w-3xl mx-auto bg-light rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-header text-primary mb-6 text-center">Payment</h2>

        <!-- Booking Summary -->
        <div class="mb-8">
            <h3 class="text-xl font-header text-secondary mb-2">Booking Summary</h3>
            <ul class="text-gray-700 space-y-1">
                <li><strong>Movie:</strong> Movie Title (placeholder)</li>
                <li><strong>Venue:</strong> <?= htmlspecialchars($venue) ?></li>
                <li><strong>Date:</strong> <?= htmlspecialchars($day) ?></li>
                <li><strong>Time:</strong> <?= htmlspecialchars($time) ?></li>
                <li><strong>Seats:</strong> <?= htmlspecialchars($seats) ?></li>
                <li><strong>Tickets:</strong>
                    <?= $adult ?> Adult, <?= $child ?> Child, <?= $senior ?> Senior
                </li>
                <li><strong>Total Price:</strong> <?= $total ?> DKK</li>
            </ul>
        </div>

        <!-- Payment Options -->
        <form action="confirmation.php" method="POST" class="flex flex-col gap-6">
            <h3 class="text-xl font-header text-secondary mb-2">Choose Payment Method</h3>

            <div class="flex flex-col gap-3">
                <label class="flex items-center gap-2">
                    <input type="radio" name="payment_method" value="card" required>
                    <span>Credit / Debit Card</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="payment_method" value="mobilepay">
                    <span>MobilePay</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="payment_method" value="paypal">
                    <span>PayPal</span>
                </label>
            </div>

            <!-- Mock Card Details -->
            <div id="card-details" class="hidden mt-4">
                <label class="block mb-2">Card Number</label>
                <input type="text" name="card_number" placeholder="1234 5678 9012 3456"
                       class="px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary w-full">

                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block mb-2">Expiry</label>
                        <input type="text" name="expiry" placeholder="MM/YY"
                               class="px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary w-full">
                    </div>
                    <div>
                        <label class="block mb-2">CVC</label>
                        <input type="text" name="cvc" placeholder="123"
                               class="px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary w-full">
                    </div>
                </div>
            </div>

            <!-- Hidden fields to pass booking -->
            <input type="hidden" name="movie_id" value="<?= htmlspecialchars($movie_id) ?>">
            <input type="hidden" name="venue" value="<?= htmlspecialchars($venue) ?>">
            <input type="hidden" name="day" value="<?= htmlspecialchars($day) ?>">
            <input type="hidden" name="time" value="<?= htmlspecialchars($time) ?>">
            <input type="hidden" name="seats" value="<?= htmlspecialchars($seats) ?>">
            <input type="hidden" name="adult" value="<?= htmlspecialchars($adult) ?>">
            <input type="hidden" name="child" value="<?= htmlspecialchars($child) ?>">
            <input type="hidden" name="senior" value="<?= htmlspecialchars($senior) ?>">
            <input type="hidden" name="total" value="<?= htmlspecialchars($total) ?>">

            <!-- Pay button -->
            <button type="submit" class="btn w-full text-center justify-center items-center mt-6">
                <i class="pi pi-check-circle"></i> Confirm & Pay <?= $total ?> DKK
            </button>
        </form>
    </div>
</section>

<script>
    const radios = document.querySelectorAll('input[name="payment_method"]');
    const cardDetails = document.getElementById('card-details');

    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            if (radio.value === 'card') {
                cardDetails.classList.remove('hidden');
            } else {
                cardDetails.classList.add('hidden');
            }
        });
    });
</script>

<?php include 'footer.php'; ?>
</body>
</html>
