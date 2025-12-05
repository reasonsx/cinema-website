<?php
require_once __DIR__ . '/../../components/table.php';
require_once __DIR__ . '/../../../shared/helpers.php';

function genderBadge(string $gender): string {
    $colors = [
        'Male'   => 'bg-blue-100 text-blue-700',
        'Female' => 'bg-pink-100 text-pink-700',
        'Other'  => 'bg-gray-200 text-gray-700',
    ];
    $class = $colors[$gender] ?? 'bg-gray-200 text-gray-700';
    return "<span class=\"px-3 py-1 rounded-full text-xs font-semibold $class\">" . e($gender) . "</span>";
}

function formatDate($date): string {
    if (!$date || $date === '0000-00-00') return '—';
    return date('d M Y', strtotime($date));
}

renderTable([
    'id'        => 'directorsTable',
    'title'     => 'All Directors',
    'searchable'=> true,
    'addLabel'  => 'Add Director',

    // ADD FORM
    'addForm' => (function () {
        ob_start(); ?>
        <form method="post" class="flex flex-col gap-6">
            <input type="hidden" name="add_director" value="1">

            <!-- First Name -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">First Name</label>
                <input type="text"
                       name="first_name"
                       class="input-edit px-4 py-2 rounded-md"
                       placeholder="First Name"
                       required>
            </div>

            <!-- Last Name -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Last Name</label>
                <input type="text"
                       name="last_name"
                       class="input-edit px-4 py-2 rounded-md"
                       placeholder="Last Name"
                       required>
            </div>

            <!-- DOB -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Date of Birth</label>
                <input type="date"
                       name="date_of_birth"
                       class="input-edit px-4 py-2 rounded-md">
            </div>

            <!-- Gender -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Gender</label>
                <select name="gender"
                        class="input-edit-select px-4 py-2 rounded-md">
                    <?php foreach (['Male','Female','Other'] as $g): ?>
                        <option><?= $g ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Description -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Description</label>
                <textarea name="description"
                          rows="5"
                          class="input-edit-textarea px-4 py-3 rounded-md leading-relaxed"
                          placeholder="Short description..."></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit"
                        class="btn-square bg-green-600 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-plus"></i>
                    Add Director
                </button>

                <button type="button"
                        onclick="toggleAddForm_directorsTable()"
                        class="btn-square bg-gray-300 text-gray-700 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-times"></i>
                    Cancel
                </button>
            </div>
        </form>
        <?php return ob_get_clean();
    })(),

    // HEADERS
    'headers' => ['ID', 'Name', 'DOB', 'Gender', 'Description'],

    // ROWS
    'rows' => $directors,

    'renderRow' => function ($d) {
        return [
            $d['id'],
            e($d['first_name'] . ' ' . $d['last_name']),
            '<span class="whitespace-nowrap">' . formatDate($d['date_of_birth']) . '</span>',
            genderBadge($d['gender']),
            e($d['description']),
        ];
    },

    // ACTIONS
    'actions' => function ($d) {
        ob_start(); ?>
        <div class="flex items-center gap-2">

            <button onclick="toggleEditRow(<?= $d['id'] ?>)"
                    class="btn-square bg-blue-600">
                <i class="pi pi-pencil"></i> Edit
            </button>

            <form method="post"
                  onsubmit="return confirm('Delete this director?')"
                  class="m-0 p-0">
                <input type="hidden" name="delete_director_id" value="<?= $d['id'] ?>">
                <button type="submit" name="delete_director"
                        class="btn-square bg-red-500">
                    <i class="pi pi-trash"></i> Delete
                </button>
            </form>

        </div>
        <?php return ob_get_clean();
    },

    // EDIT FORM
    'renderEditRow' => function ($d) {
        ob_start(); ?>
        <form method="post" class="flex flex-col gap-6">
            <input type="hidden" name="id" value="<?= $d['id'] ?>">

            <!-- First Name -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">First Name</label>
                <input type="text"
                       name="first_name"
                       value="<?= e($d['first_name']) ?>"
                       class="input-edit px-4 py-2 rounded-md">
            </div>

            <!-- Last Name -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Last Name</label>
                <input type="text"
                       name="last_name"
                       value="<?= e($d['last_name']) ?>"
                       class="input-edit px-4 py-2 rounded-md">
            </div>

            <!-- DOB -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Date of Birth</label>
                <input type="date"
                       name="date_of_birth"
                       value="<?= e($d['date_of_birth']) ?>"
                       class="input-edit px-4 py-2 rounded-md">
            </div>

            <!-- Gender -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Gender</label>
                <select name="gender" class="input-edit-select px-4 py-2 rounded-md">
                    <?php foreach (['Male','Female','Other'] as $g): ?>
                        <option <?= $d['gender'] === $g ? 'selected' : '' ?>><?= $g ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Description -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Description</label>
                <textarea name="description"
                          rows="5"
                          class="input-edit-textarea px-4 py-3 rounded-md leading-relaxed"><?= e($d['description']) ?></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit"
                        name="edit_director"
                        class="btn-square bg-green-600 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-check"></i>
                    Save Changes
                </button>

                <button type="button"
                        onclick="toggleEditRow(<?= $d['id'] ?>)"
                        class="btn-square bg-gray-300 text-gray-700 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-times"></i>
                    Cancel
                </button>
            </div>
        </form>
        <?php return ob_get_clean();
    },
]);
?>
