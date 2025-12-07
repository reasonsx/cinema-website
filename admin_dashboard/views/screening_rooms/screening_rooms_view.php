<?php
require_once __DIR__ . '/../../components/table.php';
require_once __DIR__ . '/../../../shared/helpers.php';

// Helper: Build seat layout groups + counts + manual string
function buildSeatLayout(PDO $db, int $roomId): array
{
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

    $rowCount = count($grouped);
    $maxSeatsPerRow = $rowCount > 0 ? max(array_map('count', $grouped)) : 0;
    $manualSeats = '';

    foreach ($grouped as $row => $nums) {
        foreach ($nums as $num) {
            $manualSeats .= $row . $num . ' ';
        }
    }

    return [
        'seats' => $seats,
        'grouped' => $grouped,
        'rowCount' => $rowCount,
        'maxSeatsPerRow' => $maxSeatsPerRow,
        'manualSeats' => trim($manualSeats),
    ];
}

function seatBadge(string $label): string
{
    return "<span class=\"inline-block bg-[var(--secondary)] text-white px-2 py-1 rounded-md text-xs shadow-sm mr-1 mb-1\">"
        . e($label) .
        "</span>";
}

renderTable([
    'id' => 'roomsTable',
    'title' => 'All Screening Rooms',
    'searchable' => true,
    'addLabel' => 'Add Screening Room',

    'addForm' => (function () {
        ob_start(); ?>
        <form method="post" class="flex flex-col gap-6">
            <input type="hidden" name="seat_edit_mode" id="add_seat_edit_mode" value="grid">

            <!-- Room name -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Room Name</label>
                <input type="text" name="name" placeholder="Room Name" required>
            </div>

            <!-- Mode switch -->
            <div class="flex gap-4">
                <button type="button"
                        onclick="switchAddSeatEditMode('grid')"
                        class="btn-square bg-purple-600 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-table"></i> Grid Mode
                </button>

                <button type="button"
                        onclick="switchAddSeatEditMode('manual')"
                        class="btn-square bg-gray-500 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-pencil"></i> Manual Mode
                </button>
            </div>

            <!-- Grid editor -->
            <div id="add-grid-editor" class="flex flex-col gap-4">
                <div class="flex gap-6">
                    <div class="flex flex-col gap-2">
                        <label class="text-sm text-gray-700 font-semibold">Rows</label>
                        <input type="number" min="1" name="rows" placeholder="Rows">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-sm text-gray-700 font-semibold">Seats per Row</label>
                        <input type="number" min="1" name="seats_per_row" placeholder="Seats">
                    </div>
                </div>

                <p class="text-sm text-gray-600">
                    Example: 5 rows × 10 seats = 50 seats total.
                </p>
            </div>

            <!-- Manual editor -->
            <div id="add-manual-editor" class="hidden flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Seats (manual)</label>
                <textarea name="seats_text" rows="4" placeholder="Example: A1 A2 A3 B1 B2 ..."></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit"
                        name="add_room"
                        class="btn-square bg-green-600 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-plus"></i> Add Screening Room
                </button>

                <button type="button"
                        onclick="toggleAddForm_roomsTable()"
                        class="btn-square bg-gray-300 text-gray-700 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-times"></i> Cancel
                </button>
            </div>
        </form>
        <?php return ob_get_clean();
    })(),

    'headers' => ['ID', 'Name', 'Capacity', 'Seats'],

    'rows' => $screeningRooms,

    'renderRow' => function ($room) use ($db) {
        $layout = buildSeatLayout($db, (int)$room['id']);

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
                    class="btn-square bg-blue-600">
                <i class="pi pi-pencil"></i> Edit
            </button>

            <form method="post" onsubmit="return confirm('Delete this room?')" class="m-0 p-0">
                <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                <button type="submit" name="delete_room"
                        class="btn-square bg-red-500">
                    <i class="pi pi-trash"></i> Delete
                </button>
            </form>

        </div>
        <?php return ob_get_clean();
    },

    // Edit row
    'renderEditRow' => function ($room) use ($db) {
        $layout = buildSeatLayout($db, (int)$room['id']);
        $id = (int)$room['id'];

        ob_start(); ?>
        <form method="post" class="flex flex-col gap-6">
            <input type="hidden" name="room_id" value="<?= $id ?>">
            <input type="hidden" id="edit_seat_edit_mode_<?= $id ?>" name="seat_edit_mode" value="grid">

            <!-- Room name -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Room Name</label>
                <input type="text" name="name" value="<?= e($room['name']) ?>" required>
            </div>

            <!-- Capacity (readonly) -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Capacity</label>
                <input type="number" value="<?= (int)$room['capacity'] ?>" readonly
                       class="cursor-not-allowed bg-gray-100">
            </div>

            <!-- Mode toggle -->
            <div class="flex gap-4">
                <button type="button"
                        onclick="switchEditSeatMode(<?= $id ?>, 'grid')"
                        class="btn-square bg-purple-600 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-table"></i> Grid Mode
                </button>

                <button type="button"
                        onclick="switchEditSeatMode(<?= $id ?>, 'manual')"
                        class="btn-square bg-gray-500 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-pencil"></i> Manual Mode
                </button>
            </div>

            <!-- Grid editor -->
            <div id="edit-grid-editor-<?= $id ?>" class="flex flex-col gap-4">
                <div class="flex gap-6">
                    <div class="flex flex-col gap-2">
                        <label class="text-sm text-gray-700 font-semibold">Rows</label>
                        <input type="number" min="1" name="rows" value="<?= $layout['rowCount'] ?>">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-sm text-gray-700 font-semibold">Seats per Row</label>
                        <input type="number" min="1" name="seats_per_row" value="<?= $layout['maxSeatsPerRow'] ?>">
                    </div>
                </div>

                <p class="text-sm text-gray-600">Changing rows/seats will regenerate the entire seat layout.</p>
            </div>

            <!-- Manual editor -->
            <div id="edit-manual-editor-<?= $id ?>" class="hidden flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Seats (manual)</label>
                <textarea name="seats_text" rows="4"><?= e($layout['manualSeats']) ?></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit"
                        name="edit_room"
                        class="btn-square bg-green-600 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-check"></i> Save Changes
                </button>

                <button type="button"
                        onclick="toggleEditRow(<?= $id ?>)"
                        class="btn-square bg-gray-300 text-gray-700 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-times"></i> Cancel
                </button>
            </div>
        </form>
        <?php return ob_get_clean();
    },
]);
?>

<script>
    // Switch add form mode
    function switchAddSeatEditMode(mode) {
        const grid = document.getElementById('add-grid-editor');
        const manual = document.getElementById('add-manual-editor');
        const input = document.getElementById('add_seat_edit_mode');

        grid.classList.toggle('hidden', mode !== 'grid');
        manual.classList.toggle('hidden', mode !== 'manual');
        input.value = mode;
    }

    // Switch edit form mode
    function switchEditSeatMode(id, mode) {
        const grid = document.getElementById('edit-grid-editor-' + id);
        const manual = document.getElementById('edit-manual-editor-' + id);
        const input = document.getElementById('edit_seat_edit_mode_' + id);

        grid.classList.toggle('hidden', mode !== 'grid');
        manual.classList.toggle('hidden', mode !== 'manual');
        input.value = mode;
    }
</script>
