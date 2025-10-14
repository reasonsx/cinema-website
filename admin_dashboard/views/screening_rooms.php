<section class="mb-10">
  <h2 class="text-5xl font-[Limelight] text-[var(--primary)] mb-6">Screening Rooms</h2>

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

          foreach ($seats as $seat) {
              $groupedSeats[$seat['row_number']][] = $seat['seat_number'];
          }

          // Pre-fill values
          $rowCount = count($groupedSeats);
          $maxSeatsPerRow = $rowCount > 0 ? max(array_map('count', $groupedSeats)) : 0;

          // Manual text version like "A1 A2 A3 B1 B2 ..."
          $manualSeats = '';
          foreach ($groupedSeats as $row => $seatNumbers) {
              foreach ($seatNumbers as $num) {
                  $manualSeats .= $row . $num . ' ';
              }
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
                    <div
                      class="w-8 h-8 bg-[var(--secondary)] rounded-full flex items-center justify-center text-white text-xs shadow">
                      <?= htmlspecialchars($row . $number) ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </td>

          <td class="px-4 py-4">
            <button type="button"
              onclick="toggleEditRoomForm(<?= $room['id'] ?>)"
              class="bg-[var(--primary)] text-white px-3 py-1 rounded shadow hover:bg-[var(--secondary)] text-sm">
              Edit
            </button>

            <button class="bg-red-500 text-white px-3 py-1 rounded shadow hover:bg-red-600 text-sm">
              Delete
            </button>
          </td>
        </tr>

        <!-- EDIT FORM -->
        <tr id="edit-room-<?= $room['id'] ?>" class="hidden bg-gray-50">
          <td colspan="5" class="p-6 border-t-4 border-[var(--primary)]">
            <h3 class="text-3xl font-[Limelight] text-[var(--primary)] mb-4">Edit Screening Room</h3>

            <form method="post" class="flex flex-col gap-4">
              <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
              <input type="hidden" name="seat_edit_mode" id="seat_edit_mode_<?= $room['id'] ?>" value="">


              <input type="text" name="name" value="<?= htmlspecialchars($room['name']) ?>" required
                class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1
                       focus:outline-none focus:border-[var(--secondary)]"
                placeholder="Room Name">

              <!-- Capacity (read-only) -->
              <input type="number" name="capacity" value="<?= $room['capacity'] ?>" readonly
                class="border-b-2 border-gray-300 bg-gray-100 text-gray-500 px-2 py-1 cursor-not-allowed"
                title="Capacity is calculated automatically">

              <!-- Seat Editing Mode Buttons -->
              <div class="flex gap-4 mt-2">
                <button type="button" onclick="switchSeatEditMode(<?= $room['id'] ?>, 'grid')"
                  class="bg-[var(--primary)] text-white px-4 py-2 rounded shadow hover:bg-[var(--secondary)] text-sm">
                  Edit as Grid
                </button>

                <button type="button" onclick="switchSeatEditMode(<?= $room['id'] ?>, 'manual')"
                  class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 text-sm">
                  Edit Manually
                </button>
              </div>

              <!-- GRID EDITOR -->
              <div id="grid-editor-<?= $room['id'] ?>" class="hidden flex flex-col gap-3 mt-4">
                <label class="text-lg font-[Limelight] text-[var(--primary)]">Grid Layout</label>
                <div class="flex gap-4">
                  <input type="number" name="rows" min="1" value="<?= $rowCount ?>" placeholder="Rows"
                    class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1
                           focus:outline-none focus:border-[var(--secondary)] w-24">
                  <input type="number" name="seats_per_row" min="1" value="<?= $maxSeatsPerRow ?>" placeholder="Seats/Row"
                    class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1
                           focus:outline-none focus:border-[var(--secondary)] w-24">
                </div>
                <p class="text-sm text-gray-600">Example: 5 rows × 10 seats = 50 total seats</p>
              </div>

              <!-- MANUAL EDITOR -->
              <div id="manual-editor-<?= $room['id'] ?>" class="hidden mt-4">
                <label class="text-lg font-[Limelight] text-[var(--primary)]">Manual Seat Input</label>
                <textarea name="seats_text" rows="4"
                  class="w-full border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1
                         focus:outline-none focus:border-[var(--secondary)]"
                  placeholder="Example: A1 A2 A3 B1 B2 B3 ..."><?= htmlspecialchars($manualSeats) ?></textarea>
              </div>

              <div class="flex gap-4 mt-4">
                <button type="submit" name="edit_room"
                  class="bg-[var(--primary)] text-white px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)]
                         transition-colors duration-300 font-[Limelight] text-lg">
                  Save Changes
                </button>

                <button type="button" onclick="toggleEditRoomForm(<?= $room['id'] ?>)"
                  class="bg-gray-400 text-white px-6 py-2 rounded-lg shadow-md hover:bg-gray-500
                         transition-colors duration-300 font-[Limelight] text-lg">
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
function toggleEditRoomForm(roomId) {
  const form = document.getElementById(`edit-room-${roomId}`);
  form.classList.toggle('hidden');
  if (!form.classList.contains('hidden')) {
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

function switchSeatEditMode(roomId, mode) {
  const grid = document.getElementById(`grid-editor-${roomId}`);
  const manual = document.getElementById(`manual-editor-${roomId}`);
  const modeInput = document.getElementById(`seat_edit_mode_${roomId}`);

  if (mode === 'grid') {
    grid.classList.remove('hidden');
    manual.classList.add('hidden');
    modeInput.value = 'grid';
  } else {
    manual.classList.remove('hidden');
    grid.classList.add('hidden');
    modeInput.value = 'manual';
  }
}

</script>
