<section class="mb-10">
    <h2 class="text-5xl font-[Limelight] text-[var(--primary)] mb-6">All Users</h2>

    <!-- Add User Form -->
    <details class="mb-8">
        <summary class="cursor-pointer inline-block bg-[var(--primary)] text-[var(--white)] px-6 py-3 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
            Add New User
        </summary>
        <form method="post" class="flex flex-col gap-4 mt-4">
            <input type="text" name="firstname" placeholder="First Name" required
                   class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
            <input type="text" name="lastname" placeholder="Last Name" required
                   class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
            <input type="email" name="email" placeholder="Email" required
                   class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
            <input type="password" name="password" placeholder="Password" required
                   class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
            <label class="flex items-center gap-2 text-black font-semibold">
                <input type="checkbox" name="isAdmin" class="accent-[var(--primary)]"> Admin
            </label>
            <button type="submit" name="add_user"
                    class="bg-[var(--primary)] text-[var(--white)] px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
                Add User
            </button>
        </form>
    </details>

    <?php if (!empty($users)) : ?>
        <div class="overflow-x-auto">
            <table class="min-w-full border-t-4 border-[var(--primary)] text-black">
                <thead class="font-[Limelight] text-[var(--primary)] text-lg">
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">First Name</th>
                        <th class="px-4 py-2 text-left">Last Name</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Admin</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td class="px-4 py-2"><?= htmlspecialchars($user['id']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($user['firstname']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($user['lastname']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="px-4 py-2"><?= $user['isAdmin'] ? 'Yes' : 'No' ?></td>
                            <td class="px-4 py-2">
                                <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="delete_user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" name="delete_user"
                                            class="bg-[var(--primary)] text-[var(--white)] px-3 py-1 rounded shadow hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-sm">
                                        Delete
                                    </button>
                                </form>

                                <button type="button" onclick="toggleUserEditForm(<?= $user['id'] ?>)"
                                       class="bg-[var(--primary)] text-[var(--white)] px-3 py-1 rounded shadow hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-sm">
                                Edit
                                </button>
                            </td>
                        </tr>

                        <!-- ✅ Inline Edit Form -->
                        <tr id="edit-user-form-<?= $user['id'] ?>" class="hidden bg-gray-50">
                            <td colspan="6" class="p-6 border-t-4 border-[var(--primary)]">
                                <h3 class="text-3xl font-[Limelight] text-[var(--primary)] mb-4">Edit User</h3>

                                <form method="post" class="flex flex-col gap-4">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

                                    <input type="text" name="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" required
                                           class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
                                    <input type="text" name="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" required
                                           class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
                                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled
                                           class="border-b-2 border-gray-400 bg-gray-100 text-gray-600 px-2 py-1 cursor-not-allowed">
                                    
                                    <label class="flex items-center gap-2 text-black font-semibold">
                                        <input type="checkbox" name="isAdmin" class="accent-[var(--primary)]" <?= $user['isAdmin'] ? 'checked' : '' ?>> Admin
                                    </label>

                                    <div class="flex gap-4 mt-4">
                                        <button type="submit" name="edit_user"
                                                class="bg-[var(--primary)] text-[var(--white)] px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
                                            Save Changes
                                        </button>

                                        <button type="button" onclick="toggleUserEditForm(<?= $user['id'] ?>)"
                                                class="bg-gray-400 text-white px-6 py-2 rounded-lg shadow-md hover:bg-gray-500 transition-colors duration-300 font-[Limelight] text-lg">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <p class="text-black font-semibold mt-4">No users found.</p>
    <?php endif; ?>
</section>

<script>
function toggleUserEditForm(userId) {
    const form = document.getElementById(`edit-user-form-${userId}`);
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>
