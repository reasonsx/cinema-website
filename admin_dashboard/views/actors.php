<?php
require_once __DIR__ . '/../../components/table.php';
require_once __DIR__ . '/../../include/helpers.php';
function genderBadge(string $gender): string {
    $colors = [
        'Male'   => 'bg-blue-100 text-blue-700',
        'Female' => 'bg-pink-100 text-pink-700',
        'Other'  => 'bg-gray-200 text-gray-700',
    ];

    $class = $colors[$gender] ?? 'bg-gray-200 text-gray-700';

    return "<span class=\"px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap $class\">" . e($gender) . "</span>";
}

function formatDate($date): string {
    if (!$date || $date === '0000-00-00') {
        return '—';
    }

    try {
        return date('d M Y', strtotime($date)); // Example: 11 Apr 2002
    } catch (Exception $e) {
        return '—';
    }
}

?>

<?php
renderTable([
    'id' => 'actorsTable',
    'title' => 'All Actors',
    'headers' => ['ID', 'Name', 'DOB', 'Gender', 'Description'],
    'rows' => $actors,
    'searchable' => true,
    'renderRow' => function ($actor) {
        return [
            $actor['id'],
            e($actor['first_name'] . ' ' . $actor['last_name']),
            '<span class="whitespace-nowrap">' . formatDate($actor['date_of_birth']) . '</span>',
            genderBadge($actor['gender']),
            e($actor['description']),
        ];
    },
    'actions' => function ($actor) {
        ob_start(); ?>
        <div class="flex items-center gap-2">

            <button onclick="toggleEditRow(<?= $actor['id'] ?>)"
                    class="flex items-center justify-center gap-2
                           px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold
                           hover:bg-blue-700 transition">
                <i class="pi pi-pencil"></i> Edit
            </button>

            <form method="post"
                  onsubmit="return confirm('Delete this actor?')"
                  class="flex items-center justify-center p-0 m-0 leading-none">
                <input type="hidden" name="delete_actor_id" value="<?= $actor['id'] ?>">

                <button type="submit" name="delete_actor"
                        class="flex items-center justify-center gap-2
                               px-4 py-2 rounded-lg bg-red-500 text-white text-sm font-semibold
                               hover:bg-red-600 transition">
                    <i class="pi pi-trash"></i> Delete
                </button>
            </form>

        </div>
        <?php return ob_get_clean();
    },

    // CLEAN INLINE EDIT FORM
    'renderEditRow' => function ($actor) {
        ob_start(); ?>
        <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <input type="hidden" name="actor_id" value="<?= $actor['id'] ?>">

            <!-- FIRST NAME -->
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600">First Name</label>
                <input type="text" name="first_name" value="<?= e($actor['first_name']) ?>" class="input-edit">
            </div>

            <!-- LAST NAME -->
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600">Last Name</label>
                <input type="text" name="last_name" value="<?= e($actor['last_name']) ?>" class="input-edit">
            </div>

            <!-- DOB -->
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600">Date of Birth</label>
                <input type="date" name="date_of_birth" value="<?= e($actor['date_of_birth']) ?>" class="input-edit">
            </div>

            <!-- GENDER -->
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600">Gender</label>
                <select name="gender" class="input-edit-select">
                    <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                        <option <?= $actor['gender'] === $g ? 'selected' : '' ?>><?= $g ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- DESCRIPTION -->
            <div class="md:col-span-2 flex flex-col gap-1">
                <label class="text-sm text-gray-600">Description</label>
                <textarea name="description" class="input-edit-textarea"><?= e($actor['description']) ?></textarea>
            </div>

            <!-- BUTTONS -->
            <div class="md:col-span-2 flex gap-4 mt-2">
                <button type="submit" name="edit_actor"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg
                               bg-green-500 text-white text-sm font-semibold hover:bg-green-600 transition">
                    <i class="pi pi-check"></i> Save
                </button>

                <button type="button"
                        onclick="toggleEditRow(<?= $actor['id'] ?>)"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg
                               bg-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-400 transition">
                    <i class="pi pi-times"></i> Cancel
                </button>
            </div>

        </form>
        <?php return ob_get_clean();
    },

    // CLEAN ADD FORM
    'addLabel' => 'Add Actor',
    'addForm' => (function () {
        ob_start(); ?>
        <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="hidden" name="add_actor" value="1">

            <?php foreach ([
                               'first_name' => 'First Name',
                               'last_name' => 'Last Name',
                               'date_of_birth' => 'DOB'
                           ] as $field => $label): ?>
                <div class="flex flex-col gap-1">
                    <label><?= $label ?></label>
                    <input type="<?= $field === 'date_of_birth' ? 'date' : 'text' ?>"
                           name="<?= $field ?>" class="input-edit" required>
                </div>
            <?php endforeach; ?>

            <div class="flex flex-col gap-1">
                <label>Gender</label>
                <select name="gender" class="input-edit-select">
                    <?php foreach (['Male','Female','Other'] as $g): ?>
                        <option><?= $g ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="md:col-span-2 flex flex-col gap-1">
                <label>Description</label>
                <textarea name="description" class="input-edit-textarea"></textarea>
            </div>

            <div class="md:col-span-2 flex gap-4 mt-2">
                <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg
                               bg-green-500 text-white text-sm font-semibold hover:bg-green-600 transition">
                    <i class="pi pi-check"></i> Save
                </button>

                <button type="button" onclick="toggleAddForm_actorsTable()"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg
                               bg-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-400 transition">
                    <i class="pi pi-times"></i> Cancel
                </button>
            </div>
        </form>
        <?php return ob_get_clean();
    })(),
]);
?>
