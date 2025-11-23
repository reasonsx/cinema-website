<?php
require_once __DIR__ . '/../../components/table.php';
require_once __DIR__ . '/../../../include/helpers.php';

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
    'id'        => 'directorsTable',
    'title'     => 'All Directors',
    'searchable'=> true,
    'addLabel'  => 'Add Director',

    // ADD FORM
    'addForm' => (function () {
        ob_start(); ?>
        <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="hidden" name="add_director" value="1">
            <!-- First Name -->
            <div class="flex flex-col gap-1">
                <label>First Name</label>
                <input
                        type="text"
                        name="first_name"
                        placeholder="First Name"
                        class="block h-9 w-full bg-transparent border border-gray-600 rounded-md
               px-2 text-sm placeholder:text-gray-400
               focus:outline-none focus:border-gray-300"
                        required
                >
            </div>

            <!-- Last Name -->
            <div class="flex flex-col gap-1">
                <label>Last Name</label>
                <input
                        type="text"
                        name="last_name"
                        placeholder="Last Name"
                        class="block h-9 w-full bg-transparent border border-gray-600 rounded-md
               px-2 text-sm placeholder:text-gray-400
               focus:outline-none focus:border-gray-300"
                        required
                >
            </div>

            <!-- DOB -->
            <div class="flex flex-col gap-1">
                <label>Date of Birth</label>
                <input
                        type="date"
                        name="date_of_birth"
                        class="block h-9 w-full bg-transparent border border-gray-600 rounded-md
               px-2 text-sm placeholder:text-gray-400
               focus:outline-none focus:border-gray-300"
                >
            </div>

            <!-- Gender -->
            <div class="flex flex-col gap-1">
                <label>Gender</label>
                <select
                        name="gender"
                        class="block h-9 w-full bg-transparent border border-gray-600 rounded-md
               px-2 text-sm
               focus:outline-none focus:border-gray-300"
                >
                    <?php foreach (['Male','Female','Other'] as $g): ?>
                        <option class="text-black"><?= $g ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Description -->
            <div class="md:col-span-2 flex flex-col gap-1">
                <label>Description</label>
                <textarea
                        name="description"
                        placeholder="Short description..."
                        class="w-full min-h-[80px] bg-transparent border border-gray-600 rounded-md
               px-2 py-1.5 text-sm placeholder:text-gray-400
               focus:outline-none focus:border-gray-300"
                ></textarea>
            </div>


            <!-- Buttons -->
            <div class="md:col-span-2 flex gap-4 mt-2">
                <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg bg-green-500 text-white text-sm font-semibold hover:bg-green-600 transition">
                    <i class="pi pi-check"></i> Save
                </button>

                <button type="button" onclick="toggleAddForm_directorsTable()"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-400 transition">
                    <i class="pi pi-times"></i> Cancel
                </button>
            </div>
        </form>
        <?php return ob_get_clean();
    })(),

    // TABLE HEADERS
    'headers' => ['ID', 'Name', 'DOB', 'Gender', 'Description'],

    // TABLE ROWS
    'rows' => $directors,

    // HOW TO DISPLAY EACH ROW
    'renderRow' => function ($d) {
        return [
            $d['id'],
            e($d['first_name'] . ' ' . $d['last_name']),
            '<span class="whitespace-nowrap">' . formatDate($d['date_of_birth']) . '</span>',
            genderBadge($d['gender']),
            e($d['description']),
        ];
    },

    // ACTION BUTTONS
    'actions' => function ($d) {
        ob_start(); ?>
        <div class="flex items-center gap-2">

            <!-- EDIT -->
            <button onclick="toggleEditRow(<?= $d['id'] ?>)"
                    class="flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                <i class="pi pi-pencil"></i> Edit
            </button>

            <!-- DELETE -->
            <form method="post" class="p-0 m-0 leading-none"
                  onsubmit="return confirm('Delete this director?')">
                <input type="hidden" name="delete_director_id" value="<?= $d['id'] ?>">
                <button type="submit" name="delete_director"
                        class="flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-red-500 text-white text-sm font-semibold hover:bg-red-600 transition">
                    <i class="pi pi-trash"></i> Delete
                </button>
            </form>

        </div>
        <?php return ob_get_clean();
    },

    // INLINE EDIT FORM
    'renderEditRow' => function ($d) {
        ob_start(); ?>
        <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="hidden" name="id" value="<?= $d['id'] ?>">

            <!-- First Name -->
            <div class="flex flex-col gap-1">
                <label>First Name</label>
                <input type="text" name="first_name" value="<?= e($d['first_name']) ?>" class="input-edit">
            </div>

            <!-- Last Name -->
            <div class="flex flex-col gap-1">
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?= e($d['last_name']) ?>" class="input-edit">
            </div>

            <!-- DOB -->
            <div class="flex flex-col gap-1">
                <label>DOB</label>
                <input type="date" name="date_of_birth" value="<?= e($d['date_of_birth']) ?>" class="input-edit">
            </div>

            <!-- Gender -->
            <div class="flex flex-col gap-1">
                <label>Gender</label>
                <select name="gender" class="input-edit-select">
                    <?php foreach (['Male','Female','Other'] as $g): ?>
                        <option <?= $d['gender']===$g ? 'selected' : '' ?>><?= $g ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Description -->
            <div class="md:col-span-2 flex flex-col gap-1">
                <label>Description</label>
                <textarea name="description" class="input-edit-textarea"><?= e($d['description']) ?></textarea>
            </div>

            <!-- Buttons -->
            <div class="md:col-span-2 flex gap-4 mt-2">
                <button type="submit" name="edit_director"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg bg-green-500 text-white text-sm font-semibold hover:bg-green-600 transition">
                    <i class="pi pi-check"></i> Save
                </button>

                <button type="button" onclick="toggleEditRow(<?= $d['id'] ?>)"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-400 transition">
                    <i class="pi pi-times"></i> Cancel
                </button>
            </div>
        </form>
        <?php return ob_get_clean();
    },
]);
?>
