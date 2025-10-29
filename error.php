<?php
http_response_code(404);

$code = $_GET['code'] ?? 404;
$message = $_GET['message'] ?? 'The page you’re looking for could not be found.';
$title = match ((int)$code) {
    403 => 'Access Denied',
    500 => 'Server Error',
    default => 'Page Not Found',
};

include 'head.php';
?>

<body class="relative min-h-screen flex flex-col items-center justify-center text-center text-white font-sans overflow-hidden bg-gradient-to-b from-black via-[#0a0a0a] to-black">

<!-- Decorative glow -->
<div class="absolute inset-0">
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-[400px] h-[400px] rounded-full blur-3xl opacity-30 bg-[var(--secondary)]"></div>
    <div class="absolute bottom-1/3 right-1/2 translate-x-1/2 w-[300px] h-[300px] rounded-full blur-2xl opacity-20 bg-[var(--primary)]"></div>
</div>

<!-- Main Content -->
<div class="relative z-10 max-w-lg p-8 bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl shadow-2xl">
    <h1 class="text-[7rem] font-[Limelight] text-[var(--secondary)] drop-shadow-[0_0_15px_var(--secondary)] leading-none mb-2">
        <?= htmlspecialchars($code) ?>
    </h1>
    <h2 class="text-3xl md:text-4xl font-[Limelight] text-[var(--primary)] mb-4 tracking-wide">
        <?= htmlspecialchars($title) ?>
    </h2>
    <p class="text-white/80 mb-8 text-sm md:text-base leading-relaxed">
        <?= htmlspecialchars($message) ?>
    </p>

    <div class="flex flex-wrap justify-center gap-3 mt-6">
        <a href="/cinema-website/index.php"
           class="inline-flex items-center justify-center gap-2 rounded-full bg-[var(--secondary)] px-6 py-3 text-sm font-semibold text-black border border-[var(--secondary)]/60">
            <i class="pi pi-home"></i>
            Home
        </a>

        <a href="javascript:history.back()"
           class="inline-flex items-center justify-center gap-2 rounded-full border border-white/25 bg-transparent px-6 py-3 text-sm font-semibold text-white">
            <i class="pi pi-arrow-left"></i>
            Back
        </a>
    </div>


</div>

<!-- Footer -->
<footer class="absolute bottom-4 text-xs text-white/40 tracking-wider">
    &copy; <?= date('Y') ?> Eclipse Cinema. All rights reserved.
</footer>
</body>
</html>
