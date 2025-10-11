<section class="mb-10">
    <h2 class="text-5xl font-[Limelight] text-[var(--primary)] mb-6">All Actors</h2>

    <!-- Add Actor Form -->
    <details class="mb-8">
        <summary class="cursor-pointer inline-block bg-[var(--primary)] text-[var(--white)] px-6 py-3 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
            Add New Actor
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
            <button type="submit" name="add_actor"
                    class="bg-[var(--primary)] text-[var(--white)] px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
                Add Actor
            </button>
        </form>
    </details>

    <!-- Actors Table -->
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
                <?php foreach ($actors as $actor): ?>
                    <tr class="hover:text-black transition-colors duration-300">
                        <td class="px-4 py-2"><?= $actor['id'] ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($actor['first_name'].' '.$actor['last_name']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($actor['date_of_birth']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($actor['gender']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($actor['description']) ?></td>
                        <td class="px-4 py-2">
                            <form method="post" style="display:inline;" 
                                onsubmit="return confirm('Are you sure you want to delete this actor?');">
                                <input type="hidden" name="delete_actor_id" value="<?= $actor['id'] ?>">
                                <button type="submit" name="delete_actor"
                                        class="bg-[var(--primary)] text-[var(--white)] px-3 py-1 rounded shadow hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-sm">
                                    Delete
                                </button>
                            </form>
                            <button type="button" onclick="toggleEditActorForm(<?= $actor['id'] ?>)"
                                    class="bg-[var(--primary)] text-[var(--white)] px-3 py-1 rounded shadow hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-sm">
                                Edit
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Actor Row -->
                    <tr id="edit-actor-<?= $actor['id'] ?>" class="hidden bg-gray-50">
                        <td colspan="6" class="p-6 border-t-4 border-[var(--primary)]">
                            <h3 class="text-3xl font-[Limelight] text-[var(--primary)] mb-4">Edit Actor</h3>
                            <form method="post" class="flex flex-col gap-4">
                                <input type="hidden" name="actor_id" value="<?= $actor['id'] ?>">

                                <input type="text" name="first_name" value="<?= htmlspecialchars($actor['first_name']) ?>" required
                                       class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">

                                <input type="text" name="last_name" value="<?= htmlspecialchars($actor['last_name']) ?>" required
                                       class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">

                                <input type="date" name="date_of_birth" value="<?= htmlspecialchars($actor['date_of_birth']) ?>"
                                       class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">

                                <select name="gender"
                                        class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
                                    <option value="Male" <?= $actor['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= $actor['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= $actor['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>

                                <textarea name="description" rows="3"
                                          class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]"><?= htmlspecialchars($actor['description']) ?></textarea>

                                <div class="flex gap-4 mt-4">
                                    <button type="submit" name="edit_actor"
                                            class="bg-[var(--primary)] text-[var(--white)] px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
                                        Save Changes
                                    </button>

                                    <button type="button" onclick="toggleEditActorForm(<?= $actor['id'] ?>)"
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
</section>

<script>
function toggleEditActorForm(actorId) {
    const form = document.getElementById(`edit-actor-${actorId}`);
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>