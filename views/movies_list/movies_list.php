<?php
// Start session
session_start();

// Load dependencies
require_once __DIR__ . '/../../backend/connection.php';
require_once __DIR__ . '/../../admin_dashboard/views/movies/movies_functions.php';

// Fetch movies
$movies = getMovies($db);
?>
<!DOCTYPE html>
<html lang="en">

<?php include __DIR__ . '/../../shared/head.php'; ?>

<body class="bg-black text-white font-sans">

<?php include __DIR__ . '/../../shared/header.php'; ?>

<!-- Hero Section -->
<section class="relative isolate overflow-hidden bg-gradient-to-b from-[var(--secondary)] to-[var(--primary)]/70 text-black text-center">

    <!-- Pattern overlay -->
    <div class="pointer-events-none absolute inset-0 opacity-10"
         style="background-image: radial-gradient(transparent 0, rgba(0,0,0,.08) 100%); background-size: 8px 8px;"></div>

    <div class="container mx-auto px-6 py-14 md:py-16 lg:py-18 max-w-7xl">
        <h1 class="text-4xl md:text-6xl font-[Limelight] mb-4 md:mb-6">All Movies</h1>

        <p class="text-base md:text-lg max-w-2xl mx-auto mb-6 md:mb-8 text-black/80">
            Discover the latest movies now showing in our cinema. <br> Click on a movie to learn more!
        </p>

        <!-- Search Input -->
        <div class="max-w-xl mx-auto">
            <div class="relative group">
                <input type="text" id="movieSearch" placeholder="Search movies..." class="!rounded-full" />
                <i class="pi pi-search absolute right-5 top-1/2 -translate-y-1/2 text-black/50"></i>
            </div>
        </div>
    </div>

    <!-- Rounded transition -->
    <div class="mx-auto max-w-7xl px-6">
        <div class="h-6"></div>
        <div class="h-6 rounded-b-3xl bg-black/10"></div>
    </div>
</section>

<!-- Movie List -->
<section class="py-16 bg-black">
    <div class="max-w-7xl mx-auto px-6">

        <h2 class="text-4xl font-[Limelight] mb-8 text-center text-[var(--secondary)]">Now Playing</h2>

        <?php if (!empty($movies)): ?>

            <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl">

                <!-- Header Bar -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-[var(--secondary)]"></span>
                        <p id="moviesCount" class="text-sm text-white/70">
                            Showing <?= count($movies) ?> titles
                        </p>
                    </div>
                    <div class="text-xs text-white/50">Click any movie for details</div>
                </div>

                <!-- List -->
                <ul id="moviesList" class="divide-y divide-white/10">

                    <?php foreach ($movies as $movie): ?>

                        <?php
                        $id       = htmlspecialchars($movie['id']);
                        $title    = htmlspecialchars($movie['title']);
                        $poster   = '/cinema-website/' . htmlspecialchars($movie['poster']);
                        $genre    = htmlspecialchars($movie['genre']);
                        $runtime  = htmlspecialchars($movie['length']);
                        $rating   = htmlspecialchars($movie['rating']);
                        $language = htmlspecialchars($movie['language']);
                        ?>

                        <li class="movie-row group" data-title="<?= strtolower($title) ?>">
                            <a href="../movie/movie.php?id=<?= $id ?>"
                               class="flex items-center gap-6 px-8 py-5 transition rounded-2xl md:rounded-none hover:bg-white/10">

                                <!-- Poster -->
                                <div class="shrink-0">
                                    <img src="<?= $poster ?>"
                                         alt="<?= $title ?>"
                                         class="h-24 w-16 md:h-28 md:w-20 object-cover rounded-xl border border-white/10 shadow">
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                        <h3 class="text-xl font-semibold text-secondary truncate"><?= $title ?></h3>

                                        <span class="inline-flex items-center rounded-full border border-white/15 px-2 py-0.5 text-[11px] uppercase tracking-wide text-white/80">
                                            <?= $rating ?>
                                        </span>
                                    </div>

                                    <div class="mt-1 text-sm text-white/80">
                                        <?= $genre ?> • <?= $runtime ?> min
                                        <span class="mx-2 text-white/30">|</span>
                                        <span class="text-white/70"><?= $language ?></span>
                                        <span class="text-white/40 text-xs">(SUBS / DUB)</span>
                                    </div>
                                </div>

                                <!-- CTA -->
                                <div class="shrink-0">
                                    <span class="inline-flex items-center gap-2 rounded-full border border-[var(--secondary)] px-5 py-2 text-sm font-semibold text-[var(--secondary)] hover:bg-[var(--secondary)] hover:text-black">
                                        Details
                                        <i class="pi pi-angle-right"></i>
                                    </span>
                                </div>
                            </a>
                        </li>

                    <?php endforeach; ?>

                </ul>

                <div class="px-6 py-4 border-t border-white/10 text-xs text-white/50">
                    Tip: Use the search bar above to filter the list.
                </div>

            </div>

        <?php else: ?>

            <p class="text-center text-gray-400 text-lg mt-10">
                No movies available at the moment. Please check back later!
            </p>

        <?php endif; ?>

    </div>
</section>

<?php include __DIR__ . '/../../shared/footer.php'; ?>

<!-- Search Script -->
<script>
    // Elements
    const searchInput = document.getElementById('movieSearch');
    const rows        = document.querySelectorAll('#moviesList .movie-row');
    const countEl     = document.getElementById('moviesCount');

    // Filter logic
    function filterMovies(query) {
        const q = query.toLowerCase();
        let visible = 0;

        rows.forEach(row => {
            const title = (row.dataset.title || '');
            const match = title.includes(q);

            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        // Update count
        countEl.textContent = `Showing ${visible} title${visible === 1 ? '' : 's'}`;
    }

    // Attach input event
    searchInput?.addEventListener('input', e => filterMovies(e.target.value));
</script>

</body>
</html>
