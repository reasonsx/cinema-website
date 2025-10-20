<section class="mb-10">
  <h2 class="text-5xl font-[Limelight] text-[var(--primary)] mb-6">All Bookings</h2>

  <!-- Add Booking Form -->
  <details class="mb-8">
    <summary class="cursor-pointer inline-block bg-[var(--primary)] text-[var(--white)] px-6 py-3 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
      Add New Booking
    </summary>
    <form method="post" id="add-booking-form" class="flex flex-col gap-4 mt-4">
      <select name="user_id" required class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1">
        <option value="">Select User</option>
        <?php foreach ($users as $u): ?>
          <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['firstname'].' '.$u['lastname'].' ('.$u['email'].')') ?></option>
        <?php endforeach; ?>
      </select>

      <select name="screening_id" id="add_screening_id" required class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1">
        <option value="">Select Screening</option>
        <?php foreach ($screenings as $s): ?>
          <option value="<?= $s['id'] ?>" data-room="<?= $s['screening_room_id'] ?>"><?= htmlspecialchars($s['movie_title'].' | '.$s['start_time'].' → '.$s['end_time'].' | '.$s['room_name']) ?></option>
        <?php endforeach; ?>
      </select>

      <!-- Seat Selection -->
      <div id="add-seats-container" class="grid grid-cols-10 gap-2 mt-2"></div>
        <!-- Seats will be populated by JS -->
      </div>

      <input type="hidden" name="seat_ids" id="add_selected_seats">

      <button type="submit" name="add_booking" class="bg-[var(--primary)] text-white px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg mt-2">
        Add Booking
      </button>
    </form>
  </details>

  <div class="overflow-x-auto">
    <table class="min-w-full border-t-4 border-[var(--primary)] text-black">
      <thead class="font-[Limelight] text-[var(--primary)] text-lg">
        <tr>
          <th class="px-4 py-2">ID</th>
          <th class="px-4 py-2">User</th>
          <th class="px-4 py-2">Movie</th>
          <th class="px-4 py-2">Room</th>
          <th class="px-4 py-2">Screening</th>
          <th class="px-4 py-2">Seats</th>
          <th class="px-4 py-2">Total Price</th>
          <th class="px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($bookings as $b): ?>
          <tr class="hover:text-black transition-colors duration-300 align-top">
            <td class="px-4 py-4"><?= $b['id'] ?></td>
            <td class="px-4 py-4"><?= htmlspecialchars($b['firstname'].' '.$b['lastname'].' ('.$b['email'].')') ?></td>
            <td class="px-4 py-4"><?= htmlspecialchars($b['movie_title']) ?></td>
            <td class="px-4 py-4"><?= htmlspecialchars($b['room_name']) ?></td>
            <td class="px-4 py-4"><?= htmlspecialchars($b['start_time'].' → '.$b['end_time']) ?></td>
            <td class="px-4 py-4">
              <?php foreach ($b['seats'] as $seat): ?>
                <span class="inline-block bg-[var(--secondary)] text-white px-2 py-1 rounded mr-1 mb-1 text-xs">
                  <?= htmlspecialchars($seat['row_number'].$seat['seat_number']) ?>
                </span>
              <?php endforeach; ?>
            </td>
            <td class="px-4 py-4"><?= number_format($b['total_price'],2) ?> DKK</td>
            <td class="px-4 py-4 flex flex-col gap-1">
              <form method="post"  style="display:inline;" onsubmit="return confirm('Delete this booking?');">
                <input type="hidden" name="delete_booking" value="<?= $b['id'] ?>">
                <button type="submit" class="bg-[var(--primary)] text-white px-3 py-1 rounded shadow hover:bg-[var(--secondary)] text-sm">
                  Delete
                </button>
              </form>

              <button type="button" onclick="toggleEditBookingForm(<?= $b['id'] ?>)" class="bg-[var(--primary)] text-white px-3 py-1 rounded shadow hover:bg-[var(--secondary)] text-sm">
                Edit
              </button>
            </td>
          </tr>

          <!-- Edit Booking Form -->
          <tr id="edit-booking-<?= $b['id'] ?>" class="hidden bg-gray-50">
            <td colspan="8" class="p-6 border-t-4 border-[var(--primary)]">
              <h3 class="text-3xl font-[Limelight] text-[var(--primary)] mb-4">Edit Booking #<?= $b['id'] ?></h3>
              <form method="post" class="flex flex-col gap-4">
                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">

                <select name="user_id" required class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1">
                  <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= $b['user_id']==$u['id']?'selected':'' ?>>
                      <?= htmlspecialchars($u['firstname'].' '.$u['lastname'].' ('.$u['email'].')') ?>
                    </option>
                  <?php endforeach; ?>
                </select>

                <select name="screening_id" class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 edit-screening-select" data-booking-id="<?= $b['id'] ?>">
                  <?php foreach ($screenings as $s): ?>
                    <option value="<?= $s['id'] ?>" data-room="<?= $s['screening_room_id'] ?>" <?= $b['screening_id']==$s['id']?'selected':'' ?>>
                      <?= htmlspecialchars($s['movie_title'].' | '.$s['start_time'].' → '.$s['end_time'].' | '.$s['room_name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>

                <!-- Seat selection -->
                <div id="edit-seats-container-<?= $b['id'] ?>" class="flex flex-wrap gap-2 mt-2">
                  <?php foreach ($b['seats'] as $seat): ?>
                    <span class="inline-block bg-[var(--secondary)] text-white px-2 py-1 rounded mr-1 mb-1 text-xs selected-seat" data-seat-id="<?= $seat['seat_id'] ?>">
                      <?= htmlspecialchars($seat['row_number'].$seat['seat_number']) ?>
                    </span>
                  <?php endforeach; ?>
                </div>

                <input type="hidden" name="seat_ids[]" id="edit_selected_seats_<?= $b['id'] ?>">

                <button type="submit" name="edit_booking" class="bg-[var(--primary)] text-white px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] text-lg mt-2">
                  Save Changes
                </button>
                <button type="button" onclick="toggleEditBookingForm(<?= $b['id'] ?>)" class="bg-gray-400 text-white px-6 py-2 rounded-lg shadow-md hover:bg-gray-500 text-lg mt-2">
                  Cancel
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<script>

document.getElementById('add-booking-form').addEventListener('submit', (e) => {
  const input = document.getElementById('add_selected_seats');
  console.log('Submitting seat_ids:', input.value);
});

    function toggleEditBookingForm(bookingId) {
    const form = document.getElementById(`edit-booking-${bookingId}`);
    if (!form) return;
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) return;
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// allSeats must contain DB seat IDs
const allSeats = <?php
$seatsByScreening = [];
foreach ($screenings as $s) {
    $stmt = $db->prepare("SELECT id, `row_number`, seat_number FROM seats WHERE screening_room_id = ? ORDER BY `row_number`, seat_number");
    $stmt->execute([$s['screening_room_id']]);
    $seatsByScreening[$s['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($seatsByScreening);
?>;
function renderSeats(screeningId, containerId, hiddenInputId, selectedSeatIds = []) {
    const container = document.getElementById(containerId);
    const input = document.getElementById(hiddenInputId);
    container.innerHTML = '';
    const seats = allSeats[screeningId] || [];

    seats.forEach(seat => {
        const span = document.createElement('span');
        span.className = 'seat inline-block bg-gray-300 text-black px-2 py-1 rounded mr-1 mb-1 text-xs cursor-pointer';
        span.textContent = seat.row_number + seat.seat_number;
        span.dataset.seatId = seat.id;

        // Pre-select seats
        if (selectedSeatIds.includes(seat.id)) {
            span.classList.add('selected');
            span.classList.remove('bg-gray-300','text-black');
            span.classList.add('bg-[var(--primary)]','text-white');
        }

        span.addEventListener('click', () => {
            const isSelected = span.classList.contains('selected');

            if (isSelected) {
                span.classList.remove('selected','bg-[var(--primary)]','text-white');
                span.classList.add('bg-gray-300','text-black');
            } else {
                span.classList.add('selected','bg-[var(--primary)]','text-white');
                span.classList.remove('bg-gray-300','text-black');
            }

            // Update hidden input with real DB seat IDs
            const selected = [...container.querySelectorAll('.seat.selected')]
                .map(s => s.dataset.seatId);
            input.value = selected.join(',');
        });

        container.appendChild(span);
    });

    // Initialize hidden input
    input.value = selectedSeatIds.join(',');
}


// Add Booking: update seats when screening changes
document.getElementById('add_screening_id').addEventListener('change', function() {
  renderSeats(this.value, 'add-seats-container', 'add_selected_seats', []);
});

document.getElementById('add-booking-form').addEventListener('submit', (e) => {
  const container = document.getElementById('add-seats-container');
  const input = document.getElementById('add_selected_seats');

  // Get selected seat IDs
  const selected = [...container.querySelectorAll('.seat.selected')]
    .map(s => s.dataset.seatId);

  input.value = selected.join(',');

  // Prevent submit if no seat is selected
  if (!input.value) {
    e.preventDefault();
    alert('Please select at least one seat.');
  }

  console.log('Submitting seats:', input.value);
});


// Edit Booking: update seats when screening changes
document.querySelectorAll('.edit-screening-select').forEach(select => {
  select.addEventListener('change', function() {
    const bookingId = this.dataset.bookingId;
    renderSeats(this.value, `edit-seats-container-${bookingId}`, `edit_selected_seats_${bookingId}`, []);
  });
});

</script>

