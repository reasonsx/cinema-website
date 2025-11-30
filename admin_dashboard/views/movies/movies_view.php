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
            "<span class='font-semibold text-gray-900'>" . e($movie['title']) . "</span>",
            e($movie['release_year']),
            ratingBadge($movie['rating']),
            genreBadge($movie['genre']),
            languageBadge($movie['language']),
            e($movie['length']),
            "<span class='text-sm text-gray-700'>$desc</span>",
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
                    class="flex items-center justify-center gap-2
                           px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold
                           hover:bg-blue-700 transition">
                <i class="pi pi-pencil"></i> Edit
            </button>

            <form method="post" onsubmit="return confirm('Delete movie?')"
                  class="flex items-center justify-center p-0 m-0 leading-none">
                <!--TOKEN-->
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                <input type="hidden" name="delete_movie" value="<?= $movie['id'] ?>">
                <button class="flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-red-500 text-white text-sm font-semibold hover:bg-red-600 transition">
                    <i class="pi pi-trash"></i> Delete
                </button>
            </form>
        </div>

        <?php return ob_get_clean();
    },

    /*  EDIT MOVIE FORM  */
    'renderEditRow' => function ($movie) use ($db, $allActors, $allDirectors) {

        $selectedActors = $db->query("SELECT actor_id FROM actorAppearIn WHERE movie_id={$movie['id']}")->fetchAll(PDO::FETCH_COLUMN);
        $selectedDirectors = $db->query("SELECT director_id FROM directorDirects WHERE movie_id={$movie['id']}")->fetchAll(PDO::FETCH_COLUMN);

        ob_start(); ?>
        <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!--TOKEN-->
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

            <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
            <input type="hidden" name="edit_movie" value="1">

            <?php foreach ([
                               'title' => 'Title',
                               'release_year' => 'Release Year',
                               'rating' => 'Rating',
                               'genre' => 'Genre',
                               'language' => 'Language',
                               'length' => 'Length (min)'
                           ] as $field => $label): ?>
                <div class="flex flex-col">
                    <label class="text-sm text-gray-600"><?= $label ?></label>
                    <input type="<?= $field === 'release_year' || $field === 'length' ? 'number' : 'text' ?>"
                           name="<?= $field ?>" value="<?= e($movie[$field]) ?>" class="input-edit">
                </div>
            <?php endforeach; ?>

            <div class="md:col-span-2 flex flex-col">
                <label class="text-sm text-gray-600">Description</label>
                <textarea name="description" rows="3"
                          class="input-edit-textarea"><?= e($movie['description']) ?></textarea>
            </div>

            <div class="md:col-span-2 flex flex-col">
                <label class="text-sm text-gray-600">Trailer URL</label>
                <input type="text" name="trailer_url" value="<?= e($movie['trailer_url']) ?>" class="input-edit">
            </div>

            <div class="md:col-span-2 flex flex-col">
                <label class="text-sm text-gray-600">Poster Image</label>
                <input type="file" name="poster" accept="image/*" class="w-full text-gray-700 text-sm">
            </div>

            <div>
                <label class="text-sm text-gray-600 font-medium">Actors</label>
                <div class="flex flex-col gap-1">
                    <?php foreach ($allActors as $a): ?>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="actors[]"
                                   value="<?= $a['id'] ?>" <?= in_array($a['id'], $selectedActors) ? 'checked' : '' ?>>
                            <?= e($a['first_name'] . ' ' . $a['last_name']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-600 font-medium">Directors</label>
                <div class="flex flex-col gap-1">
                    <?php foreach ($allDirectors as $d): ?>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="directors[]"
                                   value="<?= $d['id'] ?>" <?= in_array($d['id'], $selectedDirectors) ? 'checked' : '' ?>>
                            <?= e($d['first_name'] . ' ' . $d['last_name']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="md:col-span-2 flex gap-4 mt-4">
                <button class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                    <i class="pi pi-check"></i> Save
                </button>
                <button type="button" onclick="toggleEditRow(<?= $movie['id'] ?>)"
                        class="px-4 py-2 bg-gray-300 text-black rounded hover:bg-gray-400">
                    <i class="pi pi-times"></i> Cancel
                </button>
            </div>

        </form>
        <?php return ob_get_clean();
    },

    /*  ADD MOVIE FORM   */
    'addLabel' => 'Add Movie',
    'addForm' => (function () use ($allActors, $allDirectors) {

        ob_start(); ?>
        <form method="post" enctype="multipart/form-data"
              class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!--TOKEN-->
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

            <input type="hidden" name="add_movie" value="1">

            <?php foreach ([
                               'title' => 'Title',
                               'release_year' => 'Release Year',
                               'rating' => 'Rating',
                               'genre' => 'Genre',
                               'language' => 'Language',
                               'length' => 'Length (min)'
                           ] as $field => $label): ?>
                <div class="flex flex-col">
                    <label class="text-sm text-gray-600"><?= $label ?></label>
                    <input type="<?= in_array($field, ['release_year', 'length']) ? 'number' : 'text' ?>"
                           name="<?= $field ?>"
                           class="input-edit"
                           required>
                </div>
            <?php endforeach; ?>

            <div class="md:col-span-2 flex flex-col">
                <label class="text-sm text-gray-600">Description</label>
                <textarea name="description" rows="3" class="input-edit-textarea" required></textarea>
            </div>

            <div class="md:col-span-2 flex flex-col">
                <label class="text-sm text-gray-600">Trailer URL</label>
                <input type="text" name="trailer_url" class="input-edit">
            </div>

            <div class="md:col-span-2 flex flex-col">
                <label class="text-sm text-gray-600">Poster Image</label>
                <input type="file" name="poster" accept="image/*" class="w-full text-sm text-gray-700">
            </div>

            <div>
                <label class="text-sm text-gray-600 font-medium">Actors</label>
                <div class="flex flex-col gap-1">
                    <?php foreach ($allActors as $a): ?>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="actors[]" value="<?= $a['id'] ?>">
                            <?= e($a['first_name'] . ' ' . $a['last_name']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-600 font-medium">Directors</label>
                <div class="flex flex-col gap-1">
                    <?php foreach ($allDirectors as $d): ?>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="directors[]" value="<?= $d['id'] ?>">
                            <?= e($d['first_name'] . ' ' . $d['last_name']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="md:col-span-2 flex justify-end gap-4 mt-4">
                <button class="px-4 py-2 bg-[var(--primary)] text-white rounded hover:bg-[var(--secondary)]">
                    <i class="pi pi-check"></i> Add Movie
                </button>

                <button type="button" onclick="toggleAddForm_moviesTable()"
                        class="px-4 py-2 bg-gray-300 text-black rounded hover:bg-gray-400">
                    <i class="pi pi-times"></i> Cancel
                </button>
            </div>

        </form>
        <?php return ob_get_clean();

    })()
]);
?>
