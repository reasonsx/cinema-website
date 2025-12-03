<?php
require_once __DIR__ . '/../../components/table.php';
require_once __DIR__ . '/../../../shared/helpers.php';

renderTable([
    'id'        => 'usersTable',
    'title'     => 'All Users',
    'headers'   => ['ID', 'First Name', 'Last Name', 'Email', 'Role'],
    'rows'      => $users,
    'searchable'=> true,
    'renderRow' => function ($user) {
        return [
            $user['id'],
            e($user['firstname']),
            e($user['lastname']),
            e($user['email']),
            $user['isAdmin'] ?
                '<span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 border border-green-300">Admin</span>' :
                '<span class="px-2 py-0.5 text-xs rounded-full bg-gray-200 text-gray-700 border border-gray-300">User</span>'
        ];
    },

    // Action buttons
    'actions' => function ($user) {
        ob_start(); ?>
        <div class="flex items-center gap-2">

            <button onclick="toggleEditRow(<?= $user['id'] ?>)"
                    class="btn-square bg-blue-600">
                <i class="pi pi-pencil"></i> Edit
            </button>

            <form method="post" onsubmit="return confirm('Delete this user?')" class="m-0 p-0">
                <input type="hidden" name="delete_user_id" value="<?= $user['id'] ?>">
                <button type="submit" name="delete_user"
                        class="btn-square bg-red-500">
                    <i class="pi pi-trash"></i> Delete
                </button>
            </form>

        </div>
        <?php return ob_get_clean();
    },

    // Inline edit row
    'renderEditRow' => function ($user) {
        ob_start(); ?>
        <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600">First Name</label>
                <input type="text" name="firstname" value="<?= e($user['firstname']) ?>" class="input-edit">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600">Last Name</label>
                <input type="text" name="lastname" value="<?= e($user['lastname']) ?>" class="input-edit">
            </div>

            <div class="flex flex-col gap-1 md:col-span-2">
                <label class="text-sm text-gray-600">Email</label>
                <input type="email" disabled value="<?= e($user['email']) ?>"
                       class="input-edit bg-gray-200 cursor-not-allowed">
            </div>

            <label class="flex items-center gap-2 text-gray-700 font-medium">
                <input type="checkbox" name="isAdmin" <?= $user['isAdmin'] ? 'checked' : '' ?> class="accent-[var(--primary)]">
                Admin
            </label>

            <div class="md:col-span-2 flex gap-4 mt-2">
                <button type="submit" name="edit_user"
                        class="btn-square bg-green-600">
                    <i class="pi pi-check"></i> Save Changes
                </button>

                <button type="button"
                        onclick="toggleEditRow(<?= $user['id'] ?>)"
                        class="btn-square bg-gray-300 text-gray-700"><i class="pi pi-times"></i>Cancel
                </button>
            </div>
        </form>
        <?php return ob_get_clean();
    },

    // Add user button + form
    'addLabel' => 'Add User',
    'addForm' => (function () {
        ob_start(); ?>
        <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <input type="hidden" name="add_user" value="1">

            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600">First Name</label>
                <input type="text" name="firstname" class="input-edit" required>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600">Last Name</label>
                <input type="text" name="lastname" class="input-edit" required>
            </div>

            <div class="flex flex-col gap-1 md:col-span-2">
                <label class="text-sm text-gray-600">Email</label>
                <input type="email" name="email" class="input-edit" required>
            </div>

            <div class="flex flex-col gap-1 md:col-span-2">
                <label class="text-sm text-gray-600">Password</label>
                <input type="password" name="password" class="input-edit" required>
            </div>

            <label class="flex items-center gap-2 text-gray-700 font-medium">
                <input type="checkbox" name="isAdmin" class="accent-[var(--primary)]">
                Admin
            </label>

            <div class="md:col-span-2 flex gap-4 mt-2">
                <button type="submit"
                        class="btn-square bg-green-600">
                    <i class="pi pi-plus"></i> Add User
                </button>

                <button type="button" onclick="toggleAddForm_usersTable()"
                        class="btn-square bg-gray-300 text-gray-700"><i class="pi pi-times"></i>Cancel
                </button>
            </div>

        </form>
        <?php return ob_get_clean();
    })(),
]);
