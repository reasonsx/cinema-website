<?php
require_once __DIR__ . '/../../components/table.php';
require_once __DIR__ . '/../../../shared/helpers.php';

// Fetch screenings
$screenings = getScreenings($db);

// Add Form (inside table)
$addForm = (function () use ($movies, $screeningRooms) {
    ob_start(); ?>
    <form method="post" id="addScreeningForm" class="flex flex-col gap-4">

        <!-- Movie -->
        <select name="movie_id" id="movieSelect" required
                class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1">
            <option value="">Select Movie</option>
            <?php foreach ($movies as $m): ?>
                <option value="<?= $m['id'] ?>" data-length="<?= $m['length'] ?>">
                    <?= htmlspecialchars($m['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Room -->
        <select name="screening_room_id" id="roomSelect" required
                class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1">
            <option value="">Select Room</option>
            <?php foreach ($screeningRooms as $room): ?>
                <option value="<?= $room['id'] ?>">
                    <?= htmlspecialchars($room['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Start -->
        <input type="datetime-local" name="start_time" id="startTime" required
               class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1">

        <!-- End -->
        <div class="flex gap-2 items-center">
            <input type="datetime-local" name="end_time" id="endTime" required
                   class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1">

            <button type="button" id="autoSetEndTime"
                    class="bg-gray-400 text-white px-3 py-1 rounded shadow text-sm">
                Auto
            </button>
        </div>

        <button type="submit" name="add_screening"
                class="btn-square bg-green-600">
            <i class="pi pi-plus"></i>
            Add Screening
        </button>

        <p id="screeningError" class="text-red-500 font-semibold"></p>
    </form>
    <?php
    return ob_get_clean();
})();

// RENDER TABLE
renderTable([
    'id'        => 'screeningsTable',
    'title'     => 'All Screenings',
    'searchable'=> true,
    'addLabel'  => 'Add Screening',
    'addForm'   => $addForm,

    // Table headers
    'headers' => ['ID', 'Movie', 'Room', 'Start', 'End'],

    // Table rows
    'rows' => $screenings,

    // How each row is displayed
    'renderRow' => function ($s) {
        return [
            $s['id'],
            e($s['movie_title']),
            e($s['room_name']),
            e($s['start_time']),
            e($s['end_time']),
        ];
    },

    // Edit / Delete buttons
    'actions' => function ($s) {
        ob_start(); ?>

        <div class="flex items-center gap-2">

            <button onclick="toggleEditRow(<?= $s['id'] ?>)"
                    class="btn-square bg-blue-600">
                <i class="pi pi-pencil"></i> Edit
            </button>

            <form method="post"
                  onsubmit="return confirm('Delete this screening?')"
                  class="flex items-center justify-center p-0 m-0 leading-none">
                <input type="hidden" name="screening_id" value="<?= $s['id'] ?>">

                <button type="submit" name="delete_screening"
                        class="btn-square bg-red-500">
                    <i class="pi pi-trash"></i> Delete
                </button>
            </form>

        </div>
        <?php
        return ob_get_clean();
    },

    // Inline edit form
    'renderEditRow' => function ($s) use ($movies, $screeningRooms) {
        ob_start(); ?>
        <form method="post" class="flex flex-col gap-4">

            <input type="hidden" name="screening_id" value="<?= $s['id'] ?>">

            <!-- Movie -->
            <select name="movie_id" class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 movieSelectEdit" required>
                <?php foreach ($movies as $m): ?>
                    <option value="<?= $m['id'] ?>" data-length="<?= $m['length'] ?>"
                        <?= $s['movie_id']==$m['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Room -->
            <select name="screening_room_id"
                    class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 roomSelectEdit" required>
                <?php foreach ($screeningRooms as $room): ?>
                    <option value="<?= $room['id'] ?>"
                        <?= $s['screening_room_id']==$room['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($room['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Start -->
            <input type="datetime-local" name="start_time"
                   class="startTimeEdit border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1"
                   value="<?= date('Y-m-d\TH:i', strtotime($s['start_time'])) ?>" required>

            <!-- End -->
            <div class="flex gap-2 items-center">
                <input type="datetime-local" name="end_time"
                       class="endTimeEdit border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1"
                       value="<?= date('Y-m-d\TH:i', strtotime($s['end_time'])) ?>" required>

                <button type="button" class="autoSetEndTimeEdit bg-gray-400 text-white px-3 py-1 rounded text-sm">
                    Auto
                </button>
            </div>

            <div class="flex gap-3">
                <button type="submit" name="edit_screening"
                        class="btn-square bg-green-600">
                    <i class="pi pi-check"></i> Save Changes</button>
                <button type="button" onclick="toggleEditRow(<?= $s['id'] ?>)"
                        class="btn-square bg-gray-300 text-gray-700"><i class="pi pi-times"></i>Cancel</button>
            </div>

            <p class="text-red-500 font-semibold errorMsgEdit"></p>

        </form>
        <?php
        return ob_get_clean();
    },
]);
?>

<!-- JS identical to your original logic -->
<script>
    // auto end time helper
    function formatLocalDate(d) {
        const offset = d.getTimezoneOffset();
        const local = new Date(d.getTime() - offset * 60000);
        return local.toISOString().slice(0, 16);
    }

    // Add form auto-end
    document.getElementById('autoSetEndTime').addEventListener('click', () => {
        const start = new Date(document.getElementById('startTime').value);
        const movie = document.getElementById('movieSelect').selectedOptions[0];
        if (!movie) return;
        const len = parseInt(movie.dataset.length || 0);
        const end = new Date(start.getTime() + len * 60000);
        document.getElementById('endTime').value = formatLocalDate(end);
    });

    // Edit forms
    document.querySelectorAll('.autoSetEndTimeEdit').forEach(btn => {
        btn.addEventListener('click', () => {
            const form = btn.closest('form');
            const start = new Date(form.querySelector('.startTimeEdit').value);
            const movie = form.querySelector('.movieSelectEdit').selectedOptions[0];
            const len = parseInt(movie.dataset.length || 0);
            const end = new Date(start.getTime() + len * 60000);
            form.querySelector('.endTimeEdit').value = formatLocalDate(end);
        });
    });
</script>
