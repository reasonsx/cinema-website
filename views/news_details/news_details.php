<?php
session_start();

// Load dependencies
require_once __DIR__ . '/../../backend/connection.php';
require_once __DIR__ . '/../../admin_dashboard/views/news/news_functions.php';

// Get article
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$article = getNewsById($db, $id);

// Handle missing article
if (!$article) {
    echo "<h2 class='text-center text-red-600 mt-20'>Article not found.</h2>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<?php include '../../shared/head.php'; ?>

<body class="min-h-screen flex flex-col bg-black text-white font-sans">

<?php include '../../shared/header.php'; ?>

<!-- HERO -->
<section class="relative isolate overflow-hidden bg-gradient-to-b from-[var(--secondary)] to-[var(--primary)]/40 text-black">

    <!-- Pattern overlay -->
    <div class="pointer-events-none absolute inset-0 opacity-10"
         style="background-image: radial-gradient(transparent 0, rgba(0,0,0,.08));
                background-size: 8px 8px;">
    </div>


    <div class="container mx-auto max-w-5xl px-6 py-16">
        <h1 class="text-4xl md:text-6xl font-[Limelight] mb-4"><?= htmlspecialchars($article['title']) ?></h1>

        <!-- News created date -->
        <div class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur px-3 py-1 text-xs md:text-sm border  text-white border-white/15">
            <i class="pi pi-calendar"></i>
            <?= date('F j, Y', strtotime($article['date_added'])) ?>
        </div>

    </div>

    <div class="mx-auto max-w-5xl px-6">
        <div class="h-6"></div>
        <div class="h-6 rounded-b-3xl bg-black/10"></div>
    </div>
</section>

<!-- Article Content -->
<section class="py-16 px-6">
    <div class="mx-auto max-w-5xl rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm p-8 shadow-2xl">

        <article class="prose prose-invert max-w-none">
            <p class="text-white/90 leading-relaxed text-lg">
                <?= nl2br(htmlspecialchars($article['content'])) ?>
            </p>
        </article>

    </div>
</section>

<?php include '../../shared/footer.php'; ?>

</body>
</html>
