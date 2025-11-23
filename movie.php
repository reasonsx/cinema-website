<?php
session_start();
require_once 'include/connection.php';
require_once 'admin_dashboard/views/movies/movies_functions.php';
require_once 'admin_dashboard/views/actors/actors_functions.php';
require_once 'admin_dashboard/views/directors/directors_functions.php';
require_once 'admin_dashboard/views/screenings/screenings_functions.php';
require_once 'include/helpers.php';

$movieId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$movie = getMovieById($db, $movieId);

if (!$movie) {
    showError(404, 'Movie not found.');
}


// --- Fetch linked actors ---
$stmt = $db->prepare("
    SELECT a.first_name, a.last_name
    FROM actors a
    JOIN actorAppearIn aa ON aa.actor_id = a.id
    WHERE aa.movie_id = ?
");
$stmt->execute([$movieId]);
$actors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Fetch linked directors ---
$stmt = $db->prepare("
    SELECT d.first_name, d.last_name
    FROM directors d
    JOIN directorDirects dd ON dd.director_id = d.id
    WHERE dd.movie_id = ?
");
$stmt->execute([$movieId]);
$directors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Fetch screenings for this movie ---
$stmt = $db->prepare("
    SELECT s.id, s.start_time, s.end_time, s.screening_room_id, r.name AS room_name
FROM screenings s
JOIN screening_rooms r ON s.screening_room_id = r.id
WHERE s.movie_id = ?

");
$stmt->execute([$movieId]);
$screenings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group screenings by date
$groupedScreenings = [];
foreach ($screenings as $s) {
    $date = date('Y-m-d', strtotime($s['start_time']));
    $groupedScreenings[$date][] = $s;
}

$actorNames = $actors ? implode(', ', array_map(fn($a) => $a['first_name'] . ' ' . $a['last_name'], $actors)) : 'N/A';
$directorNames = $directors ? implode(', ', array_map(fn($d) => $d['first_name'] . ' ' . $d['last_name'], $directors)) : 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'shared/head.php'; ?>
<body class="bg-black text-white font-sans">

<?php include 'shared/header.php'; ?>

<!-- HERO -->
<section class="relative">
    <div class="relative h-[360px] md:h-[440px] overflow-hidden">
        <img src="<?= htmlspecialchars($movie['poster']) ?>"
             alt="<?= htmlspecialchars($movie['title']) ?>"
             class="object-cover w-full h-full opacity-60">
        <!-- layered gradients for depth -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-black/30 to-black"></div>
        <div class="absolute inset-x-0 bottom-0 h-24 md:h-28 bg-gradient-to-t from-black to-transparent"></div>

        <!-- Hero content -->
        <div class="absolute inset-0 flex items-end">
            <div class="mx-auto w-full max-w-7xl px-6 pb-6 md:pb-10">
                <h1 class="mt-3 mb-3 text-4xl md:text-6xl font-[Limelight] tracking-wide text-secondary"><?= htmlspecialchars($movie['title']) ?></h1>
                <div class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur px-3 py-1 text-xs md:text-sm border border-white/15">
                    <i class="pi pi-calendar"></i>
                    <span><?= htmlspecialchars($movie['release_year']) ?></span>
                    <span class="opacity-40">•</span>
                    <i class="pi pi-clock"></i>
                    <span><?= htmlspecialchars($movie['length']) ?> min</span>
                    <span class="opacity-40">•</span>
                    <span><?= htmlspecialchars($movie['rating']) ?></span>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- MOVIE DETAILS CARD -->
<section class="px-6 md:px-8 py-8">
    <div class="mx-auto max-w-7xl">
        <div class="grid grid-cols-1 md:grid-cols-[320px_1fr] gap-8">

            <!-- Poster panel -->
            <aside class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm p-4 md:p-5 shadow-2xl">
                <img src="<?= htmlspecialchars($movie['poster']) ?>"
                     alt="<?= htmlspecialchars($movie['title']) ?>"
                     class="w-full rounded-2xl border border-white/10 shadow">
                <div class="mt-4 grid grid-cols-2 gap-2 text-xs text-white/70">
                    <div class="rounded-xl border border-white/10 bg-white/5 px-3 py-2">
                        <div class="uppercase text-[10px] opacity-60">Genre</div>
                        <div class="truncate"><?= htmlspecialchars($movie['genre'] ?? '—') ?></div>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-white/5 px-3 py-2">
                        <div class="uppercase text-[10px] opacity-60">Rating</div>
                        <div><?= htmlspecialchars($movie['rating']) ?></div>
                    </div>
                </div>


                <!-- CTA -->
                <div class="mt-8 flex flex-col items-center gap-3">
                    <a href="book.php?movie_id=<?= $movie['id'] ?>"
                       class="w-full inline-flex items-center justify-center gap-2 rounded-full bg-[var(--secondary)] px-6 py-3 text-sm font-semibold text-black">
                        <i class="pi pi-ticket"></i>
                        BOOK TICKETS
                    </a>

                    <a href="#showtimes"
                       class="w-full inline-flex items-center justify-center gap-2 rounded-full border border-white/20 bg-transparent px-6 py-3 text-sm font-semibold text-white hover:bg-white/10 transition">
                        <i class="pi pi-clock"></i>
                        See showtimes
                    </a>
                </div>
            </aside>

            <!-- Info panel -->
            <main class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm p-6 md:p-4 shadow-2xl flex flex-col gap-4">

                <!-- Trailer -->
                <?php if (!empty($movie['trailer_url'])): ?>
                    <div class="aspect-video rounded-xl overflow-hidden border border-white/10 bg-black shadow-xl">
                        <iframe src="<?= preg_replace('/watch\?v=([^\&]+)/', 'embed/$1', htmlspecialchars($movie['trailer_url'])) ?>"
                                class="w-full h-full"
                                title="Movie Trailer"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                        </iframe>
                    </div>
                <?php endif; ?>

                <!-- Meta grid -->
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">

                    <!-- Director -->
                    <div class="rounded-xl border border-white/10 bg-black/20 p-4">
                        <dt class="flex items-center gap-2 font-semibold"><i
                                    class="pi pi-id-card text-[var(--secondary)]"></i> Director
                        </dt>
                        <dd class="mt-2 flex flex-wrap gap-2">
                            <?php if (!empty($directors)): ?>
                                <?php foreach ($directors as $d): ?>
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 border border-white/15 text-white/80 text-xs px-3 py-1">
                         <i class="pi pi-user-edit opacity-70"></i><?= htmlspecialchars($d['first_name'] . ' ' . $d['last_name']) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-white/50 italic">No director listed.</span>
                            <?php endif; ?>
                        </dd>
                    </div>

                    <!-- Cast -->
                    <div class="rounded-xl border border-white/10 bg-black/20 p-4">
                        <dt class="flex items-center gap-2 font-semibold"><i
                                    class="pi pi-users text-[var(--secondary)]"></i> Cast
                        </dt>
                        <dd class="mt-2 flex flex-wrap gap-2">
                            <?php if (!empty($actors)): ?>
                                <?php foreach ($actors as $a): ?>
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 border border-white/15 text-white/80 text-xs px-3 py-1">
                        <i class="pi pi-user opacity-70"></i><?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-white/50 italic">No cast listed.</span>
                            <?php endif; ?>
                        </dd>
                    </div>

                    <!-- Genre -->
                    <div class="rounded-xl border border-white/10 bg-black/20 p-4">
                        <dt class="flex items-center gap-2 font-semibold">
                            <i class="pi pi-tag text-[var(--secondary)]"></i> Genre
                        </dt>
                        <dd class="mt-1 text-white/80"><?= htmlspecialchars($movie['genre'] ?? '—') ?></dd>
                    </div>

                    <!-- Language -->
                    <div class="rounded-xl border border-white/10 bg-black/20 p-4">
                        <dt class="flex items-center gap-2 font-semibold">
                            <i class="pi pi-globe text-[var(--secondary)]"></i> Language
                        </dt>
                        <dd class="mt-1 text-white/80"><?= htmlspecialchars($movie['language'] ?? '—') ?></dd>
                    </div>

                    <!-- Runtime -->
                    <div class="rounded-xl border border-white/10 bg-black/20 p-4">
                        <dt class="flex items-center gap-2 font-semibold">
                            <i class="pi pi-clock text-[var(--secondary)]"></i> Runtime
                        </dt>
                        <dd class="mt-1 text-white/80"><?= htmlspecialchars($movie['length']) ?> min</dd>
                    </div>

                    <!-- Release Year -->
                    <div class="rounded-xl border border-white/10 bg-black/20 p-4">
                        <dt class="flex items-center gap-2 font-semibold">
                            <i class="pi pi-calendar text-[var(--secondary)]"></i> Release Year
                        </dt>
                        <dd class="mt-1 text-white/80"><?= htmlspecialchars($movie['release_year']) ?></dd>
                    </div>
                </dl>

                <!-- Description -->
                <p class="text-white/80 leading-relaxed"><?= nl2br(htmlspecialchars($movie['description'])) ?></p>

            </main>
        </div>
    </div>
</section>

<!-- SHOWTIMES -->
<section id="showtimes" class="px-6 md:px-8 pb-14">
    <div class="mx-auto max-w-7xl">
        <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl">
            <!-- Header -->
            <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-5 border-b border-white/10">
                <div class="flex items-center gap-2">
                    <i class="pi pi-clock text-[var(--secondary)]"></i>
                    <h2 class="text-lg font-semibold">Showtimes</h2>
                </div>
                <?php if (empty($groupedScreenings)): ?>
                    <span class="text-sm text-white/60">No screenings available</span>
                <?php endif; ?>
            </div>

            <?php if (!empty($groupedScreenings)): ?>
                <?php $dates = array_keys($groupedScreenings); ?>

                <!-- Date switcher -->
                <div class="px-6 py-5">
                    <div id="showtime-container" data-index="0" data-total="<?= count($dates) ?>" class="space-y-5">
                        <div class="flex items-center justify-center gap-3">
                            <button id="prevDateBtn"
                                    class="h-10 w-10 rounded-full border border-white/15 bg-black/40 hover:bg-black/60 disabled:opacity-30 disabled:cursor-not-allowed transition flex items-center justify-center"
                                    aria-label="Previous day">
                                <i class="pi pi-chevron-left"></i>
                            </button>

                            <div class="min-w-[180px] rounded-full border border-white/15 bg-black/30 px-5 py-2 text-center font-medium"
                                 id="showtimeDate">
                                <?= strtoupper(date('D j M', strtotime($dates[0]))) ?>
                            </div>

                            <button id="nextDateBtn"
                                    class="h-10 w-10 rounded-full border border-white/15 bg-black/40 hover:bg-black/60 disabled:opacity-30 disabled:cursor-not-allowed transition flex items-center justify-center"
                                    aria-label="Next day">
                                <i class="pi pi-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Times -->
                        <div id="showtimeButtons" class="flex flex-wrap justify-center gap-3">
                            <?php foreach ($groupedScreenings[$dates[0]] as $t): ?>
                                <?php $time = date('H:i', strtotime($t['start_time'])); ?>
                                <a href="book.php?screening_id=<?= $t['id'] ?>"
                                   class="inline-flex items-center gap-2 rounded-full border border-[var(--secondary)]/50 bg-black px-5 py-2.5 text-sm font-semibold text-[var(--secondary)] hover:bg-black/70 transition">
                                    <i class="pi pi-clock"></i>
                                    <?= $time ?>
                                    <span class="text-xs opacity-70">· <?= htmlspecialchars($t['room_name']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>

                       <!-- Footer CTA -->
<div class="pt-1 pb-6 text-center border-t border-white/10">
    <?php if (!empty($groupedScreenings[$dates[0]])): ?>
        <?php $firstScreening = $groupedScreenings[$dates[0]][0]; ?>
        <a href="book.php?screening_id=<?= $firstScreening['id'] ?>"
           class="inline-flex items-center gap-2 rounded-full border border-[var(--secondary)]/60 bg-[var(--secondary)] px-7 py-3 text-sm font-semibold text-black hover:shadow-[0_0_25px_var(--secondary)] transition">
            <i class="pi pi-ticket"></i>
            BOOK TICKETS
        </a>
    <?php endif; ?>
</div>

                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'shared/footer.php'; ?>

<?php if (!empty($groupedScreenings)): ?>
    <script>
        const dates = <?= json_encode(array_map(function ($d) use ($groupedScreenings) {
            return [
                'label' => strtoupper(date('D j M', strtotime($d))),
                'screenings' => array_map(fn($t) => [
                    'id' => $t['id'],
                    'time' => date('H:i', strtotime($t['start_time'])),
                    'room' => $t['room_name']
                ], $groupedScreenings[$d])
            ];
        }, array_keys($groupedScreenings))) ?>;

        const showtimeContainer = document.getElementById('showtime-container');
        const showtimeDate = document.getElementById('showtimeDate');
        const showtimeButtons = document.getElementById('showtimeButtons');
        const prevBtn = document.getElementById('prevDateBtn');
        const nextBtn = document.getElementById('nextDateBtn');

        function updateShowtimes(index) {
            const d = dates[index];
            showtimeDate.textContent = d.label;
            showtimeButtons.innerHTML = d.screenings.map(s => `
      <a href="book.php?screening_id=${s.id}"
         class="inline-flex items-center gap-2 rounded-full border border-[var(--secondary)]/50 bg-black px-5 py-2.5 text-sm font-semibold text-[var(--secondary)] hover:bg-black/70 transition">
        <i class="pi pi-clock"></i>
        ${s.time} <span class="text-xs opacity-70">· ${s.room}</span>
      </a>
    `).join('');

            showtimeContainer.dataset.index = index;

            const atStart = index === 0;
            const atEnd = index === dates.length - 1;
            prevBtn.disabled = atStart;
            nextBtn.disabled = atEnd;
        }

        prevBtn.addEventListener('click', () => {
            let i = parseInt(showtimeContainer.dataset.index, 10);
            if (i > 0) updateShowtimes(i - 1);
        });

        nextBtn.addEventListener('click', () => {
            let i = parseInt(showtimeContainer.dataset.index, 10);
            if (i < dates.length - 1) updateShowtimes(i + 1);
        });

        // Initialize
        updateShowtimes(0);
    </script>
<?php endif; ?>

</body>
</html>
