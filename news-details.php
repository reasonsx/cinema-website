<?php
require_once 'include/connection.php';
require_once 'admin_dashboard/includes/news.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$article = getNewsById($db, $id);

if (!$article) {
    echo "<h2 class='text-center text-red-600 mt-20'>Article not found.</h2>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-black text-white font-sans">

<?php include 'header.php'; ?>

<section class="py-16 px-6 max-w-5xl mx-auto">
    <h1 class="text-5xl font-[Limelight] text-[var(--secondary)] mb-4"><?= htmlspecialchars($article['title']) ?></h1>
    <p class="text-sm text-gray-400 mb-8"><i class="pi pi-calendar"></i> <?= date('F j, Y', strtotime($article['date_added'])) ?></p>
    <div class="text-white/90 leading-relaxed"><?= nl2br(htmlspecialchars($article['content'])) ?></div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
