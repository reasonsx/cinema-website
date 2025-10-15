<?php
$screenings = getScreenings($db);
?>

<section class="mb-10">
    <h2 class="text-5xl font-[Limelight] text-[var(--primary)] mb-6">All Screenings</h2>

    <!-- Add Screening Form -->
    <details class="mb-8">
        <summary class="cursor-pointer inline-block bg-[var(--primary)] text-[var(--white)] px-6 py-3 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
            Add New Screening
        </summary>

        <form method="post" id="addScreeningForm" class="flex flex-col gap-4 mt-4">
            <select name="movie_id" id="movieSelect" required
                class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
                <option value="">Select Movie</option>
                <?php foreach ($movies as $movie): ?>
                    <option value="<?= $movie['id'] ?>" data-length="<?= $movie['length'] ?>"><?= htmlspecialchars($movie['title']) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="screening_room_id" id="roomSelect" required
                class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
                <option value="">Select Room</option>
                <?php foreach ($screeningRooms as $room): ?>
                    <option value="<?= $room['id'] ?>"><?= htmlspecialchars($room['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label class="text-[var(--primary)] font-[Limelight]">Start Time:</label>
            <input type="datetime-local" name="start_time" id="startTime" required
                class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">

            <label class="text-[var(--primary)] font-[Limelight]">End Time:</label>
            <div class="flex gap-2 items-center">
                <input type="datetime-local" name="end_time" id="endTime" required
                    class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
                <button type="button" id="autoSetEndTime"
                    class="bg-gray-400 text-white px-3 py-1 rounded shadow hover:bg-gray-500 transition-colors duration-300 font-[Limelight] text-sm">
                    Set Automatically
                </button>
            </div>

            <button type="submit" id="addScreeningBtn" name="add_screening" 
                class="bg-[var(--primary)] text-[var(--white)] px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
                Add Screening
            </button>

            <p id="screeningError" class="text-red-500 font-[Limelight]"></p>
        </form>
    </details>

    <!-- Screenings Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full border-t-4 border-[var(--primary)] text-black">
            <thead class="font-[Limelight] text-[var(--primary)] text-lg">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Movie</th>
                    <th class="px-4 py-2 text-left">Room</th>
                    <th class="px-4 py-2 text-left">Start Time</th>
                    <th class="px-4 py-2 text-left">End Time</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($screenings as $screening): ?>
                    <tr class="hover:text-black transition-colors duration-300">
                        <td class="px-4 py-2"><?= $screening['id'] ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($screening['movie_title']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($screening['room_name']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($screening['start_time']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($screening['end_time']) ?></td>
                        <td class="px-4 py-2">
                            <form method="post" style="display:inline;"
                                  onsubmit="return confirm('Are you sure you want to delete this screening?');">
                                <input type="hidden" name="screening_id" value="<?= $screening['id'] ?>">
                                <button type="submit" name="delete_screening"
                                        class="bg-[var(--primary)] text-[var(--white)] px-3 py-1 rounded shadow hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-sm">
                                    Delete
                                </button>
                            </form>

                            <button type="button" onclick="toggleEditScreeningForm(<?= $screening['id'] ?>)"
                                    class="bg-[var(--primary)] text-[var(--white)] px-3 py-1 rounded shadow hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-sm">
                                Edit
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Row -->
                    <tr id="edit-screening-<?= $screening['id'] ?>" class="hidden bg-gray-50">
                        <td colspan="6" class="p-6 border-t-4 border-[var(--primary)]">
                            <form method="post" class="flex flex-col gap-4 editForm">
                                <input type="hidden" name="screening_id" value="<?= $screening['id'] ?>">

                                <select name="movie_id" class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 movieSelectEdit" required>
                                    <?php foreach ($movies as $movie): ?>
                                        <option value="<?= $movie['id'] ?>" data-length="<?= $movie['length'] ?>" <?= $screening['movie_title']==$movie['title'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($movie['title']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <select name="screening_room_id" class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 roomSelectEdit" required>
                                    <?php foreach ($screeningRooms as $room): ?>
                                        <option value="<?= $room['id'] ?>" <?= $screening['room_name']==$room['name'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($room['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <input type="datetime-local" name="start_time" class="startTimeEdit border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1" value="<?= date('Y-m-d\TH:i', strtotime($screening['start_time'])) ?>" required>

                                <div class="flex gap-2 items-center">
                                    <input type="datetime-local" name="end_time" class="endTimeEdit border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1" value="<?= date('Y-m-d\TH:i', strtotime($screening['end_time'])) ?>" required>
                                    <button type="button" class="autoSetEndTimeEdit bg-gray-400 text-white px-3 py-1 rounded shadow hover:bg-gray-500 transition-colors duration-300 font-[Limelight] text-sm">
                                        Set Automatically
                                    </button>
                                </div>

                                <button type="submit" name="edit_screening" class="bg-[var(--primary)] text-[var(--white)] px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg saveEditBtn">
                                    Save Changes
                                </button>
                                <p class="text-red-500 font-[Limelight] errorMsgEdit"></p>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
// Helper: format date to local datetime-local string
function formatLocalDate(d) {
    const offset = d.getTimezoneOffset();
    const local = new Date(d.getTime() - offset*60*1000);
    return local.toISOString().slice(0,16);
}

// Toggle edit row
function toggleEditScreeningForm(id) {
    const form = document.getElementById(`edit-screening-${id}`);
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Add form validation & auto-end for new screening
const startTime = document.getElementById('startTime');
const endTime = document.getElementById('endTime');
const movieSelect = document.getElementById('movieSelect');
const roomSelect = document.getElementById('roomSelect');
const addBtn = document.getElementById('addScreeningBtn');
const errorMsg = document.getElementById('screeningError');

function validateScreening(startInput, endInput, movieSelectEl, roomSelectEl, btn, msgEl) {
    const start = new Date(startInput.value);
    const end = new Date(endInput.value);
    const movieLength = parseInt(movieSelectEl.selectedOptions[0]?.dataset.length || 0);

    if (!start || !end || !movieLength) {
        btn.disabled = true;
        msgEl.textContent = '';
        return;
    }

    if ((end - start)/60000 < movieLength) {
        btn.disabled = true;
        msgEl.textContent = `End time must be at least the movie length (${movieLength} min).`;
        return;
    }

    const screeningsData = <?= json_encode($screenings) ?>;
    const roomId = roomSelectEl.value;
    const overlap = screeningsData.some(s => s.screening_room_id == roomId &&
        ((new Date(s.start_time) <= start && new Date(s.end_time) > start) ||
         (new Date(s.start_time) < end && new Date(s.end_time) >= end))
    );

    if (overlap) {
        btn.disabled = true;
        msgEl.textContent = 'There is already a screening in this room at that time.';
        return;
    }

    btn.disabled = false;
    msgEl.textContent = '';
}

// Add screening auto-end
document.getElementById('autoSetEndTime').addEventListener('click', () => {
    const start = new Date(startTime.value);
    const movieLength = parseInt(movieSelect.selectedOptions[0]?.dataset.length || 0);
    if (!start || !movieLength) return;
    const end = new Date(start.getTime() + movieLength*60000);
    endTime.value = formatLocalDate(end);
    validateScreening(startTime, endTime, movieSelect, roomSelect, addBtn, errorMsg);
});

// Validate on change
[startTime, endTime, movieSelect, roomSelect].forEach(el => el.addEventListener('change', () =>
    validateScreening(startTime, endTime, movieSelect, roomSelect, addBtn, errorMsg)
));
validateScreening(startTime, endTime, movieSelect, roomSelect, addBtn, errorMsg);

// Edit screenings auto-end & validation
document.querySelectorAll('.editForm').forEach(form => {
    const startInput = form.querySelector('.startTimeEdit');
    const endInput = form.querySelector('.endTimeEdit');
    const movieSelectEl = form.querySelector('.movieSelectEdit');
    const roomSelectEl = form.querySelector('.roomSelectEdit');
    const btn = form.querySelector('.saveEditBtn');
    const msgEl = form.querySelector('.errorMsgEdit');
    const autoBtn = form.querySelector('.autoSetEndTimeEdit');

    function validateEdit() {
        validateScreening(startInput, endInput, movieSelectEl, roomSelectEl, btn, msgEl);
    }

    [startInput, endInput, movieSelectEl, roomSelectEl].forEach(el => el.addEventListener('change', validateEdit));
    validateEdit();

    autoBtn.addEventListener('click', () => {
        const start = new Date(startInput.value);
        const movieLength = parseInt(movieSelectEl.selectedOptions[0]?.dataset.length || 0);
        if (!start || !movieLength) return;
        const end = new Date(start.getTime() + movieLength*60000);
        endInput.value = formatLocalDate(end);
        validateEdit();
    });
});
</script>
