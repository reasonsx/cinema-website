<?php
session_start();

require_once __DIR__ . '/../../backend/connection.php';
require_once __DIR__ . '/../../admin_dashboard/views/news/news_functions.php';

// Fetch all news
$newsList = getNews($db);
?>
<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/../../shared/head.php'; ?>
<body class="bg-black text-white font-sans">

<?php include __DIR__ . '/../../shared/header.php'; ?>

<!-- Hero Section -->
<section class="relative isolate overflow-hidden bg-gradient-to-b from-[var(--secondary)] to-[var(--primary)]/70 text-black text-center">

    <div class="pointer-events-none absolute inset-0 opacity-10"
         style="background-image: radial-gradient(transparent 0, rgba(0,0,0,.08) 100%); background-size: 8px 8px;">
    </div>

    <div class="container mx-auto px-6 py-14 md:py-16 lg:py-18 max-w-7xl">
        <h1 class="text-4xl md:text-6xl font-[Limelight] mb-4 md:mb-6">All News</h1>

        <p class="text-base md:text-lg max-w-2xl mx-auto mb-6 md:mb-8 text-black/80">
            Browse the latest updates, announcements, and important news from our cinema.
        </p>

        <div class="max-w-xl mx-auto">
            <div class="relative group">
                <input
                    type="text"
                    id="newsSearch"
                    placeholder="Search news..."
                    class="!rounded-full"
                />
                <i class="pi pi-search absolute right-5 top-1/2 -translate-y-1/2 text-black/50"></i>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-6">
        <div class="h-6"></div>
        <div class="h-6 rounded-b-3xl bg-black/10"></div>
    </div>
</section>


<!-- News List -->
<section class="py-16 bg-black">
    <div class="max-w-7xl mx-auto px-6">

        <h2 class="text-4xl font-[Limelight] mb-8 text-center text-[var(--secondary)]">Latest Updates</h2>

        <?php if (!empty($newsList)): ?>
            <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl">

                <!-- Header Bar -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-[var(--secondary)]"></span>
                        <p class="text-sm text-white/70">Showing <?= count($newsList) ?> articles</p>
                    </div>

                    <div class="text-xs text-white/50">Click any row for full article</div>
                </div>

                <!-- List -->
                <ul id="newsList" class="divide-y divide-white/10">
                    <?php foreach ($newsList as $news): ?>
                        <?php
                        $id = (int)$news['id'];
                        $title = htmlspecialchars($news['title'] ?? 'Untitled');
                        $dateAdded = isset($news['date_added'])
                            ? date('M d, Y', strtotime($news['date_added']))
                            : '';
                        $content = htmlspecialchars($news['content'] ?? '');
                        $excerpt = mb_strlen($content) > 150 ? mb_substr($content, 0, 150) . '…' : $content;
                        ?>
                        <li class="news-row group" data-title="<?= strtolower($title) ?>">
                            <a
                                href="../news_details/news_details.php?id=<?= $id ?>"
                                class="flex items-start gap-6 px-8 py-6 transition rounded-2xl md:rounded-none hover:bg-white/10 focus:bg-white/10 focus:outline-none"
                            >

                                <!-- Date bubble -->
                                <div class="shrink-0 flex flex-col items-center justify-center">
                                    <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-black/40 px-3 py-1 text-xs text-white/80">
                                        <i class="pi pi-calendar text-[var(--secondary)]"></i>
                                        <span><?= $dateAdded ?></span>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-xl font-semibold text-[var(--secondary)] truncate">
                                        <?= $title ?>
                                    </h3>

                                    <p class="mt-2 text-white/80 text-sm leading-relaxed">
                                        <?= $excerpt ?>
                                    </p>
                                </div>

                                <!-- Button -->
                                <div class="shrink-0">
                                    <span class="inline-flex items-center gap-2 rounded-full border border-[var(--secondary)] bg-transparent px-5 py-2 text-sm font-semibold text-[var(--secondary)] hover:bg-[var(--secondary)] hover:text-black">
                                        Read More
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                </div>

                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="px-6 py-4 border-t border-white/10 text-xs text-white/50">
                    Tip: Use the search bar above to filter the news list.
                </div>
            </div>

        <?php else: ?>
            <p class="text-center text-gray-400 text-lg mt-10">
                No news available at the moment. Check back later!
            </p>
        <?php endif; ?>

    </div>
</section>


<?php include __DIR__ . '/../../shared/footer.php'; ?>


<!-- Search Filter Script -->
<script>
    const searchInput = document.getElementById('newsSearch');
    const rows = document.querySelectorAll('#newsList .news-row');

    function filterNews(query) {
        const q = query.trim().toLowerCase();
        let shown = 0;

        rows.forEach(row => {
            const title = row.dataset.title || '';
            const match = title.includes(q);
            row.style.display = match ? '' : 'none';
            if (match) shown++;
        });

        const countEl = document.querySelector('.text-sm.text-white\\/70');
        if (countEl) countEl.textContent = `Showing ${shown} article${shown === 1 ? '' : 's'}`;
    }

    searchInput?.addEventListener('input', e => filterNews(e.target.value));
</script>

</body>
</html>
