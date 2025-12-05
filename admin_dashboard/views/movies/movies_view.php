<?php
require_once __DIR__ . '/../../components/table.php';

function e($v)
{
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

function formatDate($date)
{
    return (!$date || $date === '0000-00-00') ? '—' : date('d M Y', strtotime($date));
}

/* ---------------------------------------------------------
   BADGES
--------------------------------------------------------- */

function badge($text, $color)
{
    return "<span class='inline-flex items-center px-2 py-0.5 rounded-full text-xs border whitespace-nowrap $color'>" . e($text) . "</span>";
}

function ratingBadge($rating)
{
    return $rating ? badge($rating, 'bg-indigo-100 text-indigo-800 border-indigo-200') : '<span class="text-xs text-gray-400 italic">N/A</span>';
}

function languageBadge($lang)
{
    return $lang ? badge($lang, 'bg-sky-100 text-sky-800 border-sky-200') : '<span class="text-xs text-gray-400 italic">N/A</span>';
}

function genreBadge($genre)
{
    return $genre ? badge($genre, 'bg-fuchsia-100 text-fuchsia-800 border-fuchsia-200') : '<span class="text-xs text-gray-400 italic">N/A</span>';
}

function moviePeopleBadges(PDO $db, int $movieId, string $type)
{
    $sql = $type === 'actor' ? "SELECT first_name,last_name FROM actors a JOIN actorAppearIn aa ON a.id=aa.actor_id WHERE aa.movie_id=?" : "SELECT first_name,last_name FROM directors d JOIN directorDirects dd ON d.id=dd.director_id WHERE dd.movie_id=?";

    $color = $type === 'actor' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200';

    $stmt = $db->prepare($sql);
    $stmt->execute([$movieId]);
    $people = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$people) return '<span class="text-xs text-gray-400 italic">None</span>';

    $out = "";
    foreach ($people as $p) {
        $name = $p['first_name'] . ' ' . $p['last_name'];
        $out .= badge($name, $color);
    }
    return "<div class='flex flex-wrap gap-1'>$out</div>";
}

/* ---------------------------------------------------------
   RENDER TABLE
--------------------------------------------------------- */

renderTable([
    'id' => 'moviesTable',
    'title' => 'All Movies',
    'searchable' => true,
    'headers' => ['ID', 'Poster', 'Title', 'Year', 'Rating', 'Genre', 'Language', 'Length', 'Description', 'Trailer', 'Actors', 'Directors'],
    'rows' => $movies,
    'renderRow' => function ($movie) use ($db) {

        /* ---------------------------------------------------------
           FIX POSTER PATHS — MAGIC AUTO-CORRECTION
        --------------------------------------------------------- */

        $poster = $movie['poster'];

        if ($poster) {

            // WINDOWS PATH (C:\something)
            if (str_contains($poster, ':\\') || str_contains($poster, ':/')) {
                $poster = '/cinema-website/images/' . basename($poster);
            }

            // BROKEN /images/... path
            if (str_starts_with($poster, '/images/')) {
                $poster = '/cinema-website' . $poster;
            }

            // Missing leading slash (images/...)
            if (!str_starts_with($poster, '/')) {
                $poster = '/cinema-website/images/' . basename($poster);
            }

            // Final normal form should ALWAYS be:
            // /cinema-website/images/file.jpg
        }

        $posterHtml = $poster ? "<img src='" . e($poster) . "' width='60' class='rounded shadow-sm'>" : "<span class='text-xs text-gray-400 italic'>No poster</span>";


        /* ---------------------------------------------------------
           TRAILER
        --------------------------------------------------------- */

        $trailer = $movie['trailer_url'] ? "<a href='" . e($movie['trailer_url']) . "' target='_blank' class='text-blue-600 hover:underline text-xs'>Open</a>" : "<span class='text-xs text-gray-400 italic'>None</span>";

        /* ---------------------------------------------------------
           DESCRIPTION SHORTENING
        --------------------------------------------------------- */

        $desc = e($movie['description']);
        if (strlen($desc) > 120) $desc = substr($desc, 0, 120) . "…";

        return [
            $movie['id'],
            $posterHtml,
            "<span class='font-semibold text-gray-900 '>" . e($movie['title']) . "</span>",
            e($movie['release_year']),
            ratingBadge($movie['rating']),
            genreBadge($movie['genre']),
            languageBadge($movie['language']),
            e($movie['length']),
            "<span class='text-sm text-gray-700 block w-[240px] whitespace-normal'>$desc</span>",
            $trailer,
            moviePeopleBadges($db, $movie['id'], 'actor'),
            moviePeopleBadges($db, $movie['id'], 'director'),
        ];
    },

    /* ---------------------------------------------------------
       ACTION BUTTONS
    --------------------------------------------------------- */
    'actions' => function ($movie) {
        ob_start(); ?>

        <div class="flex items-center gap-2">
            <button onclick="toggleEditRow(<?= $movie['id'] ?>)"
                    class="btn-square bg-blue-600">
                <i class="pi pi-pencil"></i> Edit
            </button>

            <form method="post" onsubmit="return confirm('Delete movie?')"
                  class="flex items-center justify-center p-0 m-0 leading-none">

                <input type="hidden" name="delete_movie" value="<?= $movie['id'] ?>">
                <button class="btn-square bg-red-500">
                    <i class="pi pi-trash"></i> Delete
                </button>
            </form>
        </div>

        <?php return ob_get_clean();
    },

    'renderEditRow' => function ($movie) use ($db, $allActors, $allDirectors) {

        $selectedActors = $db->query("SELECT actor_id FROM actorAppearIn WHERE movie_id={$movie['id']}")->fetchAll(PDO::FETCH_COLUMN);
        $selectedDirectors = $db->query("SELECT director_id FROM directorDirects WHERE movie_id={$movie['id']}")->fetchAll(PDO::FETCH_COLUMN);

        ob_start(); ?>

        <form method="post" enctype="multipart/form-data"
              class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
            <input type="hidden" name="edit_movie" value="1">

            <!-- BASIC FIELDS (same as Add) -->
            <?php foreach ([
                               'title'        => ['Title', 'Enter movie title', 'text'],
                               'release_year' => ['Release Year', 'e.g. 2024', 'number'],
                               'rating'       => ['Rating', 'e.g. PG-13, R, G', 'text'],
                               'genre'        => ['Genre', 'e.g. Action, Drama', 'text'],
                               'language'     => ['Language', 'e.g. English', 'text'],
                               'length'       => ['Length (min)', 'Duration in minutes', 'number'],
                           ] as $field => [$label, $placeholder, $type]): ?>

                <div class="flex flex-col gap-2">
                    <label class="text-sm text-gray-700 font-semibold"><?= $label ?></label>
                    <input type="<?= $type ?>"
                           name="<?= $field ?>"
                           value="<?= e($movie[$field]) ?>"
                           class="input-edit px-4 py-2 rounded-md"
                           placeholder="<?= $placeholder ?>"
                           required>
                </div>

            <?php endforeach; ?>

            <!-- DESCRIPTION -->
            <div class="md:col-span-2 flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Description</label>
                <textarea name="description"
                          rows="5"
                          class="input-edit-textarea px-4 py-3 rounded-md leading-relaxed"
                          placeholder="Write a short movie description..."><?= e($movie['description']) ?></textarea>
            </div>

            <!-- TRAILER -->
            <div class="md:col-span-2 flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Trailer URL</label>
                <input type="text"
                       name="trailer_url"
                       value="<?= e($movie['trailer_url']) ?>"
                       class="input-edit px-4 py-2 rounded-md"
                       placeholder="https://youtube.com/...">
            </div>

            <!-- POSTER -->
            <div class="md:col-span-2 flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Poster Image</label>
                <input type="file"
                       name="poster"
                       accept="image/*"
                       class="text-sm text-gray-700">
            </div>

            <!-- ACTORS COLUMN -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Actors</label>

                <input type="text"
                       id="actorSearchEdit"
                       placeholder="Search actors..."
                       class="input-edit px-3 py-2 rounded-md text-sm">

                <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3 bg-white">
                    <div id="actorListEdit" class="flex flex-col gap-2">
                        <?php foreach ($allActors as $a): ?>
                            <label class="flex items-center gap-2 text-sm text-gray-800 actor-item-edit">
                                <input type="checkbox"
                                       name="actors[]"
                                       value="<?= $a['id'] ?>"
                                    <?= in_array($a['id'], $selectedActors) ? 'checked' : '' ?>
                                       class="accent-[var(--primary)]">
                                <?= e($a['first_name'] . ' ' . $a['last_name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <p id="actorNoResultsEdit"
                       class="text-sm text-gray-500 italic hidden">
                        No matching actors found
                    </p>
                </div>
            </div>

            <!-- DIRECTORS COLUMN -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Directors</label>

                <input type="text"
                       id="directorSearchEdit"
                       placeholder="Search directors..."
                       class="input-edit px-3 py-2 rounded-md text-sm">

                <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3 bg-white">
                    <div id="directorListEdit" class="flex flex-col gap-2">
                        <?php foreach ($allDirectors as $d): ?>
                            <label class="flex items-center gap-2 text-sm text-gray-800 director-item-edit">
                                <input type="checkbox"
                                       name="directors[]"
                                       value="<?= $d['id'] ?>"
                                    <?= in_array($d['id'], $selectedDirectors) ? 'checked' : '' ?>
                                       class="accent-[var(--primary)]">
                                <?= e($d['first_name'] . ' ' . $d['last_name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <p id="directorNoResultsEdit"
                       class="text-sm text-gray-500 italic hidden">
                        No matching directors found
                    </p>
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="md:col-span-2 flex gap-4 mt-4">
                <button class="btn-square bg-green-600 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-check"></i> Save Changes
                </button>

                <button type="button"
                        onclick="toggleEditRow(<?= $movie['id'] ?>)"
                        class="btn-square bg-gray-300 text-gray-700 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-times"></i> Cancel
                </button>
            </div>

        </form>

        <script>
            function setupSearch(inputId, itemSelector, noResultsId) {
                const input = document.getElementById(inputId);
                const items = document.querySelectorAll(itemSelector);
                const noResults = document.getElementById(noResultsId);

                input.addEventListener('input', () => {
                    const term = input.value.toLowerCase();
                    let visible = 0;

                    items.forEach(item => {
                        const show = item.textContent.toLowerCase().includes(term);
                        item.style.display = show ? "flex" : "none";
                        if (show) visible++;
                    });

                    noResults.classList.toggle("hidden", visible !== 0);
                });
            }

            setupSearch('actorSearchEdit', '.actor-item-edit', 'actorNoResultsEdit');
            setupSearch('directorSearchEdit', '.director-item-edit', 'directorNoResultsEdit');
        </script>

        <?php return ob_get_clean();
    },

    'addLabel' => 'Add Movie',
    'addForm' => (function () use ($allActors, $allDirectors) {

        ob_start(); ?>
        <form method="post" enctype="multipart/form-data"
              class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <input type="hidden" name="add_movie" value="1">

            <!-- BASIC FIELDS (2-column) -->
            <?php foreach ([
                               'title'        => ['Title', 'Enter movie title', 'text'],
                               'release_year' => ['Release Year', 'e.g. 2024', 'number'],
                               'rating'       => ['Rating', 'e.g. PG-13, R, G', 'text'],     // FIXED
                               'genre'        => ['Genre', 'e.g. Action, Drama', 'text'],
                               'language'     => ['Language', 'e.g. English', 'text'],
                               'length'       => ['Length (min)', 'Duration in minutes', 'number'],
                           ] as $field => [$label, $placeholder, $type]): ?>

                <div class="flex flex-col gap-2">
                    <label class="text-sm text-gray-700 font-semibold"><?= $label ?></label>
                    <input type="<?= $type ?>"
                           name="<?= $field ?>"
                           class="input-edit px-4 py-2 rounded-md"
                           placeholder="<?= $placeholder ?>"
                           required>
                </div>

            <?php endforeach; ?>

            <!-- DESCRIPTION -->
            <div class="md:col-span-2 flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Description</label>
                <textarea name="description"
                          rows="5"
                          class="input-edit-textarea px-4 py-3 rounded-md leading-relaxed"
                          placeholder="Write a short movie description..."
                          required></textarea>
            </div>

            <!-- TRAILER -->
            <div class="md:col-span-2 flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Trailer URL</label>
                <input type="text"
                       name="trailer_url"
                       class="input-edit px-4 py-2 rounded-md"
                       placeholder="https://youtube.com/...">
            </div>

            <!-- POSTER -->
            <div class="md:col-span-2 flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Poster Image</label>
                <input type="file"
                       name="poster"
                       accept="image/*"
                       class="text-sm text-gray-700">
            </div>

            <!-- ACTORS COLUMN -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Actors</label>

                <!-- Search -->
                <input type="text"
                       id="actorSearch"
                       placeholder="Search actors..."
                       class="input-edit px-3 py-2 rounded-md text-sm">

                <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3 bg-white">
                    <div id="actorList" class="flex flex-col gap-2">
                        <?php foreach ($allActors as $a): ?>
                            <label class="flex items-center gap-2 text-sm text-gray-800 actor-item">
                                <input type="checkbox"
                                       name="actors[]"
                                       value="<?= $a['id'] ?>"
                                       class="accent-[var(--primary)]">
                                <?= e($a['first_name'] . ' ' . $a['last_name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- No results message -->
                    <p id="actorNoResults"
                       class="text-sm text-gray-500 italic hidden">
                        No matching actors found
                    </p>
                </div>
            </div>

            <!-- DIRECTORS COLUMN -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Directors</label>

                <!-- Search -->
                <input type="text"
                       id="directorSearch"
                       placeholder="Search directors..."
                       class="input-edit px-3 py-2 rounded-md text-sm">

                <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3 bg-white">
                    <div id="directorList" class="flex flex-col gap-2">
                        <?php foreach ($allDirectors as $d): ?>
                            <label class="flex items-center gap-2 text-sm text-gray-800 director-item">
                                <input type="checkbox"
                                       name="directors[]"
                                       value="<?= $d['id'] ?>"
                                       class="accent-[var(--primary)]">
                                <?= e($d['first_name'] . ' ' . $d['last_name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- No results message -->
                    <p id="directorNoResults"
                       class="text-sm text-gray-500 italic hidden">
                        No matching directors found
                    </p>
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="md:col-span-2 flex gap-4 mt-4">
                <button class="btn-square bg-green-600 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-plus"></i> Add Movie
                </button>

                <button type="button"
                        onclick="toggleAddForm_moviesTable()"
                        class="btn-square bg-gray-300 text-gray-700 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-times"></i> Cancel
                </button>
            </div>

        </form>

        <!-- Search script for actors/directors -->
        <script>
            function setupSearch(inputId, itemSelector, noResultsId) {
                const input = document.getElementById(inputId);
                const items = document.querySelectorAll(itemSelector);
                const noResults = document.getElementById(noResultsId);

                input.addEventListener('input', () => {
                    const term = input.value.toLowerCase();
                    let visibleCount = 0;

                    items.forEach(item => {
                        const match = item.textContent.toLowerCase().includes(term);
                        item.style.display = match ? 'flex' : 'none';
                        if (match) visibleCount++;
                    });

                    // Show or hide "no results"
                    noResults.classList.toggle('hidden', visibleCount !== 0);
                });
            }

            setupSearch('actorSearch', '.actor-item', 'actorNoResults');
            setupSearch('directorSearch', '.director-item', 'directorNoResults');
        </script>
        <?php return ob_get_clean();

    })(),
]);
?>
