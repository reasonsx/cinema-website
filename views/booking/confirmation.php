<?php
session_start();

// Confirmation values
$movie_id       = $_POST['movie_id']       ?? 1;
$movie_title    = $_POST['movie_title']    ?? 'Unknown Title';
$venue          = $_POST['venue']          ?? 'Cinema 1';
$day            = $_POST['day']            ?? '2025-10-05';
$time           = $_POST['time']           ?? '20:00';
$seats          = $_POST['seats']          ?? '';
$adult          = $_POST['adult']          ?? 0;
$child          = $_POST['child']          ?? 0;
$senior         = $_POST['senior']         ?? 0;
$total          = $_POST['total']          ?? 0;
$payment_method = $_POST['payment_method'] ?? 'card';
?>
<!DOCTYPE html>
<html lang="en">

<?php include __DIR__ . '/../../shared/head.php'; ?>

<body class="bg-black text-white font-sans">

<?php include __DIR__ . '/../../shared/header.php'; ?>

<!-- CONFIRMATION -->
<section class="px-6 md:px-8 py-12">
    <div class="max-w-3xl mx-auto rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm p-10 shadow-2xl">

        <!-- Icon -->
        <div class="text-[var(--secondary)] text-6xl mb-4 text-center">
            <i class="pi pi-check-circle"></i>
        </div>

        <!-- Title -->
        <h2 class="text-4xl font-[Limelight] tracking-wide text-center text-[var(--secondary)] mb-3">
            Booking Confirmed!
        </h2>

        <p class="text-center text-white/70 mb-8">
            Thank you for your purchase. Your tickets are ready!
        </p>

        <!-- Booking Details -->
        <div class="rounded-2xl border border-white/10 bg-black/30 backdrop-blur-sm p-6 mb-8">
            <h3 class="text-xl font-semibold text-[var(--secondary)] mb-4 flex items-center gap-2">
                <i class="pi pi-ticket"></i> Booking Details
            </h3>

            <ul class="space-y-2 text-white/80 text-sm md:text-base">
                <li><strong class="text-white">Movie:</strong> <?= htmlspecialchars($movie_title) ?></li>
                <li><strong class="text-white">Venue:</strong> <?= htmlspecialchars($venue) ?></li>
                <li><strong class="text-white">Date:</strong> <?= htmlspecialchars($day) ?></li>
                <li><strong class="text-white">Time:</strong> <?= htmlspecialchars($time) ?></li>
                <li><strong class="text-white">Seats:</strong> <?= htmlspecialchars($seats) ?></li>
                <li><strong class="text-white">Tickets:</strong>
                    <?= $adult ?> Adult · <?= $child ?> Child · <?= $senior ?> Senior
                </li>
                <li><strong class="text-white">Total Paid:</strong> <?= htmlspecialchars($total) ?> DKK</li>
                <li><strong class="text-white">Payment Method:</strong> <?= ucfirst($payment_method) ?></li>
            </ul>
        </div>

        <!-- QR Code Placeholder -->
        <div class="flex justify-center mb-8">
            <div class="w-40 h-40 rounded-xl border border-white/10 bg-white/5 backdrop-blur flex items-center justify-center">
                <span class="text-white/50 text-sm">QR Code</span>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col md:flex-row justify-center gap-4">
            <a href="index.php" class="btn">
                <i class="pi pi-home"></i> Back to Home
            </a>

            <a href="tickets.php"
               class="inline-flex items-center justify-center gap-2 rounded-full bg-[var(--secondary)] px-7 py-3 text-sm font-semibold text-black hover:shadow-[0_0_25px_var(--secondary)] transition">
                <i class="pi pi-ticket"></i> My Tickets
            </a>
        </div>

    </div>
</section>

<?php include __DIR__ . '/../../shared/footer.php'; ?>
</body>
</html>
