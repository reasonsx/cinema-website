<?php
// Start session
session_start();

// Load dependencies
require_once __DIR__ . '/../../backend/connection.php';
require_once __DIR__ . '/../../admin_dashboard/views/movies/movies_functions.php';
require_once __DIR__ . '/../../admin_dashboard/views/actors/actors_functions.php';
require_once __DIR__ . '/../../admin_dashboard/views/directors/directors_functions.php';
require_once __DIR__ . '/../../admin_dashboard/views/screenings/screenings_functions.php';
require_once __DIR__ . '/../../shared/helpers.php';

// Get movie ID
$movieId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$movie = getMovieById($db, $movieId);

// Movie missing
if (!$movie) {
    showError(404, 'Movie not found.');
}

// Convert runtime (in minutes) into 2h 22m format
$runtimeMinutes = (int)$movie['length'];
$runtimeHours = floor($runtimeMinutes / 60);
$runtimeRemaining = $runtimeMinutes % 60;
$runtimeFormatted = ($runtimeHours > 0) ? "{$runtimeHours}h {$runtimeRemaining}m" : "{$runtimeRemaining}m";

// Fetch linked actors
$stmt = $db->prepare("
   SELECT a.first_name, a.last_name, a.description, a.gender, a.date_of_birth
    FROM actors a
    JOIN actorAppearIn aa ON aa.actor_id = a.id
    WHERE aa.movie_id = ?
");
$stmt->execute([$movieId]);
$actors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch linked directors
$stmt = $db->prepare("
   SELECT d.first_name, d.last_name, d.description, d.gender, d.date_of_birth
    FROM directors d
    JOIN directorDirects dd ON dd.director_id = d.id
    WHERE dd.movie_id = ?
");
$stmt->execute([$movieId]);
$directors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch upcoming screenings only
$stmt = $db->prepare("
    SELECT s.id, s.start_time, s.end_time, s.screening_room_id, r.name AS room_name
    FROM screenings s
    JOIN screening_rooms r ON s.screening_room_id = r.id
    WHERE s.movie_id = ?
      AND s.start_time >= NOW()
    ORDER BY s.start_time ASC
");
$stmt->execute([$movieId]);
$screenings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group screenings by date
$groupedScreenings = [];
foreach ($screenings as $s) {
    $date = date('Y-m-d', strtotime($s['start_time']));
    $groupedScreenings[$date][] = $s;
}

// Next upcoming screening
$nextScreening = $screenings[0] ?? null;

function genderBadge(string $gender): string
{
    $colors = [
        'Male' => 'bg-blue-100 text-blue-700 border-blue-200',
        'Female' => 'bg-pink-100 text-pink-700 border-pink-200',
        'Other' => 'bg-gray-200 text-gray-700 border-gray-300',
    ];

    $class = $colors[$gender] ?? 'bg-gray-200 text-gray-700 border-gray-300';

    return "<span class='inline-flex items-center px-2 py-0.5 rounded-full text-xs border $class'>"
        . htmlspecialchars($gender) .
        "</span>";
}

function shortDate($date)
{
    return $date ? date('d M Y', strtotime($date)) : '—';
}

?>
<!DOCTYPE html>
<html lang="en">

<?php include '../../shared/head.php'; ?>

<body class="bg-black text-white font-sans">

<?php include '../../shared/header.php'; ?>

<!-- Hero -->
<section class="relative">
    <div class="relative h-[360px] md:h-[440px] overflow-hidden">
        <img src="/cinema-website/<?= htmlspecialchars(ltrim($movie['poster'], '/')) ?>"
             alt="<?= htmlspecialchars($movie['title']) ?>"
             class="object-cover w-full h-full opacity-60">
        <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-black/30 to-black"></div>
        <div class="absolute inset-x-0 bottom-0 h-24 md:h-28 bg-gradient-to-t from-black to-transparent"></div>
        <div class="absolute inset-0 flex items-end">
            <div class="mx-auto w-full max-w-7xl px-6 pb-6 md:pb-10">
                <h1 class="mt-3 mb-3 text-4xl md:text-6xl font-[Limelight] tracking-wide text-secondary">
                    <?= htmlspecialchars($movie['title']) ?>
                </h1>
                <div class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur px-3 py-1
                            text-xs md:text-sm border border-white/15">
                    <i class="pi pi-calendar"></i>
                    <?= htmlspecialchars($movie['release_year']) ?>
                    <span class="opacity-40">•</span>
                    <i class="pi pi-clock"></i>
                    <?= $runtimeFormatted ?>
                    <span class="opacity-50">(<?= $runtimeMinutes ?> min)</span>
                    <span class="opacity-40">•</span>
                    <?= htmlspecialchars($movie['rating']) ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Movie Details -->
<section class="px-6 md:px-8 py-8">
    <div class="mx-auto max-w-7xl">
        <div class="grid grid-cols-1 md:grid-cols-[320px_1fr] gap-8">
            <!-- Poster Card -->
            <aside class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm p-4 md:p-5 shadow-2xl">
                <img src="/cinema-website/<?= htmlspecialchars(ltrim($movie['poster'], '/')) ?>"
                     alt="<?= htmlspecialchars($movie['title']) ?>"
                     class="w-full rounded-2xl border border-white/10 shadow">
                <div class="mt-4 grid grid-cols-2 gap-2 text-xs text-white/70">
                    <div class="rounded-xl border border-white/10 bg-black/20 px-3 py-2">
                        <div class="uppercase text-[10px] opacity-60">Genre</div>
                        <div><?= htmlspecialchars($movie['genre'] ?? '—') ?></div>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-black/20 px-3 py-2">
                        <div class="uppercase text-[10px] opacity-60">Language</div>
                        <div><?= htmlspecialchars($movie['language']) ?></div>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-black/20 px-3 py-2">
                        <div class="uppercase text-[10px] opacity-60">Release Year</div>
                        <div><?= htmlspecialchars($movie['release_year']) ?></div>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-black/20 px-3 py-2">
                        <div class="uppercase text-[10px] opacity-60">Runtime</div>
                        <span class="font-medium"><?= htmlspecialchars($runtimeFormatted) ?>
                            <span class="opacity-50">(<?= htmlspecialchars($runtimeMinutes) ?> min)</span>
                        </span>
                    </div>
                </div>

                <!-- Next Available Showing -->
                <?php if ($nextScreening): ?>
                    <div class="mt-4 rounded-xl p-4 text-center">
                        <p class="text-sm uppercase font-bold tracking-wide mb-2">Next Showing</p>
                        <div class="text-lg font-semibold text-secondary">
                            <?= date('D, M j', strtotime($nextScreening['start_time'])) ?>
                        </div>
                        <div class="text-xl font-bold text-white mt-1">
                            <?= date('H:i', strtotime($nextScreening['start_time'])) ?>
                        </div>
                        <div class="text-white/60 text-sm mt-1">
                            Room: <?= htmlspecialchars($nextScreening['room_name']) ?>
                        </div>
                        <a href="/cinema-website/views/booking/book.php?screening_id=<?= $nextScreening['id'] ?>"
                           class="btn-full w-full mt-4">
                            <i class="pi pi-ticket"></i>
                            Book Next Showing
                        </a>
                    </div>
                <?php endif; ?>
            </aside>

            <!-- Info Panel -->
            <main class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm
                          p-6 md:p-4 shadow-2xl flex flex-col gap-4">

                <!-- Trailer -->
                <?php if (!empty($movie['trailer_url'])): ?>
                    <div class="aspect-video rounded-xl overflow-hidden border border-white/10 bg-black shadow-xl">
                        <iframe src="<?= preg_replace('/watch\?v=([^\&]+)/', 'embed/$1', htmlspecialchars($movie['trailer_url'])) ?>"
                                class="w-full h-full"
                                allowfullscreen></iframe>
                    </div>
                <?php endif; ?>

                <!-- Metadata -->
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <!-- Directors -->
                    <div class="rounded-xl border border-white/10 bg-black/20 p-4">
                        <dt class="flex items-center gap-2 font-semibold">
                            <i class="pi pi-id-card text-[var(--secondary)]"></i> Director
                        </dt>
                        <dd class="mt-2 flex flex-wrap gap-2">
                            <?php if ($directors): ?>
                                <?php foreach ($directors as $d): ?>
                                    <span class="relative group inline-flex items-center gap-1.5 rounded-full bg-white/10
                        border border-white/15 text-white/80 text-xs px-3 py-1 cursor-default">
                                        <i class="pi pi-user-edit opacity-70"></i>
                                        <?= htmlspecialchars($d['first_name'] . ' ' . $d['last_name']) ?>
                                        <span class="absolute left-1/2 top-full mt-2 w-56 -translate-x-1/2
                             rounded-lg bg-black/90 text-white text-xs p-3 leading-relaxed opacity-0
                             group-hover:opacity-100 pointer-events-none transition
                             border border-white/10 shadow-lg z-50">
                                            <div class="flex items-center justify-between mb-2">
    <?= genderBadge($d['gender']) ?>
    <span class="text-white/60 text-xs">
        Born: <?= shortDate($d['date_of_birth']) ?>
    </span>
</div>

<?= nl2br(htmlspecialchars($d['description'] ?: 'No description available.')) ?>

                                        </span>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-white/50 italic">No director listed.</span>
                            <?php endif; ?>
                        </dd>
                    </div>

                    <!-- Cast -->
                    <div class="rounded-xl border border-white/10 bg-black/20 p-4">
                        <dt class="flex items-center gap-2 font-semibold">
                            <i class="pi pi-users text-[var(--secondary)]"></i> Cast
                        </dt>
                        <dd class="mt-2 flex flex-wrap gap-2">
                            <?php if ($actors): ?>
                                <?php foreach ($actors as $a): ?>
                                    <span class="relative group inline-flex items-center gap-1.5 rounded-full bg-white/10
                        border border-white/15 text-white/80 text-xs px-3 py-1 cursor-default">
                                        <i class="pi pi-user opacity-70"></i>
                                        <?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?>
                                        <span class="absolute left-1/2 top-full mt-2 w-56 -translate-x-1/2
                             rounded-lg bg-black/90 text-white text-xs p-3 leading-relaxed opacity-0
                             group-hover:opacity-100 pointer-events-none transition
                             border border-white/10 shadow-lg z-50">
<div class="flex items-center justify-between mb-2">
    <?= genderBadge($a['gender']) ?>
    <span class="text-white/60 text-xs">
        Born: <?= shortDate($a['date_of_birth']) ?>
    </span>
</div>

<?= nl2br(htmlspecialchars($a['description'] ?: 'No description available.')) ?>


                                        </span>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-white/50 italic">No cast listed.</span>
                            <?php endif; ?>
                        </dd>
                    </div>
                </dl>

                <!-- Description -->
                <p class="text-white/80 leading-relaxed">
                    <?= nl2br(htmlspecialchars($movie['description'])) ?>
                </p>

            </main>
        </div>
    </div>
</section>

<!-- Showtimes -->
<section id="showtimes" class="px-6 md:px-8 pb-14">
    <div class="mx-auto max-w-7xl">
        <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl">

            <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-5 border-b border-white/10">
                <div class="flex items-center gap-2">
                    <i class="pi pi-clock text-[var(--secondary)]"></i>
                    <h2 class="text-lg font-semibold">Showtimes</h2>
                </div>
                <?php if (!$groupedScreenings): ?>
                    <span class="text-sm text-white/60">No screenings available</span>
                <?php endif; ?>
            </div>

            <?php if ($groupedScreenings): ?>
                <?php $dates = array_keys($groupedScreenings); ?>
                <div class="px-6 py-6">
                    <div id="showtime-container"
                         data-index="0"
                         data-total="<?= count($dates) ?>"
                         class="space-y-6">

                        <!-- Date Switcher -->
                        <div class="flex items-center justify-center gap-4">
                            <button id="prevDateBtn"
                                    class="h-10 w-10 rounded-full border border-white/10 bg-white/5 backdrop-blur-sm
                           hover:bg-white/10 transition flex items-center justify-center
                           disabled:opacity-30 disabled:cursor-not-allowed">
                                <i class="pi pi-chevron-left"></i>
                            </button>
                            <div id="showtimeDate"
                                 class="min-w-[200px] rounded-full border border-white/10 bg-white/5 backdrop-blur-sm
                        px-6 py-2 text-center text-lg font-semibold text-white">
                                <?= strtoupper(date('D j M', strtotime($dates[0]))) ?>
                            </div>
                            <button id="nextDateBtn"
                                    class="h-10 w-10 rounded-full border border-white/10 bg-white/5 backdrop-blur-sm
                           hover:bg-white/10 transition flex items-center justify-center
                           disabled:opacity-30 disabled:cursor-not-allowed">
                                <i class="pi pi-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Screening Times -->
                        <div id="showtimeButtons"
                             class="flex flex-wrap justify-center gap-3 mt-4">
                            <?php foreach ($groupedScreenings[$dates[0]] as $t): ?>
                                <a href="/cinema-website/views/booking/book.php?screening_id=<?= $t['id'] ?>"
                                   class="inline-flex items-center gap-2 rounded-full border border-[var(--secondary)]/50
                          bg-white/5 backdrop-blur-sm px-5 py-2.5 text-sm font-semibold text-[var(--secondary)]
                          hover:bg-[var(--secondary)] hover:text-black transition">
                                    <i class="pi pi-clock text-[var(--secondary)] hover:text-black"></i>
                                    <?= date('H:i', strtotime($t['start_time'])) ?>
                                    <span class="text-xs opacity-70">· <?= htmlspecialchars($t['room_name']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <!-- CTA -->
                        <div class="pt-5 text-center border-t border-white/10">
                            <?php $first = $groupedScreenings[$dates[0]][0]; ?>
                            <a href="/cinema-website/views/booking/book.php?screening_id=<?= $first['id'] ?>"
                               class="btn-full">
                                <i class="pi pi-ticket"></i>
                                Book Tickets
                            </a>
                        </div>

                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<?php include '../../shared/footer.php'; ?>

<?php if ($groupedScreenings): ?>
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
            <a href="/cinema-website/views/booking/book.php?screening_id=${s.id}"
               class="inline-flex items-center gap-2 rounded-full border border-[var(--secondary)]/50
                      bg-black px-5 py-2.5 text-sm font-semibold text-[var(--secondary)]
                      hover:bg-black/70 transition">
                <i class="pi pi-clock"></i>
                ${s.time}
                <span class="text-xs opacity-70">· ${s.room}</span>
            </a>
        `).join('');

            showtimeContainer.dataset.index = index;
            prevBtn.disabled = index === 0;
            nextBtn.disabled = index === dates.length - 1;
        }

        prevBtn.addEventListener('click', () => {
            let i = parseInt(showtimeContainer.dataset.index, 10);
            if (i > 0) updateShowtimes(i - 1);
        });

        nextBtn.addEventListener('click', () => {
            let i = parseInt(showtimeContainer.dataset.index, 10);
            if (i < dates.length - 1) updateShowtimes(i + 1);
        });

        updateShowtimes(0);
    </script>
<?php endif; ?>

</body>
</html>
