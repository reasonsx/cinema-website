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

    return "<span class=\"px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap $class\">"
        . e($gender) .
        "</span>";
}

function formatDate($date): string {
    if (!$date || $date === '0000-00-00') return '—';
    return date('d M Y', strtotime($date));
}

renderTable([
    'id'        => 'actorsTable',
    'title'     => 'All Actors',
    'headers'   => ['ID', 'Name', 'DOB', 'Gender', 'Description'],
    'rows'      => $actors,
    'searchable'=> true,

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
                    class="btn-square bg-blue-600">
                <i class="pi pi-pencil"></i> Edit
            </button>

            <form method="post"
                  onsubmit="return confirm('Delete this actor?')"
                  class="flex items-center justify-center p-0 m-0 leading-none">
                <input type="hidden" name="delete_actor_id" value="<?= $actor['id'] ?>">

                <button type="submit" name="delete_actor"
                        class="btn-square bg-red-500">
                    <i class="pi pi-trash"></i> Delete
                </button>
            </form>

        </div>
        <?php return ob_get_clean();
    },

    'renderEditRow' => function ($actor) {
        ob_start(); ?>
        <form method="post" class="flex flex-col gap-6">

            <input type="hidden" name="actor_id" value="<?= $actor['id'] ?>">

            <!-- FIRST NAME -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">First Name</label>
                <input type="text"
                       name="first_name"
                       value="<?= e($actor['first_name']) ?>"
                       class="input-edit px-4 py-2 rounded-md">
            </div>

            <!-- LAST NAME -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Last Name</label>
                <input type="text"
                       name="last_name"
                       value="<?= e($actor['last_name']) ?>"
                       class="input-edit px-4 py-2 rounded-md">
            </div>

            <!-- DOB -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Date of Birth</label>
                <input type="date"
                       name="date_of_birth"
                       value="<?= e($actor['date_of_birth']) ?>"
                       class="input-edit px-4 py-2 rounded-md">
            </div>

            <!-- GENDER -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Gender</label>
                <select name="gender"
                        class="input-edit-select px-4 py-2 rounded-md">
                    <?php foreach (['Male','Female','Other'] as $g): ?>
                        <option <?= $actor['gender'] === $g ? 'selected' : '' ?>><?= $g ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- DESCRIPTION -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Description</label>
                <textarea name="description"
                          rows="5"
                          class="input-edit-textarea px-4 py-3 rounded-md leading-relaxed"><?= e($actor['description']) ?></textarea>
            </div>

            <!-- BUTTONS -->
            <div class="flex gap-4">
                <button type="submit"
                        name="edit_actor"
                        class="btn-square bg-green-600 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-check"></i> Save Changes
                </button>

                <button type="button"
                        onclick="toggleEditRow(<?= $actor['id'] ?>)"
                        class="btn-square bg-gray-300 text-gray-700 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-times"></i> Cancel
                </button>
            </div>

        </form>
        <?php return ob_get_clean();
    },

    'addLabel' => 'Add Actor',
    'addForm' => (function () {
        ob_start(); ?>
        <form method="post" class="flex flex-col gap-6">
            <input type="hidden" name="add_actor" value="1">

            <!-- FIRST NAME -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">First Name</label>
                <input type="text"
                       name="first_name"
                       class="input-edit px-4 py-2 rounded-md"
                       placeholder="Enter first name"
                       required>
            </div>

            <!-- LAST NAME -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Last Name</label>
                <input type="text"
                       name="last_name"
                       class="input-edit px-4 py-2 rounded-md"
                       placeholder="Enter last name"
                       required>
            </div>

            <!-- DOB -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Date of Birth</label>
                <input type="date"
                       name="date_of_birth"
                       class="input-edit px-4 py-2 rounded-md">
            </div>

            <!-- GENDER -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Gender</label>
                <select name="gender"
                        class="input-edit-select px-4 py-2 rounded-md">
                    <?php foreach (['Male','Female','Other'] as $g): ?>
                        <option><?= $g ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- DESCRIPTION -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Description</label>
                <textarea name="description"
                          rows="5"
                          class="input-edit-textarea px-4 py-3 rounded-md leading-relaxed"
                          placeholder="Short biography or actor details..."></textarea>
            </div>

            <!-- BUTTONS -->
            <div class="flex gap-4">
                <button type="submit"
                        class="btn-square bg-green-600 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-plus"></i> Add Actor
                </button>

                <button type="button"
                        onclick="toggleAddForm_actorsTable()"
                        class="btn-square bg-gray-300 text-gray-700 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-times"></i> Cancel
                </button>
            </div>

        </form>
        <?php return ob_get_clean();
    })(),
]);
?>
