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
            $user['isAdmin']
                ? '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 border border-green-300">Admin</span>'
                : '<span class="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-700 border border-gray-300">User</span>'
        ];
    },

    // ACTIONS
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

    // EDIT FORM
    'renderEditRow' => function ($user) {
        ob_start(); ?>
        <form method="post" class="flex flex-col gap-6">

            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

            <!-- First Name -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">First Name</label>
                <input type="text"
                       name="firstname"
                       value="<?= e($user['firstname']) ?>"
                       class="input-edit px-4 py-2 rounded-md"
                       required>
            </div>

            <!-- Last Name -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Last Name</label>
                <input type="text"
                       name="lastname"
                       value="<?= e($user['lastname']) ?>"
                       class="input-edit px-4 py-2 rounded-md"
                       required>
            </div>

            <!-- Email (locked) -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Email</label>
                <input type="email"
                       value="<?= e($user['email']) ?>"
                       disabled
                       class="px-4 py-2 rounded-md bg-gray-200 text-gray-600 cursor-not-allowed">
            </div>

            <!-- Admin checkbox -->
            <label class="flex items-center gap-2 text-gray-700 font-semibold">
                <input type="checkbox"
                       name="isAdmin"
                    <?= $user['isAdmin'] ? 'checked' : '' ?>
                       class="accent-[var(--primary)] w-4 h-4">
                Admin
            </label>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" name="edit_user"
                        class="btn-square bg-green-600 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-check"></i>
                    Save Changes
                </button>

                <button type="button"
                        onclick="toggleEditRow(<?= $user['id'] ?>)"
                        class="btn-square bg-gray-300 text-gray-700 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-times"></i>
                    Cancel
                </button>
            </div>

        </form>
        <?php return ob_get_clean();
    },

    // ADD USER FORM
    'addLabel' => 'Add User',
    'addForm' => (function () {
        ob_start(); ?>
        <form method="post" class="flex flex-col gap-6">

            <input type="hidden" name="add_user" value="1">

            <!-- First Name -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">First Name</label>
                <input type="text"
                       name="firstname"
                       class="input-edit px-4 py-2 rounded-md"
                       required>
            </div>

            <!-- Last Name -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Last Name</label>
                <input type="text"
                       name="lastname"
                       class="input-edit px-4 py-2 rounded-md"
                       required>
            </div>

            <!-- Email -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Email</label>
                <input type="email"
                       name="email"
                       class="input-edit px-4 py-2 rounded-md"
                       required>
            </div>

            <!-- Password -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Password</label>
                <input type="password"
                       name="password"
                       class="input-edit px-4 py-2 rounded-md"
                       required>
            </div>

            <!-- Admin checkbox -->
            <label class="flex items-center gap-2 text-gray-700 font-semibold">
                <input type="checkbox" name="isAdmin" class="accent-[var(--primary)] w-4 h-4">
                Admin
            </label>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit"
                        class="btn-square bg-green-600 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-plus"></i>
                    Add User
                </button>

                <button type="button"
                        onclick="toggleAddForm_usersTable()"
                        class="btn-square bg-gray-300 text-gray-700 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-times"></i>
                    Cancel
                </button>
            </div>

        </form>
        <?php return ob_get_clean();
    })(),
]);
