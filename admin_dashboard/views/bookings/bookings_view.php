<?php
require_once __DIR__ . '/../../components/table.php';
require_once __DIR__ . '/../../../include/helpers.php';

// ------------------------
// Build seat data per screening
// ------------------------

// All seats per screening (based on room)
$seatsByScreening = [];
foreach ($screenings as $s) {
    $stmt = $db->prepare("
        SELECT id, `row_number`, seat_number
        FROM seats
        WHERE screening_room_id = ?
        ORDER BY `row_number`, seat_number
    ");
    $stmt->execute([$s['screening_room_id']]);
    $seatsByScreening[$s['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Occupied seats per screening (already booked)
$occupiedSeatsByScreening = [];
$stmtOcc = $db->prepare("
    SELECT seat_id
    FROM booking_seats
    WHERE screening_id = ?
");
foreach ($screenings as $s) {
    $stmtOcc->execute([$s['id']]);
    $occupiedSeatsByScreening[$s['id']] = array_map(
        'intval',
        array_column($stmtOcc->fetchAll(PDO::FETCH_ASSOC), 'seat_id')
    );
}

// Helper to render seat badges in main table row
function bookingSeatBadge(string $label): string {
    return '<span class="inline-block bg-[var(--secondary)] text-white px-2 py-1 rounded text-xs mr-1 mb-1">'
        . e($label) .
        '</span>';
}

// ------------------------
// Render Table
// ------------------------

renderTable([
    'id'        => 'bookingsTable',
    'title'     => 'All Bookings',
    'searchable'=> true,
    'addLabel'  => 'Add Booking',

    // ADD FORM (top, collapsible)
    'addForm'   => (function() use ($users, $screenings) {
        ob_start(); ?>
        <form method="post" id="add-booking-form" class="flex flex-col gap-4">

            <!-- Legend -->
            <div class="flex items-center gap-4 text-sm mb-1">
                <span class="inline-block w-4 h-4 rounded bg-green-500"></span> Available
                <span class="inline-block w-4 h-4 rounded bg-[var(--primary)]"></span> Selected
                <span class="inline-block w-4 h-4 rounded bg-gray-500 opacity-50"></span> Occupied
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700">User</label>
                <select name="user_id" required
                        class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1">
                    <option value="">Select User</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>">
                            <?= htmlspecialchars($u['firstname'].' '.$u['lastname'].' ('.$u['email'].')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700">Screening</label>
                <select name="screening_id" id="add_screening_id" required
                        class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1">
                    <option value="">Select Screening</option>
                    <?php foreach ($screenings as $s): ?>
                        <option value="<?= $s['id'] ?>" data-room="<?= $s['screening_room_id'] ?>">
                            <?= htmlspecialchars($s['movie_title'].' | '.$s['start_time'].' → '.$s['end_time'].' | '.$s['room_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Seat Selection Grid -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700">Seats</label>
                <div id="add-seats-container"
                     class="seat-grid border border-gray-200 rounded-lg p-3 bg-gray-50 min-h-[56px]">
                    <!-- seats rendered by JS -->
                </div>
            </div>

            <input type="hidden" name="seat_ids" id="add_selected_seats">

            <button type="submit" name="add_booking"
                    class="bg-[var(--primary)] text-white px-6 py-2 rounded-lg hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg self-start mt-2">
                Add Booking
            </button>
        </form>
        <?php
        return ob_get_clean();
    })(),

    // TABLE HEADERS
    'headers' => ['ID', 'User', 'Movie', 'Room', 'Screening', 'Seats', 'Total Price'],

    // DATA ROWS
    'rows'    => $bookings,

    // MAIN ROW RENDERER
    'renderRow' => function(array $b) {
        $seatHtml = '';
        if (!empty($b['seats'])) {
            foreach ($b['seats'] as $seat) {
                $seatHtml .= bookingSeatBadge($seat['row_number'].$seat['seat_number']);
            }
        }

        return [
            (int)$b['id'],
            e($b['firstname'].' '.$b['lastname'].' ('.$b['email'].')'),
            e($b['movie_title']),
            e($b['room_name']),
            e($b['start_time'].' → '.$b['end_time']),
            $seatHtml ?: '<span class="text-xs text-gray-500 italic">No seats</span>',
            number_format((float)$b['total_price'], 2) . ' DKK',
        ];
    },

    // ACTIONS COLUMN
    'actions' => function(array $b) {
        ob_start(); ?>

        <div class="flex flex-row items-center gap-2">
            <button type="button"
                    onclick="toggleEditRow(<?= (int)$b['id'] ?>)"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700 transition">
                <i class="pi pi-pencil"></i> Edit
            </button>

            <form method="post" onsubmit="return confirm('Delete this booking?')" class="m-0 p-0">
                <input type="hidden" name="delete_booking" value="<?= (int)$b['id'] ?>">
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-red-500 text-white text-sm hover:bg-red-600 transition">
                    <i class="pi pi-trash"></i> Delete
                </button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    },

    // INLINE EDIT ROW
    'renderEditRow' => function(array $b) use ($users, $screenings) {
        $bookingId = (int)$b['id'];
        ob_start(); ?>
        <form method="post" class="flex flex-col gap-4" data-edit-booking="1">
            <h3 class="text-2xl font-[Limelight] text-[var(--primary)] mb-2">
                Edit Booking #<?= $bookingId ?>
            </h3>

            <input type="hidden" name="booking_id" value="<?= $bookingId ?>">

            <!-- Legend -->
            <div class="flex items-center gap-4 text-sm mb-1">
                <span class="inline-block w-4 h-4 rounded bg-green-500"></span> Available
                <span class="inline-block w-4 h-4 rounded bg-[var(--primary)]"></span> Selected
                <span class="inline-block w-4 h-4 rounded bg-gray-500 opacity-50"></span> Occupied
            </div>

            <!-- User -->
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-700">User</label>
                <select name="user_id" required
                        class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1">
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $b['user_id'] == $u['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['firstname'].' '.$u['lastname'].' ('.$u['email'].')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Screening -->
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-700">Screening</label>
                <select name="screening_id"
                        class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 edit-screening-select"
                        data-booking-id="<?= $bookingId ?>">
                    <?php foreach ($screenings as $s): ?>
                        <option value="<?= $s['id'] ?>"
                                data-room="<?= $s['screening_room_id'] ?>"
                            <?= $b['screening_id'] == $s['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['movie_title'].' | '.$s['start_time'].' → '.$s['end_time'].' | '.$s['room_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Seat grid -->
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-700">Seats</label>
                <div id="edit-seats-container-<?= $bookingId ?>"
                     class="seat-grid border border-gray-200 rounded-lg p-3 bg-gray-50 min-h-[56px]">
                    <!-- JS fills this -->
                </div>
            </div>

            <input type="hidden" name="seat_ids" id="edit_selected_seats_<?= $bookingId ?>">

            <div class="flex gap-4 mt-3">
                <button type="submit" name="edit_booking"
                        class="bg-[var(--primary)] text-[var(--white)] px-6 py-2 rounded-lg hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
                    Save Changes
                </button>

                <button type="button"
                        onclick="toggleEditRow(<?= $bookingId ?>)"
                        class="bg-gray-400 text-white px-6 py-2 rounded-lg hover:bg-gray-500 transition-colors duration-300 font-[Limelight] text-lg">
                    Cancel
                </button>
            </div>
        </form>
        <?php
        return ob_get_clean();
    },
]);
?>

<style>
    /* auto-fit seat grid (2B) */
    .seat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(2.25rem, 1fr));
        gap: 0.4rem;
    }
</style>

<script>
    // -----------------------
    // PHP → JS data
    // -----------------------
    const allSeats = <?= json_encode($seatsByScreening) ?>;
    const occupiedSeats = <?= json_encode($occupiedSeatsByScreening) ?>;

    // -----------------------
    // Core renderSeats helper
    // -----------------------
    function renderSeats(screeningId, containerId, hiddenInputId, selectedSeatIds = []) {
        const container = document.getElementById(containerId);
        const input = document.getElementById(hiddenInputId);
        if (!container || !input) return;

        container.innerHTML = "";

        const seats = allSeats[screeningId] || [];
        const occupied = occupiedSeats[screeningId] || [];
        const selectedSet = new Set((selectedSeatIds || []).map(id => parseInt(id)));

        seats.forEach(seat => {
            const sid = parseInt(seat.id);

            const span = document.createElement("span");
            span.textContent = seat.row_number + seat.seat_number;
            span.dataset.seatId = sid;

            span.className = `
            seat
            inline-flex items-center justify-center
            rounded-md text-xs font-medium
            transition-all duration-200
            cursor-pointer
            select-none
            px-1.5 py-1
        `;

            const isOccupiedByOthers =
                occupied.includes(sid) && !selectedSet.has(sid);

            // Occupied by others → gray, disabled
            if (isOccupiedByOthers) {
                span.classList.add("bg-gray-500", "text-white", "opacity-50", "cursor-not-allowed");
                container.appendChild(span);
                return;
            }

            // Pre-selected (for edit) → primary
            if (selectedSet.has(sid)) {
                span.classList.add("selected", "bg-[var(--primary)]", "text-white");
            } else {
                // Available → green
                span.classList.add("bg-green-500", "text-black");
            }

            span.onclick = () => {
                // toggle selection, but do NOT allow clicking occupied seat (we already filtered them)
                span.classList.toggle("selected");
                const isSelected = span.classList.contains("selected");

                if (isSelected) {
                    span.classList.remove("bg-green-500", "text-black");
                    span.classList.add("bg-[var(--primary)]", "text-white");
                } else {
                    span.classList.add("bg-green-500", "text-black");
                    span.classList.remove("bg-[var(--primary)]", "text-white");
                }

                const selected = [...container.querySelectorAll(".seat.selected")]
                    .map(s => s.dataset.seatId);

                input.value = selected.join(",");
            };

            container.appendChild(span);
        });

        input.value = (selectedSeatIds || []).join(",");
    }

    // -----------------------
    // Add Booking: seat handling
    // -----------------------
    const addScreeningSelect = document.getElementById('add_screening_id');
    if (addScreeningSelect) {
        addScreeningSelect.addEventListener('change', function () {
            renderSeats(this.value, 'add-seats-container', 'add_selected_seats', []);
        });
    }

    const addForm = document.getElementById('add-booking-form');
    if (addForm) {
        addForm.addEventListener('submit', (e) => {
            const input = document.getElementById('add_selected_seats');
            if (!input || !input.value) {
                e.preventDefault();
                alert('Please select at least one seat.');
            }
        });
    }

    // -----------------------
    // Edit Booking: screening change + submit validation
    // -----------------------
    document.querySelectorAll('.edit-screening-select').forEach(select => {
        select.addEventListener('change', function () {
            const bookingId = this.dataset.bookingId;
            renderSeats(
                this.value,
                `edit-seats-container-${bookingId}`,
                `edit_selected_seats_${bookingId}`,
                []
            );
        });
    });

    document.querySelectorAll('form[data-edit-booking="1"]').forEach(form => {
        form.addEventListener('submit', (e) => {
            const bookingId = form.querySelector('[name="booking_id"]').value;
            const input = document.getElementById(`edit_selected_seats_${bookingId}`);
            if (!input || !input.value) {
                e.preventDefault();
                alert('Please select at least one seat.');
            }
        });
    });

    // -----------------------
    // Preload existing seats in edit forms
    // -----------------------
    <?php foreach ($bookings as $b):
    $bid = (int)$b['id'];
    $sid = (int)$b['screening_id'];
    $seatIds = array_map(fn($s) => (int)$s['seat_id'], $b['seats']);
    ?>
    renderSeats(
        <?= $sid ?>,
        'edit-seats-container-<?= $bid ?>',
        'edit_selected_seats_<?= $bid ?>',
        <?= json_encode($seatIds) ?>
    );
    <?php endforeach; ?>
</script>
