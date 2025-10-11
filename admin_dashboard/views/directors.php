<section class="mb-10">
    <h2 class="text-5xl font-[Limelight] text-[var(--primary)] mb-6">All Directors</h2>

    <!-- Add Director Form -->
    <details class="mb-8">
        <summary class="cursor-pointer inline-block bg-[var(--primary)] text-[var(--white)] px-6 py-3 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
            Add New Director
        </summary>
        <form method="post" class="flex flex-col gap-4 mt-4">
            <input type="text" name="first_name" placeholder="First Name" required
                   class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]">
            <input type="text" name="last_name" placeholder="Last Name" required
                   class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]">
            <input type="date" name="date_of_birth"
                   class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
            <select name="gender"
                    class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <textarea name="description" placeholder="Description" rows="3"
                      class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]"></textarea>
            <button type="submit" name="add_director"
                    class="bg-[var(--primary)] text-[var(--white)] px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
                Add Director
            </button>
        </form>
    </details>

    <!-- Directors Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full border-t-4 border-[var(--primary)] text-black">
            <thead class="font-[Limelight] text-[var(--primary)] text-lg">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">DOB</th>
                    <th class="px-4 py-2 text-left">Gender</th>
                    <th class="px-4 py-2 text-left">Description</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($directors as $director): ?>
                    <tr class="hover:text-black transition-colors duration-300">
                        <td class="px-4 py-2"><?= $director['id'] ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($director['first_name'].' '.$director['last_name']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($director['date_of_birth']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($director['gender']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($director['description']) ?></td>
                        <td class="px-4 py-2 space-x-2">
                            <form method="post" style="display:inline;" 
                                onsubmit="return confirm('Are you sure you want to delete this director?');">
                                <input type="hidden" name="delete_director_id" value="<?= $director['id'] ?>">
                                <button type="submit" name="delete_director"
                                        class="bg-[var(--primary)] text-[var(--white)] px-3 py-1 rounded shadow hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-sm">
                                    Delete
                                </button>
                            </form>
                            <button type="button" 
                                onclick="toggleEditForm('<?= $director['id'] ?>')" 
                                class="bg-[var(--primary)] text-[var(--white)] px-3 py-1 rounded shadow hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-sm">
                                Edit
                            </button>
                        </td>
                    </tr>

                    <!-- Hidden Edit Form -->
                    <tr id="edit-form-<?= $director['id'] ?>" class="hidden">
                        <td colspan="6" class="border-t-2 border-[var(--primary)] p-4">
                            <form method="post" class="flex flex-col gap-3">
                                <input type="hidden" name="id" value="<?= $director['id'] ?>">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <input type="text" name="first_name" value="<?= htmlspecialchars($director['first_name']) ?>" required
                                           class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
                                    <input type="text" name="last_name" value="<?= htmlspecialchars($director['last_name']) ?>" required
                                           class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <input type="date" name="date_of_birth" value="<?= htmlspecialchars($director['date_of_birth']) ?>"
                                           class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
                                    <select name="gender"
                                            class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
                                        <option value="Male" <?= $director['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= $director['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                                        <option value="Other" <?= $director['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                                <textarea name="description" rows="3"
                                          class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]"><?= htmlspecialchars($director['description']) ?></textarea>

                                <div class="flex gap-3 mt-3">
                                    <button type="submit" name="edit_director"
                                            class="bg-[var(--primary)] text-[var(--white)] px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
                                        Save Changes
                                    </button>
                                    <button type="button" onclick="toggleEditForm('<?= $director['id'] ?>')"
                                            class="bg-gray-300 text-black px-6 py-2 rounded-lg shadow-md hover:bg-gray-400 transition-colors duration-300 font-[Limelight] text-lg">
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
</section>
<script>
function toggleEditForm(id) {
    document.querySelectorAll('[id^="edit-form-"]').forEach(f => {
        if (f.id !== `edit-form-${id}`) f.classList.add('hidden');
    });

    const form = document.getElementById(`edit-form-${id}`);
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>
