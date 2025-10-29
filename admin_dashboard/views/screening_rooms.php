<section class="mb-10">
  <h2 class="text-5xl font-[Limelight] text-[var(--primary)] mb-6">All Screening Rooms</h2>

  <!-- Add Room Form -->
  <details class="mb-8">
    <summary class="cursor-pointer inline-block bg-[var(--primary)] text-[var(--white)] px-6 py-3 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
      Add New Screening Room
    </summary>
    <form method="post" class="flex flex-col gap-4 mt-4">
      <input type="text" name="name" placeholder="Room Name" required
             class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]">

      <input type="hidden" name="seat_edit_mode" id="add_seat_edit_mode" value="grid">

      <div class="flex gap-4 mt-2">
        <button type="button" onclick="switchAddSeatEditMode('grid')"
          class="bg-[var(--primary)] text-white px-4 py-2 rounded shadow hover:bg-[var(--secondary)] text-sm">
          Grid Mode
        </button>
        <button type="button" onclick="switchAddSeatEditMode('manual')"
          class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 text-sm">
          Manual Mode
        </button>
      </div>

      <!-- GRID EDITOR -->
      <div id="add-grid-editor" class="flex flex-col gap-3 mt-4">
        <div class="flex gap-4">
          <input type="number" name="rows" min="1" placeholder="Rows"
                 class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 w-24">
          <input type="number" name="seats_per_row" min="1" placeholder="Seats/Row"
                 class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 w-24">
        </div>
        <p class="text-sm text-gray-600">Example: 5 rows × 10 seats = 50 total seats</p>
      </div>

      <!-- MANUAL EDITOR -->
      <div id="add-manual-editor" class="hidden mt-4">
        <textarea name="seats_text" rows="4" placeholder="Example: A1 A2 A3 B1 B2 ..."
                  class="w-full border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1"></textarea>
      </div>

      <button type="submit" name="add_room"
              class="bg-[var(--primary)] text-white px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg mt-2">
        Add Room
      </button>
    </form>
  </details>
    <!-- Search -->
    <div class="flex justify-between items-center mb-6">
        <input
                type="text"
                id="roomSearch"
                placeholder="Search screening rooms..."
                class="border-2 border-[var(--primary)] rounded-lg px-4 py-2 w-full max-w-md focus:outline-none focus:border-[var(--secondary)] placeholder-gray-500"
                oninput="filterRooms()"
        >
    </div>

  <div class="overflow-x-auto">
    <table class="min-w-full border-t-4 border-[var(--primary)] text-black">
      <thead class="font-[Limelight] text-[var(--primary)] text-lg">
        <tr>
          <th class="px-4 py-2 text-left">ID</th>
          <th class="px-4 py-2 text-left">Name</th>
          <th class="px-4 py-2 text-left">Capacity</th>
          <th class="px-4 py-2 text-left">Seats</th>
          <th class="px-4 py-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($screeningRooms as $room): ?>
        <?php
          $seats = getSeatsByRoom($db, $room['id']);
          $groupedSeats = [];
          foreach ($seats as $seat) $groupedSeats[$seat['row_number']][] = $seat['seat_number'];

          $rowCount = count($groupedSeats);
          $maxSeatsPerRow = $rowCount > 0 ? max(array_map('count', $groupedSeats)) : 0;

          $manualSeats = '';
          foreach ($groupedSeats as $row => $seatNumbers) {
              foreach ($seatNumbers as $num) $manualSeats .= $row.$num.' ';
          }
          $manualSeats = trim($manualSeats);
        ?>

        <tr class="hover:text-black transition-colors duration-300 align-top">
          <td class="px-4 py-4"><?= $room['id'] ?></td>
          <td class="px-4 py-4"><?= htmlspecialchars($room['name']) ?></td>
          <td class="px-4 py-4"><?= $room['capacity'] ?></td>
          <td class="px-4 py-4">
            <div class="flex flex-col gap-3">
              <?php foreach ($groupedSeats as $row => $seatNumbers): ?>
                <div class="flex gap-2">
                  <?php foreach ($seatNumbers as $number): ?>
                    <div class="w-8 h-8 bg-[var(--secondary)] rounded-full flex items-center justify-center text-white text-xs shadow">
                      <?= htmlspecialchars($row.$number) ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </td>
          <td class="px-4 py-4">
            <form method="post" style="display:inline;" onsubmit="return confirm('Delete this room?');">
              <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
              <button type="submit" name="delete_room"
                      class="bg-[var(--primary)] text-[var(--white)] px-3 py-1 rounded shadow hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-sm">
                Delete
              </button>
            </form>

            <button type="button" onclick="toggleEditRoomForm(<?= $room['id'] ?>)"
                    class="bg-[var(--primary)] text-[var(--white)] px-3 py-1 rounded shadow hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-sm">
                                 Edit
            </button>
          </td>
        </tr>

        <!-- Edit Form (like Add) -->
        <tr id="edit-room-<?= $room['id'] ?>" class="hidden bg-gray-50">
          <td colspan="5" class="p-6 border-t-4 border-[var(--primary)]">
            <h3 class="text-3xl font-[Limelight] text-[var(--primary)] mb-4">Edit Screening Room</h3>
            <form method="post" class="flex flex-col gap-4">
              <input type="hidden" name="room_id" value="<?= $room['id'] ?>">

              <input type="text" name="name" value="<?= htmlspecialchars($room['name']) ?>" required
                     class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">

              <input type="number" name="capacity" value="<?= $room['capacity'] ?>" readonly
                     class="border-b-2 border-gray-300 bg-gray-100 text-gray-500 px-2 py-1 cursor-not-allowed"
                     title="Capacity is calculated automatically">

              <input type="hidden" name="seat_edit_mode" id="edit_seat_edit_mode_<?= $room['id'] ?>" value="grid">

              <div class="flex gap-4 mt-2">
                <button type="button" onclick="switchEditSeatMode(<?= $room['id'] ?>, 'grid')"
                        class="bg-[var(--primary)] text-white px-4 py-2 rounded shadow hover:bg-[var(--secondary)] text-sm">
                  Grid Mode
                </button>
                <button type="button" onclick="switchEditSeatMode(<?= $room['id'] ?>, 'manual')"
                        class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 text-sm">
                  Manual Mode
                </button>
              </div>

              <div id="edit-grid-editor-<?= $room['id'] ?>" class="flex flex-col gap-3 mt-4">
                <div class="flex gap-4">
                  <input type="number" name="rows" min="1" value="<?= $rowCount ?>" placeholder="Rows"
                         class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 w-24">
                  <input type="number" name="seats_per_row" min="1" value="<?= $maxSeatsPerRow ?>" placeholder="Seats/Row"
                         class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 w-24">
                </div>
              </div>

              <div id="edit-manual-editor-<?= $room['id'] ?>" class="hidden mt-4">
                <textarea name="seats_text" rows="4"><?= htmlspecialchars($manualSeats) ?></textarea>
              </div>

              <div class="flex gap-4 mt-4">
                <button type="submit" name="edit_room"
                        class="bg-[var(--primary)] text-white px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] text-lg">
                  Save Changes
                </button>
                <button type="button" onclick="toggleEditRoomForm(<?= $room['id'] ?>)"
                        class="bg-gray-400 text-white px-6 py-2 rounded-lg shadow-md hover:bg-gray-500 text-lg">
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
        function filterRooms() {
        const input = document.getElementById('roomSearch').value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr:not([id^="edit-room-"])');

        rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';

        // also hide edit forms if their parent is hidden
        const editRow = document.getElementById(`edit-room-${row.querySelector('td')?.textContent.trim()}`);
        if (editRow) editRow.style.display = text.includes(input) ? '' : 'none';
    });
    }

function toggleEditRoomForm(roomId) {
  const form = document.getElementById(`edit-room-${roomId}`);
  form.classList.toggle('hidden');
  if (!form.classList.contains('hidden')) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Add Mode
function switchAddSeatEditMode(mode) {
  document.getElementById('add-grid-editor').classList.toggle('hidden', mode !== 'grid');
  document.getElementById('add-manual-editor').classList.toggle('hidden', mode !== 'manual');
  document.getElementById('add_seat_edit_mode').value = mode;
}

// Edit Mode
function switchEditSeatMode(roomId, mode) {
  document.getElementById(`edit-grid-editor-${roomId}`).classList.toggle('hidden', mode !== 'grid');
  document.getElementById(`edit-manual-editor-${roomId}`).classList.toggle('hidden', mode !== 'manual');
  document.getElementById(`edit_seat_edit_mode_${roomId}`).value = mode;
}
</script>
