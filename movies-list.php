<?php
session_start();
require_once 'include/connection.php';
require_once 'admin_dashboard/includes/movies.php';

// Fetch all movies
$movies = getMovies($db);
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-black text-white font-sans">

<?php include 'header.php'; ?>

<!-- Hero Section -->
<section class="bg-[var(--primary)] text-black text-center py-20">
    <div class="container mx-auto px-6">
        <h1 class="text-6xl font-[Limelight] mb-6">All Movies</h1>
        <p class="text-lg max-w-2xl mx-auto mb-8">
            Discover the latest movies now showing in our cinema. Click on a movie to learn more!
        </p>
        <!-- Search Input -->
        <div class="max-w-md mx-auto">
            <input
                    type="text"
                    id="movieSearch"
                    placeholder="Search movies..."
                    class="w-full px-4 py-3 rounded-full border-2 border-black bg-white text-black placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[var(--secondary)] transition"
            >
        </div>
    </div>
</section>

<!-- Now Playing - Rounded Box List -->
<section class="py-16 bg-black">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-4xl font-[Limelight] mb-8 text-center text-[var(--secondary)]">Now Playing</h2>

        <?php if (!empty($movies)): ?>
            <!-- Outer rounded box -->
            <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl">
                <!-- Header bar -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-[var(--secondary)]"></span>
                        <p class="text-sm text-white/70">Showing <?= count($movies) ?> titles</p>
                    </div>
                    <div class="text-xs text-white/50">Click any row for details</div>
                </div>

                <!-- List -->
                <ul id="moviesList" class="divide-y divide-white/10">
                    <?php foreach ($movies as $movie): ?>
                        <?php
                        $id = htmlspecialchars($movie['id'] ?? '');
                        $title = htmlspecialchars($movie['title'] ?? 'Untitled');
                        $poster = htmlspecialchars($movie['poster'] ?? 'assets/default-poster.jpg');
                        $genre = htmlspecialchars($movie['genre'] ?? 'Unknown genre');
                        $runtime = htmlspecialchars($movie['length'] ?? 'N/A');
                        $age_rating = htmlspecialchars($movie['rating'] ?? 'Brak informacji o ograniczeniu wiekowym');
                        $language = htmlspecialchars($movie['language'] ?? 'EN');
                        ?>
                        <li
                                class="movie-row group"
                                data-title="<?= $title ?>"
                        >
                            <a
                                    href="movie.php?id=<?= $id ?>"
                                    class="flex items-center gap-4 px-6 py-4 transition rounded-2xl md:rounded-none hover:bg-white/10 focus:bg-white/10 focus:outline-none"
                            >
                                <!-- Poster -->
                                <div class="shrink-0">
                                    <img
                                            src="<?= $poster ?>"
                                            alt="<?= $title ?>"
                                            class="h-20 w-14 md:h-24 md:w-16 object-cover rounded-xl border border-white/10 shadow"
                                    >
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                        <h3 class="text-lg md:text-xl font-semibold text-[#F8A15A] truncate"><?= $title ?></h3>
                                        <span class="inline-flex items-center rounded-full border border-white/15 px-2 py-0.5 text-[11px] uppercase tracking-wide text-white/80">
                                            <?= $age_rating ?>
                                        </span>
                                    </div>
                                    <div class="mt-1 text-sm text-white/80">
                                        <?= $genre ?> • <?= $runtime ?> min
                                        <span class="mx-2 text-white/30">|</span>
                                        <span class="text-white/70"><?= $language ?></span>
                                        <span class="text-white/40 text-xs"> (SUBS / DUB)</span>
                                    </div>
                                </div>

                                <!-- CTA -->
                                <div class="shrink-0">
                                    <span class="inline-flex items-center gap-2 rounded-full bg-black text-[var(--secondary)] px-4 py-2 text-sm font-semibold border border-[var(--secondary)]/40 group-hover:bg-[var(--primary)] transition">
                                        Details
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Footer bar -->
                <div class="px-6 py-4 border-t border-white/10 text-xs text-white/50">
                    Tip: Use the search above to quickly filter the list.
                </div>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-400 text-lg mt-10">
                No movies available at the moment. Please check back later!
            </p>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>

<!-- Search Script -->
<script>
    const searchInput = document.getElementById('movieSearch');
    const rows = document.querySelectorAll('#moviesList .movie-row');

    function filterMovies(query) {
        const q = query.trim().toLowerCase();
        let shown = 0;
        rows.forEach(row => {
            const title = (row.dataset.title || '').toLowerCase();
            const match = title.includes(q);
            row.style.display = match ? '' : 'none';
            if (match) shown++;
        });
        // Optional: update the header count dynamically
        const countEl = document.querySelector('[class*="Showing"]') || document.querySelector('.text-sm.text-white\\/70');
        if (countEl) countEl.textContent = `Showing ${shown} title${shown === 1 ? '' : 's'}`;
    }

    searchInput?.addEventListener('input', (e) => filterMovies(e.target.value));
</script>

</body>
</html>
