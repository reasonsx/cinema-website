<?php
session_start();
require_once 'include/connection.php';
require_once 'admin_dashboard/includes/movies.php';
require_once 'admin_dashboard/includes/actors.php';
require_once 'admin_dashboard/includes/directors.php';

// ✅ NEW: screenings include
require_once 'admin_dashboard/includes/screenings.php';

$movieId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$movie = getMovieById($db, $movieId);

if (!$movie) {
    echo "<h2 class='text-center text-red-600 mt-20'>Movie not found.</h2>";
    exit;
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
    SELECT s.id, s.start_time, s.end_time, r.name AS room_name
    FROM screenings s
    JOIN screening_rooms r ON s.screening_room_id = r.id
    WHERE s.movie_id = ?
    ORDER BY s.start_time
");
$stmt->execute([$movieId]);
$screenings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group screenings by date
$groupedScreenings = [];
foreach ($screenings as $s) {
    $date = date('Y-m-d', strtotime($s['start_time']));
    $groupedScreenings[$date][] = $s;
}

$actorNames = $actors ? implode(', ', array_map(fn($a) => $a['first_name'].' '.$a['last_name'], $actors)) : 'N/A';
$directorNames = $directors ? implode(', ', array_map(fn($d) => $d['first_name'].' '.$d['last_name'], $directors)) : 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-black text-white font-sans">

<?php include 'header.php'; ?>

<!-- HERO SECTION -->
<section class="relative w-full">
  <div class="relative h-[350px] md:h-[420px] overflow-hidden">
    <img src="<?= htmlspecialchars($movie['poster']) ?>" 
         alt="<?= htmlspecialchars($movie['title']) ?>" 
         class="object-cover w-full h-full opacity-70">
    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black"></div>
  </div>
</section>

<!-- MOVIE DETAILS -->
<section class="px-6 md:px-24 py-12 bg-black text-white">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-10 items-start">
    
    <!-- Poster -->
    <div class="flex justify-center">
      <img src="<?= htmlspecialchars($movie['poster']) ?>" 
           alt="<?= htmlspecialchars($movie['title']) ?>" 
           class="rounded-2xl shadow-lg w-[280px]">
    </div>

    <!-- Info -->
    <div class="md:col-span-2 flex flex-col gap-6">
      <h1 class="text-6xl font-[Limelight] text-[#F8A15A] tracking-wide"><?= htmlspecialchars($movie['title']) ?></h1>

      <div class="grid grid-cols-2 gap-y-2 text-sm">
        <p><span class="font-semibold">Cast:</span> <?= htmlspecialchars($actorNames) ?></p>
        <p><span class="font-semibold">Director:</span> <?= htmlspecialchars($directorNames) ?></p>
        <p><span class="font-semibold">Runtime:</span> <?= htmlspecialchars($movie['length']) ?> min</p>
        <p><span class="font-semibold">Release Year:</span> <?= htmlspecialchars($movie['release_year']) ?></p>
        <p><span class="font-semibold">Rating:</span> <?= htmlspecialchars($movie['rating']) ?></p>
      </div>

      <div>
        <h3 class="text-lg font-semibold mb-1">Synopsis</h3>
        <p class="text-gray-300 leading-relaxed"><?= nl2br(htmlspecialchars($movie['description'])) ?></p>
      </div>

      <a href="book.php?movie_id=<?= $movie['id'] ?>" 
         class="mt-6 inline-block bg-[#F8A15A] hover:bg-[#faaa68] text-black font-semibold py-3 px-8 rounded-full transition">
         BOOK TICKETS
      </a>
    </div>
  </div>
</section>

<!-- SHOWTIMES SECTION -->
<section class="bg-[#F8A15A] text-black py-10 px-6 md:px-24">
  <?php if (empty($groupedScreenings)): ?>
    <h2 class="text-center text-lg font-semibold text-gray-700">No screenings available.</h2>
  <?php else: ?>
    <?php $dates = array_keys($groupedScreenings); ?>
    
    <div id="showtime-container" data-index="0" data-total="<?= count($dates) ?>">
      <div class="flex justify-between items-center mb-6">
        <button id="prevDateBtn" class="text-2xl font-bold opacity-60 hover:opacity-100">&lt;</button>
        <h2 id="showtimeDate" class="text-xl font-semibold">
          <?= strtoupper(date('D j M', strtotime($dates[0]))) ?>
        </h2>
        <button id="nextDateBtn" class="text-2xl font-bold opacity-60 hover:opacity-100">&gt;</button>
      </div>

      <div id="showtimeButtons" class="flex flex-wrap justify-center gap-4 mb-6">
        <?php foreach ($groupedScreenings[$dates[0]] as $t): ?>
          <?php $time = date('H:i', strtotime($t['start_time'])); ?>
          <a href="book.php?screening_id=<?= $t['id'] ?>"
             class="bg-black text-[#F8A15A] hover:bg-[#1c1c1c] px-6 py-3 rounded-xl font-semibold transition">
             <?= $time ?> 
             <span class="text-xs opacity-70">(<?= htmlspecialchars($t['room_name']) ?>)</span>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="text-center">
        <a href="book.php?movie_id=<?= $movie['id'] ?>" 
           class="inline-block bg-black text-[#F8A15A] hover:bg-[#1c1c1c] px-10 py-3 rounded-full font-semibold">
           BOOK TICKETS
        </a>
      </div>
    </div>

    <script>
      const dates = <?= json_encode(array_map(function($d) use ($groupedScreenings) {
        return [
          'label' => strtoupper(date('D j M', strtotime($d))),
          'screenings' => array_map(fn($t) => [
            'id' => $t['id'],
            'time' => date('H:i', strtotime($t['start_time'])),
            'room' => $t['room_name']
          ], $groupedScreenings[$d])
        ];
      }, $dates)) ?>;

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
             class="bg-black text-[#F8A15A] hover:bg-[#1c1c1c] px-6 py-3 rounded-xl font-semibold transition">
             ${s.time} <span class="text-xs opacity-70">(${s.room})</span>
          </a>
        `).join('');
        showtimeContainer.dataset.index = index;
        prevBtn.disabled = index === 0;
        nextBtn.disabled = index === dates.length - 1;
        prevBtn.style.opacity = index === 0 ? "0.3" : "1";
        nextBtn.style.opacity = index === dates.length - 1 ? "0.3" : "1";
      }

      prevBtn.addEventListener('click', () => {
        let i = parseInt(showtimeContainer.dataset.index);
        if (i > 0) updateShowtimes(i - 1);
      });

      nextBtn.addEventListener('click', () => {
        let i = parseInt(showtimeContainer.dataset.index);
        if (i < dates.length - 1) updateShowtimes(i + 1);
      });

      // Initialize button states
      updateShowtimes(0);
    </script>
  <?php endif; ?>
</section>



<?php include 'footer.php'; ?>
</body>
</html>
