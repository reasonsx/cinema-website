<?php
http_response_code(404);

$code = $_GET['code'] ?? 404;
$message = $_GET['message'] ?? 'The page you’re looking for could not be found.';
$title = match ((int)$code) {
    403 => 'Access Denied',
    500 => 'Server Error',
    default => 'Page Not Found',
};

include 'shared/head.php';
?>
<body class="min-h-screen flex flex-col items-center justify-center text-center bg-black text-secondary font-sans">

<div class="max-w-lg p-10 rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm">

    <!-- Big Error Code -->
    <h1 class="text-[6rem] font-[Limelight] text-secondary tracking-wide leading-none mb-3">
        <?= htmlspecialchars($code) ?>
    </h1>

    <!-- Title -->
    <h2 class="text-3xl md:text-4xl font-[Limelight] text-[var(--secondary)] mb-4">
        <?= htmlspecialchars($title) ?>
    </h2>

    <!-- Message -->
    <p class="text-white/70 text-sm md:text-base leading-relaxed mb-8">
        <?= htmlspecialchars($message) ?>
    </p>

    <!-- Buttons -->
    <div class="flex flex-wrap gap-4 justify-center">

        <a href="/cinema-website/index.php" class="btn">
            <i class="pi pi-home"></i>
            Home
        </a>

        <a href="javascript:history.back()"
           class="btn border-white text-white hover:bg-white hover:text-black">
            <i class="pi pi-arrow-left"></i>
            Back
        </a>

    </div>

</div>

<footer class="mt-10 text-xs text-white/40 tracking-wider">
    &copy; <?= date('Y') ?> Eclipse Cinema. All rights reserved.
</footer>

</body>
</html>
