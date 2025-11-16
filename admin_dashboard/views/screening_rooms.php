<?php
require_once __DIR__ . '/../../components/table.php';
require_once __DIR__ . '/../../include/helpers.php';

// Helper: group seats by row and build data for edit/manual mode
function buildSeatLayout(PDO $db, int $roomId): array {
    $stmt = $db->prepare("
        SELECT id, `row_number`, seat_number
        FROM seats
        WHERE screening_room_id = ?
        ORDER BY `row_number`, seat_number
    ");
    $stmt->execute([$roomId]);
    $seats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $grouped = [];
    foreach ($seats as $seat) {
        $grouped[$seat['row_number']][] = $seat['seat_number'];
    }

    $rowCount      = count($grouped);
    $maxSeatsPerRow = $rowCount > 0 ? max(array_map('count', $grouped)) : 0;

    // Manual seats string: "A1 A2 B1 B2 ..."
    $manualSeats = '';
    foreach ($grouped as $row => $nums) {
        foreach ($nums as $num) {
            $manualSeats .= $row . $num . ' ';
        }
    }
    $manualSeats = trim($manualSeats);

    return [
        'seats'          => $seats,
        'grouped'        => $grouped,
        'rowCount'       => $rowCount,
        'maxSeatsPerRow' => $maxSeatsPerRow,
        'manualSeats'    => $manualSeats,
    ];
}

// Seat badge (Style C)
function seatBadge(string $label): string {
    return "<span class=\"inline-block bg-[var(--secondary)] text-white px-2 py-1 rounded-md text-xs shadow-sm mr-1 mb-1\">"
        . e($label) .
        "</span>";
}

renderTable([
    'id'        => 'roomsTable',
    'title'     => 'All Screening Rooms',
    'searchable'=> true,
    'addLabel'  => 'Add Screening Room',

    // ADD FORM
    'addForm' => (function () {
        ob_start(); ?>
        <form method="post" class="flex flex-col gap-4">
            <input type="hidden" name="seat_edit_mode" id="add_seat_edit_mode" value="grid">

            <!-- Room name -->
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-700">Room Name</label>
                <input type="text"
                       name="name"
                       placeholder="Room Name"
                       required
                       class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]">
            </div>

            <!-- Mode switch -->
            <div class="flex gap-4 mt-2">
                <button type="button"
                        onclick="switchAddSeatEditMode('grid')"
                        class="bg-[var(--primary)] text-white px-4 py-2 rounded shadow hover:bg-[var(--secondary)] text-sm">
                    Grid Mode
                </button>
                <button type="button"
                        onclick="switchAddSeatEditMode('manual')"
                        class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 text-sm">
                    Manual Mode
                </button>
            </div>

            <!-- GRID EDITOR -->
            <div id="add-grid-editor" class="flex flex-col gap-3 mt-4">
                <div class="flex gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-gray-700">Rows</label>
                        <input type="number" name="rows" min="1" placeholder="Rows"
                               class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 w-24">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-gray-700">Seats per Row</label>
                        <input type="number" name="seats_per_row" min="1" placeholder="Seats/Row"
                               class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 w-24">
                    </div>
                </div>
                <p class="text-sm text-gray-600">
                    Example: 5 rows × 10 seats = 50 total seats
                </p>
            </div>

            <!-- MANUAL EDITOR -->
            <div id="add-manual-editor" class="hidden mt-4">
                <label class="text-sm text-gray-700">Seats (manual)</label>
                <textarea name="seats_text"
                          rows="4"
                          placeholder="Example: A1 A2 A3 B1 B2 ..."
                          class="w-full border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-gray-400 focus:outline-none focus:border-[var(--secondary)]"></textarea>
            </div>

            <div class="flex gap-4 mt-4">
                <button type="submit"
                        name="add_room"
                        class="bg-[var(--primary)] text-white px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
                    Add Room
                </button>
            </div>
        </form>
        <?php
        return ob_get_clean();
    })(),

    // TABLE HEADERS
    'headers' => ['ID', 'Name', 'Capacity', 'Seats'],

    // TABLE ROWS
    'rows' => $screeningRooms,

    // HOW TO DISPLAY EACH ROW
    'renderRow' => function ($room) use ($db) {
        $layout = buildSeatLayout($db, (int)$room['id']);

        // Seat grid with Style C badges
        $seatHtml = '<div class="flex flex-col gap-1">';
        foreach ($layout['grouped'] as $row => $nums) {
            $seatHtml .= '<div class="flex flex-wrap gap-1">';
            foreach ($nums as $num) {
                $seatHtml .= seatBadge($row . $num);
            }
            $seatHtml .= '</div>';
        }
        $seatHtml .= '</div>';

        return [
            $room['id'],
            e($room['name']),
            (int)$room['capacity'],
            $seatHtml,
        ];
    },

    'actions' => function ($room) {
        ob_start(); ?>
        <div class="flex items-center gap-2">

            <button onclick="toggleEditRow(<?= $room['id'] ?>)"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700 transition">
                <i class="pi pi-pencil"></i> Edit
            </button>

            <form method="post" onsubmit="return confirm('Delete this room?')" class="m-0 p-0">
                <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                <button type="submit" name="delete_room"
                        class="px-4 py-2 rounded-lg bg-red-500 text-white text-sm hover:bg-red-600 transition">
                    <i class="pi pi-trash"></i> Delete
                </button>
            </form>

        </div>
        <?php
        return ob_get_clean();
    },

    // INLINE EDIT FORM
    'renderEditRow' => function ($room) use ($db) {
        $layout = buildSeatLayout($db, (int)$room['id']);
        $rowId  = (int)$room['id'];

        ob_start(); ?>
        <form method="post" class="flex flex-col gap-4">
            <input type="hidden" name="room_id" value="<?= $rowId ?>">
            <input type="hidden" name="seat_edit_mode" id="edit_seat_edit_mode_<?= $rowId ?>" value="grid">

            <!-- Name -->
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-700">Room Name</label>
                <input type="text"
                       name="name"
                       value="<?= e($room['name']) ?>"
                       required
                       class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
            </div>

            <!-- Capacity (readonly) -->
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-700">Capacity</label>
                <input type="number"
                       value="<?= (int)$room['capacity'] ?>"
                       readonly
                       class="border-b-2 border-gray-300 bg-gray-100 text-gray-500 px-2 py-1 cursor-not-allowed"
                       title="Capacity is calculated automatically from seats">
            </div>

            <!-- Mode switch -->
            <div class="flex gap-4 mt-2">
                <button type="button"
                        onclick="switchEditSeatMode(<?= $rowId ?>, 'grid')"
                        class="bg-[var(--primary)] text-white px-4 py-2 rounded shadow hover:bg-[var(--secondary)] text-sm">
                    Grid Mode
                </button>
                <button type="button"
                        onclick="switchEditSeatMode(<?= $rowId ?>, 'manual')"
                        class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 text-sm">
                    Manual Mode
                </button>
            </div>

            <!-- GRID EDITOR -->
            <div id="edit-grid-editor-<?= $rowId ?>" class="flex flex-col gap-3 mt-4">
                <div class="flex gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-gray-700">Rows</label>
                        <input type="number"
                               name="rows"
                               min="1"
                               value="<?= $layout['rowCount'] ?>"
                               placeholder="Rows"
                               class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 w-24">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-gray-700">Seats per Row</label>
                        <input type="number"
                               name="seats_per_row"
                               min="1"
                               value="<?= $layout['maxSeatsPerRow'] ?>"
                               placeholder="Seats/Row"
                               class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 w-24">
                    </div>
                </div>
                <p class="text-sm text-gray-600">
                    If you change rows/seats per row, the existing seat layout will be replaced.
                </p>
            </div>

            <!-- MANUAL EDITOR -->
            <div id="edit-manual-editor-<?= $rowId ?>" class="hidden mt-4">
                <label class="text-sm text-gray-700">Seats (manual)</label>
                <textarea name="seats_text"
                          rows="4"
                          class="w-full border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-gray-400 focus:outline-none focus:border-[var(--secondary)]"><?= e($layout['manualSeats']) ?></textarea>
            </div>

            <div class="flex gap-4 mt-4">
                <button type="submit"
                        name="edit_room"
                        class="bg-[var(--primary)] text-white px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] text-lg">
                    Save Changes
                </button>
                <button type="button"
                        onclick="toggleEditRow(<?= $rowId ?>)"
                        class="bg-gray-400 text-white px-6 py-2 rounded-lg shadow-md hover:bg-gray-500 text-lg">
                    Cancel
                </button>
            </div>
        </form>
        <?php
        return ob_get_clean();
    },
]);
?>

<script>
    // Add Room: switch between grid/manual
    function switchAddSeatEditMode(mode) {
        const grid  = document.getElementById('add-grid-editor');
        const manual = document.getElementById('add-manual-editor');
        const input = document.getElementById('add_seat_edit_mode');

        if (!grid || !manual || !input) return;

        grid.classList.toggle('hidden', mode !== 'grid');
        manual.classList.toggle('hidden', mode !== 'manual');
        input.value = mode;
    }

    // Edit Room: switch between grid/manual per room
    function switchEditSeatMode(roomId, mode) {
        const grid   = document.getElementById('edit-grid-editor-' + roomId);
        const manual = document.getElementById('edit-manual-editor-' + roomId);
        const input  = document.getElementById('edit_seat_edit_mode_' + roomId);

        if (!grid || !manual || !input) return;

        grid.classList.toggle('hidden', mode !== 'grid');
        manual.classList.toggle('hidden', mode !== 'manual');
        input.value = mode;
    }
</script>
