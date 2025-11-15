<?php
require_once __DIR__ . '/../../components/table.php';
?>
<?php
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
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
            e($actor['date_of_birth']),
            e($actor['gender']),
            e($actor['description']),
        ];
    },

    // NEW INLINE EDIT FORM
    'renderEditRow' => function ($actor) {
        ob_start(); ?>
        <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <input type="hidden" name="actor_id" value="<?= $actor['id'] ?>">

            <!-- First Name -->
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600 font-medium">First Name</label>
                <input type="text" name="first_name"
                       value="<?= e($actor['first_name']) ?>"
                       class="input-edit">
            </div>

            <!-- Last Name -->
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600 font-medium">Last Name</label>
                <input type="text" name="last_name"
                       value="<?= e($actor['last_name']) ?>"
                       class="input-edit">
            </div>

            <!-- Date of Birth -->
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600 font-medium">Date of Birth</label>
                <input type="date" name="date_of_birth"
                       value="<?= e($actor['date_of_birth']) ?>"
                       class="input-edit">
            </div>

            <!-- Gender -->
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600 font-medium">Gender</label>
                <select name="gender" class="input-edit-select">
                    <option <?= $actor['gender']==='Male'?'selected':'' ?>>Male</option>
                    <option <?= $actor['gender']==='Female'?'selected':'' ?>>Female</option>
                    <option <?= $actor['gender']==='Other'?'selected':'' ?>>Other</option>
                </select>
            </div>

            <!-- Description (full width) -->
            <div class="md:col-span-2 flex flex-col gap-1">
                <label class="text-sm text-gray-600 font-medium">Description</label>
                <textarea name="description" rows="3"
                          class="input-edit-textarea"><?= e($actor['description']) ?></textarea>
            </div>

            <div class="md:col-span-2 flex gap-4 mt-2">

                <!-- SAVE (Green primary) -->
                <button type="submit" name="edit_actor"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg
                   font-semibold text-sm
                   bg-green-500 text-white
                   hover:bg-green-600 transition">
                    Save
                </button>

                <!-- CANCEL (Neutral gray) -->
                <button type="button"
                        onclick="toggleEditRow(<?= $actor['id'] ?>)"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg
                   font-semibold text-sm
                   bg-gray-300 text-gray-700
                   hover:bg-gray-400 transition">
                    Cancel
                </button>

            </div>


        </form>
        <?php
        return ob_get_clean();
    }

]);
?>
