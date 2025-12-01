<?php
// confirmation.php

$movie_id = $_POST['movie_id'] ?? 1;
$venue = $_POST['venue'] ?? 'Cinema 1';
$day = $_POST['day'] ?? '2025-10-05';
$time = $_POST['time'] ?? '20:00';
$seats = $_POST['seats'] ?? '';
$adult = $_POST['adult'] ?? 0;
$child = $_POST['child'] ?? 0;
$senior = $_POST['senior'] ?? 0;
$total = $_POST['total'] ?? 0;
$payment_method = $_POST['payment_method'] ?? 'card';
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'shared/head.php'; ?>
<body class="bg-light text-black font-sans">

<?php include 'shared/header.php'; ?>

<section class="py-12 px-6 md:px-16 bg-white">
    <div class="max-w-2xl mx-auto bg-light rounded-2xl shadow-lg p-8 text-center">

        <!-- Success Icon -->
        <div class="text-green-600 text-6xl mb-4">
            <i class="pi pi-check-circle"></i>
        </div>

        <h2 class="text-3xl font-header text-primary mb-4">Booking Confirmed!</h2>
        <p class="text-gray-700 mb-6">Thank you for your purchase. Your tickets are ready 🎉</p>

        <!-- Booking Summary -->
        <div class="bg-white rounded-xl shadow p-6 mb-6 text-left">
            <h3 class="text-xl font-header text-secondary mb-3">Booking Details</h3>
            <ul class="space-y-1 text-gray-700">
                <li><strong>Movie:</strong> Movie Title (placeholder)</li>
                <li><strong>Venue:</strong> <?= htmlspecialchars($venue) ?></li>
                <li><strong>Date:</strong> <?= htmlspecialchars($day) ?></li>
                <li><strong>Time:</strong> <?= htmlspecialchars($time) ?></li>
                <li><strong>Seats:</strong> <?= htmlspecialchars($seats) ?></li>
                <li><strong>Tickets:</strong> <?= $adult ?> Adult, <?= $child ?> Child, <?= $senior ?> Senior</li>
                <li><strong>Total Paid:</strong> <?= htmlspecialchars($total) ?> DKK</li>
                <li><strong>Payment Method:</strong> <?= ucfirst($payment_method) ?></li>
            </ul>
        </div>

        <!-- QR Code Placeholder -->
        <div class="flex justify-center mb-6">
            <div class="w-40 h-40 bg-gray-200 flex items-center justify-center rounded-lg shadow">
                <span class="text-gray-500">QR Code</span>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col md:flex-row justify-center gap-4">
            <a href="index.php" class="btn"><i class="pi pi-home"></i> Back to Home</a>
            <a href="tickets.php" class="btn bg-secondary hover:bg-primary"><i class="pi pi-ticket"></i> My Tickets</a>
        </div>
    </div>
</section>

<?php include 'shared/footer.php'; ?>
</body>
</html>
