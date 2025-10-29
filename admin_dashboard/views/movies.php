<section class="mb-10">
    <h2 class="text-5xl font-[Limelight] text-[var(--primary)] mb-6">All Movies</h2>

    <!-- Add Movie Form -->
    <details class="mb-8">
        <summary class="cursor-pointer inline-block bg-[var(--primary)] text-[var(--white)] px-6 py-3 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 text-lg">
            Add New Movie
        </summary>
        <form method="post" enctype="multipart/form-data" class="flex flex-col gap-4 mt-4">
            <input type="text" name="title" placeholder="Title" required
                   class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]">
            <input type="number" name="release_year" placeholder="Release Year" required
                   class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]">
            <input type="text" name="rating" placeholder="Rating" required
                   class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]">
            <input type="number" name="length" placeholder="Length (min)" required
                   class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]">
            <textarea name="description" placeholder="Description" rows="3" required
                      class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]"></textarea>
            <textarea name="trailer_url" placeholder="Trailer URL" rows="3" required
                      class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]"></textarea>
            <input type="file" name="poster" accept="image/*"
                   class="border-b-2 border-[var(--primary)] bg-transparent px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">

            <!-- Actors -->
            <label class="text-black font-semibold">Actors:</label>
            <div id="actors-container" class="flex flex-wrap gap-2">
                <?php foreach ($allActors as $actor): ?>
                    <div class="actor-btn cursor-pointer px-3 py-1 border-2 border-[var(--primary)] rounded-lg hover:text-black transition-colors duration-300"
                         data-id="<?= $actor['id'] ?>">
                        <?= htmlspecialchars($actor['first_name'].' '.$actor['last_name']) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Directors -->
            <label class="text-black font-semibold">Directors:</label>
            <div id="directors-container" class="flex flex-wrap gap-2">
                <?php foreach ($allDirectors as $director): ?>
                    <div class="director-btn cursor-pointer px-3 py-1 border-2 border-[var(--primary)] rounded-lg hover:text-black transition-colors duration-300"
                         data-id="<?= $director['id'] ?>">
                        <?= htmlspecialchars($director['first_name'].' '.$director['last_name']) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" name="add_movie"
                    class="bg-[var(--primary)] text-[var(--white)] px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 text-lg mt-4">
                Add Movie
            </button>
        </form>
    </details>

    <!-- Movies Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full border-t-4 border-[var(--primary)] bg-white shadow-lg rounded-2xl">
            <thead class="bg-[var(--primary)] text-white text-lg font-[Limelight]">
            <tr>
                <th class="px-4 py-3 text-left">ID</th>
                <th class="px-4 py-3 text-left">Poster</th>
                <th class="px-4 py-3 text-left">Title</th>
                <th class="px-4 py-3 text-left">Year</th>
                <th class="px-4 py-3 text-left">Rating</th>
                <th class="px-4 py-3 text-left">Length</th>
                <th class="px-4 py-3 text-left">Description</th>
                <th class="px-4 py-3 text-left">Trailer URL</th>
                <th class="px-4 py-3 text-left">Actors</th>
                <th class="px-4 py-3 text-left">Directors</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            <?php foreach ($movies as $movie): ?>
                <tr class="hover:bg-gray-50 transition-colors duration-300">
                    <td class="px-4 py-3"><?= $movie['id'] ?></td>
                    <td class="px-4 py-3">
                        <?php if($movie['poster']): ?>
                            <img src="<?= $movie['poster'] ?>" width="60" class="rounded-lg">
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 font-semibold"><?= htmlspecialchars($movie['title']) ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars($movie['release_year']) ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars($movie['rating']) ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars($movie['length']) ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars($movie['description']) ?></td>
                    <td class="px-4 py-3">
                        <?php if (!empty($movie['trailer_url'])): ?>
                            <a href="<?= htmlspecialchars($movie['trailer_url']) ?>" target="_blank"
                               class="text-blue-600 hover:underline">[URL]</a>
                        <?php else: ?>
                            <span class="text-gray-400 italic">None</span>
                        <?php endif; ?>
                    </td>

                    <td class="px-4 py-3">
                        <?php
                        $stmt = $db->prepare("SELECT a.first_name, a.last_name FROM actors a JOIN actorAppearIn aa ON a.id=aa.actor_id WHERE aa.movie_id=?");
                        $stmt->execute([$movie['id']]);
                        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $a) {
                            echo htmlspecialchars($a['first_name'].' '.$a['last_name']).'<br>';
                        }
                        ?>
                    </td>
                    <td class="px-4 py-3">
                        <?php
                        $stmt = $db->prepare("SELECT d.first_name, d.last_name FROM directors d JOIN directorDirects dd ON d.id=dd.director_id WHERE dd.movie_id=?");
                        $stmt->execute([$movie['id']]);
                        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $d) {
                            echo htmlspecialchars($d['first_name'].' '.$d['last_name']).'<br>';
                        }
                        ?>
                    </td>
                    <td class="px-4 py-3 flex flex-col gap-2">
                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this movie?');">
                            <input type="hidden" name="delete_movie_id" value="<?= $movie['id'] ?>">
                            <button type="submit"
                                    class="bg-red-500 text-white px-4 py-2 rounded-xl hover:bg-red-600 transition">Delete</button>
                        </form>
                        <button type="button" onclick="toggleEditForm(<?= $movie['id'] ?>)"
                                class="bg-[var(--primary)] text-white px-4 py-2 rounded-xl hover:bg-[var(--secondary)] transition">
                            Edit
                        </button>
                    </td>
                </tr>

                    <!-- Edit Form Row -->
                    <tr id="edit-form-<?= $movie['id'] ?>" class="hidden bg-gray-50">
                        <td colspan="10" class="p-6 border-t-4 border-[var(--primary)]">
                            <h3 class="text-3x text-[var(--primary)] mb-4">Edit Movie</h3>

                            <?php
                                // Pre-fill current actors
                                $stmt = $db->prepare("SELECT actor_id FROM actorAppearIn WHERE movie_id = ?");
                                $stmt->execute([$movie['id']]);
                                $selectedActorIds = implode(',', $stmt->fetchAll(PDO::FETCH_COLUMN));

                                // Pre-fill current directors
                                $stmt = $db->prepare("SELECT director_id FROM directorDirects WHERE movie_id = ?");
                                $stmt->execute([$movie['id']]);
                                $selectedDirectorIds = implode(',', $stmt->fetchAll(PDO::FETCH_COLUMN));
                            ?>

                            <form method="post" enctype="multipart/form-data" class="flex flex-col gap-4">
                                <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">

                                <label class="text-black font-semibold">Title:</label>
                                <input type="text" name="title" value="<?= htmlspecialchars($movie['title']) ?>" required
                                       class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">

                                <label class="text-black font-semibold">Release year:</label>
                                <input type="number" name="release_year" value="<?= htmlspecialchars($movie['release_year']) ?>" required
                                       class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">

                                <label class="text-black font-semibold">Rating:</label>
                                <input type="text" name="rating" value="<?= htmlspecialchars($movie['rating']) ?>" required
                                       class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">

                                <label class="text-black font-semibold">Length:</label>
                                <input type="number" name="length" value="<?= htmlspecialchars($movie['length']) ?>" required
                                       class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">

                                <label class="text-black font-semibold">Description:</label>
                                <textarea name="description" rows="3" required
                                          class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]"><?= htmlspecialchars($movie['description']) ?></textarea>

                                <label class="text-black font-semibold">Trailer URL:</label>
                                <textarea name="trailer_url" rows="3"
                                          class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]"><?= htmlspecialchars($movie['trailer_url'] ?? '') ?>
</textarea>

                                <label class="text-black font-semibold">Poster:</label>
                                <input type="file" name="poster" accept="image/*"
                                       class="border-b-2 border-[var(--primary)] bg-transparent px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">

                                <!-- Actors -->
                                <label class="text-black font-semibold">Actors:</label>
                                <input type="hidden" name="actors" id="edit-actors-<?= $movie['id'] ?>" value="<?= $selectedActorIds ?>">
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($allActors as $actor): ?>
                                        <?php
                                            $isSelected = in_array($actor['id'], explode(',', $selectedActorIds));
                                        ?>
                                        <div class="actor-btn cursor-pointer px-3 py-1 border-2 rounded-lg transition-colors duration-300
                                                    <?= $isSelected ? 'border-[var(--secondary)] bg-[var(--secondary)] text-white' : 'border-[var(--primary)] hover:text-black' ?>"
                                             data-id="<?= $actor['id'] ?>">
                                            <?= htmlspecialchars($actor['first_name'].' '.$actor['last_name']) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Directors -->
                                <label class="text-black font-semibold">Directors:</label>
                                <input type="hidden" name="directors" id="edit-directors-<?= $movie['id'] ?>" value="<?= $selectedDirectorIds ?>">
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($allDirectors as $director): ?>
                                        <?php
                                            $isSelected = in_array($director['id'], explode(',', $selectedDirectorIds));
                                        ?>
                                        <div class="director-btn cursor-pointer px-3 py-1 border-2 rounded-lg transition-colors duration-300
                                                    <?= $isSelected ? 'border-[var(--secondary)] bg-[var(--secondary)] text-white' : 'border-[var(--primary)] hover:text-black' ?>"
                                             data-id="<?= $director['id'] ?>">
                                            <?= htmlspecialchars($director['first_name'].' '.$director['last_name']) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="flex gap-4 mt-4">
                                    <button type="submit" name="edit_movie"
                                            class="bg-[var(--primary)] text-[var(--white)] px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 text-lg">
                                        Save Changes
                                    </button>

                                    <button type="button" onclick="toggleEditForm(<?= $movie['id'] ?>)"
                                            class="bg-gray-400 text-white px-6 py-2 rounded-lg shadow-md hover:bg-gray-500 transition-colors duration-300 text-lg">
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
function toggleEditForm(movieId) {
    const form = document.getElementById(`edit-form-${movieId}`);
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

document.addEventListener("DOMContentLoaded", () => {
  // Actor buttons
  document.querySelectorAll(".actor-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      btn.classList.toggle("bg-[var(--secondary)]");
      btn.classList.toggle("text-white");
      btn.classList.toggle("border-[var(--secondary)]");

      const parentRow = btn.closest("tr[id^='edit-form-']");
      if (!parentRow) return;

      const movieId = parentRow.id.replace("edit-form-", "");
      const actorInput = document.getElementById(`edit-actors-${movieId}`);
      const selectedActors = Array.from(parentRow.querySelectorAll(".actor-btn.bg-\\[var\\(--secondary\\)\\]"))
        .map(b => b.dataset.id);
      actorInput.value = selectedActors.join(",");
    });
  });

  // Director buttons
  document.querySelectorAll(".director-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      btn.classList.toggle("bg-[var(--secondary)]");
      btn.classList.toggle("text-white");
      btn.classList.toggle("border-[var(--secondary)]");

      const parentRow = btn.closest("tr[id^='edit-form-']");
      if (!parentRow) return;

      const movieId = parentRow.id.replace("edit-form-", "");
      const directorInput = document.getElementById(`edit-directors-${movieId}`);
      const selectedDirectors = Array.from(parentRow.querySelectorAll(".director-btn.bg-\\[var\\(--secondary\\)\\]"))
        .map(b => b.dataset.id);
      directorInput.value = selectedDirectors.join(",");
    });
  });
});
</script>
