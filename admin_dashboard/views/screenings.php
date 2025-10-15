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

        <form method="post" class="flex flex-col gap-4 mt-4">
            <select name="movie_id" required
                class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
                <option value="">Select Movie</option>
                <?php foreach ($movies as $movie): ?>
                    <option value="<?= $movie['id'] ?>"><?= htmlspecialchars($movie['title']) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="screening_room_id" required
                class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
                <option value="">Select Room</option>
                <?php foreach ($screeningRooms as $room): ?>
                    <option value="<?= $room['id'] ?>"><?= htmlspecialchars($room['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label class="text-[var(--primary)] font-[Limelight]">Start Time:</label>
            <input type="datetime-local" name="start_time" required
                class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">

            <label class="text-[var(--primary)] font-[Limelight]">End Time:</label>
            <input type="datetime-local" name="end_time" required
                class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">

            <button type="submit" name="add_screening"
                class="bg-[var(--primary)] text-[var(--white)] px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
                Add Screening
            </button>
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

                    <!-- Edit Screening Row -->
                    <tr id="edit-screening-<?= $screening['id'] ?>" class="hidden bg-gray-50">
                        <td colspan="6" class="p-6 border-t-4 border-[var(--primary)]">
                            <h3 class="text-3xl font-[Limelight] text-[var(--primary)] mb-4">Edit Screening</h3>
                            <form method="post" class="flex flex-col gap-4">
                                <input type="hidden" name="screening_id" value="<?= $screening['id'] ?>">

                                <select name="movie_id" required
                                    class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
                                    <?php foreach ($movies as $movie): ?>
                                        <option value="<?= $movie['id'] ?>" <?= $screening['movie_title'] === $movie['title'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($movie['title']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <select name="screening_room_id" required
                                    class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
                                    <?php foreach ($screeningRooms as $room): ?>
                                        <option value="<?= $room['id'] ?>" <?= $screening['room_name'] === $room['name'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($room['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <input type="datetime-local" name="start_time"
                                       value="<?= date('Y-m-d\TH:i', strtotime($screening['start_time'])) ?>"
                                       class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">

                                <input type="datetime-local" name="end_time"
                                       value="<?= date('Y-m-d\TH:i', strtotime($screening['end_time'])) ?>"
                                       class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">

                                <div class="flex gap-4 mt-4">
                                    <button type="submit" name="edit_screening"
                                        class="bg-[var(--primary)] text-[var(--white)] px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
                                        Save Changes
                                    </button>

                                    <button type="button" onclick="toggleEditScreeningForm(<?= $screening['id'] ?>)"
                                        class="bg-gray-400 text-white px-6 py-2 rounded-lg shadow-md hover:bg-gray-500 transition-colors duration-300 font-[Limelight] text-lg">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
function toggleEditScreeningForm(id) {
    const form = document.getElementById(`edit-screening-${id}`);
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>
