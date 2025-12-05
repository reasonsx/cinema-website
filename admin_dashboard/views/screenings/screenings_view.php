<?php
require_once __DIR__ . '/../../components/table.php';
require_once __DIR__ . '/../../../shared/helpers.php';

// Fetch screenings
$screenings = getScreenings($db);

// Add Form (inside table)
$addForm = (function () use ($movies, $screeningRooms) {
    ob_start(); ?>
    <form method="post" id="addScreeningForm" class="flex flex-col gap-6">

        <!-- Movie -->
        <div class="flex flex-col gap-2">
            <label class="text-sm text-gray-700 font-semibold">Movie</label>
            <select name="movie_id" id="movieSelect"
                    class="px-4 py-2 rounded-md border border-gray-300 bg-white text-black"
                    required>
                <option value="">Select Movie</option>
                <?php foreach ($movies as $m): ?>
                    <option value="<?= $m['id'] ?>" data-length="<?= $m['length'] ?>">
                        <?= htmlspecialchars($m['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Room -->
        <div class="flex flex-col gap-2">
            <label class="text-sm text-gray-700 font-semibold">Screening Room</label>
            <select name="screening_room_id" id="roomSelect"
                    class="px-4 py-2 rounded-md border border-gray-300 bg-white text-black"
                    required>
                <option value="">Select Room</option>
                <?php foreach ($screeningRooms as $room): ?>
                    <option value="<?= $room['id'] ?>"><?= htmlspecialchars($room['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Start Time -->
        <div class="flex flex-col gap-2">
            <label class="text-sm text-gray-700 font-semibold">Start Time</label>
            <input type="datetime-local"
                   name="start_time"
                   id="startTime"
                   class="px-4 py-2 rounded-md border border-gray-300 bg-white text-black"
                   required>
        </div>

        <!-- End Time -->
        <div class="flex flex-col gap-2">
            <label class="text-sm text-gray-700 font-semibold">End Time</label>
            <div class="flex items-center gap-3">
                <input type="datetime-local"
                       name="end_time"
                       id="endTime"
                       class="px-4 py-2 rounded-md border border-gray-300 bg-white text-black endTimeEdit"
                       required>

                <button type="button" id="autoSetEndTime"
                        class="bg-gray-400 text-white px-4 py-2 rounded-md text-sm shadow">
                    Auto
                </button>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4">
            <button type="submit"
                    name="add_screening"
                    class="btn-square bg-green-600 flex gap-2 items-center px-4 py-2">
                <i class="pi pi-plus"></i>
                Add Screening
            </button>

            <button type="button"
                    onclick="toggleAddForm_screeningsTable()"
                    class="btn-square bg-gray-300 text-gray-700 flex gap-2 items-center px-4 py-2">
                <i class="pi pi-times"></i>
                Cancel
            </button>
        </div>
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
        <form method="post" class="flex flex-col gap-6">

            <input type="hidden" name="screening_id" value="<?= $s['id'] ?>">

            <!-- Movie -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Movie</label>
                <select name="movie_id"
                        class="px-4 py-2 rounded-md border border-gray-300 bg-white text-black movieSelectEdit"
                        required>
                    <?php foreach ($movies as $m): ?>
                        <option value="<?= $m['id'] ?>" data-length="<?= $m['length'] ?>"
                            <?= $s['movie_id'] == $m['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Room -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Screening Room</label>
                <select name="screening_room_id"
                        class="px-4 py-2 rounded-md border border-gray-300 bg-white text-black"
                        required>
                    <?php foreach ($screeningRooms as $room): ?>
                        <option value="<?= $room['id'] ?>"
                            <?= $s['screening_room_id'] == $room['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($room['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Start Time -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Start Time</label>
                <input type="datetime-local"
                       name="start_time"
                       value="<?= date('Y-m-d\TH:i', strtotime($s['start_time'])) ?>"
                       class="px-4 py-2 rounded-md border border-gray-300 bg-white text-black startTimeEdit"
                       required>
            </div>

            <!-- End Time -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">End Time</label>
                <div class="flex items-center gap-3">
                    <input type="datetime-local"
                           name="end_time"
                           value="<?= date('Y-m-d\TH:i', strtotime($s['end_time'])) ?>"
                           class="px-4 py-2 rounded-md border border-gray-300 bg-white text-black endTimeEdit"
                           required>

                    <button type="button"
                            class="autoSetEndTimeEdit bg-gray-400 text-white px-4 py-2 rounded-md text-sm shadow">
                        Auto
                    </button>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit"
                        name="edit_screening"
                        class="btn-square bg-green-600 flex gap-2 items-center px-4 py-2">
                    <i class="pi pi-check"></i>
                    Save Changes
                </button>

                <button type="button"
                        onclick="toggleEditRow(<?= $s['id'] ?>)"
                        class="btn-square bg-gray-300 text-gray-700 flex gap-2 items-center px-4 py-2">
                    <i class="pi pi-times"></i>
                    Cancel
                </button>
            </div>
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
